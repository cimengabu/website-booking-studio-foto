@extends('layouts.app')

@section('title', 'Detail Booking {{ $booking->booking_code }} — SnapSpace Studio')

@section('content')
<div class="py-12 max-w-5xl mx-auto px-4 space-y-8">

    <!-- Back + Status Header -->
    <div class="flex items-start gap-4">
        <a href="{{ route('my-bookings.index') }}" class="p-2.5 rounded-xl bg-[#1E1D1A] border border-[#2D2B26] text-gray-400 hover:text-white">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="font-heading font-extrabold text-2xl text-white">{{ $booking->booking_code }}</h1>
            <p class="text-xs font-mono text-gray-400">{{ $booking->room->name }} · {{ $booking->package->name }} · {{ $booking->booking_date->format('d M Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Detail Info -->
        <div class="lg:col-span-7 space-y-6">

            <!-- QR Ticket Card -->
            @if(in_array($booking->status, ['paid', 'dp_paid', 'checked_in', 'completed']))
                <div class="bg-[#1E1D1A] border-2 border-dashed border-[#2D2B26] rounded-3xl p-8 text-center space-y-4">
                    <div class="inline-block px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 font-mono text-xs font-bold uppercase">
                        <i class="fa-solid fa-ticket mr-1.5"></i> E-TIKET QR AKTIF
                    </div>
                    <!-- QR Code Display -->
                    <div class="mx-auto w-48 h-48 bg-white rounded-2xl flex items-center justify-center text-gray-800 p-4">
                        <div class="text-center">
                            <div class="grid grid-cols-3 gap-0.5 w-32 h-32 mx-auto mb-1">
                                @for($i = 0; $i < 9; $i++)
                                    <div class="rounded-sm {{ in_array($i, [0,2,6,8,1,3,4,5,7]) ? 'bg-gray-800' : 'bg-white' }}"></div>
                                @endfor
                            </div>
                            <p class="text-[8px] font-mono text-gray-600 break-all leading-tight">{{ $booking->booking_code }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 font-mono">Tunjukkan kode ini ke Staff saat tiba di studio</p>
                    <p class="font-mono text-[10px] bg-[#141311] rounded-xl py-2 px-4 text-[#F2C94C] font-bold">TOKEN: {{ $booking->qr_token }}</p>
                </div>
            @endif

            <!-- Booking Info Details -->
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">Detail Sesi Foto</h3>
                <div class="grid grid-cols-2 gap-4 text-xs font-mono">
                    <div><p class="text-gray-400">Ruangan / Tema</p><p class="font-bold text-white mt-0.5">{{ $booking->room->name }}</p></div>
                    <div><p class="text-gray-400">Paket</p><p class="font-bold text-white mt-0.5">{{ $booking->package->name }}</p></div>
                    <div><p class="text-gray-400">Tanggal Sesi</p><p class="font-bold text-[#F2C94C] mt-0.5">{{ $booking->booking_date->format('l, d M Y') }}</p></div>
                    <div><p class="text-gray-400">Jam</p><p class="font-bold text-white mt-0.5">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }} WIB</p></div>
                    <div><p class="text-gray-400">Jumlah Orang</p><p class="font-bold text-white mt-0.5">{{ $booking->people_count }} orang</p></div>
                    <div><p class="text-gray-400">Status Check-In</p><p class="font-bold mt-0.5 {{ $booking->checked_in_at ? 'text-emerald-400' : 'text-gray-500' }}">{{ $booking->checked_in_at ? $booking->checked_in_at->format('H:i d M Y') : 'Belum Check-In' }}</p></div>
                </div>
                @if($booking->notes)
                    <div class="border-t border-[#2D2B26] pt-4">
                        <p class="text-[10px] text-gray-400 font-mono mb-1">Catatan Booking:</p>
                        <p class="text-xs text-gray-200 italic">"{{ $booking->notes }}"</p>
                    </div>
                @endif
            </div>

            <!-- Foto Hasil Galeri -->
            @if($booking->photoResults->isNotEmpty())
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                        <h3 class="font-heading font-bold text-lg text-white">Galeri Foto Hasil Sesi</h3>
                        <a href="{{ route('photos.zip', $booking->booking_code) }}" class="px-4 py-2 rounded-full bg-[#1F3A2E] text-[#F2C94C] font-bold text-xs hover:bg-emerald-900 flex items-center gap-2">
                            <i class="fa-solid fa-file-zipper"></i> Unduh Semua (ZIP)
                        </a>
                    </div>

                    @if($booking->photoDelivery)
                        <div class="text-[11px] font-mono text-emerald-300 bg-emerald-950/40 border border-emerald-800/40 rounded-2xl px-4 py-2.5 flex items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-down"></i>
                            Link aktif s/d {{ $booking->photoDelivery->expires_at?->format('d M Y') }} · Telah diunduh {{ $booking->photoDelivery->download_count }}x
                        </div>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($booking->photoResults as $photo)
                            <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gray-900 border border-[#2D2B26]">
                                <img src="{{ $photo->path }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="{{ $photo->original_name }}">
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a href="{{ route('photos.download', $photo->id) }}" class="px-4 py-2 rounded-full bg-white text-black font-bold text-xs">
                                        <i class="fa-solid fa-download mr-1.5"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Review Section -->
            @if(in_array($booking->status, ['completed', 'checked_in']))
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                    <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">
                        {{ $booking->review ? 'Ulasan Anda' : 'Beri Ulasan & Rating' }}
                    </h3>

                    @if($booking->review)
                        <div class="space-y-2">
                            <div class="flex items-center gap-1 text-[#F2C94C] text-lg">
                                @for($i = 1; $i <= $booking->review->rating; $i++) <i class="fa-solid fa-star"></i> @endfor
                            </div>
                            <p class="text-xs text-gray-300 italic">"{{ $booking->review->comment }}"</p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('my-bookings.review', $booking->booking_code) }}" x-data="{ rating: 5 }" class="space-y-4">
                            @csrf
                            <div>
                                <p class="text-xs font-semibold text-gray-300 mb-2">Bintang Rating:</p>
                                <div class="flex gap-2 text-3xl">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="rating = {{ $i }}" class="transition-transform hover:scale-110" :class="rating >= {{ $i }} ? 'text-[#F2C94C]' : 'text-gray-600'">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" x-model="rating">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-300 mb-1.5 block">Komentar Jujur Kamu:</label>
                                <textarea name="comment" required placeholder="Ceritakan pengalaman sesi foto Anda di SnapSpace..." rows="3" class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white text-xs px-4 py-3 rounded-xl resize-none focus:outline-none"></textarea>
                            </div>
                            <button type="submit" class="px-6 py-3 rounded-full bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16] shadow-lg">
                                Kirim Ulasan <i class="fa-solid fa-paper-plane ml-1.5"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Payment Summary -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">Ringkasan Biaya</h3>
                <div class="space-y-2.5 text-xs font-mono text-gray-300">
                    <div class="flex justify-between"><span>Harga Paket</span><span>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
                    @if($booking->addons_total > 0)
                        <div class="flex justify-between"><span>Add-on</span><span>Rp {{ number_format($booking->addons_total, 0, ',', '.') }}</span></div>
                    @endif
                    @if($booking->discount > 0)
                        <div class="flex justify-between text-emerald-400"><span>Diskon</span><span>-Rp {{ number_format($booking->discount, 0, ',', '.') }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-white pt-2 border-t border-[#2D2B26]"><span>TOTAL</span><span class="text-[#F2C94C] font-heading text-xl">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-emerald-400"><span>Sudah Dibayar</span><span>Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span></div>
                    @if($booking->remaining_balance > 0)
                        <div class="flex justify-between text-amber-300 font-bold"><span>Sisa Tagihan</span><span>Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span></div>
                    @endif
                </div>

                @if($booking->status === 'dp_paid' && $booking->remaining_balance > 0)
                    <a href="{{ route('booking.pay', $booking->booking_code) }}" class="block w-full py-3.5 rounded-full bg-[#E8451E] text-white font-bold text-xs text-center hover:bg-[#d03a16] shadow-lg">
                        Lunasi Sisa Pembayaran <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                @endif
            </div>

            <!-- Payment History -->
            @if($booking->payments->isNotEmpty())
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-4">
                    <h3 class="font-heading font-bold text-sm text-white border-b border-[#2D2B26] pb-4">Riwayat Transaksi</h3>
                    <div class="space-y-3">
                        @foreach($booking->payments as $pay)
                            <div class="flex items-center justify-between text-xs font-mono">
                                <div>
                                    <p class="font-bold text-white">{{ strtoupper($pay->payment_kind) }} — {{ $pay->payment_type }}</p>
                                    <p class="text-gray-500 text-[10px]">{{ $pay->paid_at?->format('d M Y, H:i') ?? 'Belum terbayar' }}</p>
                                </div>
                                <span class="font-bold {{ $pay->transaction_status === 'settlement' ? 'text-emerald-400' : 'text-amber-300' }}">
                                    Rp {{ number_format($pay->gross_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
