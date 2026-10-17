<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Promo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /** @var Collection $allBookings */
        $allBookings = Booking::with(['room', 'package'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Next upcoming booking (confirmed / paid, in the future)
        $nextBooking = Booking::with(['room.images', 'package'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'paid', 'dp_paid'])
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->first();

        // Stats
        $stats = [
            'total' => $allBookings->count(),
            'upcoming' => $allBookings->whereIn('status', ['confirmed', 'paid', 'dp_paid'])->where('booking_date', '>=', now()->toDateString())->count(),
            'pending' => $allBookings->where('status', 'pending')->count(),
            'completed' => $allBookings->whereIn('status', ['completed', 'delivered', 'closed'])->count(),
            'total_spent' => $allBookings->whereIn('status', ['confirmed', 'paid', 'dp_paid', 'completed', 'delivered', 'closed'])->sum('paid_amount'),
        ];

        // Active promos (public display)
        $promos = Promo::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where('quota', '>', 0)
            ->whereRaw('used_count < quota')
            ->take(3)
            ->get();

        // Recent bookings for dashboard
        $recentBookings = $allBookings->take(5);

        return view('customer.dashboard.index', compact(
            'user', 'nextBooking', 'stats', 'promos', 'recentBookings'
        ));
    }

    public function payments()
    {
        $user = Auth::user();

        $payments = Payment::with(['booking.room', 'booking.package'])
            ->whereHas('booking', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(12);

        $totalPaid = Payment::whereHas('booking', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'success')
            ->sum('amount');

        return view('customer.dashboard.payments', compact('payments', 'totalPaid'));
    }

    public function gallery()
    {
        $user = Auth::user();

        $bookings = Booking::with(['photoResults', 'photoDelivery', 'room', 'package'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['delivered', 'completed', 'closed'])
            ->whereHas('photoResults')
            ->latest()
            ->get();

        return view('customer.dashboard.gallery', compact('bookings'));
    }
}
