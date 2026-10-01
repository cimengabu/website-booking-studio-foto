<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todayStr = now()->format('Y-m-d');
        $monthStart = now()->startOfMonth();

        // Income Stats
        $incomeToday = Payment::where('transaction_status', 'settlement')
            ->whereDate('paid_at', $todayStr)
            ->sum('gross_amount');

        $incomeMonth = Payment::where('transaction_status', 'settlement')
            ->where('paid_at', '>=', $monthStart)
            ->sum('gross_amount');

        $totalBookingsMonth = Booking::where('created_at', '>=', $monthStart)->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Recent Bookings
        $recentBookings = Booking::with(['user', 'room', 'package'])
            ->latest()
            ->take(8)
            ->get();

        // Top Packages
        $topPackages = DB::table('bookings')
            ->join('packages', 'bookings.package_id', '=', 'packages.id')
            ->select('packages.name', DB::raw('count(bookings.id) as total_bookings'))
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('total_bookings')
            ->take(5)
            ->get();

        // Hourly Heatmap / Distribution Data
        $hourlyDistribution = DB::table('bookings')
            ->select(DB::raw('SUBSTRING(start_time, 1, 2) as hour'), DB::raw('count(*) as count'))
            ->whereIn('status', ['paid', 'dp_paid', 'checked_in', 'completed'])
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        return view('admin.dashboard', compact(
            'incomeToday',
            'incomeMonth',
            'totalBookingsMonth',
            'totalCustomers',
            'recentBookings',
            'topPackages',
            'hourlyDistribution'
        ));
    }
}
