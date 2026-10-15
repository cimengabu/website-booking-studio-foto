@extends('layouts.public')

@section('title', 'SnapSpace Studio — Studio Foto Premium, Booking Real-Time Surabaya')
@section('meta_description', 'Studio foto mandiri premium Surabaya. Berbagai tema estetik, slot real-time, bayar via QRIS/GoPay/OVO. Abadikan momen terbaik bersama orang tersayang.')

@section('content')

{{-- ════════════════════════════════════════════════════════════
     HERO SECTION
     ════════════════════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center overflow-hidden bg-cream"
         x-data="{ loaded: false, activeSlide: 0 }"
         x-init="setTimeout(() => loaded = true, 80)">

    {{-- Background: large studio image --}}
    <div class="absolute inset-0 z-0">
        <img src="/images/hero-studio.jpg"
             alt="SnapSpace Studio Interior"
             class="w-full h-full object-cover object-center"
             loading="eager">
        {{-- Gradient overlay: left side readable, right side photo --}}
        <div class="absolute inset-0 bg-gradient-to-r from-cream via-cream/80 to-cream/20 lg:from-cream lg:via-cream/85 lg:to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-cream/60"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-20 lg:py-28">
        <div class="max-w-xl lg:max-w-2xl"
             :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
             style="transition: opacity 0.8s ease, transform 0.8s ease;">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gold/30 shadow-warm mb-6">
                <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                <span class="text-xs font-semibold text-teal tracking-wide uppercase">Studio Buka · Slot Tersedia Hari Ini</span>
            </div>

            {{-- H1 --}}
            <h1 class="font-serif font-bold text-[2.6rem] sm:text-5xl lg:text-[3.4rem] text-ink leading-[1.1] tracking-tight mb-5">
                Abadikan Momen<br>
                <em class="not-italic text-gold-gradient">Tak Terlupakan</em><br>
                <span class="text-ink/70 text-3xl sm:text-4xl lg:text-[2.6rem]">Bersama Orang Tersayang</span>
            </h1>

            {{-- Lead --}}
            <p class="text-base sm:text-lg text-ink-muted leading-relaxed mb-8 max-w-lg">
                Studio foto mandiri dengan <strong class="text-ink font-semibold">lighting profesional</strong> dan tema estetik pilihan.
                Booking slot real-time, bayar digital, langsung sesi foto — semua dalam 3 menit.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-10">
                <a href="{{ route('booking.wizard') }}"
                   class="btn-gold btn-bounce inline-flex items-center justify-center gap-3 px-7 py-4 rounded-2xl text-white font-bold text-base shadow-warm-lg">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Booking Sekarang
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>
                <a href="{{ route('portfolio.index') }}"
                   class="inline-flex items-center justify-center gap-3 px-7 py-4 rounded-2xl text-ink font-semibold text-base bg-white hover:bg-cream border border-cream-dark hover:border-gold transition-all duration-200 shadow-card">
                    <i class="fa-regular fa-images text-gold"></i>
                    Lihat Portfolio
                </a>
            </div>

            {{-- Social Proof --}}
            <div class="flex items-center gap-4">
                <div class="flex -space-x-2">
                    @foreach(['/images/couple.jpg', '/images/portrait.jpg'] as $img)
                        <img src="{{ $img }}" alt="Customer" class="w-9 h-9 rounded-full border-2 border-white object-cover shadow-sm" loading="lazy">
                    @endforeach
                    @foreach(['#C8A27A', '#2F4F4F', '#C8A27A'] as $c)
                        <div class="w-9 h-9 rounded-full border-2 border-white flex items-center justify-center text-white text-xs font-bold" style="background:{{ $c }}">★</div>
                    @endforeach
                </div>
                <div>
                    <div class="flex gap-0.5 text-gold text-sm mb-0.5">
                        @for($i=0;$i<5;$i++) <i class="fa-solid fa-star"></i> @endfor
                    </div>
                    <p class="text-xs text-ink-muted font-medium">500+ sesi selesai · <strong class="text-ink">4.9/5</strong> rating pelanggan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 animate-bounce opacity-60">
        <p class="text-[10px] font-semibold text-ink-muted uppercase tracking-widest">Scroll</p>
        <i class="fa-solid fa-chevron-down text-ink-muted text-xs"></i>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     TRUST BADGES STRIP
     ════════════════════════════════════════════════════════════ --}}
