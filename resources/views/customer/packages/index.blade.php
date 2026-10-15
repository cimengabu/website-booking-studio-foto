@extends('layouts.public')

@section('title', 'Paket & Harga — SnapSpace Studio Foto Surabaya')
@section('meta_description', 'Daftar paket foto self-studio SnapSpace: Basic, Standard, Premium. Harga transparan mulai Rp 99.000. Sudah termasuk editing, remote shutter, dan galeri digital.')

@section('content')

{{-- ── PAGE HEADER ── --}}
<section class="relative bg-cream-warm py-20 border-b border-cream-dark overflow-hidden">
    <div class="absolute inset-0 opacity-30" style="background: radial-gradient(ellipse 70% 70% at 100% 0%, #C8A27A18, transparent)"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-gold/5 -translate-x-1/3 translate-y-1/2"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Paket & Harga</span>
        </nav>

        <div class="max-w-2xl">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Transparan & Hemat</p>
            <h1 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-4 leading-tight">
                Paket & Harga Studio Foto
            </h1>
            <p class="text-ink-muted text-lg leading-relaxed">
                Semua paket sudah termasuk akses penuh ke studio, remote shutter wireless, properti, dan file digital berkualitas tinggi.
                <strong class="text-ink">Tidak ada biaya tersembunyi.</strong>
            </p>
        </div>
    </div>
</section>

{{-- ── MAIN PACKAGES ── --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Comparison header --}}
        <div class="text-center mb-12 reveal">
            <p class="text-sm text-ink-muted">Semua paket bisa dipilih di ruangan mana saja · Add-on tersedia saat booking</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-start">
            @foreach($packages as $pkg)
                @php $isFeatured = $loop->iteration === 2; @endphp

                <div class="relative bg-white rounded-3xl border shadow-card flex flex-col reveal
                            {{ $isFeatured
                                ? 'border-gold shadow-warm-lg ring-1 ring-gold/30 md:scale-105 md:-mt-4 md:-mb-4'
                                : 'border-cream-dark card-lift' }}"
                     data-delay="{{ $loop->index * 100 }}">

                    @if($isFeatured)
                        <div class="absolute -top-4 inset-x-0 flex justify-center">
                            <span class="bg-gold text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-warm tracking-wide uppercase">
                                ✦ Terpopuler
                            </span>
                        </div>
                    @endif

                    {{-- Header colored bar --}}
                    <div class="h-2 rounded-t-3xl {{ $isFeatured ? 'bg-gold' : 'bg-cream-dark' }}"></div>

                    <div class="p-7 {{ $isFeatured ? 'pt-8' : '' }} flex flex-col flex-1">

                        {{-- Package identity --}}
                        <div class="mb-6 pb-6 border-b border-cream-dark">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h2 class="font-serif font-bold text-2xl text-ink">{{ $pkg->name }}</h2>
                                    <p class="text-sm text-ink-muted mt-1 leading-relaxed">{{ $pkg->description }}</p>
                                </div>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-sm font-medium text-ink-muted">Rp</span>
                                <span class="font-serif font-bold text-4xl {{ $isFeatured ? 'text-gold' : 'text-ink' }}">
                                    {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                                <span class="text-sm text-ink-muted">/ sesi</span>
                            </div>
                        </div>

                        {{-- Feature list --}}
                        <div class="flex-1 mb-7">
                            <p class="text-xs font-bold text-ink-muted uppercase tracking-wide mb-4">Yang Termasuk:</p>
                            <ul class="space-y-3.5">
                                @php
                                    $features = [
                                        ['icon' => 'clock', 'text' => "Durasi sesi {$pkg->duration_minutes} menit", 'color' => 'text-gold', 'bg' => 'bg-gold/10'],
                                        ['icon' => 'users', 'text' => "Maks. {$pkg->max_people} orang / sesi", 'color' => 'text-teal', 'bg' => 'bg-teal-50'],
                                        ['icon' => 'wand-magic-sparkles', 'text' => "{$pkg->edited_photos} foto editing premium", 'color' => 'text-success', 'bg' => 'bg-success/10'],
                                        ['icon' => 'camera-retro', 'text' => 'Remote shutter wireless', 'color' => 'text-gold', 'bg' => 'bg-gold/10'],
                                        ['icon' => 'palette', 'text' => 'Bebas pilih background & properti', 'color' => 'text-teal', 'bg' => 'bg-teal-50'],
                                        ['icon' => 'cloud-arrow-down', 'text' => 'File digital softcopy full', 'color' => 'text-info', 'bg' => 'bg-blue-50'],
                                        ['icon' => 'images', 'text' => 'Galeri online 30 hari', 'color' => 'text-info', 'bg' => 'bg-blue-50'],
                                        ['icon' => 'bolt', 'text' => 'Tiket QR e-ticket instan', 'color' => 'text-gold', 'bg' => 'bg-gold/10'],
                                    ];
                                @endphp
                                @foreach($features as $feat)
                                    <li class="flex items-center gap-3 text-sm text-ink-soft">
                                        <div class="w-8 h-8 rounded-xl {{ $feat['bg'] }} flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-{{ $feat['icon'] }} text-xs {{ $feat['color'] }}"></i>
                                        </div>
                                        {{ $feat['text'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- CTA --}}
                        <a href="{{ route('booking.wizard', ['package_id' => $pkg->id]) }}"
                           id="pilih-paket-{{ $pkg->id }}"
                           class="btn-bounce flex items-center justify-center gap-2 w-full py-4 rounded-2xl font-bold text-base transition-all duration-200
                                  {{ $isFeatured
                                     ? 'btn-gold text-white shadow-warm'
                                     : 'bg-cream-dark text-ink hover:bg-gold hover:text-white border border-cream-dark hover:border-transparent' }}">
                            <i class="fa-solid fa-calendar-plus"></i>
                            Pilih {{ $pkg->name }}
                        </a>

                        <p class="text-center text-xs text-ink-muted mt-3">Tidak ada biaya pendaftaran</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Comparison note --}}
        <div class="mt-12 bg-cream rounded-3xl p-8 border border-cream-dark reveal">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                @foreach([
                    ['fa-shield-halved', 'text-success', 'Pembayaran 100% Aman', 'Diproses melalui Midtrans — gateway resmi & terpercaya'],
                    ['fa-rotate-left', 'text-gold', 'Reschedule Mudah', 'Ubah jadwal kapan saja (H-24 jam tanpa biaya tambahan)'],
                    ['fa-headset', 'text-teal', 'CS Siap Membantu', 'Tim kami siap menjawab pertanyaan Anda via WhatsApp'],
                ] as [$icon, $color, $title, $desc])
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-cream-dark flex items-center justify-center shadow-card">
                            <i class="fa-solid fa-{{ $icon }} text-lg {{ $color }}"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-ink">{{ $title }}</p>
                            <p class="text-xs text-ink-muted mt-0.5 leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── ADD-ONS SECTION ── --}}
