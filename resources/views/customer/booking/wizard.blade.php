@extends('layouts.public')

@section('title', 'Booking Studio Foto — SnapSpace')
@section('meta_description', 'Booking sesi foto di SnapSpace Studio. Pilih paket, ruangan, jadwal, isi data diri, bayar — selesai dalam 3 menit.')

@push('styles')
<style>
    /* Step indicator */
    .step-dot {
        transition: all 300ms ease;
    }
    .step-dot.done  { background: #2E9E6B; border-color: #2E9E6B; }
    .step-dot.active { background: #C8A27A; border-color: #C8A27A; box-shadow: 0 0 0 4px rgba(200,162,122,0.2); }
    .step-dot.pending { background: white; border-color: #EDE9E3; }

    .step-line { height: 2px; flex: 1; border-radius: 2px; transition: background 300ms ease; }
    .step-line.done { background: #2E9E6B; }
    .step-line.pending { background: #EDE9E3; }

    /* Time slot chip */
    .slot-chip {
        @apply px-4 py-2 rounded-xl border text-sm font-medium transition-all duration-200 cursor-pointer;
    }
    .slot-chip.available { @apply border-cream-dark text-ink hover:border-gold hover:bg-gold/5; }
    .slot-chip.selected  { @apply border-gold bg-gold/10 text-gold font-semibold; }
    .slot-chip.booked    { @apply border-cream-dark bg-cream text-ink-muted cursor-not-allowed opacity-50; }

    /* Price summary sticky */
    .price-row { @apply flex items-center justify-between text-sm py-2; }
    .price-row + .price-row { @apply border-t border-cream-dark; }
</style>
@endpush

@section('content')

<div x-data="bookingWizard({{ $selectedRoomId ?? 'null' }}, {{ $selectedPackageId ?? 'null' }})">

{{-- ── TOP STEPPER ── --}}
<div class="sticky top-[72px] z-20 bg-white border-b border-cream-dark shadow-sm">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4">

        {{-- Mobile: compact step text --}}
        <div class="flex items-center justify-between md:hidden">
            <a href="{{ route('home') }}" class="text-xs text-ink-muted hover:text-gold flex items-center gap-1">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Batal
            </a>
            <span class="text-xs font-bold text-ink-muted" x-text="`Langkah ${step} dari 4`"></span>
            <span class="text-xs font-semibold text-gold" x-text="stepLabels[step-1]"></span>
        </div>

        {{-- Desktop: full stepper --}}
        <div class="hidden md:flex items-center justify-center gap-0">
            @php
                $stepDefs = [
                    ['Paket','fa-box-open'],
                    ['Jadwal','fa-calendar-days'],
                    ['Data Diri','fa-user'],
                    ['Ringkasan','fa-receipt'],
                ];
            @endphp
            @foreach($stepDefs as $i => [$lbl, $ico])
                <div class="flex items-center">
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all duration-300 step-dot"
                             :class="step > {{ $i+1 }} ? 'done' : (step === {{ $i+1 }} ? 'active' : 'pending')">
                            <i x-show="step > {{ $i+1 }}" class="fa-solid fa-check text-white text-xs"></i>
                            <i x-show="step <= {{ $i+1 }}" class="fa-solid fa-{{ $ico }} text-xs"
                                :class="step === {{ $i+1 }} ? 'text-white' : 'text-ink-muted'"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wide transition-colors"
                              :class="step >= {{ $i+1 }} ? 'text-ink' : 'text-ink-muted'">{{ $lbl }}</span>
                    </div>
                    @if($i < count($stepDefs)-1)
                        <div class="w-16 lg:w-24 step-line mx-2 mb-5"
                             :class="step > {{ $i+1 }} ? 'done' : 'pending'"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Mobile progress bar --}}
        <div class="mt-3 md:hidden h-1 bg-cream-dark rounded-full overflow-hidden">
            <div class="h-full bg-gold rounded-full transition-all duration-500"
                 :style="`width: ${((step-1)/3)*100}%`"></div>
        </div>
    </div>
</div>

{{-- ── WIZARD BODY ── --}}
<div class="min-h-screen bg-cream py-10">

    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- ════ LEFT: Step Content ════ --}}
            <div class="lg:col-span-7 space-y-0">

                {{-- ── STEP 1: PILIH PAKET ── --}}
                <div x-show="step === 1"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <h2 class="font-serif font-bold text-2xl text-ink mb-2">Pilih Paket Foto</h2>
                        <p class="text-sm text-ink-muted mb-6">Semua paket sudah termasuk remote shutter, lighting, dan galeri digital 30 hari.</p>

                        <div class="space-y-3">
                            @foreach($packages as $pkg)
                                <label class="flex items-center justify-between p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 group"
                                       :class="selectedPackageId === {{ $pkg->id }} ? 'border-gold bg-gold/5 shadow-warm' : 'border-cream-dark bg-cream hover:border-gold/50'"
                                       @click="selectPackage({{ $pkg->id }}, {{ $pkg->price }}, '{{ addslashes($pkg->name) }}', {{ $pkg->max_people }})">
                                    <div class="flex items-center gap-4">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                             :class="selectedPackageId === {{ $pkg->id }} ? 'border-gold bg-gold' : 'border-cream-dark'">
                                            <div class="w-2 h-2 rounded-full bg-white" x-show="selectedPackageId === {{ $pkg->id }}"></div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-ink group-hover:text-gold transition-colors">{{ $pkg->name }}</p>
                                            <div class="flex flex-wrap gap-2 mt-1.5">
                                                <span class="text-[11px] text-ink-muted bg-cream px-2 py-0.5 rounded-md">
                                                    <i class="fa-solid fa-clock text-gold text-[10px] mr-1"></i>{{ $pkg->duration_minutes }} menit
                                                </span>
                                                <span class="text-[11px] text-ink-muted bg-cream px-2 py-0.5 rounded-md">
                                                    <i class="fa-solid fa-users text-teal text-[10px] mr-1"></i>Maks. {{ $pkg->max_people }} orang
                                                </span>
                                                <span class="text-[11px] text-ink-muted bg-cream px-2 py-0.5 rounded-md">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-success text-[10px] mr-1"></i>{{ $pkg->edited_photos }} foto edit
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right ml-4 flex-shrink-0">
                                        <p class="font-serif font-bold text-xl text-gold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-ink-muted">/ sesi</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- Add-ons quick select --}}
                        @if($addons->isNotEmpty())
                            <div class="mt-6 pt-6 border-t border-cream-dark">
                                <p class="text-xs font-bold text-ink-muted uppercase tracking-wide mb-3">Tambahkan Add-On (Opsional)</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($addons as $addon)
                                        <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all"
                                               :class="selectedAddons.includes({{ $addon->id }}) ? 'border-teal bg-teal-50' : 'border-cream-dark bg-cream hover:border-teal/50'"
                                               @click="toggleAddon({{ $addon->id }}, {{ $addon->price }}, '{{ addslashes($addon->name) }}')">
                                            <div class="w-5 h-5 rounded flex items-center justify-center border-2 transition-all flex-shrink-0"
                                                 :class="selectedAddons.includes({{ $addon->id }}) ? 'border-teal bg-teal' : 'border-cream-dark'">
                                                <i class="fa-solid fa-check text-white text-[9px]" x-show="selectedAddons.includes({{ $addon->id }})"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-ink truncate">{{ $addon->name }}</p>
                                            </div>
                                            <span class="text-xs font-bold text-teal flex-shrink-0">+Rp {{ number_format($addon->price, 0, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <button @click="step = 2" :disabled="!selectedPackageId"
                                class="btn-gold btn-bounce w-full py-4 rounded-2xl text-white font-bold text-base mt-6 shadow-warm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            Lanjut: Pilih Jadwal <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- ── STEP 2: PILIH RUANGAN & JADWAL ── --}}
                <div x-show="step === 2"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="space-y-5">
                        {{-- Pilih Ruangan --}}
                        <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                            <div class="flex items-center gap-3 mb-5">
                                <button @click="step = 1" class="w-8 h-8 rounded-xl border border-cream-dark flex items-center justify-center hover:border-gold transition-colors text-ink-muted hover:text-gold">
                                    <i class="fa-solid fa-arrow-left text-xs"></i>
                                </button>
                                <h2 class="font-serif font-bold text-2xl text-ink">Pilih Ruangan</h2>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($rooms as $room)
                                    <label class="relative cursor-pointer group block"
                                           @click="selectRoom({{ $room->id }}, '{{ addslashes($room->name) }}')">
                                        <div class="rounded-2xl overflow-hidden border-2 transition-all duration-200"
                                             :class="selectedRoomId === {{ $room->id }} ? 'border-gold shadow-warm' : 'border-cream-dark hover:border-gold/50'">
                                            <div class="relative aspect-[16/9] bg-cream-dark">
                                                <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=400' }}"
                                                     alt="{{ $room->name }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                                                {{-- Selected check --}}
                                                <div class="absolute top-3 right-3 w-7 h-7 rounded-full transition-all duration-200"
                                                     :class="selectedRoomId === {{ $room->id }} ? 'bg-gold flex items-center justify-center' : 'hidden'">
                                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                                </div>

                                                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-teal text-[11px] font-bold px-2.5 py-1 rounded-full">
                                                    {{ $room->theme }}
                                                </span>
                                            </div>
                                            <div class="p-3.5">
                                                <p class="font-semibold text-sm text-ink group-hover:text-gold transition-colors">{{ $room->name }}</p>
                                                <p class="text-xs text-ink-muted mt-0.5">Maks. {{ $room->capacity }} orang · {{ $room->area_m2 }} m²</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pilih Tanggal --}}
                        <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7" x-show="selectedRoomId">
                            <h3 class="font-serif font-semibold text-lg text-ink mb-4">Pilih Tanggal</h3>
                            <input type="date"
                                   x-model="selectedDate"
                                   :min="today"
                                   @change="fetchSlots()"
                                   class="w-full px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                   id="booking_date">
                        </div>

                        {{-- Time Slots --}}
                        <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7"
                             x-show="selectedRoomId && selectedDate">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-serif font-semibold text-lg text-ink">Pilih Jam Sesi</h3>
                                <div class="flex items-center gap-4 text-xs text-ink-muted">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded bg-white border border-cream-dark"></span> Tersedia
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded bg-gold border border-gold"></span> Dipilih
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-3 h-3 rounded bg-cream-dark border border-cream-dark"></span> Penuh
                                    </span>
                                </div>
                            </div>

                            {{-- Loading skeleton --}}
                            <div x-show="loadingSlots" class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                                @for($s=0;$s<8;$s++)
                                    <div class="skeleton h-14 rounded-xl"></div>
                                @endfor
                            </div>

                            {{-- Slots --}}
                            <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                                <template x-for="slot in slots" :key="slot.start">
                                    <button type="button"
                                            @click="slot.available && selectSlot(slot)"
                                            class="slot-chip relative flex flex-col items-center justify-center py-2.5 px-2 rounded-xl border transition-all text-center"
                                            :class="selectedSlot?.start === slot.start ? 'selected' : (slot.available ? 'available' : 'booked')"
                                            :disabled="!slot.available">
                                        <span class="text-sm font-bold tracking-tight" x-text="slot.start"></span>
                                        <span class="text-[11px] opacity-75 mt-0.5" x-text="slot.end ? 's/d ' + slot.end : ''"></span>
                                        <span x-show="slot.has_saver_discount" class="text-[9px] bg-amber-500/15 text-amber-600 font-semibold px-1.5 py-0.5 rounded-full mt-1">Hemat 15%</span>
                                    </button>
                                </template>
                            </div>

                            {{-- Slot selected info --}}
                            <div x-show="selectedSlot" class="mt-4 p-3.5 rounded-xl bg-gold/10 border border-gold/30 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <i class="fa-solid fa-circle-check text-gold text-base"></i>
                                    <div>
                                        <p class="text-xs font-bold text-ink">Jam Terpilih: <span class="text-gold" x-text="selectedSlot ? (selectedSlot.start + ' - ' + selectedSlot.end) : ''"></span></p>
                                        <p class="text-[11px] text-ink-muted">Tekan 'Lanjut: Isi Data Diri' untuk tahap selanjutnya</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Helper prompt when slots loaded but none selected --}}
                            <div x-show="!loadingSlots && slots.length > 0 && !selectedSlot" class="mt-3 text-center">
                                <p class="text-xs text-ink-muted flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-hand-pointer text-gold text-[11px]"></i>
                                    <span>Pilih salah satu jam yang berwarna putih di atas.</span>
                                </p>
                            </div>

                            {{-- No slots --}}
                            <div x-show="!loadingSlots && slots.length === 0 && selectedDate" class="text-center py-8">
                                <i class="fa-regular fa-calendar-xmark text-3xl text-gold/40 mb-3 block"></i>
                                <p class="text-sm font-semibold text-ink mb-1">Tidak ada slot tersedia</p>
                                <p class="text-xs text-ink-muted">Semua sesi pada tanggal ini sudah terisi atau studio tutup. Silakan pilih tanggal lain.</p>
                            </div>
                        </div>

                        <button @click="step = 3" :disabled="!selectedRoomId || !selectedSlot"
                                class="btn-gold btn-bounce w-full py-4 rounded-2xl text-white font-bold text-base shadow-warm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            Lanjut: Isi Data Diri <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- ── STEP 3: DATA DIRI ── --}}
                <div x-show="step === 3"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <div class="flex items-center gap-3 mb-5">
                            <button @click="step = 2" class="w-8 h-8 rounded-xl border border-cream-dark flex items-center justify-center hover:border-gold transition-colors text-ink-muted hover:text-gold">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                            </button>
                            <h2 class="font-serif font-bold text-2xl text-ink">Data Diri Pemesan</h2>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5" for="w_name">Nama Lengkap *</label>
                                <input id="w_name" type="text" x-model="form.name" required
                                       placeholder="Nama sesuai identitas"
                                       class="w-full px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                       autocomplete="name">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5" for="w_phone">No. WhatsApp *</label>
                                    <input id="w_phone" type="tel" x-model="form.phone" required
                                           placeholder="08xx-xxxx-xxxx"
                                           class="w-full px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5" for="w_email">Email</label>
                                    <input id="w_email" type="email" x-model="form.email"
                                           placeholder="email@kamu.com"
                                           class="w-full px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                           autocomplete="email">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5" for="w_people">Jumlah Orang *</label>
                                <input id="w_people" type="number" x-model.number="form.people_count" min="1" :max="selectedPackage ? selectedPackage.max_people : 6"
                                       class="w-full px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all">
                            </div>

                            {{-- Payment option --}}
                            <div>
                                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-2.5">Opsi Pembayaran *</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                           :class="form.payment_option === 'full' ? 'border-gold bg-gold/5' : 'border-cream-dark bg-cream hover:border-gold/50'"
                                           @click="form.payment_option = 'full'">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                             :class="form.payment_option === 'full' ? 'border-gold bg-gold' : 'border-cream-dark'">
                                            <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="form.payment_option === 'full'"></div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-xs text-ink">Bayar Lunas</p>
                                            <p class="text-[11px] text-ink-muted">Hemat & lebih praktis</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                                           :class="form.payment_option === 'dp' ? 'border-gold bg-gold/5' : 'border-cream-dark bg-cream hover:border-gold/50'"
                                           @click="form.payment_option = 'dp'">
                                        <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                             :class="form.payment_option === 'dp' ? 'border-gold bg-gold' : 'border-cream-dark'">
                                            <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="form.payment_option === 'dp'"></div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-xs text-ink">Bayar DP 50%</p>
                                            <p class="text-[11px] text-ink-muted">Sisa dilunasi H-1</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Promo code --}}
                            <div>
                                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5">Kode Promo (Opsional)</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="form.promo_code"
                                           placeholder="HEMAT15"
                                           class="flex-1 px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink uppercase placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all"
                                           style="letter-spacing: 0.05em">
                                    <button @click="applyPromo()"
                                            :disabled="!form.promo_code || loadingPromo"
                                            class="px-5 py-3 rounded-xl bg-teal text-white text-sm font-semibold hover:bg-teal-light transition-colors disabled:opacity-50 flex items-center gap-2">
                                        <i class="fa-solid fa-spinner animate-spin text-xs" x-show="loadingPromo"></i>
                                        <span>Pakai</span>
                                    </button>
                                </div>
                                <p class="text-xs text-success mt-1.5" x-show="promoMessage" x-text="promoMessage"></p>
                                <p class="text-xs text-danger mt-1.5" x-show="promoError" x-text="promoError"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-ink-muted uppercase tracking-wide mb-1.5" for="w_notes">Catatan (Opsional)</label>
                                <textarea id="w_notes" x-model="form.notes" rows="3"
                                          placeholder="Misalnya: ada anak kecil, butuh properti tertentu, dll."
                                          class="w-full px-4 py-3.5 rounded-xl border border-cream-dark bg-cream text-sm text-ink placeholder-ink-light focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/15 transition-all resize-none leading-relaxed"></textarea>
                            </div>
                        </div>

                        <button @click="goToSummary()" :disabled="!form.name || !form.phone"
                                class="btn-gold btn-bounce w-full py-4 rounded-2xl text-white font-bold text-base mt-6 shadow-warm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            Lanjut: Lihat Ringkasan <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                {{-- ── STEP 4: RINGKASAN & SUBMIT ── --}}
                <div x-show="step === 4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-7">
                        <div class="flex items-center gap-3 mb-6">
                            <button @click="step = 3" class="w-8 h-8 rounded-xl border border-cream-dark flex items-center justify-center hover:border-gold transition-colors text-ink-muted hover:text-gold">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                            </button>
                            <h2 class="font-serif font-bold text-2xl text-ink">Ringkasan Booking</h2>
                        </div>

                        {{-- Booking details --}}
                        <div class="space-y-4 mb-6">
                            @foreach([
                                ['fa-box-open', 'Paket', 'selectedPackageName'],
                                ['fa-door-open', 'Ruangan', 'selectedRoomName'],
                                ['fa-calendar', 'Tanggal', 'selectedDate'],
                                ['fa-clock', 'Jam Sesi', 'selectedSlot?.start'],
                                ['fa-users', 'Jumlah Orang', 'form.people_count + \" orang\"'],
                                ['fa-user', 'Pemesan', 'form.name'],
                            ] as [$icon, $label, $val])
                                <div class="flex items-center gap-3 py-3 border-b border-cream-dark last:border-0">
                                    <div class="w-9 h-9 rounded-xl bg-gold/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-{{ $icon }} text-gold text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-ink-muted">{{ $label }}</p>
                                        <p class="font-semibold text-sm text-ink" x-text="{{ $val }}"></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Agreement --}}
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-cream border border-cream-dark cursor-pointer mb-5">
                            <input type="checkbox" x-model="agreed" class="mt-0.5 accent-[#C8A27A]">
                            <p class="text-xs text-ink-muted leading-relaxed">
                                Saya menyetujui
                                <a href="{{ Route::has('terms') ? route('terms') : '#' }}" class="text-gold hover:underline" target="_blank">Syarat & Ketentuan</a>
                                dan
                                <a href="{{ Route::has('reschedule.policy') ? route('reschedule.policy') : '#' }}" class="text-gold hover:underline" target="_blank">Kebijakan Reschedule</a>
                                SnapSpace Studio.
                            </p>
                        </label>

                        {{-- Submit Form (real POST) --}}
                        <form method="POST" action="{{ route('booking.store') }}" id="bookingForm" @submit.prevent="submitBooking($event)">
                            @csrf
                            <input type="hidden" name="package_id"      :value="selectedPackageId">
                            <input type="hidden" name="room_id"         :value="selectedRoomId">
                            <input type="hidden" name="booking_date"    :value="selectedDate">
                            <input type="hidden" name="start_time"      :value="selectedSlot?.start">
                            <input type="hidden" name="end_time"        :value="selectedSlot?.end">
                            <input type="hidden" name="people_count"    :value="form.people_count">
                            <input type="hidden" name="payment_option"  :value="form.payment_option">
                            <input type="hidden" name="promo_code"      :value="form.promo_code">
                            <input type="hidden" name="notes"           :value="form.notes">
                            <template x-for="id in selectedAddons" :key="id">
                                <input type="hidden" name="addon_ids[]" :value="id">
                            </template>

                            <button type="submit" :disabled="!agreed || submitting"
                                    class="btn-gold btn-bounce w-full py-4 rounded-2xl text-white font-bold text-base shadow-warm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-spinner animate-spin" x-show="submitting"></i>
                                <i class="fa-solid fa-lock text-sm" x-show="!submitting"></i>
                                <span x-text="submitting ? 'Memproses...' : 'Konfirmasi & Bayar'"></span>
                            </button>
                        </form>

                        <div class="flex items-center justify-center gap-2 mt-4 text-xs text-ink-muted">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            Pembayaran diproses aman via Midtrans
                        </div>
                    </div>
                </div>

            </div>{{-- end left col --}}

            {{-- ════ RIGHT: Price Summary (Sticky) ════ --}}
            <div class="lg:col-span-5">
                <div class="sticky top-36">
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-card p-6">
                        <h3 class="font-serif font-semibold text-lg text-ink mb-5 pb-5 border-b border-cream-dark">
                            Ringkasan Harga
                        </h3>

                        {{-- Selection summary --}}
                        <div class="space-y-1 mb-5">
                            <div class="price-row">
                                <span class="text-ink-muted">Paket</span>
                                <span class="font-semibold text-ink text-right max-w-[60%] text-xs" x-text="selectedPackageName || '—'"></span>
                            </div>
                            <div class="price-row">
                                <span class="text-ink-muted">Ruangan</span>
                                <span class="font-semibold text-ink text-xs" x-text="selectedRoomName || '—'"></span>
                            </div>
                            <div class="price-row">
                                <span class="text-ink-muted">Tanggal</span>
                                <span class="font-semibold text-ink text-xs" x-text="selectedDate || '—'"></span>
                            </div>
                            <div class="price-row">
                                <span class="text-ink-muted">Jam</span>
                                <span class="font-semibold text-ink text-xs" x-text="selectedSlot ? selectedSlot.start : '—'"></span>
                            </div>
                        </div>

                        {{-- Price breakdown --}}
                        <div class="space-y-1 py-5 border-t border-cream-dark">
                            <div class="price-row">
                                <span class="text-ink-muted">Paket</span>
                                <span class="font-semibold text-ink" x-text="selectedPackageId ? 'Rp ' + formatNum(packagePrice) : '—'"></span>
                            </div>
                            <div class="price-row" x-show="addonsTotal > 0">
                                <span class="text-ink-muted">Add-ons</span>
                                <span class="font-semibold text-ink" x-text="'+ Rp ' + formatNum(addonsTotal)"></span>
                            </div>
                            <div class="price-row" x-show="markupAmount > 0">
                                <span class="text-ink-muted">Markup</span>
                                <span class="font-semibold text-warning" x-text="'+ Rp ' + formatNum(markupAmount)"></span>
                            </div>
                            <div class="price-row" x-show="discountAmount > 0">
                                <span class="text-ink-muted">Diskon</span>
                                <span class="font-semibold text-success" x-text="'- Rp ' + formatNum(discountAmount)"></span>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="border-t-2 border-cream-dark pt-4 flex items-center justify-between">
                            <span class="font-bold text-ink">Total</span>
                            <span class="font-serif font-bold text-2xl text-gold" x-text="'Rp ' + formatNum(totalPrice)"></span>
                        </div>

                        <div class="mt-4 pt-4 border-t border-cream-dark" x-show="form.payment_option === 'dp'">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-ink-muted">Bayar sekarang (DP 50%)</span>
                                <span class="font-bold text-ink" x-text="'Rp ' + formatNum(Math.ceil(totalPrice / 2))"></span>
                            </div>
                        </div>

                        {{-- Payment methods icons --}}
                        <div class="mt-5 pt-5 border-t border-cream-dark">
                            <p class="text-xs text-ink-muted mb-3">Metode pembayaran:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['QRIS','GoPay','OVO','DANA','BCA VA','BNI VA','BRI VA'] as $pm)
                                    <span class="px-2.5 py-1 rounded-lg bg-cream border border-cream-dark text-[11px] font-semibold text-ink-muted">{{ $pm }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Help --}}
                    <div class="mt-4 bg-cream rounded-2xl border border-cream-dark p-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-brands fa-whatsapp text-success text-xl"></i>
                            <div>
                                <p class="text-xs font-semibold text-ink">Butuh bantuan?</p>
                                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="text-xs text-success hover:underline">+62 812-3456-7890</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end grid --}}
    </div>
