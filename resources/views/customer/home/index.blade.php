@extends('layouts.app')

@section('title', 'SnapSpace — Studio Foto Privat No.1, Booking Online Real-Time')

@section('content')

{{-- HERO SECTION --}}
<section class="relative min-h-screen flex items-center overflow-hidden" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    {{-- Animated BG --}}
    <div class="absolute inset-0 bg-gradient-to-br from-[#141311] via-[#1E1D1A] to-[#141311]"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 50%, #E8451E22 0%, transparent 50%), radial-gradient(circle at 80% 20%, #F2C94C11 0%, transparent 50%)"></div>

    {{-- Film grain overlay --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\"><filter id=\"n\"><feTurbulence type=\"fractalNoise\" baseFrequency=\"0.85\" numOctaves=\"4\" stitchTiles=\"stitch\"/></filter><rect width=\"200\" height=\"200\" filter=\"url(%23n)\" opacity=\"1\"/></svg>')"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 py-24 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        {{-- Left Text --}}
        <div class="space-y-8" :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" style="transition: all 0.8s ease;">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#E8451E]/10 border border-[#E8451E]/30 text-[#E8451E] text-xs font-mono font-bold uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-[#E8451E] animate-pulse"></span>
                Slot Terbuka · Booking 24/7
            </div>

            <h1 class="font-heading font-extrabold text-5xl sm:text-6xl lg:text-7xl text-white leading-[1.05]">
                Studio Foto <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#E8451E] to-[#F2C94C]">Privat & Profesional</span><br>
                <span class="text-gray-400 text-4xl sm:text-5xl">Booking Real-Time.</span>
            </h1>

            <p class="text-gray-300 text-base sm:text-lg max-w-lg leading-relaxed">
                Bosan jadwal bentrok lewat DM? Di SnapSpace, booking studio foto privat cukup 3 menit — pilih tema, slot, bayar, dan tiket QR langsung di genggaman.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('booking.wizard') }}" class="group px-8 py-4 rounded-full bg-[#E8451E] text-white font-extrabold text-sm hover:bg-[#d03a16] shadow-2xl shadow-[#E8451E]/40 transition-all flex items-center gap-3">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Booking Sekarang
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('portfolio.index') }}" class="px-8 py-4 rounded-full bg-transparent border border-[#2D2B26] text-gray-300 font-semibold text-sm hover:border-[#E8451E] hover:text-white transition-all flex items-center gap-3">
                    <i class="fa-solid fa-images"></i> Lihat Portofolio
                </a>
            </div>

            {{-- Social Proof --}}
            <div class="flex items-center gap-6 pt-2">
                <div class="flex -space-x-3">
                    @php $avatarInitials = ['A','B','C','D']; @endphp
                @foreach(['#E8451E','#F2C94C','#1F3A2E','#60A5FA'] as $idx => $c)
                        <div class="w-9 h-9 rounded-full border-2 border-[#141311] flex items-center justify-center font-bold text-white text-xs" style="background-color: {{ $c }}">
                            {{ $avatarInitials[$idx] }}
                        </div>
                    @endforeach
                </div>
                <div>
                    <div class="flex items-center gap-1 text-[#F2C94C]">
                        @for($i=0;$i<5;$i++)<i class="fa-solid fa-star text-xs"></i>@endfor
                    </div>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">500+ sesi foto berhasil · Rating 4.9/5</p>
                </div>
            </div>
        </div>

        {{-- Right: Feature Cards Collage --}}
        <div class="relative hidden lg:block" :class="loaded ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-8'" style="transition: all 1s ease 0.2s;">
            <div class="grid grid-cols-2 gap-4">
                @foreach($featuredRooms->take(4) as $room)
                    <a href="{{ route('rooms.show', $room->slug) }}" class="group relative aspect-[3/4] rounded-3xl overflow-hidden bg-gray-900 border border-[#2D2B26] hover:border-[#E8451E] shadow-2xl transition-all {{ $loop->odd ? 'mt-8' : '' }}">
                        <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?q=80&w=600' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $room->name }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 p-4">
                            <span class="text-[10px] font-mono text-[#F2C94C] font-bold uppercase">{{ $room->theme }}</span>
                            <p class="text-sm font-heading font-bold text-white mt-0.5">{{ $room->name }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
        <p class="text-[10px] font-mono text-gray-500 uppercase tracking-widest">Scroll</p>
        <i class="fa-solid fa-chevron-down text-gray-500 text-xs"></i>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="py-20 bg-[#1B1A17]">
    <div class="max-w-5xl mx-auto px-6 text-center space-y-14">
        <div class="space-y-3">
            <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">SEMUDAH 1-2-3-4</span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Cara Kerja SnapSpace</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['fa-box-open', '#E8451E', '01', 'Pilih Paket & Tema', 'Basic, Standard, atau Premium. Tema Korean, Vintage, minimalis — semua ada.'],
                ['fa-calendar-days', '#F2C94C', '02', 'Pilih Slot Waktu', 'Grid slot real-time, hijau = tersedia. Tidak ada dobel booking.'],
                ['fa-credit-card', '#60A5FA', '03', 'Bayar DP atau Lunas', 'QRIS, VA, GoPay, OVO, Dana. Konfirmasi instan & tiket QR langsung.'],
                ['fa-camera-retro', '#34D399', '04', 'Datang & Sesi Foto!', 'Staff scan QR, sesi mulai. Hasil foto dikirim ke akun Anda dalam 24 jam.'],
            ] as [$icon, $color, $num, $title, $desc])
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 text-left hover:border-opacity-60 transition-all group relative overflow-hidden">
                    <span class="absolute top-4 right-4 font-heading font-black text-5xl opacity-5 text-white select-none">{{ $num }}</span>
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-lg" style="background-color: {{ $color }}22; color: {{ $color }}">
                        <i class="fa-solid fa-{{ $icon }} text-xl"></i>
                    </div>
                    <h3 class="font-heading font-bold text-base text-white mb-2">{{ $title }}</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FEATURED ROOMS --}}
