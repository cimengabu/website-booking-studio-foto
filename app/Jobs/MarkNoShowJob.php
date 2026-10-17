<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MarkNoShowJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        Booking::whereIn('status', ['confirmed', 'paid', 'dp_paid'])
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('booking_date', '<', $today)
                    ->orWhere(function ($q) use ($today, $currentTime) {
                        $q->whereDate('booking_date', $today)
                            ->whereTime('end_time', '<', $currentTime);
                    });
            })
            ->whereNull('checked_in_at')
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    $booking->update(['status' => 'no_show']);
                    ActivityLog::record(
                        $booking->user_id,
                        'booking_no_show',
                        "Booking {$booking->booking_code} ditandai No-Show otomatis karena melewati waktu sesi tanpa check-in."
                    );
                }
            });
    }
}
