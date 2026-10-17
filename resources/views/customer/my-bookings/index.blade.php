@extends('layouts.dashboard')

@section('title', 'Pesanan Saya — SnapSpace Studio')
@section('breadcrumb', 'Pesanan Saya')

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6" data-aos="fade-up">
    <div>
        <h1 class="font-heading font-bold text-2xl sm:text-3xl text-white">Pesanan Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola semua sesi foto booking Anda</p>
    </div>
    <a href="{{ route('booking.wizard') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#E8451E] text-white rounded-2xl font-semibold text-sm hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/25 transition-all hover:scale-[1.02] whitespace-nowrap">
        <i class="fa-solid fa-plus text-xs"></i> Booking Baru
    </a>
</div>

{{-- Quick Stats --}}
@php
    $allBookings = Auth::user()->bookings ?? collect();
    $upcoming    = $allBookings->whereIn('status', ['paid', 'dp_paid', 'confirmed'])->where('booking_date', '>=', now()->toDateString());
    $pending     = $allBookings->where('status', 'pending');
    $completed   = $allBookings->whereIn('status', ['completed', 'delivered', 'closed']);
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6" data-aos="fade-up" data-aos-delay="50">
    @foreach([
        [$allBookings->count(), 'Total Booking', 'fa-receipt', '#E8451E'],
        [$upcoming->count(), 'Terjadwal', 'fa-calendar-check', '#10b981'],
        [$pending->count(), 'Menunggu Bayar', 'fa-clock', '#F2C94C'],
        [$completed->count(), 'Selesai', 'fa-circle-check', '#a78bfa'],
    ] as [$count, $label, $icon, $color])
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $color }}15;">
            <i class="fa-solid {{ $icon }} text-sm" style="color: {{ $color }};"></i>
        </div>
        <div>
            <p class="font-heading font-bold text-2xl text-white leading-none">{{ $count }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── STATUS FILTER TABS ── --}}
@php $statusFilter = request('status', 'all'); @endphp
<div class="mb-5 overflow-x-auto" data-aos="fade-up" data-aos-delay="80">
    <div class="flex gap-2 min-w-max pb-1">
        @foreach([
            ['all', 'Semua'],
            ['pending', 'Menunggu Bayar'],
            ['confirmed', 'Dikonfirmasi'],
            ['dp_paid', 'DP Dibayar'],
            ['completed', 'Selesai'],
            ['delivered', 'Foto Siap'],
            ['cancelled', 'Dibatalkan'],
        ] as [$slug, $label])
        <a href="{{ route('my-bookings.index', $slug !== 'all' ? ['status' => $slug] : []) }}"
           class="px-4 py-2 rounded-full text-sm font-semibold transition-all whitespace-nowrap
                  {{ $statusFilter === $slug
                      ? 'bg-[#E8451E] text-white shadow-lg shadow-[#E8451E]/25'
                      : 'bg-[#1E1D1A] border border-[#2D2B26] text-gray-400 hover:text-white hover:border-[#E8451E]/40' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- ── BOOKING CARDS ── --}}
