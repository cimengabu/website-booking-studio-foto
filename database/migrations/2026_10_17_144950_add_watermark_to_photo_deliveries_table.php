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
        Schema::table('photo_deliveries', function (Blueprint $table) {
            $table->boolean('watermark_enabled')->default(true)->after('download_count');
            $table->string('watermark_text')->nullable()->default('SNAPSPACE STUDIO PREVIEW')->after('watermark_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photo_deliveries', function (Blueprint $table) {
            $table->dropColumn(['watermark_enabled', 'watermark_text']);
        });
    }
};
