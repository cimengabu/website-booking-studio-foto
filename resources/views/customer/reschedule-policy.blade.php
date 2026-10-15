@extends('layouts.public')
@section('title', 'Kebijakan Reschedule & Pembatalan — SnapSpace Studio')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 space-y-8">
    <nav class="flex items-center gap-2 text-xs text-ink-muted">
        <a href="{{ route('home') }}" class="hover:text-gold">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
        <span class="text-ink font-medium">Kebijakan Reschedule</span>
    </nav>
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-cream-dark shadow-card">
        <h1 class="font-serif font-bold text-3xl text-ink mb-6">Kebijakan Reschedule & Pembatalan</h1>
        <p class="text-xs text-ink-muted mb-8">Terakhir diperbarui: {{ date('d F Y') }}</p>
        <div class="space-y-6">
            @foreach([
                ['Reschedule Gratis', 'bg-success/10 border-success/20 text-success', 'Dapat dilakukan H-24 jam atau lebih sebelum jadwal sesi. Tidak dikenakan biaya apapun. Lakukan melalui halaman "Booking Saya" di akun Anda.'],
                ['Reschedule H-12 hingga H-24', 'bg-warning/10 border-warning/20 text-warning', 'Dikenakan biaya administrasi sebesar Rp 25.000. Reschedule hanya bisa dilakukan 1 kali.'],
                ['Pembatalan H-24 atau lebih', 'bg-success/10 border-success/20 text-success', 'Refund 100% (dikurangi biaya gateway pembayaran 2%). Dana dikembalikan dalam 3–7 hari kerja.'],
                ['Pembatalan kurang dari H-24', 'bg-danger/10 border-danger/20 text-danger', 'Tidak ada refund tunai. Dana dapat dikonversi menjadi kredit studio berlaku 90 hari.'],
                ['No-show (tidak datang tanpa konfirmasi)', 'bg-danger/10 border-danger/20 text-danger', 'Tidak ada refund dan tidak bisa dijadwalkan ulang.'],
            ] as [$title, $classes, $desc])
                <div class="p-5 rounded-2xl border {{ $classes }}">
                    <p class="font-bold text-sm mb-1">{{ $title }}</p>
                    <p class="text-sm text-ink-muted leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