@if($bookings->isNotEmpty())
    <div class="space-y-3">
        @foreach($bookings as $b)
        @php
            $statusMap = [
                'pending'     => ['Menunggu Bayar', 'bg-amber-500/15 text-amber-400 border-amber-500/25', 'fa-clock'],
                'confirmed'   => ['Dikonfirmasi', 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25', 'fa-calendar-check'],
                'paid'        => ['Terjadwal', 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25', 'fa-calendar-check'],
                'dp_paid'     => ['DP Dibayar', 'bg-blue-500/15 text-blue-400 border-blue-500/25', 'fa-credit-card'],
                'in_progress' => ['Sedang Berlangsung', 'bg-yellow-500/15 text-yellow-400 border-yellow-500/25', 'fa-camera'],
                'checked_in'  => ['Sedang Berlangsung', 'bg-yellow-500/15 text-yellow-400 border-yellow-500/25', 'fa-camera'],
                'completed'   => ['Selesai Sesi', 'bg-teal-500/15 text-teal-400 border-teal-500/25', 'fa-circle-check'],
                'delivered'   => ['Foto Siap Diunduh', 'bg-purple-500/15 text-purple-400 border-purple-500/25', 'fa-images'],
                'closed'      => ['Ditutup', 'bg-gray-500/15 text-gray-500 border-gray-500/25', 'fa-box-archive'],
                'rescheduled' => ['Rescheduled', 'bg-cyan-500/15 text-cyan-400 border-cyan-500/25', 'fa-calendar-days'],
                'cancelled'   => ['Dibatalkan', 'bg-rose-500/15 text-rose-400 border-rose-500/25', 'fa-ban'],
                'no_show'     => ['No-Show (Hangus)', 'bg-rose-500/15 text-rose-400 border-rose-500/25', 'fa-user-xmark'],
                'expired'     => ['Kadaluarsa', 'bg-gray-500/15 text-gray-500 border-gray-500/25', 'fa-clock-rotate-left'],
            ];
            [$statusLabel, $statusClass, $statusIcon] = $statusMap[$b->status] ?? ['Unknown', 'bg-gray-500/15 text-gray-400 border-gray-500/25', 'fa-question'];
            $isUpcoming = in_array($b->status, ['confirmed', 'paid', 'dp_paid']) && $b->booking_date->isFuture();
        @endphp

        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-5 hover:border-[#E8451E]/20 transition-all group">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                {{-- Room image --}}
                <div class="w-full sm:w-20 h-16 sm:h-20 rounded-xl overflow-hidden bg-[#141311] flex-shrink-0">
                    <img src="{{ $b->room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=200' }}"
                         alt="{{ $b->room->name }}" class="w-full h-full object-cover" loading="lazy">
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="font-mono font-bold text-xs text-[#F2C94C]">{{ $b->booking_code }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusClass }} flex items-center gap-1">
                            <i class="fa-solid {{ $statusIcon }} text-[9px]"></i> {{ $statusLabel }}
                        </span>
                    </div>
                    <p class="font-semibold text-sm text-white">{{ $b->package->name }} — {{ $b->room->name }}</p>
                    <div class="flex flex-wrap gap-3 mt-1.5 text-xs text-gray-500">
                        <span class="flex items-center gap-1"><i class="fa-solid fa-calendar text-[#E8451E] text-[10px]"></i> {{ $b->booking_date->translatedFormat('D, d M Y') }}</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-clock text-[#F2C94C] text-[10px]"></i> {{ substr($b->start_time, 0, 5) }} WIB</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-users text-emerald-400 text-[10px]"></i> {{ $b->people_count }} orang</span>
                    </div>
                </div>

                {{-- Price + Actions --}}
                <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 flex-shrink-0">
                    <p class="font-heading font-bold text-lg text-white">Rp {{ number_format($b->total_price, 0, ',', '.') }}</p>

                    <div class="flex gap-2">
                        @if($b->status === 'pending' && !($b->expires_at?->isPast()))
                            <a href="{{ route('booking.pay', $b->booking_code) }}"
                               class="px-4 py-2 bg-[#E8451E] text-white rounded-xl text-xs font-bold hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/20 transition-colors">
                                Bayar
                            </a>
                        @endif

                        <a href="{{ route('my-bookings.show', $b->booking_code) }}"
                           class="px-4 py-2 bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-white hover:border-[#E8451E]/30 rounded-xl text-xs font-semibold transition-colors">
                            Detail
                        </a>

                        @if(in_array($b->status, ['completed', 'delivered', 'checked_in', 'paid']) && !$b->review)
                            <a href="{{ route('my-bookings.show', $b->booking_code) }}#review"
                               class="px-4 py-2 bg-teal-500/15 border border-teal-500/25 text-teal-400 rounded-xl text-xs font-semibold hover:bg-teal-500/25 transition-colors">
                                Ulasan
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Upcoming countdown --}}
            @if($isUpcoming)
                <div class="mt-3 pt-3 border-t border-[#2D2B26] flex items-center gap-2">
                    <i class="fa-solid fa-bell text-[#F2C94C] text-xs"></i>
                    <p class="text-xs text-gray-500">
                        <strong class="text-gray-300">{{ $b->booking_date->diffForHumans() }}</strong>
                        — jangan lupa siapkan outfit dan datang 10 menit sebelumnya!
                    </p>
                </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8 flex justify-center">
        {{ $bookings->appends(request()->query())->links() }}
    </div>

@else
    {{-- Empty state --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] border-dashed rounded-3xl p-16 text-center" data-aos="fade-up">
        <div class="w-16 h-16 rounded-2xl bg-[#141311] border border-[#2D2B26] flex items-center justify-center mx-auto mb-4">
            <i class="fa-regular fa-calendar text-3xl text-gray-700"></i>
        </div>
        <h3 class="font-heading font-bold text-xl text-white mb-2">Belum ada booking</h3>
        <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">Anda belum memiliki booking apapun. Mulai booking sesi foto pertama Anda sekarang!</p>
        <a href="{{ route('booking.wizard') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-[#E8451E] text-white rounded-2xl font-bold text-sm shadow-lg shadow-[#E8451E]/25 hover:bg-[#d03a16] transition-all hover:scale-[1.02]">
            <i class="fa-solid fa-camera text-xs"></i> Booking Pertama Saya
        </a>
    </div>
@endif

@endsection