<section class="py-20 bg-[#141311]">
    <div class="max-w-7xl mx-auto px-6 space-y-12">
        <div class="flex items-end justify-between">
            <div class="space-y-2">
                <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">PILIH ATMOSFER FAVORITMU</span>
                <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Studio & Tema Tersedia</h2>
            </div>
            <a href="{{ route('rooms.index') }}" class="hidden sm:flex items-center gap-2 text-sm font-semibold text-gray-400 hover:text-[#E8451E] transition-colors">
                Semua Ruangan <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredRooms as $room)
                <a href="{{ route('rooms.show', $room->slug) }}" class="group bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl overflow-hidden hover:border-[#E8451E] transition-all shadow-xl">
                    <div class="relative aspect-[16/10] overflow-hidden">
                        <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $room->name }}">
                        <span class="absolute top-4 left-4 bg-[#141311]/80 backdrop-blur-md px-3 py-1 rounded-full text-[#F2C94C] text-[10px] font-mono font-bold">{{ $room->theme }}</span>
                    </div>
                    <div class="p-5 space-y-3">
                        <h3 class="font-heading font-bold text-lg text-white group-hover:text-[#E8451E] transition-colors">{{ $room->name }}</h3>
                        <p class="text-xs text-gray-400 line-clamp-2">{{ $room->description }}</p>
                        <div class="flex items-center gap-4 text-xs font-mono text-gray-400 pt-1 border-t border-[#2D2B26]">
                            <span><i class="fa-solid fa-users mr-1 text-[#E8451E]"></i> {{ $room->capacity }} orang</span>
                            <span><i class="fa-solid fa-expand mr-1 text-[#F2C94C]"></i> {{ $room->area_m2 }} m²</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- PACKAGES --}}
