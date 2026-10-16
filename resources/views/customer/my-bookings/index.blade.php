@extends('layouts.public')

@section('title', 'Pesanan Saya — SnapSpace Studio')
@section('meta_description', 'Lihat daftar dan riwayat semua booking studio foto Anda di SnapSpace.')

@section('content')

{{-- ── PAGE HEADER with Quick Stats ── --}}
<section class="bg-cream-warm py-10 border-b border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <p class="text-sm text-ink-muted mb-1">Halo, <strong class="text-ink">{{ Auth::user()->name }}</strong> 👋</p>
                <h1 class="font-serif font-bold text-3xl text-ink">Pesanan Saya</h1>
            </div>
            <a href="{{ route('booking.wizard') }}"
               class="btn-gold btn-bounce inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl text-white font-bold text-sm shadow-warm whitespace-nowrap">
                <i class="fa-solid fa-plus"></i> Booking Baru
            </a>
        </div>

        {{-- Quick Stats --}}
        @php
            $allBookings = Auth::user()->bookings ?? collect();
            $upcoming    = $allBookings->whereIn('status', ['paid', 'dp_paid', 'confirmed'])->where('booking_date', '>=', now()->toDateString());
            $pending     = $allBookings->where('status', 'pending');
            $completed   = $allBookings->whereIn('status', ['completed', 'checked_in']);
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
            @foreach([
                [$allBookings->count(), 'Total Booking', 'fa-receipt', '#C8A27A'],
                [$upcoming->count(), 'Terjadwal', 'fa-calendar-check', '#2E9E6B'],
                [$pending->count(), 'Menunggu Bayar', 'fa-clock', '#E5A53B'],
                [$completed->count(), 'Selesai', 'fa-circle-check', '#2F4F4F'],
            ] as [$count, $label, $icon, $color])
                <div class="bg-white rounded-2xl p-4 border border-cream-dark shadow-card">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $color }}15;">
                            <i class="fa-solid fa-{{ $icon }} text-sm" style="color: {{ $color }}"></i>
                        </div>
                        <div>
                            <p class="font-serif font-bold text-2xl text-ink leading-none">{{ $count }}</p>
                            <p class="text-xs text-ink-muted mt-0.5">{{ $label }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── STATUS FILTER TABS ── --}}
@php $statusFilter = request('status', 'all'); @endphp
<div class="sticky top-[72px] z-20 bg-white border-b border-cream-dark shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-x-auto py-3">
        <div class="flex gap-2 min-w-max">
            @foreach([
                ['all', 'Semua', null],
                ['pending', 'Menunggu Bayar', 'warning'],
                ['paid', 'Terjadwal', 'success'],
                ['dp_paid', 'DP Dibayar', 'info'],
                ['completed', 'Selesai', 'teal'],
                ['cancelled', 'Dibatalkan', 'danger'],
            ] as [$slug, $label, $variant])
                <a href="{{ route('my-bookings.index', $slug !== 'all' ? ['status' => $slug] : []) }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold transition-all whitespace-nowrap
                          {{ $statusFilter === $slug
                              ? 'bg-gold text-white shadow-warm'
                              : 'text-ink-muted hover:bg-cream hover:text-ink' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── BOOKING CARDS ── --}}
