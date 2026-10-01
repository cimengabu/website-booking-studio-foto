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
        // Add phone & role to users table if not exists
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['customer', 'admin', 'staff'])->default('customer')->after('phone');
            }
        });

        // Rooms / Studio Themes
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('theme');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(4);
            $table->integer('area_m2')->default(20);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Room Images
        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Packages
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->integer('max_people')->default(2);
            $table->integer('edited_photos')->default(5);
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Addons
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->enum('type', ['flat', 'per_person', 'per_unit'])->default('flat');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // System Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Pricing Rules (Weekend / Peak Hour Markups)
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('day_type', ['all', 'weekday', 'weekend'])->default('all');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('markup_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Blocked Slots (Maintenance / Holidays)
        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // Promos
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('value', 12, 2);
            $table->decimal('min_order', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->integer('quota')->default(100);
            $table->integer('used_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bookings
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('people_count')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('addons_total', 12, 2)->default(0);
            $table->decimal('markup_amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('payment_option', ['dp', 'full'])->default('full');
            $table->enum('status', ['pending', 'dp_paid', 'paid', 'checked_in', 'completed', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->string('qr_token')->unique()->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Booking Addons Pivot
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained()->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('order_id')->unique();
            $table->enum('payment_kind', ['dp', 'full', 'remaining'])->default('full');
            $table->string('payment_type')->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->string('transaction_status')->default('pending');
            $table->string('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        // Photo Results
        Schema::create('photo_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('thumb_path')->nullable();
            $table->string('original_name');
            $table->bigInteger('size')->default(0);
            $table->timestamps();
        });

        // Photo Deliveries
        Schema::create('photo_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->timestamp('delivered_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });

        // Portfolios
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // wisuda, prewedding, produk, keluarga, self-photo
            $table->string('image_path');
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // Favorites
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'room_id']);
        });

        // Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('photo_deliveries');
        Schema::dropIfExists('photo_results');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('booking_addons');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('promos');
        Schema::dropIfExists('blocked_slots');
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('rooms');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role']);
        });
    }
};
