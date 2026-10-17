<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireBookingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    $booking->update(['status' => 'expired']);
                    ActivityLog::record(
                        $booking->user_id,
                        'booking_expired',
                        "Booking {$booking->booking_code} otomatis dibatalkan karena melewati batas waktu pembayaran."
                    );
                }
            });
    }
}
