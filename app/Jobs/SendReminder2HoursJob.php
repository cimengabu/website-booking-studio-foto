<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReminder2HoursJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        $bookings = Booking::with(['user', 'room', 'package'])
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['confirmed', 'paid', 'dp_paid'])
            ->get();

        foreach ($bookings as $booking) {
            $sessionStart = Carbon::parse($booking->booking_date.' '.$booking->start_time);
            $diffInMinutes = $now->diffInMinutes($sessionStart, false);

            // Sesi yang akan mulai antara 90 menit (1.5 jam) hingga 150 menit (2.5 jam)
            if ($diffInMinutes >= 90 && $diffInMinutes <= 150) {
                $alreadySent = ActivityLog::where('action', 'notification_reminder_h2_hours')
                    ->where('description', 'like', "%{$booking->booking_code}%")
                    ->exists();

                if (! $alreadySent) {
                    $notificationService->sendReminderHMinus2Hours($booking);
                }
            }
        }
    }
}
