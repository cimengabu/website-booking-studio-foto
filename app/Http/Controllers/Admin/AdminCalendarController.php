<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminCalendarController extends Controller
{
    public function index()
    {
        $rooms = Room::all();

        return view('admin.calendar.index', compact('rooms'));
    }

    public function eventsApi(Request $request)
    {
        $bookings = Booking::with(['user', 'room', 'package'])
            ->whereIn('status', ['paid', 'dp_paid', 'checked_in', 'completed', 'pending'])
            ->get();

        $events = [];
        $colorMap = [
            'pending' => '#F2C94C',
            'dp_paid' => '#3B82F6',
            'paid' => '#10B981',
            'checked_in' => '#8B5CF6',
            'completed' => '#6B7280',
            'cancelled' => '#EF4444',
        ];

        foreach ($bookings as $b) {
            $start = $b->booking_date->format('Y-m-d').'T'.$b->start_time;
            $end = $b->booking_date->format('Y-m-d').'T'.$b->end_time;

            $events[] = [
                'id' => $b->id,
                'title' => "{$b->room->name} · {$b->user->name}",
                'start' => $start,
                'end' => $end,
                'color' => $colorMap[$b->status] ?? '#1F2937',
                'extendedProps' => [
                    'booking_id' => $b->id,
                    'status' => $b->status,
                    'package' => $b->package->name,
                ],
            ];
        }

        return response()->json($events);
    }
}
