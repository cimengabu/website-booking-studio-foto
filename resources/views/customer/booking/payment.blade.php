@extends('layouts.public')

@section('title', 'Pembayaran Booking ' . $booking->booking_code . ' — SnapSpace Studio')
@section('meta_description', 'Selesaikan pembayaran booking SnapSpace Studio. Bayar via QRIS, GoPay, OVO, DANA, Virtual Account, atau kartu kredit.')

@push('styles')
<style>
    .payment-method-card { @apply p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200; }
    .payment-method-card.active  { @apply border-gold bg-gold/5 shadow-warm; }
    .payment-method-card:not(.active) { @apply border-cream-dark bg-cream hover:border-gold/50; }
    .countdown-digit { @apply bg-ink text-white font-serif font-bold text-2xl w-14 h-14 rounded-xl flex items-center justify-center shadow-card; }
</style>
@endpush

@section('content')

<div class="min-h-screen bg-cream py-8"
     x-data="paymentPage()"
     x-init="startCountdown()">

    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        {{-- ── HEADER ── --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gold/10 border border-gold/30 mb-4">
                <i class="fa-solid fa-shield-halved text-gold text-sm"></i>
                <span class="text-xs font-bold text-gold uppercase tracking-wide">Pembayaran Aman</span>
            </div>
            <h1 class="font-serif font-bold text-3xl text-ink mb-2">Selesaikan Pembayaran</h1>
            <p class="text-sm text-ink-muted">Kode: <span class="font-mono font-bold text-gold">{{ $booking->booking_code }}</span></p>
        </div>

        {{-- ── EXPIRED ── --}}
        @if($booking->status === 'expired')
            <div class="bg-white rounded-3xl border border-danger/20 p-10 text-center shadow-card max-w-lg mx-auto">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-danger/10 border border-danger/20 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-clock-rotate-left text-danger text-2xl"></i>
                </div>
                <h2 class="font-serif font-bold text-2xl text-ink mb-3">Waktu Pembayaran Berakhir</h2>
                <p class="text-sm text-ink-muted mb-6 max-w-sm mx-auto">Slot telah otomatis dilepas karena melewati batas 60 menit. Silakan buat booking baru.</p>
                <a href="{{ route('booking.wizard') }}" class="btn-gold btn-bounce inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl text-white font-bold shadow-warm">
                    <i class="fa-solid fa-rotate-right"></i> Booking Ulang
                </a>
            </div>

        {{-- ── ALREADY PAID ── --}}
        @elseif(in_array($booking->status, ['paid', 'dp_paid', 'checked_in', 'completed']))
            <div class="bg-white rounded-3xl border border-success/20 p-10 text-center shadow-card max-w-lg mx-auto"
                 x-data="{ showConfetti: true }"
                 x-init="if (showConfetti && typeof confetti !== 'undefined') {
                     confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 }, colors: ['#C8A27A', '#2F4F4F', '#2E9E6B', '#FFFFFF'] });
                 }">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-success/10 border border-success/20 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-circle-check text-success text-4xl animate-bounce"></i>
                </div>
                <h2 class="font-serif font-bold text-3xl text-ink mb-3">Pembayaran Dikonfirmasi!</h2>
                <p class="text-sm text-ink-muted mb-2">Status: <span class="font-bold text-success uppercase">{{ $booking->status === 'dp_paid' ? 'DP Diterima' : 'Lunas' }}</span></p>
                <p class="text-sm text-ink-muted mb-6">E-Tiket QR Anda sudah aktif dan siap digunakan.</p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('my-bookings.show', $booking->booking_code) }}"
                       class="btn-gold btn-bounce inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl text-white font-bold shadow-warm">
                        <i class="fa-solid fa-qrcode"></i> Lihat E-Tiket QR
                    </a>
                    <a href="{{ route('my-bookings.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl border border-cream-dark text-ink font-semibold hover:border-gold transition-colors">
                        <i class="fa-solid fa-list"></i> Pesanan Saya
                    </a>
                </div>
            </div>

        {{-- ── PENDING PAYMENT ── --}}
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- LEFT: Order Summary --}}
                <div class="lg:col-span-5 space-y-5">
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-6">
                        <div class="flex items-center justify-between pb-4 border-b border-cream-dark mb-4">
                            <h3 class="font-serif font-semibold text-lg text-ink">Rincian Pesanan</h3>
                            <span class="px-3 py-1 rounded-full bg-warning/15 text-warning text-xs font-bold">Menunggu Bayar</span>
                        </div>

                        <div class="space-y-3 text-sm">
                            @foreach([
                                ['fa-door-open', 'Ruangan', $booking->room->name],
                                ['fa-box-open', 'Paket', $booking->package->name],
                                ['fa-calendar', 'Tanggal', $booking->booking_date->translatedFormat('l, d M Y')],
                                ['fa-clock', 'Jam Sesi', substr($booking->start_time, 0, 5).' - '.substr($booking->end_time, 0, 5).' WIB'],
                                ['fa-users', 'Jumlah Orang', $booking->people_count.' orang'],
                            ] as [$icon, $label, $val])
                                <div class="flex items-center justify-between py-2 border-b border-cream-dark last:border-0">
                                    <span class="flex items-center gap-2 text-ink-muted">
                                        <i class="fa-solid fa-{{ $icon }} text-xs text-gold w-4 text-center"></i>
                                        {{ $label }}
                                    </span>
                                    <span class="font-semibold text-ink">{{ $val }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Add-ons --}}
                        @if($booking->bookingAddons->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-cream-dark">
                                <p class="text-xs font-bold text-ink-muted uppercase tracking-wide mb-2">Add-Ons:</p>
                                @foreach($booking->bookingAddons as $ba)
                                    <div class="flex justify-between text-sm py-1">
                                        <span class="text-ink-muted">{{ $ba->addon->name }} ×{{ $ba->quantity }}</span>
                                        <span class="text-ink font-medium">Rp {{ number_format($ba->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Price breakdown --}}
                        <div class="mt-4 pt-4 border-t-2 border-cream-dark space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-ink-muted">Subtotal paket</span>
                                <span class="text-ink">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                            </div>
                            @if($booking->addon_total > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-ink-muted">Add-ons</span>
                                    <span class="text-ink">Rp {{ number_format($booking->addon_total, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($booking->markup_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-ink-muted">Markup</span>
                                    <span class="text-warning">+Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($booking->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-ink-muted">Diskon</span>
                                    <span class="text-success">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-cream-dark">
                                <span class="text-ink">Total</span>
                                <span class="font-serif text-2xl text-gold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                            </div>
                            @if($booking->payment_option === 'dp' && $booking->paid_amount < $booking->total_price)
                                <div class="bg-gold/5 rounded-xl p-3 border border-gold/20">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-ink font-semibold">Bayar DP 50% sekarang</span>
                                        <span class="font-serif font-bold text-lg text-gold">
                                            Rp {{ number_format(ceil($booking->total_price / 2), 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Countdown Timer --}}
                    <div class="bg-white rounded-2xl border border-cream-dark shadow-card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fa-solid fa-hourglass-half text-warning text-sm"></i>
                            <p class="text-xs font-bold text-ink-muted uppercase tracking-wide">Batas Waktu Pembayaran</p>
                        </div>
                        <div class="flex items-center justify-center gap-3">
                            <div class="countdown-digit" x-text="String(minutes).padStart(2, '0')"></div>
                            <span class="text-2xl text-ink-muted font-bold">:</span>
                            <div class="countdown-digit" x-text="String(seconds).padStart(2, '0')"></div>
                        </div>
                        <p class="text-center text-xs text-ink-muted mt-3">Slot akan otomatis dilepas setelah waktu habis</p>
                    </div>
                </div>

                {{-- RIGHT: Payment Methods --}}
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <h3 class="font-serif font-semibold text-lg text-ink mb-6">Pilih Metode Pembayaran</h3>

                        <form method="POST" action="{{ route('booking.pay-simulated', $booking->booking_code) }}"
                              @submit.prevent="submitPayment($event)">
                            @csrf
                            <input type="hidden" name="payment_kind" value="{{ $booking->payment_option === 'dp' && $booking->paid_amount == 0 ? 'dp' : 'full' }}">
                            <input type="hidden" name="payment_method" :value="selectedMethod">

                            <div class="space-y-3">
                                {{-- QRIS --}}
                                <div class="payment-method-card" :class="selectedMethod === 'qris' ? 'active' : ''" @click="selectedMethod = 'qris'">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center">
                                                <i class="fa-solid fa-qrcode text-gold"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-ink">QRIS</p>
                                                <p class="text-xs text-ink-muted">Scan dari semua e-wallet & mobile banking</p>
                                            </div>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                             :class="selectedMethod === 'qris' ? 'border-gold bg-gold' : 'border-cream-dark'">
                                            <div class="w-2 h-2 rounded-full bg-white" x-show="selectedMethod === 'qris'"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- E-Wallet --}}
                                @foreach([
                                    ['gopay', 'GoPay', 'Deep link langsung ke app GoPay', '#00AED6'],
                                    ['ovo', 'OVO', 'Push notifikasi langsung ke app OVO', '#4C3494'],
                                    ['dana', 'DANA', 'Deep link langsung ke app DANA', '#108EE9'],
                                    ['shopeepay', 'ShopeePay', 'Deep link langsung ke Shopee', '#EE4D2D'],
                                ] as [$slug, $name, $desc, $color])
                                    <div class="payment-method-card" :class="selectedMethod === '{{ $slug }}' ? 'active' : ''" @click="selectedMethod = '{{ $slug }}'">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $color }}15;">
                                                    <i class="fa-solid fa-wallet text-sm" style="color: {{ $color }}"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm text-ink">{{ $name }}</p>
                                                    <p class="text-xs text-ink-muted">{{ $desc }}</p>
                                                </div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                 :class="selectedMethod === '{{ $slug }}' ? 'border-gold bg-gold' : 'border-cream-dark'">
                                                <div class="w-2 h-2 rounded-full bg-white" x-show="selectedMethod === '{{ $slug }}'"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Virtual Account --}}
                                <div class="pt-2">
                                    <p class="text-xs font-bold text-ink-muted uppercase tracking-wide mb-2">Virtual Account</p>
                                </div>
                                @foreach([
                                    ['va_bca', 'BCA Virtual Account', '#003D79'],
                                    ['va_bni', 'BNI Virtual Account', '#F15A24'],
                                    ['va_bri', 'BRI Virtual Account', '#003B6F'],
                                    ['va_mandiri', 'Mandiri Virtual Account', '#003366'],
                                ] as [$slug, $name, $color])
                                    <div class="payment-method-card" :class="selectedMethod === '{{ $slug }}' ? 'active' : ''" @click="selectedMethod = '{{ $slug }}'">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $color }}15;">
                                                    <i class="fa-solid fa-building-columns text-sm" style="color: {{ $color }}"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm text-ink">{{ $name }}</p>
                                                    <p class="text-xs text-ink-muted">Transfer via ATM / mobile banking / internet banking</p>
                                                </div>
                                            </div>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                 :class="selectedMethod === '{{ $slug }}' ? 'border-gold bg-gold' : 'border-cream-dark'">
                                                <div class="w-2 h-2 rounded-full bg-white" x-show="selectedMethod === '{{ $slug }}'"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Credit Card --}}
                                <div class="pt-2">
                                    <p class="text-xs font-bold text-ink-muted uppercase tracking-wide mb-2">Kartu</p>
                                </div>
                                <div class="payment-method-card" :class="selectedMethod === 'credit_card' ? 'active' : ''" @click="selectedMethod = 'credit_card'">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-ink/5 flex items-center justify-center">
                                                <i class="fa-solid fa-credit-card text-ink text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-ink">Kartu Kredit/Debit</p>
                                                <p class="text-xs text-ink-muted">Visa, Mastercard, JCB</p>
                                            </div>
                                        </div>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                             :class="selectedMethod === 'credit_card' ? 'border-gold bg-gold' : 'border-cream-dark'">
                                            <div class="w-2 h-2 rounded-full bg-white" x-show="selectedMethod === 'credit_card'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" :disabled="!selectedMethod || submitting"
                                    class="btn-gold btn-bounce w-full py-4 rounded-2xl text-white font-bold text-base mt-6 shadow-warm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-spinner animate-spin" x-show="submitting"></i>
                                <i class="fa-solid fa-lock" x-show="!submitting"></i>
                                <span x-text="submitting ? 'Memproses Pembayaran...' : 'Bayar Sekarang — Rp {{ number_format($booking->payment_option === 'dp' && $booking->paid_amount == 0 ? ceil($booking->total_price / 2) : $booking->total_price, 0, ',', '.') }}'"></span>
                            </button>
                        </form>

                        {{-- Trust badges --}}
                        <div class="flex items-center justify-center gap-4 mt-5 pt-5 border-t border-cream-dark">
                            @foreach([
                                ['fa-shield-halved', 'Terenkripsi SSL'],
                                ['fa-lock', 'Pembayaran Aman'],
                                ['fa-building-columns', 'Berlisensi BI'],
                            ] as [$icon, $label])
                                <div class="flex items-center gap-1.5 text-xs text-ink-muted">
                                    <i class="fa-solid fa-{{ $icon }} text-success text-[10px]"></i>
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
<script>
function paymentPage() {
    return {
        selectedMethod: '',
        submitting: false,
        minutes: 0,
        seconds: 0,
        interval: null,

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
                        window.location.reload();
                    }
                }, 1000);
            @else
                this.minutes = 0;
                this.seconds = 0;
            @endif
        },

        submitPayment(event) {
            if (this.submitting || !this.selectedMethod) return;
            this.submitting = true;
            event.target.submit();
        },
    };
}
</script>
@endpush
