@extends('layouts.admin')

@section('page-title', 'Detail Booking — ' . $booking->booking_code)

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.bookings.index') }}" class="p-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="font-heading font-bold text-xl text-white">{{ $booking->booking_code }}</h2>
            <p class="text-xs text-gray-400 font-mono">{{ $booking->user->name }} · {{ $booking->booking_date->format('d M Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-6">
            <!-- Booking Info -->
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">Informasi Booking</h3>
                <div class="grid grid-cols-2 gap-4 text-xs font-mono">
                    <div><p class="text-gray-400">Pelanggan</p><p class="font-bold text-white mt-0.5">{{ $booking->user->name }}</p><p class="text-gray-500 text-[10px]">{{ $booking->user->email }} · {{ $booking->user->phone }}</p></div>
                    <div><p class="text-gray-400">Ruangan</p><p class="font-bold text-white mt-0.5">{{ $booking->room->name }}</p><p class="text-gray-500 text-[10px]">{{ $booking->room->theme }}</p></div>
                    <div><p class="text-gray-400">Paket</p><p class="font-bold text-white mt-0.5">{{ $booking->package->name }}</p></div>
                    <div><p class="text-gray-400">Tanggal & Jam</p><p class="font-bold text-[#F2C94C] mt-0.5">{{ $booking->booking_date->format('d M Y') }} · {{ substr($booking->start_time, 0, 5) }}–{{ substr($booking->end_time, 0, 5) }}</p></div>
                    <div><p class="text-gray-400">Jumlah Orang</p><p class="font-bold text-white mt-0.5">{{ $booking->people_count }} orang</p></div>
                    <div><p class="text-gray-400">QR Token</p><p class="font-bold text-white mt-0.5 text-[10px] break-all">{{ $booking->qr_token }}</p></div>
                </div>
                @if($booking->notes)
                    <div class="border-t border-[#2D2B26] pt-4">
                        <p class="text-[10px] text-gray-400 mb-1">Catatan:</p>
                        <p class="text-xs text-gray-200 italic">"{{ $booking->notes }}"</p>
                    </div>
                @endif

                <!-- Reschedule Details -->
                @if($booking->original_booking_date || $booking->reschedule_count > 0)
                    <div class="border-t border-[#2D2B26] pt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                        <p class="text-xs font-bold text-amber-300 flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-calendar-days"></i> Booking Pernah Di-Reschedule ({{ $booking->reschedule_count }}x)
                        </p>
                        <p class="text-[11px] text-gray-300">
                            Jadwal Asal: <span class="font-bold text-white">{{ $booking->original_booking_date }}</span> jam <span class="font-bold text-white">{{ substr($booking->original_start_time ?? '', 0, 5) }} WIB</span>
                        </p>
                    </div>
                @endif

                <!-- Cancellation / No Show Details -->
                @if($booking->status === 'cancelled' || $booking->status === 'no_show')
                    <div class="border-t border-[#2D2B26] pt-4 p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl space-y-1">
                        <p class="text-xs font-bold text-rose-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-ban"></i> {{ $booking->status === 'no_show' ? 'Pelanggan Tidak Hadir (No-Show)' : 'Booking Dibatalkan' }}
                        </p>
                        @if($booking->cancellation_reason)
                            <p class="text-[11px] text-gray-300">Alasan: <span class="italic text-white">"{{ $booking->cancellation_reason }}"</span></p>
                        @endif
                        <div class="flex gap-4 pt-1 text-[11px]">
                            <span>Refund: <strong class="text-emerald-400">Rp {{ number_format($booking->refund_amount ?? 0, 0, ',', '.') }}</strong></span>
                            <span>Penalti: <strong class="text-rose-400">Rp {{ number_format($booking->penalty_amount ?? 0, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Update Status Form -->
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-sm text-white">Update Status Lifecycle</h3>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-[#141311] border border-[#2D2B26] text-gray-300">
                        Status Saat Ini: <strong class="text-[#F2C94C]">{{ $booking->status_label }}</strong>
                    </span>
                </div>
                <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="flex gap-3 items-end">
                    @csrf @method('PATCH')
                    <select name="status" class="flex-grow bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                        @foreach([
                            'pending'     => 'Menunggu Pembayaran',
                            'confirmed'   => 'Dikonfirmasi (Lunas / DP)',
                            'in_progress' => 'Sedang Berlangsung (Sesi)',
                            'completed'   => 'Selesai Sesi Pemotretan',
                            'delivered'   => 'Foto Siap (Delivery)',
                            'closed'      => 'Ditutup (Closed)',
                            'rescheduled' => 'Rescheduled',
                            'cancelled'   => 'Dibatalkan',
                            'no_show'     => 'No-Show (DP Hangus)',
                            'expired'     => 'Kedaluwarsa',
                        ] as $s => $lbl)
                            <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>{{ strtoupper($s) }} — {{ $lbl }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16]">Update Status</button>
                </form>
            </div>

            <!-- Record Cash Payment -->
            @if($booking->remaining_balance > 0)
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <h3 class="font-heading font-bold text-sm text-white">Catat Pembayaran Tunai / EDC (Pelunasan)</h3>
                    <form method="POST" action="{{ route('admin.bookings.pay-cash', $booking->id) }}" class="flex gap-3 items-end">
                        @csrf
                        <div class="flex-grow">
                            <label class="text-[10px] font-mono text-gray-400 mb-1 block">Jumlah (Rp)</label>
                            <input type="number" name="amount" value="{{ $booking->remaining_balance }}" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                        </div>
                        <div>
                            <label class="text-[10px] font-mono text-gray-400 mb-1 block">Metode</label>
                            <input type="text" name="payment_method" value="Cash" class="w-32 bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none">
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-600">Catat</button>
                    </form>
                </div>
            @endif

            <!-- Photo Upload Button -->
            @if(in_array($booking->status, ['confirmed', 'paid', 'dp_paid', 'in_progress', 'checked_in', 'completed', 'delivered', 'closed']))
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-sm text-white">Unggah Hasil Foto Sesi</h3>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $booking->photoResults->count() }} foto sudah diunggah</p>
                    </div>
                    <a href="{{ route('admin.photos.upload', $booking->id) }}" class="px-5 py-2.5 rounded-xl bg-[#1F3A2E] text-[#F2C94C] font-bold text-xs hover:bg-[#152820] flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto
                    </a>
                </div>
            @endif
        </div>

        <!-- Right Payment Summary -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                <h3 class="font-heading font-bold text-sm text-white border-b border-[#2D2B26] pb-3">Rincian Biaya</h3>
                <div class="space-y-2.5 text-xs font-mono text-gray-300">
                    <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
                    @if($booking->addons_total > 0)
                        <div class="flex justify-between"><span>Add-on</span><span>Rp {{ number_format($booking->addons_total, 0, ',', '.') }}</span></div>
                    @endif
                    @if($booking->discount > 0)
                        <div class="flex justify-between text-emerald-400"><span>Diskon</span><span>-Rp {{ number_format($booking->discount, 0, ',', '.') }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-white pt-2 border-t border-[#2D2B26]"><span>TOTAL</span><span class="text-[#F2C94C]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-emerald-400"><span>Sudah Dibayar</span><span>Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span></div>
                    @if($booking->remaining_balance > 0)
                        <div class="flex justify-between text-amber-300 font-bold"><span>Sisa Tagihan</span><span>Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span></div>
                    @endif
                </div>
            </div>

            @if($booking->payments->isNotEmpty())
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-3">
                    <h3 class="font-heading font-bold text-sm text-white border-b border-[#2D2B26] pb-3">Riwayat Transaksi</h3>
                    @foreach($booking->payments as $p)
                        <div class="flex items-center justify-between text-xs font-mono">
                            <div>
                                <p class="font-bold text-white">{{ strtoupper($p->payment_kind) }}</p>
                                <p class="text-gray-500 text-[10px]">{{ $p->payment_type }} · {{ $p->paid_at?->format('d M Y') }}</p>
                            </div>
                            <span class="text-emerald-400 font-bold">Rp {{ number_format($p->gross_amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
