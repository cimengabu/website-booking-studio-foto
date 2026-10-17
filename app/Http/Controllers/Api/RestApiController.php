<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Portfolio;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RestApiController extends Controller
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected PricingService $pricingService,
        protected BookingService $bookingService,
        protected PaymentService $paymentService
    ) {}

    /**
     * 1. AUTH API
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'ok',
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                ],
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'loyalty_points' => 10, // Bonus pendaftaran
        ]);

        Auth::login($user);

        return response()->json([
            'status' => 'ok',
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                ],
            ],
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'status' => 'ok',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                ],
            ],
        ]);
    }

    /**
     * 2. PAKET & RUANGAN API
     */
    public function getPackages(): JsonResponse
    {
        $packages = Package::where('is_active', true)->get();

        return response()->json([
            'status' => 'ok',
            'data' => $packages,
        ]);
    }

    public function getRooms(): JsonResponse
    {
        $rooms = Room::with(['primaryImage', 'images'])->where('is_active', true)->orderBy('sort_order')->get();

        return response()->json([
            'status' => 'ok',
            'data' => $rooms,
        ]);
    }

    public function getRoomDetail(int $id): JsonResponse
    {
        $room = Room::with(['images', 'primaryImage'])->where('is_active', true)->findOrFail($id);

        return response()->json([
            'status' => 'ok',
            'data' => $room,
        ]);
    }

    public function getAddons(): JsonResponse
    {
        $addons = Addon::where('is_active', true)->get();

        return response()->json([
            'status' => 'ok',
            'data' => $addons,
        ]);
    }

    /**
     * 3. KETERSEDIAAN SLOT & KALKULASI HARGA
     */
    public function getSlots(Request $request): JsonResponse
    {
        $roomId = (int) ($request->query('room_id') ?: Room::where('is_active', true)->orderBy('sort_order')->value('id'));
        $packageId = (int) ($request->query('package_id') ?: Package::where('is_active', true)->value('id'));
        $date = $request->query('date') ?: now()->toDateString();

        $slots = $this->availabilityService->getSlots($roomId, $date, $packageId);

        return response()->json([
            'status' => 'ok',
            'data' => [
                'room_id' => $roomId,
                'package_id' => $packageId,
                'date' => $date,
                'slots' => $slots,
            ],
        ]);
    }

    public function calculatePrice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'people_count' => 'nullable|integer|min:1',
            'addons' => 'nullable|array',
            'promo_code' => 'nullable|string',
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $maxPoints = Auth::user()?->loyalty_points ?? 0;
        $redeemPoints = min((int) ($request->redeem_points ?? 0), $maxPoints);

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
            'status' => 'ok',
            'data' => $pricing,
        ]);
    }

    /**
     * 4. BOOKING CRUD & LOCK SLOT
     */
    public function createBooking(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'people_count' => 'required|integer|min:1',
            'payment_option' => 'required|in:dp,full',
            'addons' => 'nullable|array',
            'promo_code' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id() ?? User::where('role', 'customer')->value('id') ?? 1;

            $booking = $this->bookingService->createBooking($data);

            return response()->json([
                'status' => 'ok',
                'message' => 'Booking berhasil dibuat.',
                'data' => [
                    'booking_code' => $booking->booking_code,
                    'total_price' => $booking->total_price,
                    'status' => $booking->status,
                    'expires_at' => $booking->expires_at?->toIso8601String(),
                    'pay_url' => route('booking.pay', $booking->booking_code),
                ],
            ], 201);
        } catch (SlotUnavailableException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat booking: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getBooking(string $code): JsonResponse
    {
        $booking = Booking::with(['room', 'package', 'bookingAddons.addon', 'user', 'payments', 'review'])
            ->where('booking_code', $code)
            ->first();

        if (! $booking) {
            return response()->json(['status' => 'error', 'message' => 'Booking tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $booking,
        ]);
    }

    /**
     * 5. PEMBAYARAN: CREATE PAYMENT
     */
    public function createPaymentTransaction(Request $request, string $code): JsonResponse
    {
        $booking = Booking::where('booking_code', $code)->first();
        if (! $booking) {
            return response()->json(['status' => 'error', 'message' => 'Booking tidak ditemukan'], 404);
        }

        $kind = $request->input('payment_kind', $booking->payment_option);
        $payment = $this->paymentService->createPayment($booking, $kind);

        return response()->json([
            'status' => 'ok',
            'message' => 'Transaksi pembayaran siap.',
            'data' => [
                'order_id' => $payment->order_id,
                'gross_amount' => $payment->gross_amount,
                'payment_kind' => $payment->payment_kind,
                'snap_token' => $payment->snap_token,
                'transaction_status' => $payment->transaction_status,
            ],
        ]);
    }

    /**
     * 6. VOUCHER / PROMO CHECK
     */
    public function validateVoucher(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        $code = strtoupper(trim($request->code));

        $promo = Promo::where('code', $code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', today());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', today());
            })
            ->first();

        if (! $promo || $promo->used_count >= $promo->quota) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode voucher tidak valid atau kuota telah habis.',
            ], 422);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Voucher valid.',
            'data' => [
                'code' => $promo->code,
                'type' => $promo->type,
                'value' => $promo->value,
                'min_order' => $promo->min_order,
                'max_discount' => $promo->max_discount,
            ],
        ]);
    }

    /**
     * 7. GALERI / PORTFOLIO
     */
    public function getGalleries(): JsonResponse
    {
        $portfolios = Portfolio::with('room')->latest()->get();

        return response()->json([
            'status' => 'ok',
            'data' => $portfolios,
        ]);
    }

    /**
     * 8. ULASAN (REVIEWS)
     */
    public function getReviews(): JsonResponse
    {
        $reviews = Review::with(['user', 'booking.room'])
            ->where('is_visible', true)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'status' => 'ok',
            'data' => $reviews,
        ]);
    }

    /**
     * 9. LAPORAN / RINGKASAN
     */
    public function getReportSummary(): JsonResponse
    {
        $today = Carbon::today()->toDateString();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $todayBookings = Booking::whereDate('booking_date', $today)
            ->whereNotIn('status', ['cancelled', 'expired'])
            ->count();

        $monthRevenue = Booking::whereMonth('booking_date', $thisMonth)
            ->whereYear('booking_date', $thisYear)
            ->whereIn('status', ['paid', 'completed', 'dp_paid'])
            ->sum('paid_amount');

        $pendingBookings = Booking::where('status', 'pending')->count();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'today_bookings' => $todayBookings,
                'month_revenue' => (float) $monthRevenue,
                'pending_bookings' => $pendingBookings,
                'active_rooms' => Room::where('is_active', true)->count(),
                'active_packages' => Package::where('is_active', true)->count(),
            ],
        ]);
    }
}
