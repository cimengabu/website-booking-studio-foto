<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PhotoDelivery;
use App\Models\PhotoResult;
use App\Models\Portfolio;
use App\Models\Promo;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Admin SnapSpace',
            'email' => 'admin@snapspace.id',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $staff = User::create([
            'name' => 'Budi Staff Studio',
            'email' => 'staff@snapspace.id',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        $customer = User::create([
            'name' => 'Siti Pelanggan',
            'email' => 'customer@snapspace.id',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // 2. Settings
        Setting::setKey('open_time', '09:00');
        Setting::setKey('close_time', '21:00');
        Setting::setKey('buffer_minutes', 15);
        Setting::setKey('dp_percent', 50);
        Setting::setKey('extra_person_fee', 25000);
        Setting::setKey('studio_address', 'Jl. Tunjungan No. 88, Surabaya');
        Setting::setKey('studio_phone', '+62 812-3456-7890');

        // 3. Rooms
        $room1 = Room::create([
            'name' => 'Aesthetic Korean Studio',
            'slug' => 'aesthetic-korean-studio',
            'theme' => 'Warm Pastel Korean Photo Booth',
            'description' => 'Studio bergaya Korea dengan pencahayaan softbox hangat, background cream pastel, dan cermin estetik khas Seoul photobooth.',
            'capacity' => 6,
            'area_m2' => 25,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        RoomImage::create(['room_id' => $room1->id, 'path' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800&auto=format&fit=crop', 'is_primary' => true]);
        RoomImage::create(['room_id' => $room1->id, 'path' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=800&auto=format&fit=crop', 'is_primary' => false]);

        $room2 = Room::create([
            'name' => 'Vintage Darkroom',
            'slug' => 'vintage-darkroom',
            'theme' => 'Analog Safelight & Industrial Grain',
            'description' => 'Kamar foto bertema vintage analog 90s dengan pencahayaan safelight oranye-merah yang sinematik dan properti kamera klasik.',
            'capacity' => 4,
            'area_m2' => 20,
            'is_active' => true,
            'sort_order' => 2,
        ]);
        RoomImage::create(['room_id' => $room2->id, 'path' => 'https://images.unsplash.com/photo-1520390138845-fd2d229dd553?q=80&w=800&auto=format&fit=crop', 'is_primary' => true]);

        $room3 = Room::create([
            'name' => 'Minimalist White Box',
            'slug' => 'minimalist-white-box',
            'theme' => 'Clean High-Key Studio',
            'description' => 'Ruangan serba putih bersih tanpa bayangan (high-key), cocok untuk foto katalog produk, wisuda, maupun foto grup minimalis.',
            'capacity' => 8,
            'area_m2' => 30,
            'is_active' => true,
            'sort_order' => 3,
        ]);
        RoomImage::create(['room_id' => $room3->id, 'path' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=800&auto=format&fit=crop', 'is_primary' => true]);

        // 4. Packages
        $pkg1 = Package::create([
            'name' => 'Basic Snap',
            'slug' => 'basic-snap',
            'description' => 'Sesi kilat untuk 1-2 orang. Cocok untuk foto profil, pasangan, atau foto bareng sahabat.',
            'duration_minutes' => 30,
            'max_people' => 2,
            'edited_photos' => 5,
            'price' => 99000,
            'is_active' => true,
        ]);

        $pkg2 = Package::create([
            'name' => 'Standard Studio',
            'slug' => 'standard-studio',
            'description' => 'Paket terfavorit untuk grup kecil hingga 4 orang. Bebas ganti properti & background.',
            'duration_minutes' => 45,
            'max_people' => 4,
            'edited_photos' => 10,
            'price' => 149000,
            'is_active' => true,
        ]);

        $pkg3 = Package::create([
            'name' => 'Premium Canvas',
            'slug' => 'premium-canvas',
            'description' => 'Paket komplit untuk rombongan wisuda / keluarga besar hingga 8 orang dengan hasil foto siap cetak.',
            'duration_minutes' => 60,
            'max_people' => 8,
            'edited_photos' => 15,
            'price' => 249000,
            'is_active' => true,
        ]);

        // 5. Addons
        $addon1 = Addon::create(['name' => 'Cetak 4R Extra (Per Lembar)', 'description' => 'Cetak foto ukuran 4R dengan kertas foto glossy premium.', 'price' => 15000, 'type' => 'per_unit']);
        $addon2 = Addon::create(['name' => 'Softcopy Seluruh Foto (Google Drive)', 'description' => 'Dapatkan semua file foto hasil jepretan tanpa batas.', 'price' => 25000, 'type' => 'flat']);
        $addon3 = Addon::create(['name' => 'Kostum & Properti Premium', 'description' => 'Akses ke lemari kostum bando, kacamata unik, dan topi lucu.', 'price' => 35000, 'type' => 'per_person']);
        $addon4 = Addon::create(['name' => 'Videografis Behind the Scene (Shorts)', 'description' => 'Video pendek 15-30 detik momen keseruan sesi foto.', 'price' => 50000, 'type' => 'flat']);

        // 6. Promos
        Promo::create(['code' => 'SNAP20', 'type' => 'percent', 'value' => 20, 'min_order' => 100000, 'max_discount' => 30000, 'quota' => 50, 'used_count' => 5, 'is_active' => true]);
        Promo::create(['code' => 'STUDIOBARU', 'type' => 'fixed', 'value' => 25000, 'min_order' => 90000, 'quota' => 100, 'used_count' => 12, 'is_active' => true]);

        // 7. Portfolios
        $portfolioData = [
            ['title' => 'Momen Kelulusan Unair 2026', 'category' => 'wisuda', 'image_path' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Couple Casual Prewedding', 'category' => 'prewedding', 'image_path' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Katalog Skincare Aesthetic', 'category' => 'produk', 'image_path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Foto Keluarga Ceria', 'category' => 'keluarga', 'image_path' => 'https://images.unsplash.com/photo-1511895426328-dc8714191300?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Self Photo Korean Pose', 'category' => 'self-photo', 'image_path' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=800&auto=format&fit=crop'],
            ['title' => 'Bestie Vintage Analog Vibe', 'category' => 'self-photo', 'image_path' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?q=80&w=800&auto=format&fit=crop'],
        ];

        foreach ($portfolioData as $p) {
            Portfolio::create([
                'title' => $p['title'],
                'category' => $p['category'],
                'image_path' => $p['image_path'],
                'room_id' => $room1->id,
                'is_featured' => true,
            ]);
        }

        // 8. Sample Booking for Customer
        $bCode = 'SNAP-20261001-A1B2';
        $booking = Booking::create([
            'booking_code' => $bCode,
            'user_id' => $customer->id,
            'room_id' => $room1->id,
            'package_id' => $pkg2->id,
            'booking_date' => now()->format('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '14:45:00',
            'people_count' => 3,
            'subtotal' => 149000,
            'addons_total' => 25000,
            'markup_amount' => 0,
            'discount' => 20000,
            'total_price' => 154000,
            'paid_amount' => 154000,
            'payment_option' => 'full',
            'status' => 'completed',
            'expires_at' => now()->addHours(1),
            'qr_token' => "QR-{$bCode}-998877",
            'checked_in_at' => now()->subHours(2),
            'notes' => 'Tolong siapkan kacamata hitam estetik ya kak!',
        ]);

        BookingAddon::create([
            'booking_id' => $booking->id,
            'addon_id' => $addon2->id,
            'qty' => 1,
            'unit_price' => 25000,
            'subtotal' => 25000,
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'order_id' => "PAY-{$bCode}-001",
            'payment_kind' => 'full',
            'payment_type' => 'QRIS Midtrans Sandbox',
            'gross_amount' => 154000,
            'transaction_status' => 'settlement',
            'paid_at' => now()->subHours(3),
        ]);

        // Sample Photo Results for this completed booking
        PhotoResult::create([
            'booking_id' => $booking->id,
            'path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop',
            'thumb_path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop',
            'original_name' => 'SnapSpace_Photo_01.jpg',
            'size' => 2450000,
        ]);
        PhotoResult::create([
            'booking_id' => $booking->id,
            'path' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=800&auto=format&fit=crop',
            'thumb_path' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=800&auto=format&fit=crop',
            'original_name' => 'SnapSpace_Photo_02.jpg',
            'size' => 3120000,
        ]);

        PhotoDelivery::create([
            'booking_id' => $booking->id,
            'delivered_at' => now()->subHours(1),
            'expires_at' => now()->addDays(30),
            'download_count' => 1,
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Studio paling estetik di Surabaya! Hasil fotonya jernih banget, staf ramah, dan proses booking gampang banget tanpa ribet.',
            'is_visible' => true,
        ]);
    }
}
