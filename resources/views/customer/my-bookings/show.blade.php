@extends('layouts.dashboard')

@section('title', 'Detail Booking '.$booking->booking_code.' — SnapSpace')
@section('breadcrumb', 'Detail Booking #'.$booking->booking_code)

@section('content')

@php
    $statusMap = [
        'pending'     => ['Menunggu Pembayaran', 'warning', 'fa-clock'],
        'confirmed'   => ['Dikonfirmasi', 'success', 'fa-calendar-check'],
        'paid'        => ['Dikonfirmasi (Lunas)', 'success', 'fa-calendar-check'],
        'dp_paid'     => ['Dikonfirmasi (DP 50%)', 'info', 'fa-credit-card'],
        'in_progress' => ['Sedang Berlangsung', 'gold', 'fa-camera'],
        'checked_in'  => ['Sedang Berlangsung', 'gold', 'fa-camera'],
        'completed'   => ['Selesai Sesi', 'teal', 'fa-circle-check'],
        'delivered'   => ['Foto Siap Diunduh', 'teal', 'fa-images'],
        'closed'      => ['Ditutup', 'ink-muted', 'fa-box-archive'],
        'rescheduled' => ['Reschedule', 'gold', 'fa-calendar-days'],
        'cancelled'   => ['Dibatalkan', 'danger', 'fa-ban'],
        'expired'     => ['Kedaluwarsa', 'ink-muted', 'fa-clock-rotate-left'],
        'no_show'     => ['No-Show (Hangus)', 'danger', 'fa-user-xmark'],
    ];
    [$statusLabel, $statusVariant, $statusIcon] = $statusMap[$booking->status] ?? ['Unknown', 'ink-muted', 'fa-question'];
    $isPaid = in_array($booking->status, ['confirmed', 'paid', 'dp_paid', 'in_progress', 'checked_in', 'completed', 'delivered', 'closed']);
    $refundCalc = $booking->calculateCancellationRefund();
@endphp