<section class="bg-white border-y border-cream-dark py-5">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-4 items-center">
            @foreach([
                ['fa-bolt', '#C8A27A', 'Booking 3 Menit', 'Pilih paket, jadwal, bayar — selesai'],
                ['fa-lock', '#2F4F4F', 'Slot Realtime Lock', 'Tidak ada dobel booking sama sekali'],
                ['fa-shield-halved', '#2E9E6B', 'Pembayaran Aman', 'QRIS, GoPay, OVO, Virtual Account'],
                ['fa-cloud-arrow-down', '#C8A27A', 'Foto Dikirim 24 Jam', 'File digital langsung ke akun Anda'],
            ] as [$icon, $color, $title, $desc])
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $color }}15;">
                        <i class="fa-solid fa-{{ $icon }} text-sm" style="color: {{ $color }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-ink leading-tight">{{ $title }}</p>
                        <p class="text-xs text-ink-muted leading-tight hidden sm:block">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     HOW IT WORKS — 4 LANGKAH
     ════════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Cara Booking</p>
            <h2 class="font-serif font-bold text-4xl text-ink mb-4">Semudah 4 Langkah</h2>
            <p class="text-ink-muted leading-relaxed">Dari memilih paket hingga dapat tiket QR, prosesnya hanya butuh beberapa menit. Tidak perlu ribet DM manual lagi.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $steps = [
                    ['num' => '01', 'icon' => 'box-open', 'title' => 'Pilih Paket & Ruangan', 'desc' => 'Pilih paket foto (Basic, Standard, Premium) dan tema ruangan favoritmu.', 'color' => '#C8A27A'],
                    ['num' => '02', 'icon' => 'calendar-days', 'title' => 'Pilih Slot Waktu', 'desc' => 'Grid slot real-time — hijau artinya tersedia. Pilih tanggal & jam yang cocok.', 'color' => '#2F4F4F'],
                    ['num' => '03', 'icon' => 'credit-card', 'title' => 'Bayar & Dapat Tiket', 'desc' => 'Bayar via QRIS, GoPay, OVO, atau VA Bank. Tiket QR langsung masuk ke akunmu.', 'color' => '#C8A27A'],
                    ['num' => '04', 'icon' => 'camera-retro', 'title' => 'Datang & Berpose!', 'desc' => 'Scan QR di studio, mulai sesi foto! Hasil edit dikirim dalam 24 jam ke akunmu.', 'color' => '#2E9E6B'],
                ];
            @endphp

            @foreach($steps as $idx => $step)
                <div class="relative bg-white rounded-3xl p-7 shadow-card card-lift reveal border border-cream-dark" data-delay="{{ $idx * 100 }}">
                    {{-- Step number watermark --}}
                    <span class="absolute top-5 right-6 font-serif font-bold text-6xl text-cream-dark select-none">{{ $step['num'] }}</span>

                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5 relative z-10"
                         style="background: {{ $step['color'] }}18;">
                        <i class="fa-solid fa-{{ $step['icon'] }} text-lg" style="color: {{ $step['color'] }}"></i>
                    </div>

                    <h3 class="font-serif font-semibold text-lg text-ink mb-2 leading-snug relative z-10">{{ $step['title'] }}</h3>
                    <p class="text-sm text-ink-muted leading-relaxed relative z-10">{{ $step['desc'] }}</p>

                    {{-- Connector arrow (desktop) --}}
                    @if(!$loop->last)
                        <div class="hidden lg:block absolute -right-4 top-1/2 -translate-y-1/2 z-20">
                            <div class="w-8 h-8 rounded-full bg-cream-dark border border-cream-dark flex items-center justify-center">
                                <i class="fa-solid fa-arrow-right text-gold text-xs"></i>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     FEATURED ROOMS / STUDIO THEMES
     ════════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12 reveal">
            <div>
                <p class="text-xs font-bold text-gold uppercase tracking-widest mb-2">Pilih Atmosfer Favoritmu</p>
                <h2 class="font-serif font-bold text-4xl text-ink">Ruangan & Tema Studio</h2>
            </div>
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gold hover:text-gold-dark transition-colors whitespace-nowrap">
                Lihat Semua Ruangan <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredRooms as $room)
                <a href="{{ route('rooms.show', $room->slug) }}"
                   class="group bg-cream rounded-3xl overflow-hidden card-lift border border-cream-dark reveal"
                   data-delay="{{ $loop->index * 80 }}">

                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-cream-dark">
                        <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800&auto=format&fit=crop' }}"
                             alt="{{ $room->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                             loading="lazy">

                        {{-- Category badge --}}
                        <div class="absolute top-4 left-4">
                            <span class="bg-white/90 backdrop-blur-sm text-teal text-xs font-bold px-3 py-1.5 rounded-full border border-white/60">
                                {{ $room->theme }}
                            </span>
                        </div>

                        {{-- Hover CTA overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                            <div class="p-6">
                                <span class="inline-flex items-center gap-2 bg-gold text-white text-sm font-semibold px-4 py-2 rounded-full">
                                    Pilih Ruangan Ini <i class="fa-solid fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-5">
                        <h3 class="font-serif font-semibold text-lg text-ink group-hover:text-gold transition-colors mb-1.5">{{ $room->name }}</h3>
                        <p class="text-sm text-ink-muted line-clamp-2 leading-relaxed mb-4">{{ $room->description }}</p>
                        <div class="flex items-center gap-5 text-xs font-medium text-ink-muted border-t border-cream-dark pt-4">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-users text-gold"></i> {{ $room->capacity }} orang
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-expand text-teal"></i> {{ $room->area_m2 }} m²
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     PACKAGES — PRICING CARDS
     ════════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Transparan, Tanpa Biaya Tersembunyi</p>
            <h2 class="font-serif font-bold text-4xl text-ink mb-4">Pilih Paket yang Tepat</h2>
            <p class="text-ink-muted leading-relaxed">Setiap paket sudah mencakup remote shutter nirkabel, lighting studio profesional, dan akses galeri online 30 hari.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-start">
            @foreach($popularPackages as $pkg)
                @php $isFeatured = $loop->iteration === 2; @endphp

                <div class="relative bg-white rounded-3xl border shadow-card flex flex-col card-lift reveal
                            {{ $isFeatured ? 'border-gold shadow-warm-lg ring-1 ring-gold/30 scale-[1.02]' : 'border-cream-dark' }}"
                     data-delay="{{ $loop->index * 100 }}">

                    {{-- Featured badge --}}
                    @if($isFeatured)
                        <div class="absolute -top-4 left-0 right-0 flex justify-center">
                            <span class="bg-gold text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-warm tracking-wide uppercase">
                                ✦ Paling Populer
                            </span>
                        </div>
                    @endif

                    <div class="p-7 pt-{{ $isFeatured ? '10' : '7' }} flex flex-col flex-1">
                        {{-- Header --}}
                        <div class="mb-6">
                            <div class="w-10 h-10 rounded-xl {{ $isFeatured ? 'bg-gold' : 'bg-cream-dark' }} flex items-center justify-center mb-4">
                                <i class="fa-solid fa-camera text-sm {{ $isFeatured ? 'text-white' : 'text-gold' }}"></i>
                            </div>
                            <h3 class="font-serif font-bold text-2xl text-ink">{{ $pkg->name }}</h3>
                            <p class="text-sm text-ink-muted mt-1.5 leading-relaxed">{{ $pkg->description }}</p>
                        </div>

                        {{-- Price --}}
                        <div class="pb-6 mb-6 border-b border-cream-dark">
                            <p class="text-xs text-ink-muted mb-1">Mulai dari</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-semibold text-ink-muted">Rp</span>
                                <span class="font-serif font-bold text-4xl {{ $isFeatured ? 'text-gold' : 'text-ink' }}">{{ number_format($pkg->price, 0, ',', '.') }}</span>
                                <span class="text-sm text-ink-muted">/ sesi</span>
                            </div>
                        </div>

                        {{-- Features --}}
                        <ul class="space-y-3.5 flex-1 mb-7">
                            @foreach([
                                ['fa-clock', 'text-gold', "Durasi sesi {$pkg->duration_minutes} menit"],
                                ['fa-users', 'text-teal', "Maksimal {$pkg->max_people} orang"],
                                ['fa-wand-magic-sparkles', 'text-success', "{$pkg->edited_photos} foto edit retouch premium"],
                                ['fa-camera-retro', 'text-gold', 'Remote shutter wireless bebas jepret'],
                                ['fa-cloud-arrow-down', 'text-info', 'Galeri online 30 hari'],
                                ['fa-palette', 'text-teal', 'Bebas pilih background & properti'],
                            ] as [$icon, $color, $text])
                                <li class="flex items-center gap-3 text-sm text-ink-soft">
                                    <div class="w-5 h-5 rounded-full {{ $isFeatured ? 'bg-gold/15' : 'bg-cream' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-check text-[10px] {{ $isFeatured ? 'text-gold' : 'text-ink-muted' }}"></i>
                                    </div>
                                    {{ $text }}
                                </li>
                            @endforeach
                        </ul>

                        {{-- CTA --}}
                        <a href="{{ route('booking.wizard', ['package_id' => $pkg->id]) }}"
                           class="btn-bounce flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl font-bold text-sm transition-all duration-200
                                  {{ $isFeatured
                                     ? 'btn-gold text-white shadow-warm'
                                     : 'bg-cream-dark text-ink hover:bg-gold hover:text-white border border-cream-dark hover:border-gold' }}">
                            Pilih {{ $pkg->name }}
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Compare CTA --}}
        <div class="text-center mt-10 reveal">
            <a href="{{ route('packages.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gold hover:text-gold-dark transition-colors">
                Bandingkan semua paket & add-on <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     COUPLE SECTION — Emotional CTA with photo
     ════════════════════════════════════════════════════════════ --}}
