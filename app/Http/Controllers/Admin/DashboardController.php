<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $todayStr = now()->format('Y-m-d');
        $monthStart = now()->startOfMonth();

        // ── 1. OWNER / ADMIN STATS ──
        $incomeToday = Payment::where('transaction_status', 'settlement')
            ->whereDate('paid_at', $todayStr)
            ->sum('gross_amount');

        $incomeMonth = Payment::where('transaction_status', 'settlement')
            ->where('paid_at', '>=', $monthStart)
            ->sum('gross_amount');

        $totalBookingsMonth = Booking::where('created_at', '>=', $monthStart)->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Top Packages
        $topPackages = DB::table('bookings')
            ->join('packages', 'bookings.package_id', '=', 'packages.id')
            ->select('packages.name', DB::raw('count(bookings.id) as total_bookings'))
            ->groupBy('packages.id', 'packages.name')
            ->orderByDesc('total_bookings')
            ->take(5)
            ->get();

        // Hourly Distribution Data
        $hourlyDistribution = DB::table('bookings')
            ->select(DB::raw('SUBSTRING(start_time, 1, 2) as hour'), DB::raw('count(*) as count'))
            ->whereIn('status', ['paid', 'dp_paid', 'checked_in', 'completed', 'confirmed', 'in_progress'])
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // ── 2. STAFF / OPERATIONAL STATS ──
        $todayBookings = Booking::with(['user', 'room', 'package', 'photographer'])
            ->whereDate('booking_date', $todayStr)
            ->orderBy('start_time')
            ->get();

        $pendingPaymentsCount = Booking::where('status', 'pending')->count();
        $checkedInTodayCount = $todayBookings->whereIn('status', ['checked_in', 'in_progress'])->count();

        // ── 3. PHOTOGRAPHER STATS ──
        $myPhotographerBookings = collect();
        $myPendingUploads = collect();
        $photographerStats = [
            'today_sessions' => 0,
            'upcoming_sessions' => 0,
            'pending_uploads' => 0,
            'completed_uploads' => 0,
        ];

        if ($user->isPhotographer() || $user->isOwner()) {
            $photoQuery = Booking::with(['user', 'room', 'package', 'photoResults', 'photoDelivery']);
            if ($user->isPhotographer()) {
                $photoQuery->where(function ($q) use ($user) {
                    $q->where('photographer_id', $user->id)
                        ->orWhereNull('photographer_id');
                });
            }

            $myPhotographerBookings = (clone $photoQuery)
                ->whereDate('booking_date', $todayStr)
                ->orderBy('start_time')
                ->get();

            $myPendingUploads = (clone $photoQuery)
                ->whereIn('status', ['completed', 'checked_in', 'in_progress', 'confirmed'])
                ->doesntHave('photoResults')
                ->latest()
                ->take(6)
                ->get();

            $photographerStats = [
                'today_sessions' => $myPhotographerBookings->count(),
                'upcoming_sessions' => Booking::when($user->isPhotographer(), fn ($q) => $q->where('photographer_id', $user->id))
                    ->where('booking_date', '>=', $todayStr)->count(),
                'pending_uploads' => Booking::when($user->isPhotographer(), fn ($q) => $q->where('photographer_id', $user->id))
                    ->whereIn('status', ['completed', 'checked_in'])->doesntHave('photoResults')->count(),
                'completed_uploads' => Booking::when($user->isPhotographer(), fn ($q) => $q->where('photographer_id', $user->id))
                    ->has('photoResults')->count(),
            ];
        }

        // Recent Bookings (All)
        $recentBookings = Booking::with(['user', 'room', 'package', 'photographer'])
            ->latest()
            ->take(8)
            ->get();

        $totalRooms = Room::count();
        $bookingTodayCount = $todayBookings->count();
        $occupancyRate = min(100, round(($bookingTodayCount / max(1, $totalRooms * 12)) * 100));
        $recentActivities = ActivityLog::with('user')->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'user',
            'incomeToday',
            'incomeMonth',
            'totalBookingsMonth',
            'totalCustomers',
            'recentBookings',
            'topPackages',
            'hourlyDistribution',
            'todayBookings',
            'bookingTodayCount',
            'occupancyRate',
            'pendingPaymentsCount',
            'checkedInTodayCount',
            'myPhotographerBookings',
            'myPendingUploads',
            'photographerStats',
            'recentActivities'
        ));
    }

    /**
     * Topbar Global Live Search API (Ctrl + K)
     */
    public function searchApi(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // 1. Search Bookings
        $bookings = Booking::with(['user', 'room', 'package'])
            ->where(function ($query) use ($q) {
                $query->where('booking_code', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            })
            ->latest()
            ->take(6)
            ->get();

        foreach ($bookings as $b) {
            $results[] = [
                'category' => 'Bookings',
                'badge' => $b->status_label,
                'badge_class' => 'bg-[#E8451E]/20 text-[#E8451E] border border-[#E8451E]/30',
                'title' => $b->booking_code.' — '.($b->user?->name ?? 'Tamu Walk-in'),
                'subtitle' => ($b->room?->name ?? '-').' · '.date('d M Y', strtotime((string) $b->booking_date)).' ('.substr($b->start_time, 0, 5).' WIB)',
                'url' => route('admin.bookings.show', $b->id),
                'icon' => 'fa-ticket',
            ];
        }

        // 2. Search Users & Staff
        $users = User::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%");
        })
            ->take(4)
            ->get();

        foreach ($users as $u) {
            $results[] = [
                'category' => 'Pengguna & Tim',
                'badge' => $u->role_label,
                'badge_class' => $u->role_badge_class,
                'title' => $u->name,
                'subtitle' => $u->email.($u->phone ? ' · '.$u->phone : ''),
                'url' => route('admin.customers.index', ['search' => $u->name]),
                'icon' => 'fa-user',
            ];
        }

        // 3. Search Rooms
        $rooms = Room::where('name', 'like', "%{$q}%")
            ->orWhere('theme', 'like', "%{$q}%")
            ->take(3)
            ->get();

        foreach ($rooms as $r) {
            $results[] = [
                'category' => 'Ruangan Studio',
                'badge' => 'Ruangan',
                'badge_class' => 'bg-[#F2C94C]/20 text-[#F2C94C] border border-[#F2C94C]/30',
                'title' => $r->name,
                'subtitle' => 'Tema: '.$r->theme.' · Kapasitas maks '.$r->capacity.' orang',
                'url' => route('admin.rooms.index'),
                'icon' => 'fa-door-open',
            ];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Topbar Notifications Alert API
     */
    public function notificationsApi(): JsonResponse
    {
        $notifications = collect();

        // 1. Pending Bookings (Perlu konfirmasi/bayar)
        $pending = Booking::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn ($b) => [
                'id' => 'p_'.$b->id,
                'title' => 'Booking Menunggu Pembayaran',
                'message' => $b->booking_code.' · '.($b->user?->name ?? 'Guest'),
                'time' => $b->created_at->diffForHumans(),
                'url' => route('admin.bookings.show', $b->id),
                'icon' => 'fa-clock',
                'color' => 'text-[#F2C94C]',
                'bg' => 'bg-[#F2C94C]/15',
            ]);

        // 2. Pembayaran Baru Masuk (Confirmed / DP / Paid)
        $payments = Booking::with('user')
            ->whereIn('status', ['confirmed', 'paid', 'dp_paid'])
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(fn ($b) => [
                'id' => 'pay_'.$b->id,
                'title' => 'Pembayaran Diterima',
                'message' => $b->booking_code.' · Rp '.number_format($b->paid_amount, 0, ',', '.'),
                'time' => $b->updated_at->diffForHumans(),
                'url' => route('admin.bookings.show', $b->id),
                'icon' => 'fa-circle-check',
                'color' => 'text-emerald-400',
                'bg' => 'bg-emerald-500/15',
            ]);

        // 3. Ulasan Baru dari Pelanggan
        $reviews = Review::with(['user', 'booking'])
            ->latest()
            ->take(2)
            ->get()
            ->map(fn ($r) => [
                'id' => 'rev_'.$r->id,
                'title' => 'Ulasan Baru ★ '.$r->rating,
                'message' => ($r->user?->name ?? 'User').': "'.Str::limit($r->comment, 40).'"',
                'time' => $r->created_at->diffForHumans(),
                'url' => route('admin.reviews.index'),
                'icon' => 'fa-star',
                'color' => 'text-[#E8451E]',
                'bg' => 'bg-[#E8451E]/15',
            ]);

        $all = $pending->concat($payments)->concat($reviews);

        return response()->json([
            'unread_count' => $pending->count(),
            'notifications' => $all,
        ]);
    }
}
