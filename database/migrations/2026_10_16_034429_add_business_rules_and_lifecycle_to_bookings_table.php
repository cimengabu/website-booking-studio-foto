<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->change();
            $table->unsignedTinyInteger('reschedule_count')->default(0)->after('status');
            $table->date('original_booking_date')->nullable()->after('reschedule_count');
            $table->time('original_start_time')->nullable()->after('original_booking_date');
            $table->text('cancellation_reason')->nullable()->after('original_start_time');
            $table->decimal('refund_amount', 12, 2)->default(0)->after('cancellation_reason');
            $table->decimal('penalty_amount', 12, 2)->default(0)->after('refund_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'reschedule_count',
                'original_booking_date',
                'original_start_time',
                'cancellation_reason',
                'refund_amount',
                'penalty_amount',
            ]);
        });
    }
};
