@extends('layouts.public')
@section('title', 'Syarat & Ketentuan — SnapSpace Studio')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 space-y-8">
    <nav class="flex items-center gap-2 text-xs text-ink-muted">
        <a href="{{ route('home') }}" class="hover:text-gold">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-ink font-medium">Syarat & Ketentuan</span>
    </nav>
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-cream-dark shadow-card">
        <h1 class="font-serif font-bold text-3xl text-ink mb-6">Syarat & Ketentuan</h1>
        <p class="text-xs text-ink-muted mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>
        <div class="prose prose-sm max-w-none text-ink-soft leading-relaxed space-y-6">
            <p>Dengan menggunakan layanan SnapSpace Studio, Anda menyetujui syarat dan ketentuan berikut ini. Harap baca dengan seksama sebelum melakukan pemesanan.</p>
            <h2 class="font-serif font-semibold text-xl text-ink">1. Pemesanan dan Pembayaran</h2>
            <p>Pemesanan dianggap sah setelah pembayaran diterima dan dikonfirmasi oleh sistem. Harga yang tertera di website adalah harga akhir dan sudah termasuk semua fasilitas dalam paket yang dipilih.</p>
            <h2 class="font-serif font-semibold text-xl text-ink">2. Penggunaan Studio</h2>
            <p>Pelanggan bertanggung jawab penuh atas keamanan dan kebersihan studio selama sesi berlangsung. Dilarang membawa makanan/minuman yang berwarna ke dalam area studio.</p>
            <h2 class="font-serif font-semibold text-xl text-ink">3. Hak Cipta</h2>
            <p>Seluruh foto yang dihasilkan adalah milik pelanggan. SnapSpace berhak menggunakan foto untuk keperluan promosi kecuali pelanggan menyatakan keberatan secara tertulis.</p>
        </div>
    </div>
</div>
@endsection
