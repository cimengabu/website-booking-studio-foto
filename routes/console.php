<?php

use App\Jobs\CleanupExpiredDataJob;
use App\Jobs\ExpireBookingJob;
use App\Jobs\MarkNoShowJob;
use App\Jobs\SendReminder2HoursJob;
use App\Jobs\SendReminderHMinus1Job;
use App\Jobs\SendReviewRequestJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Queue & Cron Jobs untuk otomasi SnapSpace Studio
Schedule::job(new ExpireBookingJob)->everyMinute()->withoutOverlapping();
Schedule::job(new SendReminder2HoursJob)->everyFifteenMinutes()->withoutOverlapping();
Schedule::job(new SendReminderHMinus1Job)->dailyAt('09:00');
Schedule::job(new SendReviewRequestJob)->dailyAt('18:00');
Schedule::job(new MarkNoShowJob)->hourly()->withoutOverlapping();
Schedule::job(new CleanupExpiredDataJob)->dailyAt('02:00');

// Perintah manual konsol cadangan
Schedule::command('booking:send-reminders')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
