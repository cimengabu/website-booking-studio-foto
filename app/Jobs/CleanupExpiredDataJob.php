<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupExpiredDataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Kadaluarsakan transaksi pembayaran pending yang booking-nya sudah expired atau cancelled
        Payment::whereHas('booking', function ($q) {
            $q->whereIn('status', ['expired', 'cancelled']);
        })
            ->where('transaction_status', 'pending')
            ->where('created_at', '<', now()->subHours(2))
            ->update(['transaction_status' => 'expire']);

        // 2. Bersihkan sessions usang jika menggunakan database session driver
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(30)->timestamp)
                ->delete();
        }

        // 3. Bersihkan failed_jobs yang lebih dari 7 hari
        if (Schema::hasTable('failed_jobs')) {
            DB::table('failed_jobs')
                ->where('failed_at', '<', now()->subDays(7))
                ->delete();
        }
    }
}
