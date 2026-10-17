<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings') && ! Schema::hasColumn('bookings', 'photographer_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('photographer_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'photographer_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['photographer_id']);
                $table->dropColumn('photographer_id');
            });
        }
    }
};
