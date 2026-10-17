<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Room;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingWizardController extends Controller
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected PricingService $pricingService,
        protected BookingService $bookingService,
        protected PaymentService $paymentService
    ) {}

    public function showWizard(Request $request)
    {
        $rooms = Room::with('primaryImage')->where('is_active', true)->orderBy('sort_order')->get();
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();

        $selectedPackageId = $request->query('package_id', $packages->first()?->id);
        $selectedRoomId = $request->query('room_id', $rooms->first()?->id);

        return view('customer.booking.wizard', compact('rooms', 'packages', 'addons', 'selectedPackageId', 'selectedRoomId'));
    }

    public function getSlotsApi(Request $request)
    {
        $roomId = (int) ($request->query('room_id') ?: Room::where('is_active', true)->orderBy('sort_order')->value('id'));
        $packageId = (int) ($request->query('package_id') ?: Package::where('is_active', true)->value('id'));
        $date = $request->query('date') ?: now()->toDateString();

        if (! Room::where('id', $roomId)->where('is_active', true)->exists()) {
            $roomId = (int) (Room::where('is_active', true)->orderBy('sort_order')->value('id') ?? 1);
        }

        if (! Package::where('id', $packageId)->where('is_active', true)->exists()) {
            $packageId = (int) (Package::where('is_active', true)->value('id') ?? 1);
        }

        $slots = $this->availabilityService->getSlots($roomId, $date, $packageId);

        return response()->json([
            'status' => 'success',
            'room_id' => $roomId,
            'package_id' => $packageId,
            'date' => $date,
            'slots' => $slots,
        ]);
    }

    public function calculatePriceApi(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'people_count' => 'nullable|integer|min:1',
            'addons' => 'nullable|array',
            'promo_code' => 'nullable|string',
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        $maxUserPoints = Auth::user()?->loyalty_points ?? 0;
        $redeemPoints = min((int) ($request->redeem_points ?? 0), $maxUserPoints);

        $pricing = $this->pricingService->calculate(
            (int) $request->package_id,
            $request->date,
            $request->start_time,
            (int) ($request->people_count ?? 1),
            $request->addons ?? [],
            $request->promo_code,
            $redeemPoints
        );

        return response()->json([
            'status' => 'success',
            'pricing' => $pricing,
        ]);
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'people_count' => 'required|integer|min:1',
            'payment_option' => 'required|in:dp,full',
            'addons' => 'nullable|array',
            'promo_code' => 'nullable|string',
            'redeem_points' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ], [
            'booking_date.required' => 'Tanggal booking wajib dipilih.',
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh di masa lampau.',
            'start_time.required' => 'Jam sesi foto wajib dipilih.',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

            // Deduct redeemed points if used
            if (! empty($data['redeem_points']) && Auth::user()) {
                $pointsToDeduct = min((int) $data['redeem_points'], Auth::user()->loyalty_points);
                if ($pointsToDeduct > 0) {
                    Auth::user()->decrement('loyalty_points', $pointsToDeduct);
                }
            }

            $booking = $this->bookingService->createBooking($data);

            return redirect()->route('booking.pay', $booking->booking_code)
                ->with('success', 'Booking berhasil dibuat! Silakan selesaikan pembayaran sebelum batas waktu berakhir.');
        } catch (SlotUnavailableException $e) {
            return back()->withInput()->withErrors(['slot_error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['slot_error' => 'Gagal membuat booking: '.$e->getMessage()]);
        }
    }

    public function showPaymentPage(string $code)
    {
        $booking = Booking::with(['room', 'package', 'bookingAddons.addon', 'user'])
            ->where('booking_code', $code)
            ->firstOrFail();

        // Ensure user is authorized to pay for this booking
        if ($booking->user_id !== Auth::id() && ! Auth::user()?->isAdmin()) {
            abort(403);
        }

        // Check if expired
        if ($booking->status === 'pending' && $booking->expires_at->isPast()) {
            $booking->update(['status' => 'expired']);
        }

        return view('customer.booking.payment', compact('booking'));
    }

    public function processSimulatedPayment(Request $request, string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();

        $request->validate([
            'payment_kind' => 'required|in:dp,full,remaining',
            'payment_method' => 'required|string',
        ]);

        $payment = $this->paymentService->createPayment($booking, $request->payment_kind);
        $this->paymentService->processPaymentSuccess($payment, $request->payment_method, [
            'simulated' => true,
            'gateway' => 'Midtrans Sandbox Simulator',
        ]);

        return redirect()->route('my-bookings.show', $booking->booking_code)
            ->with('success', 'Pembayaran berhasil dikonfirmasi! E-Tiket QR Anda telah terbit.');
    }

    /**
     * Edge case handling: Pembayaran berhasil tapi webhook terlambat / user menutup halaman
     * Verifikasi langsung ke payment gateway tanpa harus menunggu webhook
     */
    public function checkPaymentStatus(Request $request, string $code)
    {
        $booking = Booking::with('payments')->where('booking_code', $code)->firstOrFail();

        // 1. Jika sudah dikonfirmasi
        if (in_array($booking->status, ['confirmed', 'paid', 'dp_paid', 'completed'])) {
            return response()->json([
                'status' => 'success',
                'is_paid' => true,
                'redirect_url' => route('my-bookings.show', $booking->booking_code),
                'message' => 'Pembayaran Anda telah terkonfirmasi!',
            ]);
        }

        // 2. Cek apakah expired
        if ($booking->status === 'pending' && $booking->expires_at && $booking->expires_at->isPast()) {
            $booking->update(['status' => 'expired']);

            return response()->json([
                'status' => 'expired',
                'is_paid' => false,
                'redirect_url' => route('booking.pay', $booking->booking_code),
                'message' => 'Waktu pembayaran telah habis. Slot telah dilepaskan kembali ke sistem.',
            ]);
        }

        // 3. Query Midtrans API langsung jika order_id ada
        $latestPayment = $booking->payments()->latest()->first();
        if ($latestPayment) {
            $serverKey = config('services.midtrans.server_key');
            $isProduction = config('services.midtrans.is_production', false);

            if (! empty($serverKey) && ! str_starts_with($latestPayment->order_id, 'SIMULATED')) {
                try {
                    $url = $isProduction
                        ? "https://api.midtrans.com/v2/{$latestPayment->order_id}/status"
                        : "https://api.sandbox.midtrans.com/v2/{$latestPayment->order_id}/status";

                    $res = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                        ->timeout(5)
                        ->get($url);

                    if ($res->successful()) {
                        $data = $res->json();
                        $status = $data['transaction_status'] ?? '';
                        $fraud = $data['fraud_status'] ?? 'accept';

                        if ($status === 'settlement' || ($status === 'capture' && $fraud === 'accept')) {
                            $this->paymentService->processPaymentSuccess(
                                $latestPayment,
                                $data['payment_type'] ?? 'midtrans',
                                $data
                            );

                            return response()->json([
                                'status' => 'success',
                                'is_paid' => true,
                                'redirect_url' => route('my-bookings.show', $booking->booking_code),
                                'message' => 'Pembayaran berhasil diverifikasi langsung dari Gateway!',
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("Direct Midtrans status check error: {$e->getMessage()}");
                }
            }
        }

        return response()->json([
            'status' => 'pending',
            'is_paid' => false,
            'message' => 'Pembayaran belum terdeteksi. Silakan selesaikan transaksi pada aplikasi pembayaran Anda.',
        ]);
    }

    public function checkBookingApi(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string',
            'identifier' => 'required|string',
        ]);

        $code = trim($request->booking_code);
        $identifier = trim($request->identifier);

        $booking = Booking::with(['user', 'room', 'package', 'payments'])
            ->where('booking_code', $code)
            ->whereHas('user', function ($q) use ($identifier) {
                $q->where('email', $identifier)
                    ->orWhere('phone', $identifier);
            })
            ->first();

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Kode booking tidak ditemukan atau data email/nomor HP tidak cocok.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'booking_code' => $booking->booking_code,
                'status' => $booking->status,
                'status_label' => $booking->status_label,
                'package_name' => $booking->package->name,
                'room_name' => $booking->room->name,
                'schedule' => $booking->booking_date->translatedFormat('d M Y').' ('.substr($booking->start_time, 0, 5).'–'.substr($booking->end_time, 0, 5).' WIB)',
                'total_price' => 'Rp '.number_format($booking->total_price, 0, ',', '.'),
                'paid_amount' => 'Rp '.number_format($booking->paid_amount, 0, ',', '.'),
                'remaining_balance' => (float) $booking->remaining_balance,
                'remaining_balance_formatted' => 'Rp '.number_format($booking->remaining_balance, 0, ',', '.'),
                'pay_url' => route('booking.pay', $booking->booking_code),
                'reschedule_count' => $booking->reschedule_count,
            ],
        ]);
    }
}
