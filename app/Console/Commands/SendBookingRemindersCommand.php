<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'booking:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Kirim notifikasi pengingat otomatis H-1, H-2 jam, dan permintaan ulasan pelanggan';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Memulai pengecekan jadwal notifikasi otomatis SnapSpace...');

        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay()->toDateString();
        $today = $now->toDateString();

        // 1. PENGINGAT H-1 (Booking besok yang belum dapat notifikasi H-1)
        $tomorrowBookings = Booking::with(['user', 'room', 'package'])
            ->whereDate('booking_date', $tomorrow)
            ->whereIn('status', ['paid', 'dp_paid'])
            ->get();

        $h1Count = 0;
        foreach ($tomorrowBookings as $booking) {
            $alreadySent = ActivityLog::where('action', 'notification_reminder_h1')
                ->where('description', 'like', "%{$booking->booking_code}%")
                ->exists();

            if (! $alreadySent) {
                $notificationService->sendReminderHMinus1($booking);
                $this->line("✅ Terkirim Pengingat H-1 ke {$booking->user?->name} ({$booking->booking_code})");
                $h1Count++;
            }
        }

        // 2. PENGINGAT H-2 JAM (Booking hari ini yang akan dimulai 1.5 - 2.5 jam lagi)
        $todayBookings = Booking::with(['user', 'room', 'package'])
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['paid', 'dp_paid'])
            ->get();

        $h2Count = 0;
        foreach ($todayBookings as $booking) {
            $sessionStart = Carbon::parse($booking->booking_date.' '.$booking->start_time);
            $diffInMinutes = $now->diffInMinutes($sessionStart, false);

            // Jika sesi akan mulai antara 90 menit (1.5 jam) hingga 150 menit (2.5 jam) ke depan
            if ($diffInMinutes >= 90 && $diffInMinutes <= 150) {
                $alreadySent = ActivityLog::where('action', 'notification_reminder_h2_hours')
                    ->where('description', 'like', "%{$booking->booking_code}%")
                    ->exists();

                if (! $alreadySent) {
                    $notificationService->sendReminderHMinus2Hours($booking);
                    $this->line("⚡ Terkirim Pengingat H-2 Jam ke {$booking->user?->name} ({$booking->booking_code})");
                    $h2Count++;
                }
            }
        }

        // 3. PERMINTAAN ULASAN SETELAH SESI (Booking yang berstatus completed dalam 24 jam terakhir)
        $completedBookings = Booking::with(['user', 'room', 'package', 'review'])
            ->where('status', 'completed')
            ->doesntHave('review')
            ->where('updated_at', '>=', $now->copy()->subHours(24))
            ->get();

        $reviewCount = 0;
        foreach ($completedBookings as $booking) {
            $alreadySent = ActivityLog::where('action', 'notification_review_request')
                ->where('description', 'like', "%{$booking->booking_code}%")
                ->exists();

            if (! $alreadySent) {
                $notificationService->sendReviewRequestNotification($booking);
                $this->line("⭐ Terkirim Permintaan Ulasan ke {$booking->user?->name} ({$booking->booking_code})");
                $reviewCount++;
            }
        }

        $this->info("Selesai! Terkirim: {$h1Count} pengingat H-1, {$h2Count} pengingat H-2 jam, {$reviewCount} permintaan ulasan.");

        return Command::SUCCESS;
    }
}
