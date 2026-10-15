@extends('layouts.public')

@section('title', '404 — Halaman Tidak Ditemukan')

@section('content')
<section class="min-h-[80vh] flex items-center justify-center bg-cream py-16">
    <div class="text-center px-4 max-w-lg">
        {{-- Animated 404 --}}
        <div class="relative mb-8">
            <p class="font-serif font-black text-[120px] sm:text-[180px] text-cream-dark leading-none select-none">404</p>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-24 h-24 rounded-3xl bg-gold/20 flex items-center justify-center">
                    <i class="fa-solid fa-camera-retro text-gold text-4xl"></i>
                </div>
            </div>
        </div>

        <h1 class="font-serif font-bold text-3xl text-ink mb-3">Ups, Halaman Tidak Ditemukan</h1>
        <p class="text-ink-muted leading-relaxed mb-8 max-w-sm mx-auto">
            Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau alamat URL yang Anda masukkan salah.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="btn-gold btn-bounce inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl text-white font-bold shadow-warm">
                <i class="fa-solid fa-house"></i> Kembali ke Beranda
            </a>
            <a href="{{ route('booking.wizard') }}"
               class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl border border-cream-dark text-ink font-semibold hover:border-gold transition-colors">
                <i class="fa-solid fa-calendar-plus text-gold"></i> Booking Sekarang
            </a>
        </div>

        {{-- Quick Links --}}
        <div class="mt-10 pt-8 border-t border-cream-dark">
            <p class="text-xs text-ink-muted mb-3">Mungkin Anda mencari:</p>
            <div class="flex flex-wrap gap-2 justify-center">
                @foreach([
                    ['Paket & Harga', route('packages.index')],
                    ['Galeri Portfolio', route('portfolio.index')],
                    ['FAQ', Route::has('faq') ? route('faq') : '#'],
                    ['Kontak', Route::has('contact') ? route('contact') : '#'],
                ] as [$label, $url])
                    <a href="{{ $url }}" class="px-4 py-2 rounded-full bg-white border border-cream-dark text-xs font-semibold text-ink-muted hover:text-gold hover:border-gold transition-all">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
