<?php

namespace App\Console\Commands;

use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateRoomImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-room-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update room images, portfolios, and reviews with contextually matching photography studio content';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $r1 = Room::where('slug', 'aesthetic-korean-studio')->first();
        if ($r1) {
            RoomImage::where('room_id', $r1->id)->delete();
            RoomImage::create(['room_id' => $r1->id, 'path' => 'https://images.unsplash.com/photo-1554995207-c18c203602cb?q=80&w=1200&auto=format&fit=crop', 'is_primary' => true]);
            RoomImage::create(['room_id' => $r1->id, 'path' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=1200&auto=format&fit=crop', 'is_primary' => false]);
            $this->info("Updated {$r1->name}");
        }

        $r2 = Room::where('slug', 'vintage-darkroom')->first();
        if ($r2) {
            RoomImage::where('room_id', $r2->id)->delete();
            RoomImage::create(['room_id' => $r2->id, 'path' => 'https://images.unsplash.com/photo-1674507887903-ef7d4712500b?q=80&w=1200&auto=format&fit=crop', 'is_primary' => true]);
            RoomImage::create(['room_id' => $r2->id, 'path' => 'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=1200&auto=format&fit=crop', 'is_primary' => false]);
            $this->info("Updated {$r2->name}");
        }

        $r3 = Room::where('slug', 'minimalist-white-box')->first();
        if ($r3) {
            RoomImage::where('room_id', $r3->id)->delete();
            RoomImage::create(['room_id' => $r3->id, 'path' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=1200&auto=format&fit=crop', 'is_primary' => true]);
            RoomImage::create(['room_id' => $r3->id, 'path' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=1200&auto=format&fit=crop', 'is_primary' => false]);
            $this->info("Updated {$r3->name}");
        }

        // --- UPDATE PORTFOLIO TO REAL STUDIO PORTRAITS ---
        Portfolio::query()->delete();
        $portfolioData = [
            ['title' => 'Momen Wisuda Bersama Sahabat', 'category' => 'wisuda', 'image_path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800&auto=format&fit=crop', 'room_id' => $r3?->id],
            ['title' => 'Couple Studio Casual Prewedding', 'category' => 'prewedding', 'image_path' => 'https://images.unsplash.com/photo-1647833190352-0e7e579b45b6?q=80&w=800&auto=format&fit=crop', 'room_id' => $r1?->id],
            ['title' => 'Sesi Studio Foto Keluarga Harmonis', 'category' => 'keluarga', 'image_path' => 'https://images.unsplash.com/photo-1647833197770-ecf18b7f0ef7?q=80&w=800&auto=format&fit=crop', 'room_id' => $r3?->id],
            ['title' => 'Self Photo Pose Korean Minimalist', 'category' => 'self-photo', 'image_path' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?q=80&w=800&auto=format&fit=crop', 'room_id' => $r1?->id],
            ['title' => 'Studio Portrait Pria Gaya Editorial', 'category' => 'self-photo', 'image_path' => 'https://images.unsplash.com/photo-1668852572751-9636b33f1453?q=80&w=800&auto=format&fit=crop', 'room_id' => $r2?->id],
            ['title' => 'Self Photo Expressive Monokrom', 'category' => 'self-photo', 'image_path' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=800&auto=format&fit=crop', 'room_id' => $r2?->id],
            ['title' => 'Studio Beauty & Editorial Portrait', 'category' => 'produk', 'image_path' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop', 'room_id' => $r3?->id],
            ['title' => 'Graduation Joy Studio Session', 'category' => 'wisuda', 'image_path' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=800&auto=format&fit=crop', 'room_id' => $r1?->id],
        ];
        foreach ($portfolioData as $p) {
            Portfolio::create([
                'title' => $p['title'],
                'category' => $p['category'],
                'image_path' => $p['image_path'],
                'room_id' => $p['room_id'],
                'is_featured' => true,
            ]);
        }
        $this->info('Portfolio images updated to real studio portraits.');

        // --- SEED RICH REVIEWS / TESTIMONIALS ---
        $customer = User::where('email', 'customer@snapspace.test')->first() ?? User::first();
        $sampleReviews = [
            [
                'guest_name' => 'Rina Wijaya',
                'user_id' => $customer?->id,
                'room_id' => $r1?->id,
                'rating' => 5,
                'comment' => 'Studio paling estetik di Surabaya! Hasil fotonya jernih banget, staf ramah, dan proses booking gampang banget tanpa ribet.',
            ],
            [
                'guest_name' => 'Dimas Pratama',
                'user_id' => null,
                'room_id' => $r2?->id,
                'rating' => 5,
                'comment' => 'Nuansa ruangan Vintage Darkroom-nya juara banget! Beneran kayak studio analog klasik dengan lighting merah yang atmosferik. Recommended buat yang suka foto estetik anti-mainstream.',
            ],
            [
                'guest_name' => 'Siti Nurhaliza',
                'user_id' => null,
                'room_id' => $r3?->id,
                'rating' => 5,
                'comment' => 'Minimalist White Box-nya bersih pol, lighting rata dan tanpa bayangan! Pas banget buat foto wisuda bareng sahabat kemarin. Hasil cetakannya juga premium.',
            ],
            [
                'guest_name' => 'Kevin & Jessica',
                'user_id' => null,
                'room_id' => $r1?->id,
                'rating' => 5,
                'comment' => 'Pakai paket Couple Snap di ruangan Korean Studio buat foto prewedding casual. Hasil fotonya natural banget dan bisa langsung dapet softcopy file dalam hitungan jam!',
            ],
            [
                'guest_name' => 'Alvin Wicaksono',
                'user_id' => null,
                'room_id' => $r3?->id,
                'rating' => 5,
                'comment' => 'Self photo studio terbaik di Tunjungan! Remote shutter responsif, monitor preview gede, jadi bisa eksplor gaya sendiri tanpa canggung dilihatin fotografer.',
            ],
            [
                'guest_name' => 'Nadia Rahmawati',
                'user_id' => null,
                'room_id' => $r2?->id,
                'rating' => 5,
                'comment' => 'Sangat puas! Lokasinya strategis di Jl. Tunjungan, tempatnya dingin, wangi, dan peralatan kameranya profesional (full-frame Sony). Pasti bakal balik lagi bareng bestie.',
            ],
        ];

        foreach ($sampleReviews as $sr) {
            $existing = Review::where('comment', $sr['comment'])->first();
            if (! $existing) {
                Review::create([
                    'booking_id' => null,
                    'user_id' => $sr['user_id'],
                    'guest_name' => $sr['guest_name'],
                    'room_id' => $sr['room_id'],
                    'rating' => $sr['rating'],
                    'comment' => $sr['comment'],
                    'is_visible' => true,
                ]);
            }
        }
        $this->info('Testimonials updated with rich reviews.');

        return 0;
    }
}
