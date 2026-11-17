@extends('layouts.public')

@section('title', 'E-Tiket Sesi Foto ' . $booking->booking_code . ' — SnapSpace Studio')
@section('meta_description', 'E-Tiket resmi sesi foto SnapSpace Studio Klaten. Tunjukkan kode QR ini ke meja resepsionis saat kedatangan di studio.')

@section('content')
<div class="bg-cream py-10 sm:py-16 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">E-Tiket #{{ $booking->booking_code }}</span>
        </nav>

        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-check text-xl text-emerald-600 shrink-0"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-info text-xl text-amber-600 shrink-0"></i>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-xl text-rose-600 shrink-0"></i>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- ══ THE BOARDING PASS TICKET CARD ══ --}}
        <div class="bg-white rounded-3xl border border-cream-dark shadow-card overflow-hidden relative">

            {{-- Top Accent Bar --}}
            <div class="h-2.5 bg-gradient-to-r from-gold via-teal to-gold"></div>

            <div class="p-6 sm:p-8">

                {{-- Header Studio --}}
                <div class="flex items-center justify-between pb-6 border-b border-cream-dark gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gold flex items-center justify-center text-white shadow-warm">
                            <i class="fa-solid fa-camera-retro text-xl"></i>
                        </div>
                        <div>
                            <span class="font-serif font-bold text-xl text-ink block">SnapSpace Studio</span>
                            <span class="text-xs text-ink-muted">Klaten, Jawa Tengah · Indonesia</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-gold uppercase tracking-wider block">Official E-Ticket</span>
                        <span class="text-xs font-mono font-bold text-ink">{{ $booking->booking_code }}</span>
                    </div>
                </div>

                {{-- Status Banner --}}
                <div class="my-6 p-4 rounded-2xl flex items-center justify-between gap-3
                     @if(in_array($booking->status, ['confirmed', 'paid'])) bg-emerald-50 border border-emerald-200 text-emerald-800
                     @elseif($booking->status === 'dp_paid') bg-amber-50 border border-amber-200 text-amber-800
                     @elseif(in_array($booking->status, ['in_progress', 'checked_in'])) bg-blue-50 border border-blue-200 text-blue-800
                     @elseif(in_array($booking->status, ['completed', 'delivered'])) bg-teal/10 border border-teal/20 text-teal
                     @else bg-stone-100 border border-stone-200 text-stone-700 @endif">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid
                           @if(in_array($booking->status, ['confirmed', 'paid'])) fa-circle-check text-emerald-600
                           @elseif($booking->status === 'dp_paid') fa-wallet text-amber-600
                           @elseif(in_array($booking->status, ['in_progress', 'checked_in'])) fa-user-check text-blue-600
                           @elseif(in_array($booking->status, ['completed', 'delivered'])) fa-award text-teal
                           @else fa-clock text-stone-600 @endif text-lg"></i>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider block">
                                @if(in_array($booking->status, ['confirmed', 'paid'])) Tiket Terkonfirmasi (Lunas 100%)
                                @elseif($booking->status === 'dp_paid') DP 50% Diterima (Slot Terkunci)
                                @elseif(in_array($booking->status, ['in_progress', 'checked_in'])) Sudah Check-In (Sesi Berlangsung)
                                @elseif(in_array($booking->status, ['completed', 'delivered'])) Sesi Foto Selesai
                                @else Status: {{ $booking->status_label }} @endif
                            </span>
                            <span class="text-[11px] opacity-80 block">
                                @if($booking->status === 'dp_paid')
                                    Sisa tagihan Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }} dapat dilunasi di kasir studio sebelum sesi.
                                @elseif(in_array($booking->status, ['confirmed', 'paid']))
                                    Slot jadwal aman terkunci. Tunjukkan QR ini ke resepsionis studio.
                                @elseif(in_array($booking->status, ['in_progress', 'checked_in']))
                                    Tercatat hadir pada {{ $booking->checked_in_at?->format('H:i, d M Y') ?? 'hari ini' }}.
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Scannable QR Code Display --}}
                <div class="text-center py-4 bg-cream/50 rounded-2xl border border-cream-dark mb-6">
                    <div class="inline-block p-4 bg-white rounded-2xl border-2 border-stone-200 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(route('ticket.show', $booking->booking_code)) }}"
                             alt="QR Code Tiket {{ $booking->booking_code }}"
                             class="w-48 h-48 mx-auto object-contain">
                    </div>
                    <p class="font-mono font-bold text-sm text-ink mt-3 tracking-wider">{{ $booking->booking_code }}</p>
                    <p class="text-xs text-ink-muted mt-1 px-4 max-w-sm mx-auto">
                        Arahkan kamera scanner staf studio atau kamera HP ke kode QR di atas untuk verifikasi instan.
                    </p>
                </div>

                {{-- Schedule & Room Details --}}
                <div class="grid grid-cols-2 gap-4 pb-6 border-b border-cream-dark text-xs sm:text-sm">
                    <div class="p-4 rounded-2xl bg-cream">
                        <span class="text-ink-muted text-xs block mb-1">Tanggal Sesi:</span>
                        <span class="font-bold text-ink flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-day text-gold"></i>
                            {{ $booking->booking_date->translatedFormat('l, d M Y') }}
                        </span>
                    </div>
                    <div class="p-4 rounded-2xl bg-cream">
                        <span class="text-ink-muted text-xs block mb-1">Jam Sesi (WIB):</span>
                        <span class="font-bold text-ink font-mono flex items-center gap-1.5">
                            <i class="fa-solid fa-clock text-gold"></i>
                            {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }} WIB
                        </span>
                    </div>
                    <div class="p-4 rounded-2xl bg-cream">
                        <span class="text-ink-muted text-xs block mb-1">Ruangan Studio:</span>
                        <span class="font-bold text-ink flex items-center gap-1.5">
                            <i class="fa-solid fa-door-open text-teal"></i>
                            {{ $booking->room->name }}
                        </span>
                    </div>
                    <div class="p-4 rounded-2xl bg-cream">
                        <span class="text-ink-muted text-xs block mb-1">Paket & Peserta:</span>
                        <span class="font-bold text-ink flex items-center gap-1.5">
                            <i class="fa-solid fa-users text-gold"></i>
                            {{ $booking->package->name }} ({{ $booking->people_count }} orang)
                        </span>
                    </div>
                </div>

                {{-- Guest Info & Payment Summary --}}
                <div class="py-6 border-b border-cream-dark space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Nama Pelanggan:</span>
                        <span class="font-bold text-ink">{{ $booking->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Total Tagihan:</span>
                        <span class="font-mono text-ink">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-700 font-semibold">
                        <span>Sudah Dibayar:</span>
                        <span class="font-mono">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->remaining_balance > 0)
                        <div class="flex justify-between text-amber-700 font-bold pt-2 border-t border-cream-dark">
                            <span>Sisa Tagihan (Pelunasan):</span>
                            <span class="font-mono">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 space-y-3">
                    {{-- Tombol Check-In Kehadiran Langsung --}}
                    @if(!in_array($booking->status, ['in_progress', 'checked_in', 'completed', 'delivered', 'cancelled', 'expired']))
                        <form method="POST" action="{{ route('ticket.confirm', $booking->booking_code) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3.5 rounded-2xl bg-gold hover:bg-gold-dark text-white font-bold text-sm shadow-warm flex items-center justify-center gap-2 transition-all">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Konfirmasi Check-In Hadir di Studio</span>
                            </button>
                        </form>
                    @endif

                    {{-- Jika ada sisa pembayaran --}}
                    @if($booking->remaining_balance > 0 && in_array($booking->status, ['confirmed', 'dp_paid', 'paid']))
                        <a href="{{ route('booking.pay', $booking->booking_code) }}"
                           class="w-full py-3 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <span>Bayar Pelunasan Sisa Online (Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }})</span>
                        </a>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <button type="button" onclick="window.print()"
                                class="py-2.5 px-4 rounded-xl border border-cream-dark hover:border-gold text-ink text-xs font-semibold flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-print text-gold"></i>
                            <span>Cetak Tiket / Simpan PDF</span>
                        </button>
                        <a href="https://maps.google.com/?q=SnapSpace+Studio+Klaten" target="_blank" rel="noopener"
                           class="py-2.5 px-4 rounded-xl border border-cream-dark hover:border-teal text-ink text-xs font-semibold flex items-center justify-center gap-2 transition-colors">
                            <i class="fa-solid fa-location-dot text-teal"></i>
                            <span>Petunjuk Arah Maps</span>
                        </a>
                    </div>
                </div>

                {{-- Location Footer Note --}}
                <div class="mt-8 pt-4 border-t border-cream-dark text-center text-xs text-ink-muted">
                    <p class="font-semibold text-ink">Lokasi Studio SnapSpace:</p>
                    <p class="mt-0.5">Jl. Klaten - Solo No. 88, Pandanan, Wonosari, Klaten · Buka Setiap Hari 09:00 – 21:00 WIB</p>
                    <p class="mt-1 text-[11px] text-ink-muted/80">Mohon hadir 10–15 menit sebelum jam sesi foto untuk persiapan kostum dan persiapan.</p>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
