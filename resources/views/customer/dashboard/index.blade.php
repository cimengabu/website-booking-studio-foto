@extends('layouts.dashboard')

@section('title', 'Ringkasan Dashboard — SnapSpace Studio')
@section('breadcrumb', 'Ringkasan')

@section('content')

{{-- ── GREETING HEADER ── --}}
<div class="mb-8" data-aos="fade-up">
    <p class="text-sm text-gray-500 mb-1">
        {{ now()->hour < 12 ? 'Selamat pagi' : (now()->hour < 17 ? 'Selamat siang' : 'Selamat malam') }},
    </p>
    <h1 class="font-heading font-bold text-3xl sm:text-4xl text-white">
        {{ explode(' ', $user->name)[0] }} <span class="text-[#E8451E]">👋</span>
    </h1>
    <p class="text-gray-500 text-sm mt-1">Selamat datang di dasbor SnapSpace Studio kamu.</p>
</div>

{{-- ── STATS CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Booking', 'value' => $stats['total'], 'icon' => 'fa-receipt', 'color' => '#E8451E', 'suffix' => 'sesi'],
        ['label' => 'Terjadwal', 'value' => $stats['upcoming'], 'icon' => 'fa-calendar-check', 'color' => '#10b981', 'suffix' => 'mendatang'],
        ['label' => 'Menunggu Bayar', 'value' => $stats['pending'], 'icon' => 'fa-clock', 'color' => '#F2C94C', 'suffix' => 'booking'],
        ['label' => 'Total Pengeluaran', 'value' => 'Rp ' . number_format($stats['total_spent'], 0, ',', '.'), 'icon' => 'fa-wallet', 'color' => '#a78bfa', 'suffix' => ''],
    ] as $idx => $stat)
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-5 hover:border-[#E8451E]/30 transition-colors group"
         data-aos="fade-up" data-aos-delay="{{ $idx * 80 }}">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $stat['color'] }}18;">
                <i class="fa-solid {{ $stat['icon'] }} text-base" style="color: {{ $stat['color'] }};"></i>
            </div>
            <i class="fa-solid fa-arrow-trend-up text-xs text-gray-700 group-hover:text-gray-500 transition-colors"></i>
        </div>
        <p class="font-heading font-bold text-2xl text-white leading-none">{{ $stat['value'] }}</p>
        @if($stat['suffix'])
            <p class="text-[11px] text-gray-600 font-mono mt-0.5">{{ $stat['suffix'] }}</p>
        @endif
        <p class="text-xs text-gray-500 mt-1.5">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── NEXT BOOKING COUNTDOWN CARD ── --}}
