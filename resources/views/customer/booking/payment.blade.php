@extends('layouts.public')

@section('title', 'Pembayaran Booking ' . $booking->booking_code . ' — SnapSpace Studio')
@section('meta_description', 'Selesaikan pembayaran booking studio foto SnapSpace Klaten secara instan dan aman via QRIS, Virtual Account, E-Wallet, atau Kartu.')

@push('styles')
<style>
    .payment-tab-btn {
        @apply px-4 py-3 rounded-2xl font-semibold text-xs sm:text-sm transition-all duration-200 border text-center flex items-center justify-center gap-2 cursor-pointer;
    }
    .payment-tab-btn.active {
        @apply bg-ink text-white border-ink shadow-md;
    }
    .payment-tab-btn:not(.active) {
        @apply bg-white text-ink-muted border-stone-200 hover:border-gold hover:text-ink;
    }
    .countdown-digit {
        @apply bg-ink text-white font-mono font-bold text-2xl w-14 h-14 rounded-xl flex items-center justify-center shadow-md;
    }
    .qris-box {
        background: #ffffff;
        border: 2px solid #E5E7EB;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')

@php
    $payAmount = ($booking->payment_option === 'dp' && $booking->paid_amount == 0)
        ? (int) ceil($booking->total_price / 2)
        : (int) ($booking->remaining_balance > 0 ? $booking->remaining_balance : $booking->total_price);
    $payKind = ($booking->payment_option === 'dp' && $booking->paid_amount == 0)
        ? 'dp'
        : ($booking->remaining_balance > 0 && $booking->paid_amount > 0 ? 'remaining' : 'full');
@endphp

<div class="min-h-screen bg-cream py-8 sm:py-12"
     x-data="paymentPage()"
     x-init="initPayment()">

    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- ── TOP HEADER ── --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold/10 border border-gold/30 mb-3">
                <i class="fa-solid fa-shield-halved text-gold text-xs"></i>
                <span class="text-xs font-bold text-gold uppercase tracking-wider">Gateway Pembayaran Resmi Terenkripsi</span>
            </div>
            <h1 class="font-serif font-bold text-2xl sm:text-4xl text-ink mb-2">Selesaikan Pembayaran Anda</h1>
            <p class="text-xs sm:text-sm text-ink-muted">
                Kode Booking: <span class="font-mono font-bold text-gold bg-gold/10 px-2 py-0.5 rounded-md">{{ $booking->booking_code }}</span>
                · SnapSpace Studio Klaten
            </p>
        </div>

        {{-- ── 1. EXPIRED STATE ── --}}
        @if($booking->status === 'expired')
            <div class="bg-white rounded-3xl border border-danger/20 p-8 sm:p-12 text-center shadow-card max-w-lg mx-auto">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-danger/10 border border-danger/20 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-clock-rotate-left text-danger text-2xl"></i>
                </div>
                <h2 class="font-serif font-bold text-2xl text-ink mb-2">Batas Waktu Pembayaran Berakhir</h2>
                <p class="text-xs sm:text-sm text-ink-muted mb-6 leading-relaxed">
                    Sistem otomatis melepaskan slot jadwal karena melewati batas waktu pembayaran. Silakan pilih jadwal sesi baru yang tersedia.
                </p>
                <a href="{{ route('booking.wizard') }}" class="btn-gold btn-bounce inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl text-white font-bold text-sm shadow-warm">
                    <i class="fa-solid fa-rotate-right"></i> Buat Pesanan Baru
                </a>
            </div>

        {{-- ── 2. ALREADY PAID STATE ── --}}
        @elseif(in_array($booking->status, ['paid', 'dp_paid', 'checked_in', 'completed', 'delivered']))
            <div class="bg-white rounded-3xl border border-success/30 p-8 sm:p-12 text-center shadow-card max-w-xl mx-auto"
                 x-data="{ showConfetti: true }"
                 x-init="if (typeof confetti !== 'undefined') {
                     confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 }, colors: ['#C8A27A', '#2F4F4F', '#2E9E6B', '#E5A43B'] });
                 }">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-success/10 border border-success/20 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-circle-check text-success text-4xl animate-bounce"></i>
                </div>
                <div class="inline-block px-3 py-1 bg-success/10 text-success rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                    Transaksi Terverifikasi
                </div>
                <h2 class="font-serif font-bold text-2xl sm:text-3xl text-ink mb-2">Pembayaran Berhasil!</h2>
                <p class="text-xs sm:text-sm text-ink-muted mb-4 max-w-md mx-auto">
                    Terima kasih, tagihan Anda telah lunas diverifikasi secara otomatis oleh sistem SnapSpace Studio.
                </p>

                <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200 text-left text-xs space-y-2 mb-6 max-w-md mx-auto">
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Kode Booking:</span>
                        <span class="font-mono font-bold text-ink">{{ $booking->booking_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Status Transaksi:</span>
                        <span class="font-bold text-success uppercase">{{ $booking->status === 'dp_paid' ? 'DP 50% Diterima' : 'Lunas (100%)' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Jumlah Telah Dibayar:</span>
                        <span class="font-mono font-bold text-ink">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->remaining_balance > 0)
                        <div class="flex justify-between text-rose-600 font-semibold pt-1 border-t border-stone-200">
                            <span>Sisa Pembayaran:</span>
                            <span class="font-mono">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Action Buttons: Invoice & Ticket --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('booking.invoice', $booking->booking_code) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-ink text-white font-bold text-sm hover:bg-ink-soft transition-all shadow-md">
                        <i class="fa-solid fa-file-invoice-dollar text-gold"></i> Cetak / Unduh Nota Resmi
                    </a>
                    <a href="{{ route('my-bookings.show', $booking->booking_code) }}"
                       class="btn-gold btn-bounce inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl text-white font-bold text-sm shadow-warm">
                        <i class="fa-solid fa-qrcode"></i> Buka E-Tiket QR
                    </a>
                </div>
            </div>

        {{-- ── 3. PENDING PAYMENT STATE (THE CORE EXPERIENCE) ── --}}
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- ══ LEFT COLUMN: ORDER SUMMARY & COUNTDOWN ══ --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Order Breakdown Card --}}
                    <div class="bg-white rounded-3xl border border-stone-200 shadow-card p-6">
                        <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-4">
                            <div>
                                <h3 class="font-serif font-bold text-lg text-ink">Rincian Pesanan</h3>
                                <p class="text-xs text-ink-muted">Konfirmasi paket & jadwal studio</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                                Menunggu Pembayaran
                            </span>
                        </div>

                        {{-- Metadata List --}}
                        <div class="space-y-3 text-xs sm:text-sm">
                            <div class="flex items-center justify-between py-1.5 border-b border-stone-100">
                                <span class="flex items-center gap-2 text-ink-muted">
                                    <i class="fa-solid fa-door-open text-gold w-4 text-center"></i> Ruangan Studio
                                </span>
                                <span class="font-bold text-ink">{{ $booking->room->name }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-stone-100">
                                <span class="flex items-center gap-2 text-ink-muted">
                                    <i class="fa-solid fa-box-open text-gold w-4 text-center"></i> Paket Foto
                                </span>
                                <span class="font-bold text-ink">{{ $booking->package->name }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-stone-100">
                                <span class="flex items-center gap-2 text-ink-muted">
                                    <i class="fa-solid fa-calendar-day text-gold w-4 text-center"></i> Tanggal
                                </span>
                                <span class="font-semibold text-ink">{{ $booking->booking_date->translatedFormat('l, d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-stone-100">
                                <span class="flex items-center gap-2 text-ink-muted">
                                    <i class="fa-solid fa-clock text-gold w-4 text-center"></i> Jam Sesi
                                </span>
                                <span class="font-mono font-bold text-gold">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-stone-100">
                                <span class="flex items-center gap-2 text-ink-muted">
                                    <i class="fa-solid fa-users text-gold w-4 text-center"></i> Jumlah Orang
                                </span>
                                <span class="font-semibold text-ink">{{ $booking->people_count }} orang</span>
                            </div>
                        </div>

                        {{-- Add-ons Breakdown if any --}}
                        @if($booking->bookingAddons->isNotEmpty())
                            <div class="mt-4 pt-3 border-t border-stone-100">
                                <p class="text-[11px] font-bold text-ink-muted uppercase tracking-wider mb-2">Item Tambahan (Add-Ons):</p>
                                <div class="space-y-1.5 text-xs">
                                    @foreach($booking->bookingAddons as $ba)
                                        <div class="flex justify-between py-1">
                                            <span class="text-ink-muted">{{ $ba->addon->name }} ×{{ $ba->qty }}</span>
                                            <span class="font-mono text-ink font-medium">Rp {{ number_format($ba->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Price Calculations --}}
                        <div class="mt-4 pt-4 border-t-2 border-stone-200 space-y-2 text-xs">
                            <div class="flex justify-between text-ink-muted">
                                <span>Subtotal Paket & Tambahan</span>
                                <span class="font-mono text-ink">Rp {{ number_format($booking->subtotal + $booking->addons_total, 0, ',', '.') }}</span>
                            </div>

                            @if($booking->markup_amount > 0)
                                <div class="flex justify-between text-ink-muted">
                                    <span>Penyesuaian Weekend / Surcharge</span>
                                    <span class="font-mono text-amber-700">+Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            {{-- REALISTIC PROMO DISCOUNT HIGHLIGHT --}}
                            @if($booking->discount > 0)
                                <div class="flex justify-between items-center bg-emerald-50 text-emerald-800 px-3 py-2 rounded-xl border border-emerald-200">
                                    <span class="font-semibold flex items-center gap-1.5">
                                        <i class="fa-solid fa-tags text-xs"></i>
                                        <span>Potongan Diskon Promo {{ $booking->promo_code ? '('.$booking->promo_code.')' : '' }}</span>
                                    </span>
                                    <span class="font-mono font-bold text-sm">-Rp {{ number_format($booking->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-sm font-bold pt-2 border-t border-stone-100">
                                <span class="text-ink">Total Tagihan</span>
                                <span class="font-mono text-xl text-ink">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </div>

                            {{-- If DP mode --}}
                            @if($booking->payment_option === 'dp' && $booking->paid_amount == 0)
                                <div class="bg-gold/10 rounded-2xl p-3.5 border border-gold/30 mt-2">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-xs font-bold text-gold-dark block">Tagihan Pembayaran DP 50%</span>
                                            <span class="text-[11px] text-ink-muted">Sisa Rp {{ number_format($booking->total_price - $payAmount, 0, ',', '.') }} dilunasi di studio</span>
                                        </div>
                                        <span class="font-mono font-black text-lg text-gold-dark">
                                            Rp {{ number_format($payAmount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @elseif($booking->remaining_balance > 0 && $booking->paid_amount > 0)
                                <div class="bg-amber-50 rounded-2xl p-3.5 border border-amber-300 mt-2">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-xs font-bold text-amber-800 block">Pelunasan Sisa Tagihan</span>
                                            <span class="text-[11px] text-ink-muted">Sudah dibayar DP: Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <span class="font-mono font-black text-lg text-amber-800">
                                            Rp {{ number_format($payAmount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Countdown Timer Card --}}
                    <div class="bg-white rounded-3xl border border-stone-200 shadow-card p-6 text-center">
                        <div class="flex items-center justify-center gap-2 mb-3 text-amber-600">
                            <i class="fa-solid fa-hourglass-half text-sm animate-pulse"></i>
                            <span class="text-xs font-bold uppercase tracking-wider">Sisa Waktu Pembayaran</span>
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <div class="countdown-digit" x-text="String(minutes).padStart(2, '0')">00</div>
                            <span class="text-2xl text-ink font-bold">:</span>
                            <div class="countdown-digit" x-text="String(seconds).padStart(2, '0')">00</div>
                        </div>
                        <p class="text-xs text-ink-muted mt-3">
                            Slot booking terkunci khusus untuk Anda selama hitungan mundur berlangsung.
                        </p>
                    </div>

                    {{-- Security & Trust Badges --}}
                    <div class="bg-white rounded-2xl border border-stone-200 p-4 text-xs text-ink-muted space-y-2">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span class="font-medium text-ink">Standar Keamanan Transaksi ASPI & BI</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-gold"></i>
                            <span>Verifikasi pembayaran otomatis seketika dalam &lt; 5 detik</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-teal"></i>
                            <span>Bukti pembayaran & Nota resmi langsung diterbitkan</span>
                        </div>
                    </div>

                </div>

                {{-- ══ RIGHT COLUMN: AUTHENTIC PAYMENT INTERFACE ══ --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Main Payment Box --}}
                    <div class="bg-white rounded-3xl border border-stone-200 shadow-card p-6 sm:p-8">

                        <h3 class="font-serif font-bold text-xl text-ink mb-1">Pilih Saluran Pembayaran</h3>
                        <p class="text-xs text-ink-muted mb-6">Pilih metode yang paling nyaman untuk Anda selesaikan sekarang.</p>

                        {{-- Payment Method Tabs --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6">
                            <button type="button"
                                    class="payment-tab-btn"
                                    :class="activeTab === 'qris' ? 'active' : ''"
                                    @click="selectTab('qris')">
                                <i class="fa-solid fa-qrcode text-base"></i>
                                <span>QRIS</span>
                            </button>
                            <button type="button"
                                    class="payment-tab-btn"
                                    :class="activeTab === 'va' ? 'active' : ''"
                                    @click="selectTab('va')">
                                <i class="fa-solid fa-building-columns text-base"></i>
                                <span>Virtual Acc.</span>
                            </button>
                            <button type="button"
                                    class="payment-tab-btn"
                                    :class="activeTab === 'ewallet' ? 'active' : ''"
                                    @click="selectTab('ewallet')">
                                <i class="fa-solid fa-wallet text-base"></i>
                                <span>E-Wallet</span>
                            </button>
                            <button type="button"
                                    class="payment-tab-btn"
                                    :class="activeTab === 'card' ? 'active' : ''"
                                    @click="selectTab('card')">
                                <i class="fa-solid fa-credit-card text-base"></i>
                                <span>Kartu</span>
                            </button>
                        </div>

                        {{-- ── TAB 1: QRIS SCREEN (REAL ASPI STANDARD & SANDBOX SIMULATOR) ── --}}
                        <div x-show="activeTab === 'qris'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
                            <div class="qris-box p-6 mb-6">
                                <!-- Sandbox Notice -->
                                <div class="mb-5 p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                                    <i class="fa-solid fa-flask text-amber-600 mt-0.5 text-base shrink-0"></i>
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-xs block">Mode Sandbox Simulator (Demo Pengujian)</span>
                                        <p class="text-[11px] text-amber-800 leading-relaxed">
                                            Sistem pembayaran beroperasi dalam mode pengujian. Anda tidak perlu mentransfer saldo rekening sungguhan. Gunakan tombol <strong>"Simulasikan Pembayaran Sukses"</strong> di bawah untuk menyelesaikan pesanan seketika.
                                        </p>
                                    </div>
                                </div>

                                <!-- QRIS Header Bar -->
                                <div class="flex items-center justify-between pb-4 border-b border-stone-200 mb-5">
                                    <div class="flex items-center gap-2">
                                        <!-- QRIS Official-Style Logo Text -->
                                        <div class="bg-red-600 text-white font-black text-sm px-2.5 py-1 rounded tracking-widest font-mono">
                                            QRIS
                                        </div>
                                        <span class="text-[10px] font-bold text-ink-muted uppercase tracking-wider hidden sm:inline">
                                            Standar Pembayaran Nasional
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-stone-500 font-mono block">NMID: ID1020240988771</span>
                                        <span class="text-xs font-bold text-ink block">SNAPSPACE KLATEN</span>
                                    </div>
                                </div>

                                <!-- Dynamic QR Code Display -->
                                <div class="text-center py-2">
                                    <div class="inline-block p-4 bg-white rounded-2xl border-2 border-stone-300 shadow-inner relative group">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode(route('booking.pay', $booking->booking_code)) }}"
                                             alt="Kode Pembayaran QRIS SnapSpace"
                                             class="w-48 h-48 sm:w-56 sm:h-56 mx-auto object-contain">
                                        <div class="text-[10px] font-mono text-stone-500 mt-2 font-semibold">
                                            REF: {{ $booking->booking_code }} · SCAN DENGAN KAMERA HP UNTUK BAYAR
                                        </div>
                                    </div>

                                    <!-- Exact Nominal to Pay -->
                                    <div class="mt-4 bg-cream/70 rounded-2xl p-4 border border-cream-dark max-w-sm mx-auto">
                                        <p class="text-[11px] font-semibold text-ink-muted uppercase tracking-wider mb-1">
                                            {{ $booking->payment_option === 'dp' && $booking->paid_amount == 0 ? 'Nominal Pembayaran DP 50%' : 'Nominal Yang Harus Dibayar' }}
                                        </p>
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="font-mono font-bold text-2xl text-ink">
                                                Rp {{ number_format($payAmount, 0, ',', '.') }}
                                            </span>
                                            <button type="button"
                                                    @click="copyText('{{ $payAmount }}', 'Nominal berhasil disalin!')"
                                                    class="p-2 text-ink-muted hover:text-gold hover:bg-white rounded-lg transition-colors"
                                                    title="Salin nominal">
                                                <i class="fa-regular fa-copy text-sm"></i>
                                            </button>
                                        </div>
                                        @if($booking->payment_option === 'dp' && $booking->paid_amount == 0)
                                            <p class="text-[11px] text-amber-700 font-medium mt-1">
                                                Sisa Rp {{ number_format($booking->total_price - $payAmount, 0, ',', '.') }} dilunasi sebelum sesi foto.
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Direct 1-Click Simulation Button for QRIS -->
                                    <div class="max-w-sm mx-auto mt-4">
                                        <button type="button"
                                                @click="triggerGatewayProcess()"
                                                :disabled="submitting || !online"
                                                class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md flex items-center justify-center gap-2 transition-all cursor-pointer">
                                            <i class="fa-solid fa-bolt"></i>
                                            <span>⚡ Simulasikan Pembayaran {{ $booking->payment_option === 'dp' && $booking->paid_amount == 0 ? 'DP 50%' : 'Lunas' }} Sukses</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Supported Apps Logos -->
                                <div class="mt-6 pt-5 border-t border-stone-100">
                                    <p class="text-[10px] font-bold text-center text-ink-muted uppercase tracking-wider mb-3">
                                        Dapat dipindai dari kamera ponsel / aplikasi e-wallet & m-Banking:
                                    </p>
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        @foreach(['BCA Mobile', 'Livin Mandiri', 'BRImo', 'BNI Mobile', 'GoPay', 'OVO', 'DANA', 'ShopeePay', 'LinkAja'] as $appName)
                                            <span class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 text-[11px] font-semibold rounded-lg transition-colors">
                                                {{ $appName }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Step by step instructions -->
                                <div class="mt-6 bg-stone-50 rounded-2xl p-4 border border-stone-200 text-xs text-ink-muted space-y-1.5">
                                    <p class="font-bold text-ink mb-1">Petunjuk Pembayaran:</p>
                                    <ol class="list-decimal list-inside space-y-1 leading-relaxed">
                                        <li>Buka kamera ponsel Anda dan arahkan ke kode QR di atas untuk membuka halaman pembayaran di HP.</li>
                                        <li>Atau langsung klik tombol <strong>"⚡ Simulasikan Pembayaran Sukses"</strong> untuk pengujian instan.</li>
                                        <li>Setelah transaksi diverifikasi, E-Tiket sesi foto ber-QR Code resmi akan langsung diterbitkan otomatis.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        {{-- ── TAB 2: VIRTUAL ACCOUNT SCREEN ── --}}
                        <div x-show="activeTab === 'va'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
                            <div class="space-y-4 mb-6">
                                <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Pilih Bank Virtual Account:</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @php
                                        $vaList = [
                                            ['bca', 'Bank BCA', '82708' . substr(preg_replace('/\D/', '', $booking->user->phone ?? '08123456789'), -8), '#003D79'],
                                            ['mandiri', 'Bank Mandiri', '89808' . substr(preg_replace('/\D/', '', $booking->user->phone ?? '08123456789'), -8), '#003366'],
                                            ['bni', 'Bank BNI', '8808' . substr(preg_replace('/\D/', '', $booking->user->phone ?? '08123456789'), -8), '#F15A24'],
                                            ['bri', 'Bank BRI', '12808' . substr(preg_replace('/\D/', '', $booking->user->phone ?? '08123456789'), -8), '#003B6F'],
                                        ];
                                    @endphp
                                    @foreach($vaList as [$code, $bName, $vaNum, $color])
                                        <div @click="selectedVa = '{{ $code }}'; selectedVaNumber = '{{ $vaNum }}'; selectedVaBank = '{{ $bName }}'"
                                             class="p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex items-center justify-between"
                                             :class="selectedVa === '{{ $code }}' ? 'border-gold bg-gold/5 shadow-sm' : 'border-stone-200 bg-white hover:border-gold/50'">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white text-xs" style="background: {{ $color }}">
                                                    {{ strtoupper($code) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-sm text-ink">{{ $bName }}</p>
                                                    <p class="text-[11px] text-ink-muted">Verifikasi Otomatis</p>
                                                </div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                 :class="selectedVa === '{{ $code }}' ? 'border-gold bg-gold' : 'border-stone-300'">
                                                <div class="w-2 h-2 rounded-full bg-white" x-show="selectedVa === '{{ $code }}'"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Virtual Account Detail Card -->
                                <div class="bg-stone-50 rounded-2xl p-5 border border-stone-200 mt-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-stone-200">
                                        <div>
                                            <span class="text-xs text-ink-muted block">Nomor Virtual Account <span class="font-bold text-ink" x-text="selectedVaBank"></span>:</span>
                                            <span class="font-mono font-bold text-xl sm:text-2xl text-ink tracking-wider" x-text="selectedVaNumber"></span>
                                        </div>
                                        <button type="button"
                                                @click="copyText(selectedVaNumber, 'Nomor Virtual Account berhasil disalin!')"
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-stone-300 rounded-xl text-xs font-bold text-ink hover:bg-stone-100 transition-colors shadow-xs">
                                            <i class="fa-regular fa-copy text-gold"></i> Salin Nomor VA
                                        </button>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3">
                                        <div>
                                            <span class="text-xs text-ink-muted block">Total Transfer:</span>
                                            <span class="font-mono font-bold text-lg text-ink">Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <button type="button"
                                                @click="copyText('{{ $payAmount }}', 'Jumlah transfer berhasil disalin!')"
                                                class="text-xs text-gold font-semibold hover:underline">
                                            Salin Jumlah
                                        </button>
                                    </div>
                                </div>

                                <!-- Accordion Guides for VA -->
                                <div class="text-xs border border-stone-200 rounded-2xl divide-y divide-stone-200 bg-white overflow-hidden mt-3"
                                     x-data="{ openGuide: 'mbanking' }">
                                    <div class="p-3 cursor-pointer bg-stone-50 flex items-center justify-between font-bold text-ink"
                                         @click="openGuide = (openGuide === 'mbanking' ? '' : 'mbanking')">
                                        <span>Petunjuk Transfer Mobile Banking</span>
                                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="openGuide === 'mbanking' ? 'rotate-180' : ''"></i>
                                    </div>
                                    <div x-show="openGuide === 'mbanking'" class="p-4 text-ink-muted space-y-1.5 leading-relaxed bg-white">
                                        <p>1. Login ke aplikasi m-Banking Anda.</p>
                                        <p>2. Pilih menu <strong>Transfer &gt; Virtual Account</strong>.</p>
                                        <p>3. Masukkan nomor VA: <span class="font-mono font-bold text-ink" x-text="selectedVaNumber"></span>.</p>
                                        <p>4. Masukkan nominal pembayaran: <strong>Rp {{ number_format($payAmount, 0, ',', '.') }}</strong> (jika belum otomatis).</p>
                                        <p>5. Periksa detail transaksi dan masukkan PIN transaksi Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── TAB 3: E-WALLET SCREEN ── --}}
                        <div x-show="activeTab === 'ewallet'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
                            <div class="space-y-4 mb-6">
                                <p class="text-xs font-semibold text-ink-muted uppercase tracking-wider">Pilih Aplikasi E-Wallet:</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @php
                                        $wallets = [
                                            ['gopay', 'GoPay', '#00AED6', 'fa-wallet'],
                                            ['ovo', 'OVO', '#4C3494', 'fa-coins'],
                                            ['dana', 'DANA', '#108EE9', 'fa-money-bill-wave'],
                                            ['shopeepay', 'ShopeePay', '#EE4D2D', 'fa-bag-shopping'],
                                        ];
                                    @endphp
                                    @foreach($wallets as [$wSlug, $wName, $wColor, $wIcon])
                                        <div @click="selectedWallet = '{{ $wSlug }}'"
                                             class="p-4 rounded-2xl border-2 cursor-pointer text-center transition-all duration-200"
                                             :class="selectedWallet === '{{ $wSlug }}' ? 'border-gold bg-gold/5 shadow-sm' : 'border-stone-200 bg-white hover:border-gold/50'">
                                            <div class="w-10 h-10 mx-auto rounded-xl flex items-center justify-center text-white mb-2 shadow-xs" style="background: {{ $wColor }}">
                                                <i class="fa-solid {{ $wIcon }}"></i>
                                            </div>
                                            <p class="font-bold text-xs text-ink">{{ $wName }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200 text-xs text-ink-muted leading-relaxed">
                                    <p class="font-semibold text-ink mb-1">
                                        <i class="fa-solid fa-mobile-screen-button text-gold mr-1"></i> Notifikasi Pembayaran Langsung
                                    </p>
                                    <p>
                                        Setelah menekan tombol di bawah, Anda akan diarahkan langsung atau menerima push notifikasi di aplikasi E-Wallet terdaftar Anda untuk menyelesaikan pembayaran sebesar <strong>Rp {{ number_format($payAmount, 0, ',', '.') }}</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- ── TAB 4: CREDIT / DEBIT CARD SCREEN ── --}}
                        <div x-show="activeTab === 'card'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
                            <div class="space-y-4 mb-6">
                                <!-- Realistic Card Mockup -->
                                <div class="bg-gradient-to-tr from-stone-900 via-stone-800 to-stone-700 text-white rounded-2xl p-6 shadow-xl max-w-sm mx-auto relative overflow-hidden">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-10 h-8 rounded-md bg-amber-400/80 border border-amber-300"></div>
                                        <div class="flex gap-1.5 text-stone-300">
                                            <i class="fa-brands fa-cc-visa text-2xl"></i>
                                            <i class="fa-brands fa-cc-mastercard text-2xl"></i>
                                            <i class="fa-brands fa-cc-jcb text-2xl"></i>
                                        </div>
                                    </div>
                                    <div class="font-mono text-base tracking-widest mb-4">
                                        •••• •••• •••• 4242
                                    </div>
                                    <div class="flex justify-between text-[11px] text-stone-300 uppercase">
                                        <div>
                                            <span class="block text-[9px] text-stone-400">Nama Pemegang</span>
                                            <span class="font-semibold">{{ $booking->user->name }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[9px] text-stone-400">Kadaluwarsa</span>
                                            <span class="font-mono font-semibold">12/28</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div class="col-span-2">
                                        <label class="block font-semibold text-ink mb-1">Nomor Kartu (Simulasi Sandbox)</label>
                                        <input type="text" readonly value="4000 0012 3456 4242"
                                               class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-stone-50 font-mono text-xs text-ink focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-ink mb-1">Masa Berlaku</label>
                                        <input type="text" readonly value="12/28"
                                               class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-stone-50 font-mono text-xs text-ink text-center focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-ink mb-1">CVV / CVV2</label>
                                        <input type="password" readonly value="123"
                                               class="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 bg-stone-50 font-mono text-xs text-ink text-center focus:outline-none">
                                    </div>
                                </div>
                                <p class="text-[11px] text-ink-muted text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-lock text-success text-xs"></i> Didukung oleh 3D Secure OTP & PCI-DSS Compliant Gateway
                                </p>
                            </div>
                        </div>

                        {{-- ── PAYMENT SUBMISSION FORM (REAL SIMULATION ENGINE) ── --}}
                        <form id="paymentForm" method="POST" action="{{ route('booking.pay-simulated', $booking->booking_code) }}"
                              @submit.prevent="triggerGatewayProcess($event)">
                            @csrf
                            <input type="hidden" name="payment_kind" value="{{ $payKind }}">
                            <input type="hidden" name="payment_method" :value="resolvedPaymentMethod()">

                            <!-- Main Pay Button -->
                            <button type="submit"
                                    :disabled="submitting || !online"
                                    class="btn-gold btn-bounce w-full py-4 rounded-2xl text-white font-bold text-base shadow-warm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Konfirmasi & Selesaikan Pembayaran (Rp {{ number_format($payAmount, 0, ',', '.') }})</span>
                            </button>
                        </form>

                        {{-- Edge case: Manual Webhook / Status Checker --}}
                        <div class="mt-5 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-cream/40 p-3.5 rounded-2xl text-xs">
                            <div class="text-ink-muted text-center sm:text-left">
                                <span class="font-bold text-ink block">Sudah transfer / scan QR di aplikasi Anda?</span>
                                <span>Klik jika status belum otomatis berganti dalam beberapa detik.</span>
                            </div>
                            <button type="button" @click="checkStatusManual()" :disabled="checkingStatus"
                                    class="px-4 py-2 rounded-xl bg-ink text-white font-bold hover:bg-ink-soft transition-all flex items-center gap-2 shadow-xs disabled:opacity-50 flex-shrink-0 text-xs">
                                <i class="fa-solid fa-rotate text-xs" :class="checkingStatus ? 'animate-spin' : ''"></i>
                                <span x-text="checkingStatus ? 'Mengecek...' : 'Cek Status Gateway'"></span>
                            </button>
                        </div>
                        <p x-show="checkMessage" x-text="checkMessage" class="text-xs text-center font-medium mt-2" :class="checkSuccess ? 'text-success font-bold' : 'text-ink-muted'"></p>

                    </div>

                    {{-- Cross-Device QR Container (Pay on Phone) --}}
                    <div class="p-4 rounded-2xl border border-gold/30 bg-gold/5 flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-white p-1 border border-gold/30 flex-shrink-0 flex items-center justify-center shadow-xs">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(url()->current()) }}"
                                 alt="Scan untuk Bayar di HP" class="w-full h-full object-contain">
                        </div>
                        <div class="text-xs text-center sm:text-left flex-1">
                            <span class="font-bold text-ink block flex items-center justify-center sm:justify-start gap-1.5 text-gold">
                                <i class="fa-solid fa-mobile-screen-button"></i> Buka Pembayaran di Ponsel Anda
                            </span>
                            <span class="text-ink-muted mt-0.5 block leading-relaxed">
                                Pindai QR ini menggunakan kamera ponsel untuk membuka halaman ini di browser smartphone dan membayar langsung melalui aplikasi m-Banking atau E-Wallet di HP.
                            </span>
                        </div>
                    </div>

                </div>

            </div>
        @endif

    </div>

    {{-- ══ AUTHENTIC PAYMENT GATEWAY PROCESSING MODAL ══ --}}
    <div x-show="showProcessingModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="bg-white rounded-3xl max-w-md w-full p-8 text-center shadow-2xl border border-stone-200 relative overflow-hidden">
            <!-- Modal Accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-gold via-amber-400 to-success"></div>

            <template x-if="processingStep === 1">
                <div class="space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gold/10 flex items-center justify-center text-gold text-2xl">
                        <i class="fa-solid fa-circle-notch animate-spin"></i>
                    </div>
                    <h4 class="font-serif font-bold text-xl text-ink">Menghubungkan ke Gateway...</h4>
                    <p class="text-xs text-ink-muted">Mengirim sinyal verifikasi transaksi ke sistem perbankan nasional.</p>
                </div>
            </template>

            <template x-if="processingStep === 2">
                <div class="space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-teal/10 flex items-center justify-center text-teal text-2xl">
                        <i class="fa-solid fa-network-wired animate-pulse"></i>
                    </div>
                    <h4 class="font-serif font-bold text-xl text-ink">Memverifikasi Pembayaran...</h4>
                    <p class="text-xs text-ink-muted">Mencocokkan mutasi saldo QRIS / Virtual Account untuk <span class="font-mono font-bold">{{ $booking->booking_code }}</span>.</p>
                </div>
            </template>

            <template x-if="processingStep === 3">
                <div class="space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-success/15 flex items-center justify-center text-success text-3xl">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h4 class="font-serif font-bold text-xl text-ink">Pembayaran Terverifikasi!</h4>
                    <p class="text-xs text-ink-muted">Menerbitkan Bukti Pembayaran Resmi & E-Tiket QR Anda...</p>
                </div>
            </template>

            <!-- Progress Bar -->
            <div class="w-full bg-stone-100 rounded-full h-2 mt-6 overflow-hidden">
                <div class="bg-gold h-full transition-all duration-500 rounded-full"
                     :style="'width: ' + (processingStep === 1 ? '33%' : (processingStep === 2 ? '75%' : '100%'))"></div>
            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION --}}
    <div x-show="toastShow"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 right-6 z-50 bg-ink text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 border border-stone-700 text-xs font-semibold">
        <i class="fa-solid fa-circle-check text-success text-sm"></i>
        <span x-text="toastMessage"></span>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
function paymentPage() {
    return {
        activeTab: 'qris',
        selectedVa: 'bca',
        selectedVaNumber: '82708{{ substr(preg_replace('/\D/', '', $booking->user->phone ?? '08123456789'), -8) }}',
        selectedVaBank: 'Bank BCA',
        selectedWallet: 'gopay',
        submitting: false,
        showProcessingModal: false,
        processingStep: 1,
        checkingStatus: false,
        checkMessage: '',
        checkSuccess: false,
        minutes: 0,
        seconds: 0,
        interval: null,
        pollInterval: null,
        online: navigator.onLine,
        toastShow: false,
        toastMessage: '',

        initPayment() {
            window.addEventListener('online', () => { this.online = true; });
            window.addEventListener('offline', () => { this.online = false; });
            this.startCountdown();
        },

        selectTab(tab) {
            this.activeTab = tab;
        },

        resolvedPaymentMethod() {
            if (this.activeTab === 'qris') return 'qris';
            if (this.activeTab === 'va') return 'va_' + this.selectedVa;
            if (this.activeTab === 'ewallet') return this.selectedWallet;
            if (this.activeTab === 'card') return 'credit_card';
            return 'qris';
        },

        copyText(text, msg) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
                this.showToast(msg);
            }
        },

        showToast(msg) {
            this.toastMessage = msg;
            this.toastShow = true;
            setTimeout(() => { this.toastShow = false; }, 3000);
        },

        startCountdown() {
            @if($booking->expires_at && $booking->status === 'pending')
                const expiresAt = new Date('{{ $booking->expires_at->toISOString() }}').getTime();
                this.interval = setInterval(() => {
                    const now = Date.now();
                    const diff = Math.max(0, Math.floor((expiresAt - now) / 1000));
                    this.minutes = Math.floor(diff / 60);
                    this.seconds = diff % 60;
                    if (diff <= 0) {
                        clearInterval(this.interval);
                        clearInterval(this.pollInterval);
                        window.location.reload();
                    }
                }, 1000);

                // Auto-poll status periodically
                this.pollInterval = setInterval(() => {
                    if (this.online && !this.submitting) {
                        this.silentCheckStatus();
                    }
                }, 8000);
            @else
                this.minutes = 0;
                this.seconds = 0;
            @endif
        },

        async silentCheckStatus() {
            try {
                const res = await fetch('{{ route('booking.check-payment-status', $booking->booking_code) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.is_paid && data.redirect_url) {
                    window.location.href = data.redirect_url;
                }
            } catch(e) {}
        },

        async checkStatusManual() {
            if (this.checkingStatus) return;
            this.checkingStatus = true;
            this.checkMessage = '';
            try {
                const res = await fetch('{{ route('booking.check-payment-status', $booking->booking_code) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                this.checkMessage = data.message || 'Pemeriksaan status selesai.';
                if (data.is_paid && data.redirect_url) {
                    this.checkSuccess = true;
                    setTimeout(() => { window.location.href = data.redirect_url; }, 1000);
                } else {
                    this.checkSuccess = false;
                }
            } catch(e) {
                this.checkMessage = 'Gagal memeriksa ke gateway. Mohon periksa koneksi internet Anda.';
            } finally {
                this.checkingStatus = false;
            }
        },

        triggerGatewayProcess() {
            if (this.submitting || !this.online) return;
            this.submitting = true;
            this.showProcessingModal = true;
            this.processingStep = 1;

            setTimeout(() => {
                this.processingStep = 2;
                setTimeout(() => {
                    this.processingStep = 3;
                    setTimeout(() => {
                        const form = document.getElementById('paymentForm');
                        if (form) {
                            form.submit();
                        }
                    }, 600);
                }, 900);
            }, 800);
        }
    };
}
</script>
@endpush
