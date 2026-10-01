<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminBookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room', 'package', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->latest()->paginate(15);
        $rooms = Room::all();

        return view('admin.bookings.index', compact('bookings', 'rooms'));
    }

    public function show(int $id)
    {
        $booking = Booking::with(['user', 'room', 'package', 'bookingAddons.addon', 'payments', 'photoResults', 'photoDelivery', 'review'])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    public function createWalkin()
    {
        $rooms = Room::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();
        $customers = User::where('role', 'customer')->get();

        return view('admin.bookings.create-walkin', compact('rooms', 'packages', 'customers'));
    }

    public function storeWalkin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'package_id' => 'required|exists:packages,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'people_count' => 'required|integer|min:1',
            'payment_kind' => 'required|in:dp,full',
            'payment_method' => 'required|string',
        ]);

        try {
            $data = $request->all();
            $data['payment_option'] = $request->payment_kind;

            $booking = $this->bookingService->createBooking($data);

            $payment = $this->paymentService->createPayment($booking, $request->payment_kind);
            $this->paymentService->processPaymentSuccess($payment, $request->payment_method.' (Walk-in Cash/Edc)');

            return redirect()->route('admin.bookings.show', $booking->id)
                ->with('success', "Booking Walk-in berhasil dibuat: {$booking->booking_code}");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['walkin_error' => $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,dp_paid,paid,checked_in,completed,cancelled,expired',
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        ActivityLog::record(Auth::id(), 'admin_update_booking_status', "Mengubah status booking {$booking->booking_code} dari {$oldStatus} ke {$request->status}");

        return back()->with('success', "Status booking berhasil diubah ke {$request->status}.");
    }

    public function recordCashPayment(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|string',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'order_id' => "CASH-{$booking->booking_code}-".time(),
            'payment_kind' => $booking->paid_amount > 0 ? 'remaining' : 'full',
            'payment_type' => $request->payment_method,
            'gross_amount' => $request->amount,
            'transaction_status' => 'settlement',
            'paid_at' => now(),
        ]);

        $newPaidAmount = (float) $booking->paid_amount + (float) $request->amount;
        $booking->paid_amount = $newPaidAmount;
        if ($newPaidAmount >= (float) $booking->total_price) {
            $booking->status = 'paid';
        }
        $booking->save();

        ActivityLog::record(Auth::id(), 'admin_record_cash', 'Mencatat pembayaran tunai Rp '.number_format($request->amount, 0, ',', '.')." untuk {$booking->booking_code}");

        return back()->with('success', 'Pembayaran tunai berhasil dicatat.');
    }
}