@if($nextBooking)
<div class="mb-8" data-aos="fade-up" data-aos-delay="100">
    <h2 class="font-heading font-bold text-lg text-white mb-4 flex items-center gap-2">
        <i class="fa-solid fa-calendar-star text-[#E8451E]"></i>
        Sesi Foto Berikutnya
    </h2>

    @php
        $sessionDateTime = \Carbon\Carbon::parse($nextBooking->booking_date->format('Y-m-d') . ' ' . $nextBooking->start_time);
        $hoursUntil = now()->diffInHours($sessionDateTime, false);
        $minutesUntil = now()->diffInMinutes($sessionDateTime, false);
        $daysUntil = now()->diffInDays($sessionDateTime, false);
    @endphp

    <div class="relative overflow-hidden bg-gradient-to-br from-[#1E1D1A] via-[#1A1917] to-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 sm:p-8"
         style="background-image: radial-gradient(circle at 80% 20%, rgba(232,69,30,0.08) 0%, transparent 60%);">

        {{-- Decorative elements --}}
        <div class="absolute top-4 right-4 w-32 h-32 rounded-full bg-[#E8451E]/5 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-48 h-48 rounded-full bg-[#F2C94C]/5 blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center gap-6">

            {{-- Room image --}}
            <div class="w-full sm:w-40 h-32 sm:h-36 rounded-2xl overflow-hidden flex-shrink-0 ring-2 ring-[#E8451E]/20">
                <img src="{{ $nextBooking->room->images->first()?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=400' }}"
                     alt="{{ $nextBooking->room->name }}"
                     class="w-full h-full object-cover">
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-[#E8451E]/15 text-[#E8451E] border border-[#E8451E]/30 rounded-full text-xs font-mono font-bold">
                        {{ $nextBooking->booking_code }}
                    </span>
                    <span class="px-2.5 py-1 bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i>Dikonfirmasi
                    </span>
                </div>

                <h3 class="font-heading font-bold text-xl text-white mb-1">{{ $nextBooking->package->name }}</h3>
                <p class="text-gray-400 text-sm mb-3">
                    <i class="fa-solid fa-door-open text-[#E8451E] text-xs mr-1"></i>{{ $nextBooking->room->name }}
                </p>

                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calendar text-[#F2C94C] text-xs"></i>
                        <span class="text-gray-300 font-medium">{{ $nextBooking->booking_date->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock text-[#F2C94C] text-xs"></i>
                        <span class="text-gray-300 font-medium">{{ substr($nextBooking->start_time, 0, 5) }} — {{ substr($nextBooking->end_time, 0, 5) }} WIB</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users text-[#F2C94C] text-xs"></i>
                        <span class="text-gray-300 font-medium">{{ $nextBooking->people_count }} orang</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('my-bookings.show', $nextBooking->booking_code) }}"
                       class="px-4 py-2 bg-[#E8451E] text-white rounded-xl text-sm font-semibold hover:bg-[#d03a16] transition-colors shadow-lg shadow-[#E8451E]/20">
                        <i class="fa-solid fa-eye text-xs mr-1.5"></i>Lihat Detail
                    </a>
                    <a href="https://wa.me/6281234567890?text=Halo+SnapSpace,+saya+ingin+konfirmasi+booking+{{ $nextBooking->booking_code }}"
                       target="_blank"
                       class="px-4 py-2 bg-[#25D366]/15 text-[#25D366] border border-[#25D366]/30 rounded-xl text-sm font-semibold hover:bg-[#25D366]/25 transition-colors">
                        <i class="fa-brands fa-whatsapp text-sm mr-1.5"></i>Chat Admin
                    </a>
                </div>
            </div>

            {{-- Countdown timer --}}
            <div class="flex-shrink-0 text-center bg-[#141311] border border-[#2D2B26] rounded-2xl p-5 min-w-[140px]"
                 x-data="countdown('{{ $sessionDateTime->toISOString() }}')"
                 x-init="init()">
                <p class="text-[10px] font-mono text-gray-600 uppercase tracking-widest mb-3">Dimulai dalam</p>
                @if($daysUntil > 0)
                    <p class="font-heading font-black text-4xl text-[#E8451E] tabular-nums" x-text="days">{{ $daysUntil }}</p>
                    <p class="text-xs text-gray-500 font-mono mt-1">hari lagi</p>
                    <div class="flex justify-center gap-1 mt-3 text-xs font-mono text-gray-600">
                        <span x-text="hours + 'j'">0j</span>
                        <span>:</span>
                        <span x-text="minutes + 'm'">0m</span>
                    </div>
                @else
                    <p class="font-heading font-black text-3xl text-[#F2C94C] tabular-nums" x-text="hours + 'j ' + minutes + 'm'">0j 0m</p>
                    <p class="text-xs text-gray-500 font-mono mt-1">lagi</p>
                @endif
                <div class="mt-3 px-2 py-1 bg-[#E8451E]/10 rounded-lg">
                    <p class="text-[10px] font-mono text-[#E8451E]">
                        {{ $nextBooking->booking_date->translatedFormat('d M') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tips banner --}}
        <div class="mt-5 pt-5 border-t border-[#2D2B26] flex items-start gap-3">
            <i class="fa-solid fa-lightbulb text-[#F2C94C] text-sm mt-0.5 flex-shrink-0"></i>
            <p class="text-xs text-gray-500 leading-relaxed">
                <strong class="text-gray-300">Tips:</strong> Datang 10–15 menit sebelum sesi dimulai untuk persiapan ruangan.
                Bawa outfit yang sudah disiapkan dan pastikan baterai HP Anda penuh!
                <a href="{{ route('my-bookings.show', $nextBooking->booking_code) }}" class="text-[#E8451E] hover:underline ml-1">Lihat e-ticket →</a>
            </p>
        </div>
    </div>
</div>
@else
{{-- No upcoming booking CTA --}}
<div class="mb-8 bg-gradient-to-br from-[#1E1D1A] to-[#141311] border border-[#2D2B26] border-dashed rounded-3xl p-8 text-center" data-aos="fade-up">
    <div class="w-16 h-16 rounded-2xl bg-[#E8451E]/10 flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-camera-retro text-[#E8451E] text-2xl"></i>
    </div>
    <h3 class="font-heading font-bold text-xl text-white mb-2">Belum Ada Sesi Terjadwal</h3>
    <p class="text-gray-500 text-sm mb-5 max-w-sm mx-auto">Abadikan momen istimewa Anda bersama orang tersayang. Booking sesi foto sekarang!</p>
    <a href="{{ route('booking.wizard') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-[#E8451E] text-white rounded-2xl font-semibold text-sm hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/25 transition-all hover:scale-[1.02]">
        <i class="fa-solid fa-plus text-xs"></i> Booking Sesi Foto
    </a>
</div>
@endif

{{-- ── 2-COLUMN LAYOUT: Recent Bookings + Active Promos ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Recent bookings --}}
    <div class="lg:col-span-2" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-[#E8451E] text-sm"></i>
                Aktivitas Terbaru
            </h2>
            <a href="{{ route('my-bookings.index') }}" class="text-xs text-[#E8451E] hover:underline font-mono">
                Lihat semua →
            </a>
        </div>

        <div class="space-y-3">
            @forelse($recentBookings as $b)
            @php
                $statusColors = [
                    'pending'     => ['bg-amber-500/15', 'text-amber-400', 'border-amber-500/25', 'fa-clock'],
                    'confirmed'   => ['bg-emerald-500/15', 'text-emerald-400', 'border-emerald-500/25', 'fa-calendar-check'],
                    'paid'        => ['bg-emerald-500/15', 'text-emerald-400', 'border-emerald-500/25', 'fa-calendar-check'],
                    'dp_paid'     => ['bg-blue-500/15', 'text-blue-400', 'border-blue-500/25', 'fa-credit-card'],
                    'in_progress' => ['bg-yellow-500/15', 'text-yellow-400', 'border-yellow-500/25', 'fa-camera'],
                    'completed'   => ['bg-teal-500/15', 'text-teal-400', 'border-teal-500/25', 'fa-circle-check'],
                    'delivered'   => ['bg-purple-500/15', 'text-purple-400', 'border-purple-500/25', 'fa-images'],
                    'closed'      => ['bg-gray-500/15', 'text-gray-500', 'border-gray-500/25', 'fa-box-archive'],
                    'cancelled'   => ['bg-rose-500/15', 'text-rose-400', 'border-rose-500/25', 'fa-ban'],
                    'expired'     => ['bg-gray-500/15', 'text-gray-500', 'border-gray-500/25', 'fa-clock-rotate-left'],
                    'no_show'     => ['bg-rose-500/15', 'text-rose-400', 'border-rose-500/25', 'fa-user-xmark'],
                ];
                [$bg, $textColor, $border, $icon] = $statusColors[$b->status] ?? ['bg-gray-500/15', 'text-gray-400', 'border-gray-500/25', 'fa-question'];
            @endphp
            <a href="{{ route('my-bookings.show', $b->booking_code) }}"
               class="flex items-center gap-4 bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-4 hover:border-[#E8451E]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                    <img src="{{ $b->room->images->first()?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=100' }}"
                         alt="{{ $b->room->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="font-mono text-xs text-[#F2C94C] font-bold">{{ $b->booking_code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $bg }} {{ $textColor }} {{ $border }} flex items-center gap-1">
                            <i class="fa-solid {{ $icon }} text-[8px]"></i>
                            {{ $b->status_label }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-white truncate">{{ $b->package->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <i class="fa-solid fa-calendar text-[10px] mr-1"></i>{{ $b->booking_date->translatedFormat('d M Y') }}
                        <span class="ml-2"><i class="fa-solid fa-clock text-[10px] mr-1"></i>{{ substr($b->start_time, 0, 5) }}</span>
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-bold text-sm text-white">Rp {{ number_format($b->total_price, 0, ',', '.') }}</p>
                    <i class="fa-solid fa-chevron-right text-xs text-gray-600 group-hover:text-[#E8451E] transition-colors mt-1"></i>
                </div>
            </a>
            @empty
            <div class="bg-[#1E1D1A] border border-[#2D2B26] border-dashed rounded-2xl p-8 text-center">
                <i class="fa-regular fa-calendar text-3xl text-gray-700 mb-2"></i>
                <p class="text-gray-500 text-sm">Belum ada riwayat booking</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Loyalty Points Card --}}
    <div class="mb-6 bg-gradient-to-br from-[#1E1D1A] to-[#141311] border border-[#F2C94C]/30 rounded-2xl p-5 relative overflow-hidden"
         data-aos="fade-up" data-aos-delay="120"
         style="background-image: radial-gradient(circle at 90% 10%, rgba(242,201,76,0.12) 0%, transparent 70%);">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#F2C94C]/15 border border-[#F2C94C]/30 flex items-center justify-center">
                    <i class="fa-solid fa-coins text-[#F2C94C] text-sm"></i>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-sm text-white">SnapPoints Loyalti</h3>
                    <p class="text-[11px] text-gray-500 font-mono">Tukar Poin Jadi Diskon</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-[#F2C94C]/20 text-[#F2C94C] border border-[#F2C94C]/40 uppercase tracking-wider">
                Member
            </span>
        </div>

        <div class="flex items-baseline gap-2 my-2">
            <span class="font-heading font-black text-3xl text-white">{{ number_format($user->loyalty_points ?? 0, 0, ',', '.') }}</span>
            <span class="text-xs font-mono text-[#F2C94C] uppercase tracking-wider font-bold">Poin</span>
            <span class="text-xs text-gray-500 ml-auto">≈ Rp {{ number_format(($user->loyalty_points ?? 0) * 100, 0, ',', '.') }}</span>
        </div>

        <div class="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] text-[11px] text-gray-400 space-y-1 mb-3">
            <p class="flex items-center gap-1.5">
                <i class="fa-solid fa-circle-check text-emerald-400 text-[9px]"></i>
                <span>Dapat 1 poin tiap transaksi Rp 1.000</span>
            </p>
            <p class="flex items-center gap-1.5">
                <i class="fa-solid fa-circle-check text-emerald-400 text-[9px]"></i>
                <span>Bonus +50 poin tiap kirim ulasan & foto testimoni</span>
            </p>
        </div>

        <a href="{{ route('booking.wizard') }}"
           class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-[#F2C94C] hover:bg-[#dfb73e] text-black font-bold text-xs transition-colors">
            <i class="fa-solid fa-bolt"></i> Gunakan Poin Saat Booking
        </a>
    </div>

    {{-- Active Promos --}}
    <div data-aos="fade-up" data-aos-delay="150">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                <i class="fa-solid fa-tag text-[#F2C94C] text-sm"></i>
                Promo Aktif
            </h2>
        </div>

        <div class="space-y-3">
            @forelse($promos as $promo)
            <div class="bg-gradient-to-br from-[#1E1D1A] to-[#141311] border border-[#2D2B26] rounded-2xl p-4 overflow-hidden relative"
                 style="background-image: radial-gradient(circle at 90% 10%, rgba(242,201,76,0.06) 0%, transparent 60%);">
                <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-[#F2C94C]/15 flex items-center justify-center">
                    <i class="fa-solid fa-star text-[#F2C94C] text-[8px]"></i>
                </div>

                {{-- Code badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#F2C94C]/10 border border-[#F2C94C]/25 rounded-xl mb-3">
                    <i class="fa-solid fa-ticket text-[#F2C94C] text-xs"></i>
                    <span class="font-mono font-bold text-sm text-[#F2C94C] tracking-wider">{{ $promo->code }}</span>
                </div>

                <p class="font-heading font-bold text-lg text-white mb-1">
                    @if($promo->type === 'percent')
                        Diskon {{ number_format($promo->value, 0) }}%
                    @else
                        Hemat Rp {{ number_format($promo->value, 0, ',', '.') }}
                    @endif
                </p>

                <div class="space-y-1 text-xs text-gray-500">
                    @if($promo->min_order)
                        <p><i class="fa-solid fa-circle-info text-[10px] mr-1"></i>Min. order Rp {{ number_format($promo->min_order, 0, ',', '.') }}</p>
                    @endif
                    @if($promo->max_discount && $promo->type === 'percent')
                        <p><i class="fa-solid fa-circle-info text-[10px] mr-1"></i>Maks. diskon Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</p>
                    @endif
                    @if($promo->end_date)
                        <p class="text-amber-600/80">
                            <i class="fa-solid fa-clock text-[10px] mr-1"></i>Berlaku s/d {{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d M Y') }}
                        </p>
                    @endif
                    <p>
                        <i class="fa-solid fa-users text-[10px] mr-1"></i>Sisa {{ $promo->quota - $promo->used_count }} kuota
                    </p>
                </div>

                {{-- Copy button --}}
                <button onclick="copyCode('{{ $promo->code }}', this)"
                        class="mt-3 w-full py-2 border border-[#2D2B26] hover:border-[#F2C94C]/40 rounded-xl text-xs font-mono font-semibold text-gray-400 hover:text-[#F2C94C] transition-all">
                    <i class="fa-regular fa-copy mr-1.5"></i>Salin Kode
                </button>
            </div>
            @empty
            <div class="bg-[#1E1D1A] border border-[#2D2B26] border-dashed rounded-2xl p-6 text-center">
                <i class="fa-solid fa-tag text-3xl text-gray-700 mb-2"></i>
                <p class="text-gray-500 text-xs">Tidak ada promo aktif saat ini</p>
            </div>
            @endforelse
        </div>

        {{-- Quick action cards --}}
        <div class="mt-6 space-y-2">
            <p class="text-xs font-mono text-gray-600 uppercase tracking-widest mb-3">Aksi Cepat</p>
            @foreach([
                ['Booking Sesi Foto', 'fa-camera-retro', 'booking.wizard', '#E8451E'],
                ['Cek Status Booking', 'fa-magnifying-glass', 'booking.check', '#F2C94C'],
                ['Galeri Foto Saya', 'fa-images', 'dashboard.gallery', '#a78bfa'],
            ] as [$label, $icon, $route, $color])
            <a href="{{ route($route) }}"
               class="flex items-center gap-3 bg-[#1E1D1A] border border-[#2D2B26] rounded-xl px-4 py-3 hover:border-[{{ $color }}]/30 transition-colors group">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $color }}15;">
                    <i class="fa-solid {{ $icon }} text-xs" style="color: {{ $color }};"></i>
                </div>
                <span class="text-sm font-medium text-gray-300 group-hover:text-white transition-colors">{{ $label }}</span>
                <i class="fa-solid fa-chevron-right text-xs text-gray-700 ml-auto group-hover:text-gray-400 transition-colors"></i>
            </a>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Countdown Alpine component
document.addEventListener('alpine:init', () => {
    Alpine.data('countdown', (targetDate) => ({
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        interval: null,
        init() {
            this.update();
            this.interval = setInterval(() => this.update(), 1000);
        },
        update() {
            const now = new Date().getTime();
            const target = new Date(targetDate).getTime();
            const diff = target - now;

            if (diff <= 0) {
                clearInterval(this.interval);
                this.days = this.hours = this.minutes = this.seconds = 0;
                return;
            }

            this.days    = Math.floor(diff / (1000 * 60 * 60 * 24));
            this.hours   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
        }
    }));
});

// Copy promo code
function copyCode(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check mr-1.5 text-emerald-400"></i><span class="text-emerald-400">Tersalin!</span>';
        setTimeout(() => btn.innerHTML = orig, 2000);
    });
}
</script>
@endpush
