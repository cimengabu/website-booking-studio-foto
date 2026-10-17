<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
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
                    'type' => 'booking',
                ],
            ];
        }

        // Include Blocked Slots (Holidays / Maintenance)
        $blockedSlots = BlockedSlot::with('room')->get();
        foreach ($blockedSlots as $block) {
            $roomName = $block->room ? $block->room->name : 'Semua Ruangan';
            $events[] = [
                'id' => 'block_'.$block->id,
                'title' => "[TUTUP] {$roomName}: {$block->reason}",
                'start' => $block->date.'T'.$block->start_time,
                'end' => $block->date.'T'.$block->end_time,
                'color' => '#BE123C', // Rose 700
                'editable' => false,
                'extendedProps' => [
                    'type' => 'blocked',
                    'reason' => $block->reason,
                ],
            ];
        }

        return response()->json($events);
    }

    public function rescheduleApi(Request $request, int $id)
    {
        $booking = Booking::with('package')->findOrFail($id);
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
        ]);

        $start = Carbon::parse($request->start_time);
        $duration = $booking->package->duration_minutes ?? 30;
        $end = (clone $start)->addMinutes($duration);

        $booking->update([
            'original_booking_date' => $booking->original_booking_date ?? $booking->booking_date,
            'original_start_time' => $booking->original_start_time ?? $booking->start_time,
            'booking_date' => $request->date,
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'status' => 'rescheduled',
            'reschedule_count' => ($booking->reschedule_count ?? 0) + 1,
        ]);

        ActivityLog::record(
            auth()->id(),
            'reschedule',
            "Reschedule booking {$booking->booking_code} ke {$request->date} jam {$start->format('H:i')} WIB via kalender"
        );

        return response()->json([
            'success' => true,
            'message' => "Jadwal booking {$booking->booking_code} berhasil dipindahkan ke {$request->date} jam {$start->format('H:i')} WIB.",
        ]);
    }
}