<section class="relative py-20 overflow-hidden bg-ink">
    {{-- Background photo --}}
    <div class="absolute inset-0">
        <img src="/images/couple.jpg" alt="Studio Foto Couple" class="w-full h-full object-cover opacity-25" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-r from-ink/95 to-ink/60"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="reveal space-y-6">
                <p class="text-xs font-bold text-gold uppercase tracking-widest">Momen Berharga</p>
                <h2 class="font-serif font-bold text-4xl sm:text-5xl text-white leading-tight">
                    Setiap Tawa, Setiap Pose<br>
                    <em class="not-italic text-gold">Abadikan Bersama</em>
                </h2>
                <p class="text-white/70 text-lg leading-relaxed max-w-md">
                    Dari pre-wedding hingga foto wisuda, ultah, sampai sesi santai bersama sahabat — SnapSpace hadir untuk membuat setiap momen menjadi karya seni.
                </p>
                <div class="flex flex-wrap gap-3">
                    @foreach(['Prewedding', 'Wisuda', 'Family', 'Sahabat', 'Self Photo', 'Produk'] as $cat)
                        <span class="px-4 py-1.5 rounded-full text-sm font-medium border border-gold/30 text-gold/80 hover:bg-gold/10 transition-colors cursor-default">
                            {{ $cat }}
                        </span>
                    @endforeach
                </div>
                <a href="{{ route('booking.wizard') }}"
                   class="btn-gold btn-bounce inline-flex items-center gap-3 px-7 py-4 rounded-2xl text-white font-bold text-base shadow-warm-lg">
                    <i class="fa-solid fa-camera-retro"></i>
                    Booking Studio Sekarang
                </a>
            </div>

            {{-- Stats grid --}}
            <div class="grid grid-cols-2 gap-4 reveal" data-delay="150">
                @foreach([
                    ['500+', 'Sesi Foto Selesai', 'fa-camera'],
                    ['4.9★', 'Rating Pelanggan', 'fa-star'],
                    ['15+', 'Tema Ruangan Unik', 'fa-palette'],
                    ['24/7', 'Booking Online', 'fa-calendar'],
                ] as [$val, $label, $icon])
                    <div class="bg-white/8 backdrop-blur-sm border border-white/10 rounded-2xl p-6 text-center">
                        <div class="w-10 h-10 rounded-xl bg-gold/20 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-{{ $icon }} text-gold text-sm"></i>
                        </div>
                        <p class="font-serif font-bold text-3xl text-white mb-1">{{ $val }}</p>
                        <p class="text-xs text-white/60 font-medium">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     PORTFOLIO PREVIEW
     ════════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-12 reveal">
            <div>
                <p class="text-xs font-bold text-gold uppercase tracking-widest mb-2">100% Foto Asli Pelanggan</p>
                <h2 class="font-serif font-bold text-4xl text-ink">Portfolio Terbaru</h2>
            </div>
            <a href="{{ route('portfolio.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gold hover:text-gold-dark transition-colors whitespace-nowrap">
                Galeri Lengkap <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if($portfolioPreview->count() > 0)
            {{-- Masonry-style grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                @foreach($portfolioPreview as $port)
                    <div class="group relative overflow-hidden rounded-2xl bg-cream-dark border border-cream-dark card-lift reveal
                                {{ $loop->iteration === 1 ? 'sm:col-span-2 sm:row-span-2' : '' }}"
                         data-delay="{{ min($loop->index * 60, 300) }}"
                         style="{{ $loop->iteration === 1 ? 'aspect-ratio: auto' : 'aspect-ratio: 1/1' }}">

                        <div class="{{ $loop->iteration === 1 ? 'aspect-square sm:aspect-[1/1.2]' : 'aspect-square' }}">
                            <img src="{{ $port->image_path }}"
                                 alt="{{ $port->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                 loading="lazy">
                        </div>

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <span class="text-[10px] font-bold text-gold uppercase tracking-wide mb-0.5">{{ $port->category }}</span>
                            <p class="text-sm font-semibold text-white line-clamp-1">{{ $port->title }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty state --}}
            <div class="text-center py-16 bg-cream rounded-3xl border border-cream-dark">
                <div class="w-16 h-16 rounded-2xl bg-cream-dark flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-images text-2xl text-gold/60"></i>
                </div>
                <p class="text-ink-muted font-medium">Portfolio segera hadir</p>
            </div>
        @endif
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     TESTIMONIALS
     ════════════════════════════════════════════════════════════ --}}
