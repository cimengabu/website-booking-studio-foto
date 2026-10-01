@extends('layouts.app')

@section('title', 'Booking Saya — SnapSpace Studio')

@section('content')
<div class="py-12 max-w-5xl mx-auto px-4">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Booking Saya</h1>
            <p class="text-xs text-gray-400 mt-1">Riwayat & Status Seluruh Sesi Foto Anda</p>
        </div>
        <a href="{{ route('booking.wizard') }}" class="px-6 py-3 rounded-full bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16] shadow-lg transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Booking Baru
        </a>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-16 text-center space-y-4">
            <i class="fa-solid fa-camera text-5xl text-[#E8451E]/40"></i>
            <h3 class="font-heading font-bold text-xl text-white">Belum Ada Booking</h3>
            <p class="text-xs text-gray-400">Yuk, booking sesi foto pertama Anda sekarang!</p>
            <a href="{{ route('booking.wizard') }}" class="inline-block px-8 py-3.5 rounded-full bg-[#E8451E] text-white font-bold text-sm mt-2">
                Booking Pertama Saya
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-500/10 border-amber-500/40 text-amber-300',
                        'dp_paid' => 'bg-blue-500/10 border-blue-500/40 text-blue-300',
                        'paid' => 'bg-emerald-500/10 border-emerald-500/40 text-emerald-300',
                        'checked_in' => 'bg-violet-500/10 border-violet-500/40 text-violet-300',
                        'completed' => 'bg-gray-500/10 border-gray-500/40 text-gray-300',
                        'cancelled' => 'bg-rose-500/10 border-rose-500/40 text-rose-300',
                        'expired' => 'bg-rose-900/20 border-rose-900/40 text-rose-400',
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Bayar',
                        'dp_paid' => 'DP Terbayar',
                        'paid' => 'Lunas — Terkonfirmasi',
                        'checked_in' => 'Sedang Check-In',
                        'completed' => 'Sesi Selesai',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Kadaluarsa',
                    ];
                @endphp
                <a href="{{ route('my-bookings.show', $booking->booking_code) }}" class="block bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 hover:border-[#E8451E] transition-all shadow-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-[#141311] border border-[#2D2B26] flex items-center justify-center text-[#E8451E] text-xl">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="font-heading font-bold text-white">{{ $booking->room->name }}</p>
                                <p class="text-xs font-mono text-gray-400">{{ $booking->package->name }} — {{ $booking->booking_date->format('d M Y') }} · {{ substr($booking->start_time, 0, 5) }}-{{ substr($booking->end_time, 0, 5) }}</p>
                                <p class="text-[10px] font-mono text-gray-500">Kode: {{ $booking->booking_code }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="font-heading font-bold text-[#F2C94C] text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                <p class="text-[10px] font-mono text-gray-500">Total Tagihan</p>
                            </div>
                            <span class="px-3 py-1.5 rounded-full border text-[10px] font-mono font-bold uppercase {{ $statusColors[$booking->status] ?? '' }}">
                                {{ $statusLabels[$booking->status] ?? $booking->status }}
                            </span>
                            @if($booking->status === 'pending')
                                <a href="{{ route('booking.pay', $booking->booking_code) }}" class="px-4 py-2 rounded-full bg-[#E8451E] text-white text-xs font-bold hover:bg-[#d03a16]" onclick="event.stopPropagation()">
                                    Bayar
                                </a>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
