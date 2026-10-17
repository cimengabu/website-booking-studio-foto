<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReminderHMinus1Job implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        $tomorrow = Carbon::now()->addDay()->toDateString();

        $bookings = Booking::with(['user', 'room', 'package'])
            ->whereDate('booking_date', $tomorrow)
            ->whereIn('status', ['confirmed', 'paid', 'dp_paid'])
            ->get();

        foreach ($bookings as $booking) {
            $alreadySent = ActivityLog::where('action', 'notification_reminder_h1')
                ->where('description', 'like', "%{$booking->booking_code}%")
                ->exists();

            if (! $alreadySent) {
                $notificationService->sendReminderHMinus1($booking);
            }
        }
    }
}