<section class="py-8 bg-cream min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($bookings->isNotEmpty())
            <div class="space-y-4">
                @foreach($bookings as $b)
                    @php
                        $statusMap = [
                            'pending'     => ['Menunggu Bayar', 'bg-warning/15 text-warning border-warning/20', 'fa-clock'],
                            'confirmed'   => ['Dikonfirmasi', 'bg-success/15 text-success border-success/20', 'fa-calendar-check'],
                            'paid'        => ['Terjadwal', 'bg-success/15 text-success border-success/20', 'fa-calendar-check'],
                            'dp_paid'     => ['DP Dibayar', 'bg-info/15 text-info border-info/20', 'fa-credit-card'],
                            'in_progress' => ['Sedang Berlangsung', 'bg-gold/15 text-gold border-gold/20', 'fa-camera'],
                            'checked_in'  => ['Sedang Berlangsung', 'bg-gold/15 text-gold border-gold/20', 'fa-camera'],
                            'completed'   => ['Selesai Sesi', 'bg-teal/15 text-teal border-teal/20', 'fa-circle-check'],
                            'delivered'   => ['Foto Siap Diunduh', 'bg-teal/15 text-teal border-teal/20', 'fa-images'],
                            'closed'      => ['Ditutup', 'bg-ink/10 text-ink-muted border-ink/10', 'fa-box-archive'],
                            'rescheduled' => ['Rescheduled', 'bg-amber-500/15 text-amber-700 border-amber-500/20', 'fa-calendar-days'],
                            'cancelled'   => ['Dibatalkan', 'bg-danger/15 text-danger border-danger/20', 'fa-ban'],
                            'no_show'     => ['No-Show (Hangus)', 'bg-danger/15 text-danger border-danger/20', 'fa-user-xmark'],
                            'expired'     => ['Kadaluarsa', 'bg-ink/10 text-ink-muted border-ink/10', 'fa-clock-rotate-left'],
                        ];
                        [$statusLabel, $statusClass, $statusIcon] = $statusMap[$b->status] ?? ['Unknown', 'bg-cream text-ink-muted', 'fa-question'];
                        $isUpcoming = in_array($b->status, ['confirmed', 'paid', 'dp_paid']) && $b->booking_date->isFuture();
                    @endphp

                    <div class="bg-white rounded-2xl border border-cream-dark shadow-card hover:shadow-warm transition-shadow p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                            {{-- Room image --}}
                            <div class="w-full sm:w-20 h-16 sm:h-20 rounded-xl overflow-hidden bg-cream-dark flex-shrink-0">
                                <img src="{{ $b->room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=200' }}"
                                     alt="{{ $b->room->name }}" class="w-full h-full object-cover" loading="lazy">
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="font-mono font-bold text-xs text-gold">{{ $b->booking_code }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusClass }} flex items-center gap-1">
                                        <i class="fa-solid fa-{{ $statusIcon }} text-[9px]"></i> {{ $statusLabel }}
                                    </span>
                                </div>
                                <p class="font-semibold text-sm text-ink">{{ $b->package->name }} — {{ $b->room->name }}</p>
                                <div class="flex flex-wrap gap-3 mt-1.5 text-xs text-ink-muted">
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-calendar text-gold text-[10px]"></i> {{ $b->booking_date->translatedFormat('D, d M Y') }}</span>
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-clock text-teal text-[10px]"></i> {{ substr($b->start_time, 0, 5) }} WIB</span>
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-users text-success text-[10px]"></i> {{ $b->people_count }} orang</span>
                                </div>
                            </div>

                            {{-- Price + Actions --}}
                            <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 flex-shrink-0">
                                <p class="font-serif font-bold text-lg text-gold">Rp {{ number_format($b->total_price, 0, ',', '.') }}</p>

                                <div class="flex gap-2">
                                    @if($b->status === 'pending' && !($b->expires_at?->isPast()))
                                        <a href="{{ route('booking.pay', $b->booking_code) }}"
                                           class="btn-gold btn-bounce px-4 py-2 rounded-xl text-white text-xs font-bold shadow-warm">
                                            Bayar
                                        </a>
                                    @endif

                                    <a href="{{ route('my-bookings.show', $b->booking_code) }}"
                                       class="px-4 py-2 rounded-xl border border-cream-dark text-ink text-xs font-semibold hover:border-gold hover:text-gold transition-colors">
                                        Detail
                                    </a>

                                    @if(in_array($b->status, ['completed', 'checked_in', 'paid']) && !$b->review)
                                        <a href="{{ route('my-bookings.show', $b->booking_code) }}#review"
                                           class="px-4 py-2 rounded-xl bg-teal/10 text-teal text-xs font-semibold border border-teal/20">
                                            Beri Ulasan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Upcoming countdown --}}
                        @if($isUpcoming)
                            <div class="mt-3 pt-3 border-t border-cream-dark flex items-center gap-2">
                                <i class="fa-solid fa-bell text-gold text-xs"></i>
                                <p class="text-xs text-ink-muted">
                                    <strong class="text-ink">{{ $b->booking_date->diffForHumans() }}</strong>
                                    — jangan lupa siapkan outfit dan datang 10 menit sebelumnya!
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center">
                {{ $bookings->appends(request()->query())->links('vendor.pagination.portfolio') }}
            </div>

        @else
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 rounded-3xl bg-cream border border-cream-dark flex items-center justify-center mb-5">
                    <i class="fa-regular fa-calendar text-3xl text-gold/50"></i>
                </div>
                <h3 class="font-serif font-semibold text-xl text-ink mb-2">Belum ada booking</h3>
                <p class="text-sm text-ink-muted mb-6 max-w-sm">Anda belum memiliki booking apapun. Mulai booking sesi foto pertama Anda sekarang!</p>
                <a href="{{ route('booking.wizard') }}" class="btn-gold btn-bounce inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white font-bold shadow-warm">
                    <i class="fa-solid fa-camera"></i> Booking Pertama Saya
                </a>
            </div>
        @endif

    </div>
</section>

@endsection
