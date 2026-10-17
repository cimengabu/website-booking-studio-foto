<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReviewRequestJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        $now = Carbon::now();

        // Ambil booking completed dalam 48 jam terakhir yang belum ada review
        $completedBookings = Booking::with(['user', 'room', 'package', 'review'])
            ->where('status', 'completed')
            ->doesntHave('review')
            ->where('updated_at', '>=', $now->copy()->subHours(48))
            ->get();

        foreach ($completedBookings as $booking) {
            $alreadySent = ActivityLog::where('action', 'notification_review_request')
                ->where('description', 'like', "%{$booking->booking_code}%")
                ->exists();

            if (! $alreadySent) {
                $notificationService->sendReviewRequestNotification($booking);
            }
        }
    }
}
