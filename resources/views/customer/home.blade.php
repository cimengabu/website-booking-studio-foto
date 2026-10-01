@extends('layouts.app')

@section('title', 'SnapSpace — Self-Photo Studio Online Booking Platform')

@section('content')
<!-- Hero Section -->
<section class="relative pt-12 pb-20 overflow-hidden bg-[#141311]">
    <!-- Background Accents -->
    <div class="absolute top-10 left-1/4 w-72 h-72 bg-[#E8451E]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-[#F2C94C]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-[#1E1D1A] border border-[#2D2B26] text-xs font-mono font-semibold text-[#F2C94C]">
                    <span class="w-2 h-2 rounded-full bg-[#E8451E] animate-ping"></span>
                    <span>SELF-PHOTO STUDIO NO. 1 DI SURABAYA</span>
                </div>

                <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
                    Abadikan Momen Bebas Malu di <span class="text-[#E8451E] underline decoration-wavy decoration-1 underline-offset-8">SnapSpace</span>
                </h1>

                <p class="text-gray-300 text-base sm:text-lg max-w-2xl leading-relaxed">
                    Studio foto mandiri dengan remote shutter tanpa fotografer. Pilih ruangan estetik, kalender slot realtime tanpa takut jadwal bentrok, dan unduh foto langsung di akun Anda.
                </p>

                <!-- Quick Action CTA -->
                <div class="flex flex-col sm:flex-row items-center gap-4 pt-4 justify-center lg:justify-start">
                    <a href="{{ route('booking.wizard') }}" class="w-full sm:w-auto px-8 py-4 rounded-full bg-[#E8451E] text-white font-extrabold text-base hover:bg-[#d03a16] shadow-xl shadow-[#E8451E]/30 hover:scale-[1.03] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid fa-bolt"></i> Booking Slot Sekarang
                    </a>
                    <a href="{{ route('rooms.index') }}" class="w-full sm:w-auto px-7 py-4 rounded-full bg-[#1E1D1A] text-gray-200 border border-[#2D2B26] hover:border-[#E8451E] hover:text-white font-semibold text-sm transition-all text-center">
                        Lihat Pilihan Ruangan
                    </a>
                </div>

                <!-- Stats Highlights -->
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-[#2D2B26]/80 text-center lg:text-left">
                    <div>
                        <p class="font-heading font-black text-2xl sm:text-3xl text-white">4.9/5★</p>
                        <p class="text-xs text-gray-400 font-mono">1.200+ Ulasan Pelanggan</p>
                    </div>
                    <div>
                        <p class="font-heading font-black text-2xl sm:text-3xl text-[#F2C94C]">15 Menit</p>
                        <p class="text-xs text-gray-400 font-mono">Buffer Higienis Steril</p>
                    </div>
                    <div>
                        <p class="font-heading font-black text-2xl sm:text-3xl text-[#E8451E]">100%</p>
                        <p class="text-xs text-gray-400 font-mono">Privasi Sesi Foto</p>
                    </div>
                </div>
            </div>

            <!-- Right Visual Contact Sheet Showcase -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative w-full max-w-sm">
                    <!-- Photo Strip Frame -->
                    <div class="bg-[#1E1D1A] border border-[#2D2B26] p-4 space-y-4 rounded-3xl shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                        <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-gray-900">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover" alt="Korean Photobooth pose">
                            <span class="absolute top-3 left-3 bg-[#E8451E] text-white px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase">FRAME 01A</span>
                        </div>
                        <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-gray-900">
                            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover" alt="Couple pose">
                            <span class="absolute top-3 left-3 bg-[#1F3A2E] text-[#F2C94C] px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase">FRAME 02A</span>
                        </div>

                        <!-- Strip Footer -->
                        <div class="flex items-center justify-between pt-2 px-1 text-xs font-mono font-bold text-gray-400 border-t border-dashed border-[#2D2B26]">
                            <span>SNAPSPACE PHOTO STUDIO</span>
                            <span class="text-[#F2C94C]">SAFELIGHT VIBE</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Rekomendasi Jam Hemat Banner -->
<section class="bg-[#1F3A2E] border-y border-emerald-800/40 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4 text-center md:text-left">
            <div class="w-12 h-12 rounded-2xl bg-[#F2C94C] text-[#1B1A17] flex items-center justify-center font-bold text-2xl shrink-0">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <h3 class="font-heading font-extrabold text-lg text-white">Promo Hemat Midweek Diskon 15%!</h3>
                <p class="text-xs text-emerald-200">Booking sesi foto pada hari Selasa atau Rabu sebelum jam 12:00 WIB dan dapatkan potongan otomatis 15%!</p>
            </div>
        </div>
        <a href="{{ route('booking.wizard') }}" class="px-6 py-2.5 rounded-full bg-[#F2C94C] text-[#1B1A17] font-extrabold text-xs hover:bg-yellow-300 transition-colors shrink-0">
            Cek Slot Hemat <i class="fa-solid fa-arrow-right ml-1"></i>
        </a>
    </div>
</section>

<!-- Featured Rooms / Studio Themes -->
<section class="py-20 bg-[#141311]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-12">
            <div>
                <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">PILIHAN TEMPAT & AMBIENCE</span>
                <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white mt-1">Ruangan Studio Unggulan</h2>
            </div>
            <a href="{{ route('rooms.index') }}" class="text-sm font-semibold text-[#E8451E] hover:underline mt-4 md:mt-0 flex items-center gap-2">
                Lihat Semua Ruangan <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($rooms as $room)
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl overflow-hidden group hover:border-[#E8451E] transition-all flex flex-col justify-between shadow-xl">
                    <div>
                        <!-- Image Container -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-gray-900">
                            <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800&auto=format&fit=crop' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $room->name }}">
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-[#141311]/80 backdrop-blur-md text-[#F2C94C] text-xs font-mono font-semibold">
                                {{ $room->theme }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-6 space-y-4">
                            <h3 class="font-heading font-bold text-xl text-white group-hover:text-[#E8451E] transition-colors">{{ $room->name }}</h3>
                            <p class="text-xs text-gray-400 leading-relaxed line-clamp-2">{{ $room->description }}</p>

                            <!-- Specs -->
                            <div class="flex items-center gap-4 text-xs font-mono text-gray-300 pt-2 border-t border-[#2D2B26]">
                                <span><i class="fa-solid fa-users text-[#E8451E] mr-1"></i> Kapasitas {{ $room->capacity }} Orang</span>
                                <span><i class="fa-solid fa-expand text-[#F2C94C] mr-1"></i> Luas {{ $room->area_m2 }} m²</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-0">
                        <a href="{{ route('booking.wizard', ['room_id' => $room->id]) }}" class="w-full py-3 rounded-2xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] hover:bg-[#E8451E] text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all">
                            Pilih Ruangan Ini <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Popular Packages -->
<section class="py-20 bg-[#1E1D1A] border-y border-[#2D2B26]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">TRANSPARAN TANPA BIAYA TERSEBUNYI</span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Daftar Paket Foto Favorit</h2>
            <p class="text-xs text-gray-400">Setiap paket sudah termasuk remote shutter nirkabel, pencahayaan profesional, dan softcopy digital.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($packages as $pkg)
                <div class="bg-[#141311] border border-[#2D2B26] rounded-3xl p-8 space-y-6 flex flex-col justify-between relative hover:border-[#E8451E] transition-all shadow-xl">
                    @if($loop->iteration === 2)
                        <span class="absolute -top-3 right-6 px-4 py-1 rounded-full bg-[#E8451E] text-white font-mono font-bold text-[10px] uppercase shadow-lg">
                            PALING POPULER
                        </span>
                    @endif

                    <div class="space-y-4">
                        <h3 class="font-heading font-extrabold text-2xl text-white">{{ $pkg->name }}</h3>
                        <p class="text-xs text-gray-400 min-h-[36px]">{{ $pkg->description }}</p>

                        <div class="pt-4 border-t border-[#2D2B26]">
                            <span class="text-xs font-mono text-gray-400">Mulai dari</span>
                            <div class="font-heading font-black text-3xl text-white">
                                Rp {{ number_format($pkg->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Features list -->
                        <ul class="space-y-3 text-xs text-gray-300 pt-2 font-medium">
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-clock text-[#E8451E]"></i> Durasi Sesi {{ $pkg->duration_minutes }} Menit
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-users text-[#F2C94C]"></i> Maksimal {{ $pkg->max_people }} Orang
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-wand-magic-sparkles text-emerald-400"></i> Gratis {{ $pkg->edited_photos }} Foto Edit Retouch
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-cloud-arrow-down text-blue-400"></i> Softcopy via Akun SnapSpace
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('booking.wizard', ['package_id' => $pkg->id]) }}" class="w-full py-3.5 rounded-full bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16] text-center shadow-lg shadow-[#E8451E]/20 transition-all">
                        Pilih Paket {{ $pkg->name }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Portfolio Showcase Filter -->
<section class="py-20 bg-[#141311]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-12">
            <div>
                <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">HASIL JREPRETAN REAL PELANGGAN</span>
                <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white mt-1">Galeri Inspirasi Pose</h2>
            </div>
            <a href="{{ route('portfolio.index') }}" class="text-sm font-semibold text-[#E8451E] hover:underline mt-4 md:mt-0">Lihat Semua Portofolio <i class="fa-solid fa-arrow-right text-xs"></i></a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($portfolios as $port)
                <div class="group relative aspect-[3/4] rounded-2xl overflow-hidden bg-gray-900 border border-[#2D2B26]">
                    <img src="{{ $port->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $port->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <span class="text-[10px] font-mono font-bold text-[#F2C94C] uppercase">{{ $port->category }}</span>
                        <p class="text-xs font-bold text-white leading-tight truncate">{{ $port->title }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-[#1E1D1A] border-t border-[#2D2B26]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">APA KATA MEREKA</span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Ulasan Jujur Pelanggan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($reviews as $rev)
                <div class="bg-[#141311] border border-[#2D2B26] p-6 rounded-3xl space-y-4">
                    <div class="flex items-center gap-1 text-[#F2C94C] text-xs">
                        @for($i=1; $i<=$rev->rating; $i++)
                            <i class="fa-solid fa-star"></i>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-300 italic leading-relaxed">"{{ $rev->comment }}"</p>
                    <div class="flex items-center gap-3 pt-4 border-t border-[#2D2B26]">
                        <div class="w-8 h-8 rounded-full bg-[#1F3A2E] text-[#F2C94C] flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr($rev->user->name ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">{{ $rev->user->name ?? 'Pelanggan SnapSpace' }}</p>
                            <p class="text-[10px] font-mono text-gray-500">{{ $rev->booking->room->name ?? 'Studio Room' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