</div>
</div>{{-- end x-data bookingWizard --}}

@endsection

@push('scripts')
<script>
function bookingWizard(initRoomId, initPackageId) {
    return {
        step: 1,
        stepLabels: ['Pilih Paket', 'Pilih Jadwal', 'Data Diri', 'Ringkasan'],

        // Selections
        selectedPackageId: initPackageId,
        selectedPackageName: '',
        selectedPackageMaxPeople: 6,
        selectedPackage: null,
        selectedRoomId: initRoomId,
        selectedRoomName: '',
        selectedDate: '',
        selectedSlot: null,
        selectedAddons: [],

        // Form
        form: {
            name: '{{ Auth::user()?->name ?? '' }}',
            phone: '{{ Auth::user()?->phone ?? '' }}',
            email: '{{ Auth::user()?->email ?? '' }}',
            people_count: 1,
            payment_option: 'full',
            promo_code: '',
            notes: '',
        },

        // Pricing
        packagePrice: 0,
        addonsTotal: 0,
        markupAmount: 0,
        discountAmount: 0,
        get totalPrice() {
            return Math.max(0, this.packagePrice + this.addonsTotal + this.markupAmount - this.discountAmount);
        },

        // State
        slots: [],
        loadingSlots: false,
        today: (() => {
            const d = new Date();
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        })(),
        agreed: false,
        submitting: false,
        loadingPromo: false,
        promoMessage: '',
        promoError: '',

        init() {
            // Set default selectedDate to tomorrow if empty
            if (!this.selectedDate) {
                const tmrw = new Date();
                tmrw.setDate(tmrw.getDate() + 1);
                const year = tmrw.getFullYear();
                const month = String(tmrw.getMonth() + 1).padStart(2, '0');
                const day = String(tmrw.getDate()).padStart(2, '0');
                this.selectedDate = `${year}-${month}-${day}`;
            }

            // Pre-populate selections from URL params
            if (this.selectedPackageId) {
                const pkgEl = document.querySelector(`input[name="package"][value="${this.selectedPackageId}"]`);
                if (pkgEl) pkgEl.dispatchEvent(new Event('click'));
            }
            // Pre-populate known packages
            @foreach($packages as $pkg)
            if (this.selectedPackageId === {{ $pkg->id }}) {
                this.selectedPackageName = '{{ $pkg->name }}';
                this.packagePrice = {{ $pkg->price }};
                this.selectedPackageMaxPeople = {{ $pkg->max_people }};
                this.selectedPackage = { id: {{ $pkg->id }}, name: '{{ $pkg->name }}', price: {{ $pkg->price }}, max_people: {{ $pkg->max_people }} };
            }
            @endforeach

            @foreach($rooms as $room)
            if (this.selectedRoomId === {{ $room->id }}) {
                this.selectedRoomName = '{{ $room->name }}';
            }
            @endforeach

            // Immediately load slots for default room & date
            if (this.selectedRoomId && this.selectedDate) {
                this.fetchSlots();
            }
        },

        selectPackage(id, price, name, maxPeople = 6) {
            this.selectedPackageId = id;
            this.packagePrice = price;
            this.selectedPackageName = name;
            this.selectedPackageMaxPeople = maxPeople;
            this.selectedPackage = { id, name, price, max_people: maxPeople };
            this.recalculate();
            if (this.selectedRoomId && this.selectedDate) {
                this.fetchSlots();
            }
        },

        selectRoom(id, name) {
            this.selectedRoomId = id;
            this.selectedRoomName = name;
            this.selectedSlot = null;
            this.slots = [];
            if (this.selectedDate) {
                this.fetchSlots();
            }
        },

        selectSlot(slot) {
            this.selectedSlot = slot;
        },

        toggleAddon(id, price, name) {
            const idx = this.selectedAddons.indexOf(id);
            if (idx === -1) {
                this.selectedAddons.push(id);
                this.addonsTotal += price;
            } else {
                this.selectedAddons.splice(idx, 1);
                this.addonsTotal -= price;
            }
        },

        async fetchSlots() {
            if (!this.selectedRoomId || !this.selectedDate) return;
            this.loadingSlots = true;
            this.slots = [];
            this.selectedSlot = null;
            try {
                const res = await fetch(`/api/slots?room_id=${this.selectedRoomId}&date=${this.selectedDate}&package_id=${this.selectedPackageId || ''}`);
                const data = await res.json();
                this.slots = (data.slots ?? []).map(s => ({
                    ...s,
                    start: s.start ?? s.start_time,
                    end: s.end ?? s.end_time,
                    available: (s.available !== undefined) ? s.available : (s.status === 'available'),
                }));
                // Also grab markup
                if (data.markup_percent) {
                    this.markupAmount = Math.round(this.packagePrice * data.markup_percent / 100);
                } else {
                    this.markupAmount = 0;
                }
            } catch(e) {
                console.error('Slot fetch error', e);
                this.slots = [];
            } finally {
                this.loadingSlots = false;
            }
        },

        async applyPromo() {
            this.promoMessage = '';
            this.promoError = '';
            this.loadingPromo = true;
            try {
                const res = await fetch('/api/calculate-price', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({
                        package_id: this.selectedPackageId,
                        addon_ids: this.selectedAddons,
                        promo_code: this.form.promo_code,
                        booking_date: this.selectedDate,
                        start_time: this.selectedSlot?.start,
                    })
                });
                const data = await res.json();
                if (data.error) {
                    this.promoError = data.error;
                } else {
                    this.discountAmount = data.discount ?? 0;
                    this.markupAmount = data.markup ?? 0;
                    this.promoMessage = data.discount > 0 ? `Promo berhasil! Diskon Rp ${this.formatNum(data.discount)}` : 'Promo tidak memberikan diskon.';
                }
            } catch(e) {
                this.promoError = 'Gagal memvalidasi promo.';
            } finally {
                this.loadingPromo = false;
            }
        },

        goToSummary() {
            if (!this.form.name || !this.form.phone) return;
            this.recalculate();
            this.step = 4;
        },

        recalculate() {
            // Recalculate if we have pricing already
        },

        submitBooking(event) {
            if (!this.agreed || this.submitting) return;
            this.submitting = true;
            event.target.submit();
        },

        formatNum(n) {
            return (n || 0).toLocaleString('id-ID');
        },
    };
}
</script>
@endpush
