@extends('layouts.app')

@section('title', 'Pembayaran Booking {{ $booking->booking_code }} — SnapSpace Studio')

@section('content')
<div class="py-12 max-w-4xl mx-auto px-4">

    <div class="text-center space-y-2 mb-10">
        <h1 class="font-heading font-extrabold text-3xl text-white">Selesaikan Pembayaran</h1>
        <p class="text-xs font-mono text-gray-400">Kode Booking: <span class="text-[#F2C94C] font-bold">{{ $booking->booking_code }}</span></p>
    </div>

    @if($booking->status === 'expired')
        <div class="bg-rose-950/80 border border-rose-500/40 text-rose-200 p-8 rounded-3xl text-center space-y-4">
            <i class="fa-solid fa-clock-rotate-left text-5xl text-rose-400"></i>
            <h2 class="font-heading font-bold text-2xl">Waktu Pembayaran Berakhir</h2>
            <p class="text-sm">Maaf, slot waktu yang Anda pesan sudah otomatis dilepas karena melewati batas 15 menit. Silakan buat booking baru.</p>
            <a href="{{ route('booking.wizard') }}" class="inline-block px-6 py-3 rounded-full bg-[#E8451E] text-white font-bold text-sm mt-2">
                Buat Booking Baru
            </a>
        </div>
    @elseif(in_array($booking->status, ['paid', 'dp_paid']))
        <div class="bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 p-8 rounded-3xl text-center space-y-4">
            <i class="fa-solid fa-circle-check text-5xl text-emerald-400"></i>
            <h2 class="font-heading font-bold text-2xl">Pembayaran Sudah Diterima!</h2>
            <p class="text-sm">Booking Anda berstatus <strong>{{ strtoupper($booking->status) }}</strong>. E-Tiket QR sudah disiapkan.</p>
            <a href="{{ route('my-bookings.show', $booking->booking_code) }}" class="inline-block px-6 py-3 rounded-full bg-emerald-600 text-white font-bold text-sm mt-2">
                Lihat E-Tiket QR Saya
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Order Summary Left -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                    <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">Detail Booking</h3>
                    
                    <div class="space-y-3 text-xs font-mono text-gray-300">
                        <div class="flex justify-between"><span>Ruangan:</span><span class="font-bold text-white">{{ $booking->room->name }}</span></div>
                        <div class="flex justify-between"><span>Paket:</span><span class="font-bold text-white">{{ $booking->package->name }}</span></div>
                        <div class="flex justify-between"><span>Tanggal Sesi:</span><span class="text-[#F2C94C]">{{ $booking->booking_date->translatedFormat('D, d M Y') }}</span></div>
                        <div class="flex justify-between"><span>Jam Sesi:</span><span class="font-bold text-white">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</span></div>
                        <div class="flex justify-between"><span>Jumlah Orang:</span><span>{{ $booking->people_count }} orang</span></div>
                    </div>

                    <div class="border-t border-[#2D2B26] pt-4 space-y-2 text-xs font-mono">
                        <div class="flex justify-between text-gray-400"><span>Subtotal Paket</span><span>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span></div>
                        @if($booking->addons_total > 0)
                            <div class="flex justify-between text-gray-400"><span>Total Add-on</span><span>Rp {{ number_format($booking->addons_total, 0, ',', '.') }}</span></div>
                        @endif
                        @if($booking->discount > 0)
                            <div class="flex justify-between text-emerald-400"><span>Diskon/Promo</span><span>-Rp {{ number_format($booking->discount, 0, ',', '.') }}</span></div>
                        @endif
                        <div class="flex justify-between font-bold text-white border-t border-[#2D2B26] pt-2">
                            <span>TOTAL</span>
                            <span class="text-[#F2C94C] font-heading text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($booking->status === 'pending')
                        <!-- 15-min countdown -->
                        <div class="bg-amber-950/60 border border-amber-800/40 rounded-2xl p-4 text-center">
                            <p class="text-[10px] font-mono text-amber-300 uppercase font-bold mb-1">Slot Dikunci — Bayar Sebelum:</p>
                            <div id="countdown-timer" class="font-heading font-black text-2xl text-amber-300"></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Payment Form -->
            <div class="lg:col-span-7">
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-6">
                    <h3 class="font-heading font-bold text-lg text-white border-b border-[#2D2B26] pb-4">Pilih Metode Pembayaran (Simulasi Sandbox)</h3>

                    <form method="POST" action="{{ route('booking.pay-simulated', $booking->booking_code) }}" class="space-y-5">
                        @csrf

                        <!-- Payment kind: DP or Full -->
                        <div>
                            <p class="text-xs font-semibold text-gray-300 mb-3">Opsi Pembayaran:</p>
                            <div class="grid grid-cols-2 gap-3">
                                @if($booking->payment_option === 'dp')
                                    <label class="p-4 rounded-2xl border border-[#E8451E] bg-[#E8451E]/10 cursor-pointer flex items-center gap-3">
                                        <input type="radio" name="payment_kind" value="dp" checked class="text-[#E8451E] focus:ring-0">
                                        <div>
                                            <p class="font-bold text-xs text-white">DP 50%</p>
                                            <p class="text-sm font-heading font-black text-[#F2C94C]">Rp {{ number_format($booking->total_price * 0.5, 0, ',', '.') }}</p>
                                        </div>
                                    </label>
                                    <label class="p-4 rounded-2xl border border-[#2D2B26] bg-[#141311] cursor-pointer flex items-center gap-3">
                                        <input type="radio" name="payment_kind" value="full" class="text-[#E8451E] focus:ring-0">
                                        <div>
                                            <p class="font-bold text-xs text-white">Lunas 100%</p>
                                            <p class="text-sm font-heading font-black text-[#F2C94C]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                        </div>
                                    </label>
                                @else
                                    <label class="col-span-2 p-4 rounded-2xl border border-[#E8451E] bg-[#E8451E]/10 cursor-pointer flex items-center gap-3">
                                        <input type="radio" name="payment_kind" value="full" checked class="text-[#E8451E] focus:ring-0">
                                        <div>
                                            <p class="font-bold text-xs text-white">Lunas — Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                        </div>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <div>
                            <p class="text-xs font-semibold text-gray-300 mb-3">Metode Bayar:</p>
                            <div class="grid grid-cols-2 gap-3 text-xs font-mono font-bold">
                                @foreach(['QRIS Midtrans', 'VA BCA', 'VA Mandiri', 'GoPay', 'OVO', 'Dana'] as $method)
                                    <label class="p-3 rounded-2xl border border-[#2D2B26] bg-[#141311] hover:border-[#E8451E] cursor-pointer flex items-center gap-2">
                                        <input type="radio" name="payment_method" value="{{ $method }}" {{ $loop->first ? 'checked' : '' }} class="text-[#E8451E] focus:ring-0">
                                        <span class="text-gray-300">{{ $method }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sandbox note -->
                        <div class="bg-[#141311] border border-dashed border-[#2D2B26] p-4 rounded-2xl text-[11px] text-gray-400 font-mono">
                            <i class="fa-solid fa-flask text-[#F2C94C] mr-1.5"></i>
                            <strong class="text-[#F2C94C]">Mode Sandbox Demo:</strong> Klik tombol di bawah untuk mensimulasikan pembayaran berhasil & mendapatkan e-tiket QR.
                        </div>

                        <button type="submit" class="w-full py-4 rounded-full bg-[#E8451E] text-white font-extrabold text-sm hover:bg-[#d03a16] shadow-xl shadow-[#E8451E]/30 transition-all flex items-center justify-center gap-3">
                            <i class="fa-solid fa-circle-check text-lg"></i> Simulasikan Pembayaran Berhasil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@if($booking->status === 'pending')
<script>
    const expiresAt = new Date("{{ $booking->expires_at->toISOString() }}");
    function updateCountdown() {
        const now = new Date();
        const diff = expiresAt - now;
        if (diff <= 0) {
            document.getElementById('countdown-timer').textContent = '00:00';
            setTimeout(() => location.reload(), 1500);
            return;
        }
        const mins = Math.floor(diff / 60000);
        const secs = Math.floor((diff % 60000) / 1000);
        document.getElementById('countdown-timer').textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endif
@endsection
