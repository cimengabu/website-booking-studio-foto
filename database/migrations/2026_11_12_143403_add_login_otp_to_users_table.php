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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('failed_login_count')->default(0)->after('password');
            $table->string('login_otp', 6)->nullable()->after('failed_login_count');
            $table->timestamp('login_otp_expires_at')->nullable()->after('login_otp');
            $table->timestamp('otp_verified_at')->nullable()->after('login_otp_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['failed_login_count', 'login_otp', 'login_otp_expires_at', 'otp_verified_at']);
        });
    }
};