@if(isset($addons) && $addons->count() > 0)
<section class="py-16 bg-cream border-t border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 reveal">
            <div>
                <p class="text-xs font-bold text-gold uppercase tracking-widest mb-2">Opsional</p>
                <h2 class="font-serif font-bold text-3xl text-ink">Layanan Tambahan (Add-Ons)</h2>
                <p class="text-sm text-ink-muted mt-2">Dapat dipilih langsung saat proses booking.</p>
            </div>
            <span class="px-4 py-2 rounded-full bg-white border border-cream-dark text-xs font-bold text-teal whitespace-nowrap shadow-card">
                {{ $addons->count() }} add-on tersedia
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($addons as $addon)
                <div class="bg-white rounded-2xl p-6 border border-cream-dark card-lift reveal shadow-card" data-delay="{{ $loop->index * 60 }}">
                    {{-- Icon --}}
                    <div class="w-11 h-11 rounded-xl bg-gold/10 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-{{ $addon->type === 'per_unit' ? 'print' : ($addon->type === 'per_person' ? 'shirt' : 'cloud-arrow-down') }} text-gold text-sm"></i>
                    </div>

                    <h3 class="font-semibold text-sm text-ink mb-1.5">{{ $addon->name }}</h3>
                    <p class="text-xs text-ink-muted leading-relaxed mb-4">{{ $addon->description }}</p>

                    <div class="flex items-center justify-between pt-4 border-t border-cream-dark">
                        <span class="font-serif font-bold text-lg text-gold">
                            +Rp {{ number_format($addon->price, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] font-medium text-ink-muted bg-cream px-2.5 py-1 rounded-full">
                            {{ $addon->type === 'per_unit' ? '/ unit' : ($addon->type === 'per_person' ? '/ orang' : 'flat') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── FAQ + CTA ── --}}
<section class="py-16 bg-white border-t border-cream-dark">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 reveal">
            <h2 class="font-serif font-bold text-3xl text-ink">Masih ada pertanyaan soal paket?</h2>
        </div>
        <div class="flex flex-col sm:flex-row gap-4 justify-center reveal" data-delay="100">
            @if(Route::has('faq'))
                <a href="{{ route('faq') }}" class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-ink font-semibold text-sm bg-cream border border-cream-dark hover:border-gold transition-all duration-200">
                    <i class="fa-solid fa-circle-question text-gold"></i>
                    Baca FAQ Lengkap
                </a>
            @endif
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-white font-semibold text-sm bg-success hover:bg-success/90 transition-colors shadow-card">
                <i class="fa-brands fa-whatsapp"></i>
                Tanya via WhatsApp
            </a>
            <a href="{{ route('booking.wizard') }}"
               class="btn-gold btn-bounce inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl text-white font-semibold text-sm shadow-warm">
                <i class="fa-solid fa-calendar-plus"></i>
                Booking Sekarang
            </a>
        </div>
    </div>
</section>

@endsection
