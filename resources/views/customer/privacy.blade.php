@extends('layouts.public')
@section('title', 'Kebijakan Privasi — SnapSpace Studio')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 space-y-8">
    <nav class="flex items-center gap-2 text-xs text-ink-muted">
        <a href="{{ route('home') }}" class="hover:text-gold">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-ink font-medium">Kebijakan Privasi</span>
    </nav>
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-cream-dark shadow-card">
        <h1 class="font-serif font-bold text-3xl text-ink mb-6">Kebijakan Privasi</h1>
        <p class="text-xs text-ink-muted mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>
        <div class="prose prose-sm max-w-none text-ink-soft leading-relaxed space-y-6">
            <p>SnapSpace Studio berkomitmen untuk melindungi privasi dan keamanan data pribadi Anda.</p>
            <h2 class="font-serif font-semibold text-xl text-ink">Data yang Kami Kumpulkan</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>Nama dan informasi kontak (email, nomor HP)</li>
                <li>Data booking dan transaksi pembayaran</li>
                <li>Foto yang Anda hasilkan selama sesi (disimpan sementara)</li>
            </ul>
            <h2 class="font-serif font-semibold text-xl text-ink">Penggunaan Data</h2>
            <p>Data Anda digunakan semata-mata untuk keperluan layanan SnapSpace dan tidak akan dijual atau dibagikan kepada pihak ketiga tanpa izin Anda, kecuali diwajibkan oleh hukum.</p>
        </div>
    </div>
</div>
@endsection
