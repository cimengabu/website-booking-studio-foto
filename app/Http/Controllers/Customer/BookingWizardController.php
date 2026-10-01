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
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'package_id' => 'required|exists:packages,id',
        ]);

        $slots = $this->availabilityService->getSlots(
            (int) $request->room_id,
            $request->date,
            (int) $request->package_id
        );

        return response()->json([
            'status' => 'success',
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
        ]);

        $pricing = $this->pricingService->calculate(
            (int) $request->package_id,
            $request->date,
            $request->start_time,
            (int) ($request->people_count ?? 1),
            $request->addons ?? [],
            $request->promo_code
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
            'notes' => 'nullable|string|max:500',
        ], [
            'booking_date.required' => 'Tanggal booking wajib dipilih.',
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh di masa lampau.',
            'start_time.required' => 'Jam sesi foto wajib dipilih.',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();

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
}