<section class="py-20 bg-[#1B1A17]">
    <div class="max-w-5xl mx-auto px-6 space-y-12">
        <div class="text-center space-y-3">
            <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">TRANSPARAN, TANPA BIAYA TERSEMBUNYI</span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Paket Foto Kami</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($popularPackages as $pkg)
                @php $isMiddle = $loop->iteration === 2; @endphp
                <div class="bg-[#1E1D1A] border-2 {{ $isMiddle ? 'border-[#E8451E]' : 'border-[#2D2B26]' }} rounded-3xl p-6 space-y-6 relative {{ $isMiddle ? 'shadow-2xl shadow-[#E8451E]/20 scale-[1.02]' : '' }}">
                    @if($isMiddle)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[#E8451E] text-white text-[10px] font-mono font-bold px-4 py-1 rounded-full uppercase">Most Popular</div>
                    @endif
                    <div>
                        <h3 class="font-heading font-extrabold text-xl text-white">{{ $pkg->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ $pkg->description }}</p>
                    </div>
                    <div>
                        <span class="font-heading font-black text-3xl text-[#F2C94C]">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                        <span class="text-xs text-gray-500 font-mono"> / sesi</span>
                    </div>
                    <ul class="space-y-2.5 text-xs font-mono text-gray-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-clock text-[#E8451E]"></i> {{ $pkg->duration_minutes }} menit sesi</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-users text-[#F2C94C]"></i> Maks. {{ $pkg->max_people }} orang</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-image text-blue-400"></i> {{ $pkg->edited_photos }} foto edit siap cetak</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-400"></i> Remote shutter & properti</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-400"></i> Galeri online 30 hari</li>
                    </ul>
                    <a href="{{ route('booking.wizard', ['package_id' => $pkg->id]) }}" class="block w-full py-3.5 rounded-full {{ $isMiddle ? 'bg-[#E8451E] text-white shadow-lg shadow-[#E8451E]/30' : 'bg-[#141311] border border-[#2D2B26] text-gray-300 hover:border-[#E8451E] hover:text-white' }} text-center font-bold text-sm transition-all">
                        Pilih Paket Ini
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PORTFOLIO PREVIEW --}}
<section class="py-20 bg-[#141311]">
    <div class="max-w-7xl mx-auto px-6 space-y-12">
        <div class="flex items-end justify-between">
            <div class="space-y-2">
                <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">100% FOTO ASLI PELANGGAN</span>
                <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Portofolio Terbaru</h2>
            </div>
            <a href="{{ route('portfolio.index') }}" class="hidden sm:flex items-center gap-2 text-sm font-semibold text-gray-400 hover:text-[#E8451E] transition-colors">
                Galeri Lengkap <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($portfolioPreview as $port)
                <div class="group relative {{ $loop->iteration === 1 || $loop->iteration === 6 ? 'col-span-2 row-span-2' : '' }} aspect-square rounded-2xl overflow-hidden bg-gray-900 border border-[#2D2B26] hover:border-[#E8451E] transition-all">
                    <img src="{{ $port->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="{{ $port->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <span class="text-[10px] font-mono font-bold text-[#F2C94C] uppercase">{{ $port->category }}</span>
                        <p class="text-xs font-bold text-white">{{ $port->title }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
@if($reviews->isNotEmpty())
<section class="py-20 bg-[#1B1A17]">
    <div class="max-w-5xl mx-auto px-6 space-y-12">
        <div class="text-center space-y-3">
            <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">APA KATA MEREKA</span>
            <h2 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Ulasan Pelanggan Nyata</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reviews as $rev)
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <div class="flex gap-1 text-[#F2C94C] text-sm">
                        @for($i=1;$i<=$rev->rating;$i++) <i class="fa-solid fa-star"></i> @endfor
                    </div>
                    <p class="text-xs text-gray-300 italic leading-relaxed">"{{ $rev->comment }}"</p>
                    <div class="flex items-center gap-3 pt-2 border-t border-[#2D2B26]">
                        <div class="w-8 h-8 rounded-full bg-[#E8451E] text-white font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($rev->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-white">{{ $rev->user->name }}</p>
                            <p class="text-[10px] font-mono text-gray-500">{{ $rev->booking->room->name }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA SECTION --}}
<section class="py-24 bg-[#141311] relative overflow-hidden">
    <div class="absolute inset-0 opacity-20" style="background: radial-gradient(circle at 50% 50%, #E8451E 0%, transparent 70%)"></div>
    <div class="relative max-w-3xl mx-auto px-6 text-center space-y-8">
        <h2 class="font-heading font-extrabold text-4xl sm:text-5xl text-white leading-tight">
            Sudah siap <span class="text-[#E8451E]">tampil percaya diri</span> di depan kamera?
        </h2>
        <p class="text-gray-300 text-base">Slot terbatas tiap harinya. Amankan jadwal sesiomu sekarang sebelum diambil orang lain.</p>
        <a href="{{ route('booking.wizard') }}" class="inline-flex items-center gap-3 px-10 py-5 rounded-full bg-[#E8451E] text-white font-extrabold text-base hover:bg-[#d03a16] shadow-2xl shadow-[#E8451E]/50 transition-all">
            <i class="fa-solid fa-camera-retro"></i>
            Booking Studio Sekarang
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</section>

@endsection
