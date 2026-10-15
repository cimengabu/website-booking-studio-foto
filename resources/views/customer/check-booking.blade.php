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
             x-data="{ loading: false, result: null, error: null }">

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
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-sm text-ink" x-text="result?.booking_code"></span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold"
                                  :class="{
                                      'bg-success/15 text-success': result?.status === 'paid' || result?.status === 'completed',
                                      'bg-warning/15 text-warning': result?.status === 'dp_paid' || result?.status === 'pending',
                                      'bg-danger/15 text-danger': result?.status === 'cancelled' || result?.status === 'expired',
                                      'bg-teal/15 text-teal': result?.status === 'checked_in',
                                  }"
                                  x-text="result?.status_label">
                            </span>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Paket</span>
                                <span class="font-semibold text-ink" x-text="result?.package_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Ruangan</span>
                                <span class="font-semibold text-ink" x-text="result?.room_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Tanggal & Waktu</span>
                                <span class="font-semibold text-ink" x-text="result?.schedule"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ink-muted">Total</span>
                                <span class="font-serif font-bold text-gold" x-text="result?.total_price"></span>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="btn-bounce flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-teal text-white font-semibold text-sm transition-colors hover:bg-teal-light">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Login untuk lihat detail lengkap
                        </a>
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
        // In production, this would be an AJAX call to the API
        // For demo purposes, show a mock error
        data.loading = true;
        data.error = null;
        data.result = null;

        setTimeout(() => {
            data.loading = false;
            // Simulate API response
            if (!data.bookingCode || !data.identifier) {
                data.error = 'Mohon isi kode booking dan email/nomor HP.';
                return;
            }
            data.error = 'Kode booking tidak ditemukan atau data tidak cocok. Pastikan kode booking dan email/HP yang dimasukkan benar.';
        }, 1200);
    }
</script>
@endpush
