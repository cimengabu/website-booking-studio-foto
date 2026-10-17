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
use App\Services\NotificationService;
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
        $user = Auth::user();
        $query = Booking::with(['user', 'room', 'package', 'payments', 'photographer']);

        // Photographer RBAC: only see their assigned bookings
        if ($user->isPhotographer()) {
            $query->where('photographer_id', $user->id);
        } elseif ($request->filled('photographer_id')) {
            $query->where('photographer_id', $request->photographer_id);
        }

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
        $photographers = User::whereIn('role', ['photographer', 'fotografer'])->get();

        return view('admin.bookings.index', compact('bookings', 'rooms', 'photographers'));
    }

    public function assignPhotographer(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'photographer_id' => 'nullable|exists:users,id',
        ]);

        $booking->update(['photographer_id' => $request->photographer_id]);

        $name = $booking->photographer ? $booking->photographer->name : 'Dikosongkan';
        ActivityLog::record(Auth::id(), 'assign_photographer', "Menugaskan fotografer {$name} untuk booking {$booking->booking_code}");

        return back()->with('success', "Fotografer untuk {$booking->booking_code} berhasil diperbarui ({$name}).");
    }

    public function show(int $id)
    {
        $booking = Booking::with(['user', 'room', 'package', 'bookingAddons.addon', 'payments', 'photoResults', 'photoDelivery', 'review', 'photographer'])->findOrFail($id);
        $photographers = User::whereIn('role', ['photographer', 'fotografer'])->get();

        return view('admin.bookings.show', compact('booking', 'photographers'));
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
            'status' => 'required|in:pending,confirmed,in_progress,completed,delivered,closed,rescheduled,cancelled,expired,no_show,dp_paid,paid,checked_in',
        ]);

        $oldStatus = $booking->status;

        if ($request->status === 'no_show') {
            $booking->update([
                'status' => 'no_show',
                'refund_amount' => 0,
                'penalty_amount' => $booking->paid_amount,
            ]);
            ActivityLog::record(Auth::id(), 'admin_mark_no_show', "Menandai booking {$booking->booking_code} sebagai No-Show (DP Hangus).");
        } else {
            $booking->update(['status' => $request->status]);
            ActivityLog::record(Auth::id(), 'admin_update_booking_status', "Mengubah status booking {$booking->booking_code} dari {$oldStatus} ke {$request->status}");
        }

        if ($request->status === 'cancelled') {
            app(NotificationService::class)->sendCancellationNotification($booking, 'Dibatalkan oleh pihak studio/pelanggan');
        } elseif ($request->status === 'completed' || $request->status === 'delivered') {
            app(NotificationService::class)->sendReviewRequestNotification($booking);
        }

        return back()->with('success', "Status booking berhasil diubah ke {$booking->status_label}.");
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
        $booking->status = 'confirmed';
        $booking->save();

        ActivityLog::record(Auth::id(), 'admin_record_cash', 'Mencatat pembayaran tunai Rp '.number_format($request->amount, 0, ',', '.')." untuk {$booking->booking_code}");

        if ($payment->payment_kind === 'remaining') {
            app(NotificationService::class)->sendSettlementReceived($payment);
        } else {
            app(NotificationService::class)->sendPaymentReceived($payment);
        }

        return back()->with('success', 'Pembayaran tunai berhasil dicatat.');
    }
}
