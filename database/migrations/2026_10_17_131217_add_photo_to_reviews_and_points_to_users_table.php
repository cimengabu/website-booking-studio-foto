<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('loyalty_points')->default(0)->after('avatar');
            });
        }

        if (Schema::hasTable('reviews') && ! Schema::hasColumn('reviews', 'photo')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('comment');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'loyalty_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('loyalty_points');
            });
        }

        if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'photo')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
};
