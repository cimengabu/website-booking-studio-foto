@extends('layouts.public')

@section('title', 'Cek Status Booking — SnapSpace Studio')
@section('meta_description', 'Cek status booking studio foto Anda tanpa perlu login. Masukkan kode booking dan email/nomor HP untuk melihat detail dan status pembayaran.')

@section('content')

<section class="min-h-[80vh] flex items-center py-16 bg-cream">
    <div class="max-w-xl mx-auto px-4 w-full">
        {{-- Icon --}}
        <div class="text-center mb-8 reveal">
            <div class="w-16 h-16 rounded-2xl bg-gold flex items-center justify-center mx-auto mb-4 shadow-warm">
                <i class="fa-solid fa-ticket text-white text-2xl"></i>
            </div>
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-2">Tanpa Login</p>
            <h1 class="font-serif font-bold text-3xl text-ink mb-2">Cek Status Booking</h1>
            <p class="text-sm text-ink-muted">Masukkan kode booking dan email atau nomor HP yang terdaftar.</p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-3xl p-8 border border-cream-dark shadow-card reveal" data-delay="100"
             x-data="{ loading: false, result: null, error: null, bookingCode: '', identifier: '' }">

            <form @submit.prevent="checkBooking($data)" class="space-y-4">
                <div>
                    <label for="booking_code" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Kode Booking *</label>
                    <input type="text" id="booking_code" name="booking_code" required
                           placeholder="Contoh: SS-20241015-ABC123"
                           x-model="bookingCode"
                           class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all font-mono uppercase"
                           style="letter-spacing: 0.05em">
                </div>

                <div>
                    <label for="identifier" class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Email atau No. HP *</label>
                    <input type="text" id="identifier" name="identifier" required
                           placeholder="email@kamu.com atau 08xx-xxxx-xxxx"
                           x-model="identifier"
                           class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all">
                </div>

                <button type="submit"
                        :disabled="loading"
                        class="btn-gold btn-bounce w-full flex items-center justify-center gap-2 py-4 rounded-2xl text-white font-bold text-base shadow-warm disabled:opacity-70">
                    <i class="fa-solid fa-magnifying-glass" x-show="!loading"></i>
                    <i class="fa-solid fa-spinner animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Mencari...' : 'Cek Status Booking'"></span>
                </button>
            </form>

            {{-- Result --}}
            <div x-show="result" x-transition class="mt-6 pt-6 border-t border-cream-dark">
                <template x-if="result">
                    <div class="space-y-4">
                        {{-- Status badge --}}
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="font-mono font-bold text-sm text-ink" x-text="result?.booking_code"></span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold"
                                  :class="{
                                      'bg-success/15 text-success': ['confirmed', 'paid', 'completed', 'delivered'].includes(result?.status),
                                      'bg-warning/15 text-warning': ['pending', 'rescheduled'].includes(result?.status),
                                      'bg-info/15 text-info': result?.status === 'dp_paid',
                                      'bg-danger/15 text-danger': ['cancelled', 'expired', 'no_show'].includes(result?.status),
                                      'bg-teal/15 text-teal': ['in_progress', 'checked_in'].includes(result?.status),
                                      'bg-ink-muted/15 text-ink-muted': result?.status === 'closed',
                                  }"
                                  x-text="result?.status_label">
                            </span>
                        </div>

                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Paket</span>
                                <span class="font-semibold text-ink" x-text="result?.package_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Ruangan Studio</span>
                                <span class="font-semibold text-ink" x-text="result?.room_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Tanggal & Jam</span>
                                <span class="font-semibold text-ink" x-text="result?.schedule"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Total Biaya</span>
                                <span class="font-serif font-bold text-gold" x-text="result?.total_price"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Sudah Dibayar</span>
                                <span class="font-semibold text-success" x-text="result?.paid_amount"></span>
                            </div>
                        </div>

                        {{-- Pelunasan atau Pembayaran Awal --}}
                        <template x-if="result?.status === 'pending'">
                            <a :href="result?.pay_url" class="btn-gold btn-bounce flex items-center justify-center gap-2 w-full py-3 rounded-xl text-white font-bold text-sm shadow-warm">
                                <i class="fa-solid fa-credit-card"></i> Selesaikan Pembayaran Awal
                            </a>
                        </template>

                        <template x-if="result?.remaining_balance > 0 && ['confirmed', 'dp_paid'].includes(result?.status)">
                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200">
                                <div class="flex justify-between items-center text-xs mb-2">
                                    <span class="text-danger font-bold">Sisa Pelunasan Wajib:</span>
                                    <span class="font-bold text-danger" x-text="result?.remaining_balance_formatted"></span>
                                </div>
                                <a :href="result?.pay_url" class="btn-gold btn-bounce flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-white font-bold text-xs shadow-warm">
                                    <i class="fa-solid fa-money-bill-wave"></i> Bayar Sisa Pelunasan Sekarang
                                </a>
                            </div>
                        </template>

                        <div class="pt-2 flex flex-col gap-2">
                            <a href="{{ route('login') }}" class="btn-bounce flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-teal text-white font-semibold text-xs transition-colors hover:bg-teal-light">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                Login untuk Mengelola / Reschedule
                            </a>
                            <a href="{{ route('reschedule.policy') }}" target="_blank" class="text-center text-[11px] text-ink-muted hover:text-gold transition-colors">
                                Baca Kebijakan Reschedule & Pembatalan Studio →
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Error --}}
            <div x-show="error" x-transition class="mt-6 p-4 bg-danger/10 rounded-2xl border border-danger/20">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-danger"></i>
                    <p class="text-sm font-medium text-danger" x-text="error"></p>
                </div>
            </div>
        </div>

        {{-- Login prompt --}}
        <div class="text-center mt-6 reveal" data-delay="200">
            <p class="text-sm text-ink-muted">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-gold font-semibold hover:underline">Login untuk lihat semua booking Anda</a>
            </p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function checkBooking(data) {
        data.loading = true;
        data.error = null;
        data.result = null;

        if (!data.bookingCode || !data.identifier) {
            data.loading = false;
            data.error = 'Mohon isi kode booking dan email/nomor HP.';
            return;
        }

        fetch('{{ route('api.check-booking') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                booking_code: data.bookingCode,
                identifier: data.identifier,
            })
        })
        .then(res => res.json())
        .then(resData => {
            data.loading = false;
            if (resData.success) {
                data.result = resData.booking;
            } else {
                data.error = resData.message || 'Kode booking tidak ditemukan atau data tidak cocok.';
            }
        })
        .catch(err => {
            data.loading = false;
            data.error = 'Terjadi gangguan jaringan saat memeriksa booking. Silakan coba lagi.';
        });
    }
</script>
@endpush
