@extends('layouts.public')

@section('title', 'Detail Booking '.$booking->booking_code.' — SnapSpace')

@section('content')

@php
    $statusMap = [
        'pending'    => ['Menunggu Pembayaran', 'warning', 'fa-clock'],
        'paid'       => ['Dikonfirmasi / Terjadwal', 'success', 'fa-calendar-check'],
        'dp_paid'    => ['DP Dibayar — Sisa Belum Lunas', 'info', 'fa-credit-card'],
        'checked_in' => ['Sedang Berlangsung', 'gold', 'fa-camera'],
        'completed'  => ['Selesai', 'teal', 'fa-circle-check'],
        'cancelled'  => ['Dibatalkan', 'danger', 'fa-ban'],
        'expired'    => ['Kadaluarsa', 'ink-muted', 'fa-clock-rotate-left'],
    ];
    [$statusLabel, $statusVariant, $statusIcon] = $statusMap[$booking->status] ?? ['Unknown', 'ink-muted', 'fa-question'];
    $isPaid = in_array($booking->status, ['paid', 'dp_paid', 'checked_in', 'completed']);
@endphp

{{-- ── HEADER ── --}}
<section class="bg-cream-warm py-8 border-b border-cream-dark">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-6">
            <a href="{{ route('my-bookings.index') }}" class="hover:text-gold transition-colors flex items-center gap-1">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Pesanan Saya
            </a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">{{ $booking->booking_code }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-mono font-bold text-lg text-gold">{{ $booking->booking_code }}</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-{{ $statusVariant }}/15 text-{{ $statusVariant }} border border-{{ $statusVariant }}/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-{{ $statusIcon }} text-[10px]"></i> {{ $statusLabel }}
                    </span>
                </div>
                <p class="text-sm text-ink-muted">{{ $booking->package->name }} · {{ $booking->room->name }}</p>
            </div>

            <div class="flex gap-2 flex-wrap">
                @if($booking->status === 'pending' && !$booking->expires_at?->isPast())
                    <a href="{{ route('booking.pay', $booking->booking_code) }}"
                       class="btn-gold btn-bounce px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-warm flex items-center gap-2">
                        <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                    </a>
                @endif
                @if($booking->status === 'dp_paid')
                    <a href="{{ route('booking.pay', $booking->booking_code) }}"
                       class="px-5 py-2.5 rounded-xl bg-info/10 text-info text-sm font-bold border border-info/20 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill"></i> Lunasi Sisa
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="py-8 bg-cream">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- ══ LEFT COLUMN ══ --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- ── STATUS TIMELINE ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                    <h2 class="font-serif font-semibold text-lg text-ink mb-6">Timeline Status</h2>

                    @php
                        $timeline = [
                            ['status' => 'pending',    'label' => 'Booking Dibuat',        'icon' => 'fa-plus-circle',    'desc' => 'Menunggu pembayaran'],
                            ['status' => 'paid',       'label' => 'Pembayaran Diterima',   'icon' => 'fa-credit-card',    'desc' => 'Slot terkunci, tiket aktif'],
                            ['status' => 'checked_in', 'label' => 'Check-In di Studio',    'icon' => 'fa-qrcode',         'desc' => 'Scan QR berhasil'],
                            ['status' => 'completed',  'label' => 'Sesi Foto Selesai',     'icon' => 'fa-camera',         'desc' => 'Foto sedang diproses'],
                        ];
                        $statusOrder = ['pending', 'dp_paid', 'paid', 'checked_in', 'completed'];
                        $currentIdx  = array_search($booking->status, $statusOrder);
                        if ($currentIdx === false) $currentIdx = -1;
                    @endphp

                    <div class="space-y-0 relative">
                        @foreach($timeline as $i => $t)
                            @php
                                $tIdx    = array_search($t['status'], $statusOrder);
                                $isDone  = $tIdx !== false && $currentIdx >= $tIdx;
                                $isActive = ($booking->status === $t['status']) || ($booking->status === 'dp_paid' && $t['status'] === 'paid');
                            @endphp
                            <div class="flex items-start gap-4 {{ $i < count($timeline)-1 ? 'pb-6' : '' }} relative">
                                {{-- Vertical line --}}
                                @if($i < count($timeline)-1)
                                    <div class="absolute left-4 top-9 w-0.5 h-full {{ $isDone && !$isActive ? 'bg-success' : 'bg-cream-dark' }}"></div>
                                @endif

                                {{-- Dot --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center z-10 flex-shrink-0
                                            {{ $isDone ? ($isActive ? 'bg-gold text-white ring-4 ring-gold/20' : 'bg-success text-white') : 'bg-cream border-2 border-cream-dark text-ink-muted' }}">
                                    @if($isDone && !$isActive)
                                        <i class="fa-solid fa-check text-xs"></i>
                                    @else
                                        <i class="fa-solid fa-{{ $t['icon'] }} text-xs"></i>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <p class="font-semibold text-sm {{ $isDone ? 'text-ink' : 'text-ink-muted' }}">{{ $t['label'] }}</p>
                                    <p class="text-xs text-ink-muted">{{ $t['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(in_array($booking->status, ['cancelled', 'expired']))
                        <div class="mt-4 p-4 rounded-xl bg-danger/5 border border-danger/20 flex items-center gap-3">
                            <i class="fa-solid fa-{{ $booking->status === 'cancelled' ? 'ban' : 'clock-rotate-left' }} text-danger"></i>
                            <p class="text-sm font-semibold text-danger">
                                {{ $booking->status === 'cancelled' ? 'Booking ini telah dibatalkan.' : 'Booking ini telah kedaluwarsa karena melewati batas pembayaran.' }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- ── BOOKING DETAILS ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                    <h2 class="font-serif font-semibold text-lg text-ink mb-5">Detail Booking</h2>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach([
                            ['fa-door-open', '#C8A27A', 'Ruangan', $booking->room->name],
                            ['fa-box-open', '#2F4F4F', 'Paket', $booking->package->name],
                            ['fa-calendar', '#C8A27A', 'Tanggal', $booking->booking_date->translatedFormat('l, d M Y')],
                            ['fa-clock', '#2E9E6B', 'Jam Sesi', substr($booking->start_time, 0, 5).' – '.substr($booking->end_time, 0, 5).' WIB'],
                            ['fa-users', '#2F4F4F', 'Jumlah Orang', $booking->people_count.' orang'],
                            ['fa-timer', '#C8A27A', 'Durasi', $booking->package->duration_minutes.' menit'],
                        ] as [$icon, $color, $label, $val])
                            <div class="p-3.5 rounded-xl bg-cream border border-cream-dark">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fa-solid fa-{{ $icon }} text-xs" style="color: {{ $color }}"></i>
                                    <span class="text-[11px] text-ink-muted">{{ $label }}</span>
                                </div>
                                <p class="font-semibold text-sm text-ink">{{ $val }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if($booking->notes)
                        <div class="mt-4 p-3.5 rounded-xl bg-cream border border-cream-dark">
                            <p class="text-[11px] text-ink-muted mb-1"><i class="fa-solid fa-sticky-note text-gold mr-1"></i> Catatan</p>
                            <p class="text-sm text-ink">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- ── PHOTO GALLERY ── --}}
                @if($booking->photoResults && $booking->photoResults->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="font-serif font-semibold text-lg text-ink">Galeri Foto Anda</h2>
                            <a href="{{ route('photos.zip', $booking->booking_code) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal/10 text-teal text-xs font-bold border border-teal/20 hover:bg-teal/20 transition-colors">
                                <i class="fa-solid fa-download"></i> Unduh Semua (ZIP)
                            </a>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @foreach($booking->photoResults as $photo)
                                <div class="relative group aspect-square rounded-xl overflow-hidden bg-cream-dark border border-cream-dark">
                                    <img src="{{ Storage::url($photo->path) }}" alt="{{ $photo->original_name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                    <div class="absolute inset-0 bg-ink/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <a href="{{ route('photos.download', $photo->id) }}"
                                           class="w-9 h-9 rounded-full bg-white/90 flex items-center justify-center hover:bg-white transition-colors">
                                            <i class="fa-solid fa-download text-ink text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($booking->photoDelivery)
                            <div class="mt-4 p-3 rounded-xl bg-cream border border-cream-dark text-xs text-ink-muted flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-gold"></i>
                                Link galeri aktif hingga {{ $booking->photoDelivery->expires_at?->translatedFormat('d M Y') ?? '30 hari setelah sesi' }}.
                                @if($booking->photoDelivery->download_count > 0)
                                    Sudah diunduh {{ $booking->photoDelivery->download_count }}x.
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ── REVIEW SECTION ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7" id="review">
                    @if($booking->review)
                        <h2 class="font-serif font-semibold text-lg text-ink mb-4">Ulasan Anda</h2>
                        <div class="flex gap-1 text-gold text-lg mb-3">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $booking->review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="text-sm text-ink-soft leading-relaxed italic">"{{ $booking->review->comment }}"</p>
                        <p class="text-xs text-ink-muted mt-3">Dikirim {{ $booking->review->created_at->translatedFormat('d M Y H:i') }}</p>
                    @elseif(in_array($booking->status, ['completed', 'checked_in', 'paid']))
                        <h2 class="font-serif font-semibold text-lg text-ink mb-5">Bagaimana Pengalaman Anda?</h2>
                        <form method="POST" action="{{ route('my-bookings.review', $booking->booking_code) }}"
                              x-data="{ rating: 5, comment: '' }">
                            @csrf
                            {{-- Star Rating --}}
                            <div class="flex gap-1 mb-5">
                                @for($i=1; $i<=5; $i++)
                                    <button type="button" @click="rating = {{ $i }}"
                                            class="text-3xl transition-colors"
                                            :class="rating >= {{ $i }} ? 'text-gold' : 'text-cream-dark'">
                                        <i class="fa-solid fa-star"></i>
                                    </button>
                                @endfor
                                <input type="hidden" name="rating" :value="rating">
                            </div>
                            <textarea name="comment" x-model="comment" rows="3" required
                                      placeholder="Ceritakan pengalaman sesi foto Anda..."
                                      class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all resize-none mb-4"></textarea>
                            <button type="submit" :disabled="!comment"
                                    class="btn-gold btn-bounce px-6 py-3 rounded-xl text-white font-bold text-sm shadow-warm disabled:opacity-40">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Ulasan
                            </button>
                        </form>
                    @else
                        <div class="text-center py-6">
                            <i class="fa-regular fa-comment-dots text-3xl text-cream-dark mb-3 block"></i>
                            <p class="text-sm text-ink-muted">Ulasan dapat diberikan setelah sesi foto selesai.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ RIGHT COLUMN ══ --}}
            <div class="lg:col-span-5 space-y-5">

                {{-- ── E-TICKET QR ── --}}
                @if($isPaid)
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7 text-center">
                        <div class="w-12 h-12 rounded-xl bg-gold flex items-center justify-center mx-auto mb-4 shadow-warm">
                            <i class="fa-solid fa-ticket text-white text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-gold uppercase tracking-widest mb-2">E-Tiket Anda</p>
                        <h3 class="font-serif font-bold text-2xl text-ink mb-4">{{ $booking->booking_code }}</h3>

                        {{-- QR Code --}}
                        <div class="bg-cream rounded-2xl p-5 border border-cream-dark inline-block mb-4">
                            <div class="w-40 h-40 mx-auto" id="qr-code"></div>
                        </div>

                        <p class="text-xs text-ink-muted mb-4">Tunjukkan QR ini ke staff saat check-in di studio.</p>

                        <div class="flex flex-col gap-2">
                            <a href="#" onclick="window.print(); return false;"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-cream-dark text-ink text-sm font-semibold hover:border-gold hover:text-gold transition-colors">
                                <i class="fa-solid fa-print text-xs"></i> Cetak Tiket
                            </a>
                            <a href="https://wa.me/?text={{ urlencode('🎬 Booking SnapSpace: '.$booking->booking_code."\n📅 ".$booking->booking_date->format('d/m/Y').' '.$booking->start_time."\n📍 Studio: ".$booking->room->name) }}"
                               target="_blank" rel="noopener"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-success/10 text-success text-sm font-semibold border border-success/20 hover:bg-success/20 transition-colors">
                                <i class="fa-brands fa-whatsapp"></i> Share via WA
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ── PRICE BREAKDOWN ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                    <h3 class="font-serif font-semibold text-lg text-ink mb-4">Rincian Harga</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-1.5">
                            <span class="text-ink-muted">Paket {{ $booking->package->name }}</span>
                            <span class="text-ink">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                        </div>
                        @if($booking->addon_total > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Add-ons</span>
                                <span class="text-ink">Rp {{ number_format($booking->addon_total, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->markup_amount > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Markup</span>
                                <span class="text-warning">+Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->discount_amount > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Diskon</span>
                                <span class="text-success">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between py-2 border-t-2 border-cream-dark">
                            <span class="font-bold text-ink">Total</span>
                            <span class="font-serif font-bold text-xl text-gold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>

                        @if($booking->paid_amount > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Sudah dibayar</span>
                                <span class="text-success font-semibold">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->status === 'dp_paid')
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Sisa pembayaran</span>
                                <span class="text-danger font-semibold">Rp {{ number_format($booking->total_price - $booking->paid_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── PAYMENT HISTORY ── --}}
                @if($booking->payments->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <h3 class="font-serif font-semibold text-lg text-ink mb-4">Riwayat Pembayaran</h3>
                        <div class="space-y-3">
                            @foreach($booking->payments as $pay)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-cream border border-cream-dark">
                                    <div class="w-9 h-9 rounded-xl {{ $pay->status === 'paid' ? 'bg-success/15' : 'bg-warning/15' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-{{ $pay->status === 'paid' ? 'check' : 'clock' }} text-xs {{ $pay->status === 'paid' ? 'text-success' : 'text-warning' }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-xs text-ink">Rp {{ number_format($pay->amount, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-ink-muted">{{ ucfirst($pay->method ?? 'N/A') }} · {{ $pay->created_at->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                    <span class="text-[11px] font-bold {{ $pay->status === 'paid' ? 'text-success' : 'text-warning' }}">
                                        {{ $pay->status === 'paid' ? 'Sukses' : 'Pending' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── QUICK ACTIONS ── --}}
                <div class="bg-white rounded-2xl border border-cream-dark shadow-card p-5 space-y-2">
                    <a href="https://wa.me/6281234567890?text=Halo%20SnapSpace%2C%20saya%20ingin%20bertanya%20tentang%20booking%20{{ $booking->booking_code }}"
                       target="_blank" rel="noopener"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
                        <i class="fa-brands fa-whatsapp text-success"></i>
                        <span class="text-sm font-semibold text-ink">Chat Admin</span>
                        <i class="fa-solid fa-arrow-right text-xs text-ink-muted ml-auto"></i>
                    </a>
                    @if(in_array($booking->status, ['paid', 'dp_paid']))
                        <a href="https://calendar.google.com/calendar/event?action=TEMPLATE&text={{ urlencode('📸 SnapSpace — '.$booking->package->name) }}&dates={{ $booking->booking_date->format('Ymd') }}T{{ str_replace(':', '', substr($booking->start_time, 0, 5)) }}00/{{ $booking->booking_date->format('Ymd') }}T{{ str_replace(':', '', substr($booking->end_time, 0, 5)) }}00&details={{ urlencode('Kode: '.$booking->booking_code) }}&location={{ urlencode('SnapSpace Studio Surabaya') }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
                            <i class="fa-solid fa-calendar-plus text-gold"></i>
                            <span class="text-sm font-semibold text-ink">Tambah ke Google Calendar</span>
                            <i class="fa-solid fa-arrow-right text-xs text-ink-muted ml-auto"></i>
                        </a>
                    @endif
                    <a href="{{ route('booking.wizard') }}"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
                        <i class="fa-solid fa-rotate-right text-teal"></i>
                        <span class="text-sm font-semibold text-ink">Pesan Lagi</span>
                        <i class="fa-solid fa-arrow-right text-xs text-ink-muted ml-auto"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qrContainer = document.getElementById('qr-code');
    if (qrContainer) {
        QRCode.toCanvas(document.createElement('canvas'), '{{ $booking->booking_code }}', {
            width: 160,
            margin: 1,
            color: { dark: '#1A1A1A', light: '#F8F6F3' },
        }, function (err, canvas) {
            if (!err) {
                canvas.style.cssText = 'width:100%;height:auto;border-radius:8px;';
                qrContainer.appendChild(canvas);
            }
        });
    }
});
</script>
@endpush