@if($reviews->isNotEmpty())
<section class="py-20 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 reveal">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Testimoni</p>
            <h2 class="font-serif font-bold text-4xl text-ink mb-4">Kata Mereka tentang SnapSpace</h2>
            <p class="text-ink-muted">Ribuan momen indah telah diabadikan. Inilah cerita nyata dari pelanggan kami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reviews as $rev)
                <div class="bg-white rounded-3xl p-7 border border-cream-dark shadow-card card-lift reveal" data-delay="{{ $loop->index * 80 }}">
                    {{-- Stars --}}
                    <div class="flex gap-1 text-gold text-sm mb-4">
                        @for($i=1; $i<=$rev->rating; $i++)
                            <i class="fa-solid fa-star"></i>
                        @endfor
                        @for($i=$rev->rating+1; $i<=5; $i++)
                            <i class="fa-regular fa-star text-cream-dark"></i>
                        @endfor
                    </div>

                    {{-- Quote --}}
                    <div class="relative mb-5">
                        <i class="fa-solid fa-quote-left text-3xl text-gold/15 absolute -top-1 -left-1"></i>
                        <p class="text-sm text-ink-soft leading-relaxed pl-4">{{ $rev->comment }}</p>
                    </div>

                    {{-- Author --}}
                    <div class="flex items-center gap-3 pt-5 border-t border-cream-dark">
                        <div class="w-10 h-10 rounded-full bg-teal text-white flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($rev->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-ink">{{ $rev->user->name }}</p>
                            <p class="text-xs text-ink-muted">{{ $rev->booking->room->name ?? 'SnapSpace Studio' }}</p>
                        </div>
                        <div class="ml-auto">
                            <span class="w-7 h-7 rounded-full bg-success/10 flex items-center justify-center">
                                <i class="fa-solid fa-circle-check text-success text-xs"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════════════════════════
     FAQ PREVIEW
     ════════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Pertanyaan Umum</p>
            <h2 class="font-serif font-bold text-4xl text-ink">Ada yang Ingin Ditanyakan?</h2>
        </div>

        <div class="space-y-3 reveal" data-delay="100">
            @foreach([
                ['Apakah saya perlu membawa fotografer sendiri?', 'Tidak perlu! Studio kami dilengkapi dengan kamera DSLR/Mirrorless + tripod + remote shutter wireless. Anda bisa foto sendiri dengan mudah tanpa bantuan fotografer.'],
                ['Apa saja metode pembayaran yang tersedia?', 'Kami menerima QRIS, GoPay, OVO, DANA, ShopeePay, Virtual Account BCA/BNI/BRI/Mandiri, dan kartu kredit melalui gateway pembayaran Midtrans yang aman.'],
                ['Berapa lama foto siap diterima setelah sesi?', 'Foto yang sudah melalui proses editing akan dikirimkan ke galeri online akun Anda dalam waktu maksimal 24 jam setelah sesi selesai.'],
                ['Apakah bisa reschedule jika ada halangan?', 'Reschedule bisa dilakukan maksimal H-24 jam sebelum jadwal sesi tanpa biaya tambahan. Pembatalan H-24 dikenakan biaya 50% dari nilai paket.'],
                ['Berapa maksimal orang dalam satu sesi foto?', 'Tergantung paket dan ruangan yang dipilih. Umumnya paket Basic untuk 1-2 orang, Standard 1-4 orang, dan Premium hingga 6 orang.'],
            ] as $idx => [$q, $a])
                <div x-data="{ open: {{ $idx === 0 ? 'true' : 'false' }} }"
                     class="bg-cream rounded-2xl border border-cream-dark overflow-hidden">
                    <button @click="open = !open"
                            class="w-full flex items-center justify-between gap-4 px-6 py-4 text-left hover:bg-cream-warm transition-colors"
                            :aria-expanded="open">
                        <span class="font-semibold text-sm sm:text-base text-ink">{{ $q }}</span>
                        <div class="flex-shrink-0 w-7 h-7 rounded-full bg-white border border-cream-dark flex items-center justify-center transition-transform duration-200"
                             :class="open ? 'rotate-180 border-gold' : ''">
                            <i class="fa-solid fa-chevron-down text-xs text-ink-muted" :class="open ? 'text-gold' : ''"></i>
                        </div>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="px-6 pb-5">
                        <p class="text-sm text-ink-muted leading-relaxed">{{ $a }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-8 reveal" data-delay="200">
            @if(Route::has('faq'))
                <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gold hover:text-gold-dark transition-colors">
                    Lihat semua pertanyaan <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════════
     FINAL CTA SECTION
     ════════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-28 relative overflow-hidden bg-cream-warm">
    {{-- Decorative elements --}}
    <div class="absolute top-0 left-0 w-72 h-72 rounded-full bg-gold/8 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-teal/5 translate-x-1/3 translate-y-1/3"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
        <div class="bg-white rounded-4xl p-10 sm:p-14 shadow-warm-lg border border-gold/20">
            <div class="w-16 h-16 rounded-2xl bg-gold flex items-center justify-center mx-auto mb-6 shadow-warm">
                <i class="fa-solid fa-camera-retro text-2xl text-white"></i>
            </div>

            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-4">Siap untuk Tampil Percaya Diri?</p>
            <h2 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-5 leading-tight">
                Slot Terbatas Setiap Harinya
            </h2>
            <p class="text-ink-muted text-lg leading-relaxed mb-8 max-w-xl mx-auto">
                Jangan sampai jadwal pilihan Anda diambil orang lain. Amankan slot sesi foto Anda sekarang!
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('booking.wizard') }}"
                   class="btn-gold btn-bounce inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl text-white font-bold text-base shadow-warm-lg">
                    <i class="fa-solid fa-calendar-plus"></i>
                    Booking Studio Sekarang
                </a>
                @if(Route::has('contact'))
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl text-ink font-semibold text-base bg-cream border border-cream-dark hover:border-gold transition-all duration-200">
                        <i class="fa-brands fa-whatsapp text-success"></i>
                        Tanya via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
