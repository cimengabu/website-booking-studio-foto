@extends('layouts.dashboard')

@section('title', 'Riwayat Pembayaran — SnapSpace Studio')
@section('breadcrumb', 'Riwayat Pembayaran')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8" data-aos="fade-up">
    <div>
        <h1 class="font-heading font-bold text-2xl sm:text-3xl text-white">Riwayat Pembayaran</h1>
        <p class="text-gray-500 text-sm mt-1">Rekap seluruh transaksi dan invoice booking Anda</p>
    </div>
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl px-5 py-3.5">
        <p class="text-[10px] font-mono text-gray-600 uppercase tracking-widest mb-1">Total Pengeluaran</p>
        <p class="font-heading font-bold text-xl text-[#F2C94C]">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Payments list --}}
@if($payments->isNotEmpty())
<div class="space-y-3" data-aos="fade-up" data-aos-delay="50">
    @foreach($payments as $payment)
    @php
        $statusConfig = [
            'success' => ['bg-emerald-500/15', 'text-emerald-400', 'border-emerald-500/25', 'fa-circle-check', 'Berhasil'],
            'pending' => ['bg-amber-500/15', 'text-amber-400', 'border-amber-500/25', 'fa-clock', 'Menunggu'],
            'failed'  => ['bg-rose-500/15', 'text-rose-400', 'border-rose-500/25', 'fa-circle-xmark', 'Gagal'],
            'refunded'=> ['bg-blue-500/15', 'text-blue-400', 'border-blue-500/25', 'fa-rotate-left', 'Refund'],
        ];
        [$bg, $textColor, $border, $icon, $label] = $statusConfig[$payment->status] ?? ['bg-gray-500/15', 'text-gray-400', 'border-gray-500/25', 'fa-question', 'Unknown'];

        $methodIcons = [
            'bank_transfer' => 'fa-building-columns',
            'qris'         => 'fa-qrcode',
            'credit_card'  => 'fa-credit-card',
            'cash'         => 'fa-money-bill-wave',
            'midtrans'     => 'fa-money-check-dollar',
        ];
        $methodIcon = $methodIcons[$payment->payment_method ?? ''] ?? 'fa-credit-card';
    @endphp

    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-5 hover:border-[#2D2B26]/80 transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

            {{-- Method icon --}}
            <div class="w-12 h-12 rounded-xl bg-[#141311] border border-[#2D2B26] flex items-center justify-center flex-shrink-0">
                <i class="fa-solid {{ $methodIcon }} text-[#F2C94C] text-lg"></i>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-mono text-xs text-[#F2C94C] font-bold">
                        {{ $payment->booking->booking_code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $bg }} {{ $textColor }} {{ $border }} flex items-center gap-1">
                        <i class="fa-solid {{ $icon }} text-[8px]"></i>{{ $label }}
                    </span>
                    @if(($payment->payment_kind ?? '') === 'dp' || ($payment->payment_type ?? '') === 'dp')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20">DP 50%</span>
                    @elseif(($payment->payment_kind ?? '') === 'remaining' || ($payment->payment_type ?? '') === 'settlement')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Pelunasan</span>
                    @elseif(($payment->payment_kind ?? '') === 'full')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Lunas Penuh</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-white truncate">
                    {{ $payment->booking->package->name ?? 'Paket Foto' }} — {{ $payment->booking->room->name ?? '-' }}
                </p>
                <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <i class="fa-solid fa-calendar text-[10px]"></i>
                        {{ $payment->created_at->translatedFormat('d M Y, H:i') }}
                    </span>
                    <span class="flex items-center gap-1 capitalize">
                        <i class="fa-solid {{ $methodIcon }} text-[10px]"></i>
                        {{ str_replace('_', ' ', $payment->payment_type ?? $payment->payment_method ?? 'Online') }}
                    </span>
                    @if($payment->order_id ?? $payment->transaction_id)
                    <span class="flex items-center gap-1 font-mono">
                        <i class="fa-solid fa-hashtag text-[10px]"></i>
                        {{ $payment->order_id ?? $payment->transaction_id }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Amount + Actions --}}
            <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-1.5 flex-shrink-0">
                <p class="font-heading font-bold text-lg {{ $payment->status === 'success' ? 'text-white' : 'text-gray-500' }}">
                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </p>
                <a href="{{ route('my-bookings.show', $payment->booking->booking_code) }}"
                   class="text-xs font-mono text-[#E8451E] hover:underline">
                    Detail Booking →
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div class="mt-8 flex justify-center">
    {{ $payments->links() }}
</div>

@else
{{-- Empty state --}}
<div class="bg-[#1E1D1A] border border-[#2D2B26] border-dashed rounded-3xl p-16 text-center" data-aos="fade-up">
    <div class="w-16 h-16 rounded-2xl bg-[#141311] border border-[#2D2B26] flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-receipt text-3xl text-gray-700"></i>
    </div>
    <h3 class="font-heading font-bold text-xl text-white mb-2">Belum Ada Transaksi</h3>
    <p class="text-gray-500 text-sm mb-6 max-w-sm mx-auto">Riwayat pembayaran akan muncul di sini setelah Anda melakukan booking pertama.</p>
    <a href="{{ route('booking.wizard') }}"
       class="inline-flex items-center gap-2 px-6 py-3 bg-[#E8451E] text-white rounded-2xl font-semibold text-sm hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/25 transition-all">
        <i class="fa-solid fa-camera text-xs"></i> Booking Sekarang
    </a>
</div>
@endif

@endsection
