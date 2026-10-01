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
            </div>

            <!-- Update Status Form -->
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                <h3 class="font-heading font-bold text-sm text-white">Update Status Manual</h3>
                <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="flex gap-3 items-end">
                    @csrf @method('PATCH')
                    <select name="status" class="flex-grow bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
                        @foreach(['pending', 'dp_paid', 'paid', 'checked_in', 'completed', 'cancelled', 'expired'] as $s)
                            <option value="{{ $s }}" {{ $booking->status === $s ? 'selected' : '' }}>{{ strtoupper($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16]">Update</button>
                </form>
            </div>

            <!-- Record Cash Payment -->
            @if($booking->remaining_balance > 0)
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <h3 class="font-heading font-bold text-sm text-white">Catat Pembayaran Tunai / EDC</h3>
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
            @if(in_array($booking->status, ['paid', 'dp_paid', 'checked_in', 'completed']))
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