<div x-data="{
    showRescheduleModal: false,
    showCancelModal: false,
    rescheduleDate: '',
    rescheduleSlots: [],
    selectedSlot: '',
    loadingSlots: false,
    slotError: '',
    fetchSlots() {
        if (!this.rescheduleDate) return;
        this.loadingSlots = true;
        this.slotError = '';
        this.selectedSlot = '';
        fetch(`{{ route('api.slots') }}?date=${this.rescheduleDate}&room_id={{ $booking->room_id }}&package_id={{ $booking->package_id }}`)
            .then(res => res.json())
            .then(data => {
                this.loadingSlots = false;
                if (data.success) {
                    this.rescheduleSlots = data.slots || [];
                } else {
                    this.slotError = data.message || 'Gagal memuat slot jadwal.';
                }
            })
            .catch(err => {
                this.loadingSlots = false;
                this.slotError = 'Terjadi kesalahan saat memuat slot.';
            });
    }
}">

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

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-success/10 border border-success/20 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-success text-base flex-shrink-0"></i>
                <p class="text-sm font-semibold text-success">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-danger/10 border border-danger/20">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-danger"></i>
                    <p class="text-sm font-bold text-danger">Perhatian:</p>
                </div>
                <ul class="text-xs text-danger list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <span class="font-mono font-bold text-lg text-gold">{{ $booking->booking_code }}</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-{{ $statusVariant }}/15 text-{{ $statusVariant }} border border-{{ $statusVariant }}/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-{{ $statusIcon }} text-[10px]"></i> {{ $statusLabel }}
                    </span>
                    @if($booking->reschedule_count > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gold/15 text-gold border border-gold/20">
                            Reschedule {{ $booking->reschedule_count }}x
                        </span>
                    @endif
                </div>
                <p class="text-sm text-ink-muted">{{ $booking->package->name }} · {{ $booking->room->name }}</p>
            </div>

            <div class="flex gap-2 flex-wrap items-center">
                {{-- Bayar DP / Pelunasan Awal --}}
                @if($booking->status === 'pending' && !$booking->expires_at?->isPast())
                    <a href="{{ route('booking.pay', $booking->booking_code) }}"
                       class="btn-gold btn-bounce px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow-warm flex items-center gap-2">
                        <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                    </a>
                @endif

                {{-- Pelunasan Sisa Sebelum Sesi --}}
                @if($booking->remaining_balance > 0 && in_array($booking->status, ['confirmed', 'dp_paid', 'paid']))
                    <a href="{{ route('booking.pay', $booking->booking_code) }}"
                       class="px-5 py-2.5 rounded-xl bg-gold/15 text-gold hover:bg-gold hover:text-white transition-all text-sm font-bold border border-gold/30 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave"></i> Bayar Pelunasan (Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }})
                    </a>
                @endif

                {{-- Tombol Ajukan Reschedule --}}
                @if($booking->isReschedulable())
                    <button type="button" @click="showRescheduleModal = true"
                            class="px-4 py-2.5 rounded-xl bg-white hover:bg-cream border border-cream-dark text-ink text-sm font-semibold transition-colors flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-calendar-days text-gold"></i> Ajukan Reschedule
                    </button>
                @endif

                {{-- Tombol Batalkan Booking --}}
                @if($booking->isCancellable())
                    <button type="button" @click="showCancelModal = true"
                            class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-danger text-sm font-semibold transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-ban"></i> Batalkan
                    </button>
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

                {{-- ── STATUS TIMELINE (6 TAHAPAN RESMI & CABANG) ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="font-serif font-semibold text-lg text-ink">Alur & Status Booking</h2>
                        <span class="text-xs text-ink-muted">Siklus Resmi SnapSpace</span>
                    </div>

                    @php
                        // 6 linear stages:
                        // 1. pending
                        // 2. confirmed (includes dp_paid / paid)
                        // 3. in_progress (includes checked_in)
                        // 4. completed
                        // 5. delivered
                        // 6. closed
                        $lifecycleStages = [
                            [
                                'key' => 'pending',
                                'label' => '1. Menunggu Pembayaran',
                                'icon' => 'fa-clock',
                                'desc' => 'Menunggu pembayaran DP / Full dari pelanggan.',
                            ],
                            [
                                'key' => 'confirmed',
                                'label' => '2. Dikonfirmasi',
                                'icon' => 'fa-calendar-check',
                                'desc' => 'Slot jadwal terkunci, E-Tiket aktif & QR code terbit.',
                            ],
                            [
                                'key' => 'in_progress',
                                'label' => '3. Sedang Berlangsung',
                                'icon' => 'fa-camera',
                                'desc' => 'Check-in studio berhasil, sesi foto sedang berjalan.',
                            ],
                            [
                                'key' => 'completed',
                                'label' => '4. Selesai Sesi',
                                'icon' => 'fa-circle-check',
                                'desc' => 'Pemotretan selesai, tim studio mengedit & kurasi foto.',
                            ],
                            [
                                'key' => 'delivered',
                                'label' => '5. Foto Siap (Delivery)',
                                'icon' => 'fa-images',
                                'desc' => 'Hasil foto siap diunduh secara online & ZIP.',
                            ],
                            [
                                'key' => 'closed',
                                'label' => '6. Ditutup',
                                'icon' => 'fa-box-archive',
                                'desc' => 'Pesanan selesai sepenuhnya, terima kasih atas ulasannya.',
                            ],
                        ];

                        $statusWeight = [
                            'pending'     => 1,
                            'confirmed'   => 2,
                            'paid'        => 2,
                            'dp_paid'     => 2,
                            'in_progress' => 3,
                            'checked_in'  => 3,
                            'completed'   => 4,
                            'delivered'   => 5,
                            'closed'      => 6,
                        ];

                        $currentWeight = $statusWeight[$booking->status] ?? 0;
                        $isBranchStatus = in_array($booking->status, ['cancelled', 'expired', 'no_show']);
                    @endphp

                    <div class="space-y-0 relative">
                        @foreach($lifecycleStages as $i => $stage)
                            @php
                                $stageWeight = $statusWeight[$stage['key']];
                                $isDone = !$isBranchStatus && ($currentWeight > $stageWeight);
                                $isCurrent = !$isBranchStatus && (
                                    $booking->status === $stage['key'] ||
                                    ($stage['key'] === 'confirmed' && in_array($booking->status, ['paid', 'dp_paid'])) ||
                                    ($stage['key'] === 'in_progress' && $booking->status === 'checked_in')
                                );
                            @endphp
                            <div class="flex items-start gap-4 {{ $i < count($lifecycleStages)-1 ? 'pb-6' : '' }} relative">
                                {{-- Line --}}
                                @if($i < count($lifecycleStages)-1)
                                    <div class="absolute left-4 top-8 w-0.5 h-full {{ $isDone ? 'bg-success' : 'bg-cream-dark' }}"></div>
                                @endif

                                {{-- Node --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center z-10 flex-shrink-0
                                            {{ $isDone ? 'bg-success text-white' : ($isCurrent ? 'bg-gold text-white ring-4 ring-gold/20 shadow-warm' : 'bg-cream border-2 border-cream-dark text-ink-muted') }}">
                                    @if($isDone)
                                        <i class="fa-solid fa-check text-xs"></i>
                                    @else
                                        <i class="fa-solid {{ $stage['icon'] }} text-xs"></i>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-sm {{ $isDone || $isCurrent ? 'text-ink' : 'text-ink-muted' }}">{{ $stage['label'] }}</p>
                                        @if($isCurrent)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gold/15 text-gold border border-gold/20 animate-pulse">Aktif</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-ink-muted mt-0.5">{{ $stage['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Cabang Alerts (Dibatalkan, Expired, No-Show, Reschedule Info) --}}
                    @if($booking->status === 'cancelled')
                        <div class="mt-6 p-4 rounded-2xl bg-danger/10 border border-danger/20 space-y-2">
                            <div class="flex items-center gap-2 text-danger font-bold text-sm">
                                <i class="fa-solid fa-ban"></i> Booking Dibatalkan
                            </div>
                            <p class="text-xs text-danger/90">
                                Alasan: <span class="font-medium italic">"{{ $booking->cancellation_reason ?? 'Dibatalkan' }}"</span>
                            </p>
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-danger/20 text-xs">
                                <div>
                                    <span class="text-ink-muted">Dana Dikembalikan (Refund):</span>
                                    <p class="font-bold text-success text-sm">Rp {{ number_format($booking->refund_amount ?? 0, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="text-ink-muted">Biaya Penalti / Administrasi:</span>
                                    <p class="font-bold text-danger text-sm">Rp {{ number_format($booking->penalty_amount ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif($booking->status === 'expired')
                        <div class="mt-6 p-4 rounded-2xl bg-ink-muted/10 border border-ink-muted/20 flex items-center gap-3">
                            <i class="fa-solid fa-clock-rotate-left text-ink-muted text-lg"></i>
                            <div>
                                <p class="text-sm font-bold text-ink">Booking Kedaluwarsa</p>
                                <p class="text-xs text-ink-muted">Pembayaran tidak diselesaikan dalam batas waktu yang ditentukan. Slot telah dirilis kembali.</p>
                            </div>
                        </div>
                    @elseif($booking->status === 'no_show')
                        <div class="mt-6 p-4 rounded-2xl bg-danger/10 border border-danger/20 space-y-2">
                            <div class="flex items-center gap-2 text-danger font-bold text-sm">
                                <i class="fa-solid fa-user-xmark"></i> Status: No-Show (Tidak Hadir)
                            </div>
                            <p class="text-xs text-danger/90">
                                Pelanggan tidak hadir pada waktu sesi yang telah ditentukan. Sesuai dengan aturan bisnis studio, pembayaran yang telah masuk dinyatakan hangus dan tidak dapat dikembalikan.
                            </p>
                        </div>
                    @endif

                    @if($booking->original_booking_date)
                        <div class="mt-4 p-3.5 rounded-xl bg-gold/10 border border-gold/20 flex items-start gap-2.5">
                            <i class="fa-solid fa-calendar-days text-gold text-sm mt-0.5"></i>
                            <div class="text-xs text-ink">
                                <span class="font-bold">Riwayat Reschedule:</span>
                                Jadwal semula adalah <strong>{{ \Carbon\Carbon::parse($booking->original_booking_date)->translatedFormat('l, d M Y') }}</strong> jam <strong>{{ substr($booking->original_start_time, 0, 5) }} WIB</strong>.
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── BOOKING DETAILS ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                    <h2 class="font-serif font-semibold text-lg text-ink mb-5">Detail Sesi Studio</h2>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach([
                            ['fa-door-open', '#C8A27A', 'Ruangan', $booking->room->name],
                            ['fa-box-open', '#2F4F4F', 'Paket', $booking->package->name],
                            ['fa-calendar', '#C8A27A', 'Tanggal Sesi', $booking->booking_date->translatedFormat('l, d M Y')],
                            ['fa-clock', '#2E9E6B', 'Jam Sesi', substr($booking->start_time, 0, 5).' – '.substr($booking->end_time, 0, 5).' WIB'],
                            ['fa-users', '#2F4F4F', 'Jumlah Orang', $booking->people_count.' orang'],
                            ['fa-stopwatch', '#C8A27A', 'Durasi', $booking->package->duration_minutes.' menit'],
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
                            <p class="text-[11px] text-ink-muted mb-1"><i class="fa-solid fa-sticky-note text-gold mr-1"></i> Catatan Khusus</p>
                            <p class="text-sm text-ink">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- ── PHOTO GALLERY ── --}}
                @if($booking->photoResults && $booking->photoResults->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h2 class="font-serif font-semibold text-lg text-ink">Galeri Foto Anda</h2>
                                <p class="text-xs text-ink-muted">{{ $booking->photoResults->count() }} foto siap diunduh</p>
                            </div>
                            <a href="{{ $booking->signed_zip_url }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal/10 text-teal text-xs font-bold border border-teal/20 hover:bg-teal/20 transition-colors">
                                <i class="fa-solid fa-download"></i> Unduh Semua (ZIP Privat)
                            </a>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            @foreach($booking->photoResults as $photo)
                                <div class="relative group aspect-square rounded-xl overflow-hidden bg-cream-dark border border-cream-dark">
                                    <img src="{{ Storage::url($photo->path) }}" alt="{{ $photo->original_name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                                    <div class="absolute inset-0 bg-ink/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <a href="{{ $photo->signed_download_url }}"
                                           class="w-9 h-9 rounded-full bg-white/90 flex items-center justify-center hover:bg-white transition-colors"
                                           title="Unduh foto privat ini (Tautan Terenkripsi)">
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
                        @if($booking->review->photo)
                            <div class="mt-3 rounded-xl overflow-hidden border border-cream-dark max-w-xs">
                                <img src="{{ Storage::url($booking->review->photo) }}" alt="Foto Testimoni" class="w-full h-40 object-cover">
                            </div>
                        @endif
                        <p class="text-xs text-ink-muted mt-3">Dikirim {{ $booking->review->created_at->translatedFormat('d M Y H:i') }}</p>
                    @elseif(in_array($booking->status, ['completed', 'delivered', 'closed', 'checked_in', 'paid']))
                        <h2 class="font-serif font-semibold text-lg text-ink mb-2">Bagaimana Pengalaman Sesi Anda?</h2>
                        <p class="text-xs text-ink-muted mb-4">Beri ulasan dan upload foto testimoni untuk mendapatkan +50 Poin Loyalti!</p>
                        <form method="POST" action="{{ route('my-bookings.review', $booking->booking_code) }}"
                              enctype="multipart/form-data"
                              x-data="{ rating: 5, comment: '', photoPreview: null }">
                            @csrf
                            <div class="flex gap-1 mb-4">
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
                                      placeholder="Ceritakan pengalaman sesi foto Anda bersama tim SnapSpace..."
                                      class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all resize-none mb-3"></textarea>

                            {{-- Upload Foto Testimoni --}}
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-ink-muted mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-camera text-gold"></i>
                                    <span>Upload Foto Testimoni (Opsional - Bonus +50 Poin)</span>
                                </label>
                                <input type="file" name="photo" accept="image/*"
                                       class="text-xs text-ink-muted file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gold/10 file:text-gold hover:file:bg-gold/20"
                                       @change="photoPreview = URL.createObjectURL($event.target.files[0])">
                                <template x-if="photoPreview">
                                    <div class="mt-2.5 relative inline-block">
                                        <img :src="photoPreview" class="w-24 h-24 rounded-xl object-cover border border-gold/40 shadow-sm">
                                    </div>
                                </template>
                            </div>

                            <button type="submit" :disabled="!comment"
                                    class="btn-gold btn-bounce px-6 py-3 rounded-xl text-white font-bold text-sm shadow-warm disabled:opacity-40">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Ulasan & Dapatkan Poin
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

                {{-- ── E-TICKET QR (Jika Dikonfirmasi / Sesi Aktif) ── --}}
                @if($isPaid)
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7 text-center">
                        <div class="w-12 h-12 rounded-xl bg-gold flex items-center justify-center mx-auto mb-4 shadow-warm">
                            <i class="fa-solid fa-ticket text-white text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-gold uppercase tracking-widest mb-1">E-Tiket Sesi</p>
                        <h3 class="font-serif font-bold text-2xl text-ink mb-4">{{ $booking->booking_code }}</h3>

                        {{-- QR Code --}}
                        <div class="bg-cream rounded-2xl p-5 border border-cream-dark inline-block mb-4">
                            <div class="w-40 h-40 mx-auto" id="qr-code"></div>
                        </div>

                        <p class="text-xs text-ink-muted mb-4">Tunjukkan QR code ini ke receptionist studio saat kedatangan untuk check-in.</p>

                        <div class="flex flex-col gap-2">
                            <a href="#" onclick="window.print(); return false;"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-cream-dark text-ink text-sm font-semibold hover:border-gold hover:text-gold transition-colors">
                                <i class="fa-solid fa-print text-xs"></i> Cetak Tiket
                            </a>
                            <a href="https://wa.me/?text={{ urlencode('🎬 E-Tiket SnapSpace Studio: '.$booking->booking_code."\n📅 Tanggal: ".$booking->booking_date->translatedFormat('d M Y').' '.$booking->start_time."\n📍 Ruangan: ".$booking->room->name) }}"
                               target="_blank" rel="noopener"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-success/10 text-success text-sm font-semibold border border-success/20 hover:bg-success/20 transition-colors">
                                <i class="fa-brands fa-whatsapp"></i> Bagikan Tiket via WA
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ── RINCIAN PEMBAYARAN & SISA TAGIHAN ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                    <h3 class="font-serif font-semibold text-lg text-ink mb-4">Rincian Pembayaran</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-1.5">
                            <span class="text-ink-muted">Paket {{ $booking->package->name }}</span>
                            <span class="text-ink">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                        </div>
                        @if($booking->addon_total > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Add-ons Tambahan</span>
                                <span class="text-ink">Rp {{ number_format($booking->addon_total, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->markup_amount > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Markup / Biaya Tambahan</span>
                                <span class="text-warning">+Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->discount_amount > 0)
                            <div class="flex justify-between py-1.5">
                                <span class="text-ink-muted">Diskon Voucher</span>
                                <span class="text-success">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between py-2 border-t-2 border-cream-dark">
                            <span class="font-bold text-ink">Total Tagihan</span>
                            <span class="font-serif font-bold text-xl text-gold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between py-1.5">
                            <span class="text-ink-muted">Sudah Dibayar</span>
                            <span class="text-success font-semibold">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($booking->remaining_balance > 0)
                            <div class="flex justify-between py-2 border-t border-dashed border-rose-200 bg-rose-50/50 p-2.5 rounded-xl">
                                <div>
                                    <span class="font-bold text-danger block">Sisa Pelunasan</span>
                                    <span class="text-[10px] text-ink-muted">Wajib lunas sebelum sesi</span>
                                </div>
                                <span class="text-danger font-bold text-base">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                            </div>

                            <a href="{{ route('booking.pay', $booking->booking_code) }}"
                               class="btn-gold btn-bounce block text-center w-full py-3 rounded-xl text-white font-bold text-xs shadow-warm mt-3">
                                <i class="fa-solid fa-wallet mr-1.5"></i> Lunasi Sisa Tagihan Sekarang
                            </a>
                        @endif
                    </div>
                </div>

                {{-- ── ATURAN & KEBIJAKAN RESMI ── --}}
                <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-6 space-y-3">
                    <div class="flex items-center gap-2 text-ink font-bold text-sm">
                        <i class="fa-solid fa-shield-halved text-gold"></i>
                        <span>Ketentuan Layanan Studio</span>
                    </div>
                    <ul class="text-xs text-ink-muted space-y-2 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-gold text-[10px] mt-1 flex-shrink-0"></i>
                            <span><strong>Reschedule:</strong> Maksimal 1x, minimal H-2 sebelum hari H (slot realtime terkunci).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-gold text-[10px] mt-1 flex-shrink-0"></i>
                            <span><strong>Pelunasan:</strong> Sisa tagihan wajib lunas sebelum sesi pemotretan dimulai di studio.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-gold text-[10px] mt-1 flex-shrink-0"></i>
                            <span><strong>No-Show:</strong> Ketidakhadiran tanpa konfirmasi membatalkan sesi dan DP hangus.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-gold text-[10px] mt-1 flex-shrink-0"></i>
                            <span><strong>Pembatalan:</strong> Refund H-3 (100%), H-2..H-1 (50%), Hari H (0%).</span>
                        </li>
                    </ul>
                    <a href="{{ route('reschedule.policy') }}" target="_blank"
                       class="text-[11px] font-semibold text-gold hover:underline inline-flex items-center gap-1 pt-1">
                        Baca Kebijakan Lengkap <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                    </a>
                </div>

                {{-- ── RIWAYAT TRANSAKSI ── --}}
                @if($booking->payments->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <h3 class="font-serif font-semibold text-lg text-ink mb-4">Riwayat Transaksi</h3>
                        <div class="space-y-3">
                            @foreach($booking->payments as $pay)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-cream border border-cream-dark">
                                    <div class="w-9 h-9 rounded-xl {{ $pay->status === 'paid' || $pay->transaction_status === 'settlement' ? 'bg-success/15' : 'bg-warning/15' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-{{ $pay->status === 'paid' || $pay->transaction_status === 'settlement' ? 'check' : 'clock' }} text-xs {{ $pay->status === 'paid' || $pay->transaction_status === 'settlement' ? 'text-success' : 'text-warning' }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-xs text-ink">Rp {{ number_format($pay->gross_amount ?? $pay->amount, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-ink-muted">{{ ucfirst($pay->payment_kind ?? $pay->payment_type ?? 'Online') }} · {{ $pay->created_at->translatedFormat('d M Y H:i') }}</p>
                                    </div>
                                    <span class="text-[11px] font-bold {{ $pay->status === 'paid' || $pay->transaction_status === 'settlement' ? 'text-success' : 'text-warning' }}">
                                        {{ $pay->status === 'paid' || $pay->transaction_status === 'settlement' ? 'Berhasil' : 'Menunggu' }}
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
                        <span class="text-sm font-semibold text-ink">Bantuan & CS Studio</span>
                        <i class="fa-solid fa-arrow-right text-xs text-ink-muted ml-auto"></i>
                    </a>
                    @if(in_array($booking->status, ['confirmed', 'paid', 'dp_paid']))
                        <a href="https://calendar.google.com/calendar/event?action=TEMPLATE&text={{ urlencode('📸 SnapSpace — '.$booking->package->name) }}&dates={{ $booking->booking_date->format('Ymd') }}T{{ str_replace(':', '', substr($booking->start_time, 0, 5)) }}00/{{ $booking->booking_date->format('Ymd') }}T{{ str_replace(':', '', substr($booking->end_time, 0, 5)) }}00&details={{ urlencode('Kode Booking: '.$booking->booking_code) }}&location={{ urlencode('SnapSpace Studio') }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
                            <i class="fa-solid fa-calendar-plus text-gold"></i>
                            <span class="text-sm font-semibold text-ink">Simpan ke Google Calendar</span>
                            <i class="fa-solid fa-arrow-right text-xs text-ink-muted ml-auto"></i>
                        </a>
                    @endif
                    <a href="{{ route('booking.wizard') }}"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
                        <i class="fa-solid fa-rotate-right text-teal"></i>
                        <span class="text-sm font-semibold text-ink">Pesan Sesi Baru</span>
                        <i class="fa-solid fa-arrow-right text-xs text-ink-muted ml-auto"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- ── MODAL: AJUKAN RESCHEDULE (MAKS 1X, MIN H-2) ── --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div x-show="showRescheduleModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-ink/70 backdrop-blur-xs"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-3xl max-w-lg w-full p-7 border border-cream-dark shadow-2xl relative"
         @click.away="showRescheduleModal = false">

        {{-- Close Button --}}
        <button type="button" @click="showRescheduleModal = false"
                class="absolute right-5 top-5 w-8 h-8 rounded-full bg-cream hover:bg-cream-dark text-ink-muted flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gold/15 text-gold flex items-center justify-center">
                <i class="fa-solid fa-calendar-days text-base"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-ink">Ajukan Reschedule Sesi</h3>
                <p class="text-xs text-ink-muted">Kode Booking: {{ $booking->booking_code }}</p>
            </div>
        </div>

        {{-- Policy Notice --}}
        <div class="p-3.5 rounded-2xl bg-cream border border-cream-dark mb-5 text-xs text-ink-soft space-y-1">
            <p class="font-bold text-ink"><i class="fa-solid fa-circle-info text-gold mr-1"></i> Ketentuan Reschedule:</p>
            <p>• Reschedule maksimal <strong>1 kali</strong> (Pengajuan ke: <strong>{{ (int)$booking->reschedule_count + 1 }}</strong>/1).</p>
            <p>• Minimal pengajuan <strong>H-2</strong> sebelum jadwal berlangsung.</p>
            <p>• Slot baru dikunci dengan proteksi anti-double booking real-time.</p>
        </div>

        <form method="POST" action="{{ route('my-bookings.reschedule', $booking->booking_code) }}" class="space-y-4">
            @csrf

            {{-- Tanggal Baru --}}
            <div>
                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Pilih Tanggal Baru *</label>
                <input type="date" name="booking_date" required
                       min="{{ now()->addDays(2)->format('Y-m-d') }}"
                       x-model="rescheduleDate"
                       @change="fetchSlots()"
                       class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-cream text-sm text-ink focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all">
                <span class="text-[10px] text-ink-muted mt-1 block">Minimal H-2 dari hari ini (mulai {{ now()->addDays(2)->translatedFormat('d M Y') }})</span>
            </div>

            {{-- Slot Jam --}}
            <div>
                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Pilih Jam Sesi Baru *</label>

                <div x-show="loadingSlots" class="p-4 text-center text-xs text-ink-muted">
                    <i class="fa-solid fa-spinner animate-spin text-gold mr-1"></i> Memeriksa ketersediaan slot...
                </div>

                <div x-show="slotError" class="p-3 rounded-xl bg-danger/10 text-danger text-xs" x-text="slotError"></div>

                <div x-show="!loadingSlots && rescheduleSlots.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-1">
                    <template x-for="slot in rescheduleSlots" :key="slot.start_time">
                        <button type="button"
                                :disabled="!slot.is_available"
                                @click="selectedSlot = slot.start_time"
                                class="p-2 rounded-xl border text-xs font-mono font-semibold transition-all text-center"
                                :class="{
                                    'opacity-40 cursor-not-allowed bg-cream-dark/50 border-cream-dark text-ink-muted': !slot.is_available,
                                    'bg-gold text-white border-gold shadow-xs': selectedSlot === slot.start_time,
                                    'bg-cream hover:bg-cream-dark border-cream-dark text-ink': slot.is_available && selectedSlot !== slot.start_time
                                }">
                            <span x-text="slot.start_time.substring(0, 5)"></span>
                            <span class="block text-[9px]" x-text="slot.is_available ? 'Tersedia' : 'Penuh'"></span>
                        </button>
                    </template>
                </div>

                <input type="hidden" name="start_time" :value="selectedSlot" required>

                <p x-show="!loadingSlots && rescheduleDate && rescheduleSlots.length === 0 && !slotError"
                   class="text-xs text-ink-muted italic py-2">
                    Tidak ada slot tersedia di tanggal ini. Silakan coba tanggal lain.
                </p>
            </div>

            <div class="flex gap-3 pt-3 border-t border-cream-dark">
                <button type="button" @click="showRescheduleModal = false"
                        class="flex-1 py-3 rounded-xl border border-cream-dark text-ink text-sm font-semibold hover:bg-cream transition-colors">
                    Batal
                </button>
                <button type="submit"
                        :disabled="!rescheduleDate || !selectedSlot"
                        class="btn-gold btn-bounce flex-1 py-3 rounded-xl text-white text-sm font-bold shadow-warm disabled:opacity-40">
                    Konfirmasi Reschedule
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- ── MODAL: BATALKAN BOOKING & ESTIMASI REFUND ── --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div x-show="showCancelModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-ink/70 backdrop-blur-xs"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-3xl max-w-lg w-full p-7 border border-cream-dark shadow-2xl relative"
         @click.away="showCancelModal = false">

        <button type="button" @click="showCancelModal = false"
                class="absolute right-5 top-5 w-8 h-8 rounded-full bg-cream hover:bg-cream-dark text-ink-muted flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-danger flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-ink">Batalkan Booking</h3>
                <p class="text-xs text-ink-muted">{{ $booking->booking_code }} · {{ $booking->booking_date->translatedFormat('d M Y') }}</p>
            </div>
        </div>

        {{-- Breakdown Refund Live --}}
        <div class="p-4 rounded-2xl bg-cream border border-cream-dark mb-4 space-y-2.5">
            <div class="flex justify-between text-xs">
                <span class="text-ink-muted">Total Dana Masuk:</span>
                <span class="font-bold text-ink">Rp {{ number_format($refundCalc['paid_amount'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-ink-muted">Tenggat Waktu:</span>
                <span class="font-semibold text-ink">{{ $refundCalc['tier'] }}</span>
            </div>
            <div class="flex justify-between text-xs border-t border-cream-dark pt-2">
                <span class="text-ink-muted">Biaya Penalti Pembatalan:</span>
                <span class="font-bold text-danger">Rp {{ number_format($refundCalc['penalty_amount'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm border-t border-cream-dark pt-2">
                <span class="font-bold text-ink">Estimasi Pengembalian (Refund):</span>
                <span class="font-serif font-bold text-base text-success">Rp {{ number_format($refundCalc['refund_amount'], 0, ',', '.') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('my-bookings.cancel', $booking->booking_code) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Alasan Pembatalan (Opsional)</label>
                <textarea name="reason" rows="3"
                          placeholder="Mohon sebutkan alasan pembatalan agar kami dapat terus berbenah..."
                          class="w-full px-4 py-3 rounded-xl border border-cream-dark bg-cream text-sm text-ink focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all resize-none"></textarea>
            </div>

            <div class="p-3 rounded-xl bg-danger/5 border border-danger/20 text-[11px] text-danger leading-relaxed">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Setelah dibatalkan, slot studio Anda akan langsung dibuka kembali untuk umum dan status tidak dapat diubah kembali.
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showCancelModal = false"
                        class="flex-1 py-3 rounded-xl border border-cream-dark text-ink text-sm font-semibold hover:bg-cream transition-colors">
                    Kembali
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-danger text-white text-sm font-bold hover:bg-rose-700 transition-colors shadow-xs">
                    Ya, Batalkan Booking
                </button>
            </div>
        </form>
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

