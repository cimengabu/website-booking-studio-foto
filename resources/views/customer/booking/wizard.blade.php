@extends('layouts.app')

@section('title', 'Booking Studio Foto — SnapSpace')

@section('content')
<div class="py-10 max-w-5xl mx-auto px-4"
     x-data="bookingWizard({{ $selectedRoomId ?? 'null' }}, {{ $selectedPackageId ?? 'null' }})"
     x-init="init()">

    {{-- Step Progress Bar --}}
    <div class="mb-10">
        <div class="flex items-center justify-center gap-0">
            @foreach([
                ['1', 'Paket'],
                ['2', 'Ruangan'],
                ['3', 'Jadwal'],
                ['4', 'Konfirmasi'],
            ] as $i => [$num, $label])
                <div class="flex items-center">
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-mono font-bold text-sm border-2 transition-all duration-300"
                             :class="step > {{ $i+1 }} ? 'bg-emerald-600 border-emerald-600 text-white' : (step === {{ $i+1 }} ? 'bg-[#E8451E] border-[#E8451E] text-white shadow-lg shadow-[#E8451E]/40' : 'bg-[#141311] border-[#2D2B26] text-gray-500')">
                            <span x-show="step <= {{ $i+1 }}">{{ $num }}</span>
                            <i x-show="step > {{ $i+1 }}" class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span class="text-[10px] font-mono uppercase font-bold transition-colors duration-300"
                              :class="step >= {{ $i+1 }} ? 'text-white' : 'text-gray-600'">{{ $label }}</span>
                    </div>
                    @if($i < 3)
                        <div class="w-16 sm:w-24 h-0.5 mx-2 mb-5 rounded-full transition-colors duration-300"
                             :class="step > {{ $i+1 }} ? 'bg-emerald-600' : 'bg-[#2D2B26]'"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- LEFT MAIN WIZARD CONTENT --}}
        <div class="lg:col-span-7">

            {{-- STEP 1: Pilih Paket --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                    <h2 class="font-heading font-bold text-xl text-white">Pilih Paket Foto</h2>
                    <div class="space-y-3">
                        @foreach($packages as $pkg)
                            <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer transition-all"
                                   :class="selectedPackageId === {{ $pkg->id }} ? 'border-[#E8451E] bg-[#E8451E]/10' : 'border-[#2D2B26] bg-[#141311] hover:border-gray-500'">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="package" value="{{ $pkg->id }}" x-model.number="selectedPackageId" class="text-[#E8451E] focus:ring-0">
                                    <div>
                                        <p class="font-bold text-white text-sm">{{ $pkg->name }}</p>
                                        <p class="text-[10px] font-mono text-gray-400 mt-0.5">
                                            {{ $pkg->duration_minutes }} mnt · Maks {{ $pkg->max_people }} orang · {{ $pkg->edited_photos }} foto edit
                                        </p>
                                    </div>
                                </div>
                                <span class="font-heading font-black text-lg text-[#F2C94C] shrink-0">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button @click="step = 2" :disabled="!selectedPackageId"
                            class="w-full py-4 rounded-full bg-[#E8451E] text-white font-bold text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#d03a16] shadow-lg transition-all">
                        Lanjut Pilih Ruangan <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: Pilih Ruangan --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <button @click="step = 1" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white text-xs"><i class="fa-solid fa-arrow-left"></i></button>
                        <h2 class="font-heading font-bold text-xl text-white">Pilih Ruangan & Tema</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($rooms as $room)
                            <label class="relative cursor-pointer group" @click="selectedRoomId = {{ $room->id }}">
                                <div class="rounded-2xl overflow-hidden border-2 transition-all"
                                     :class="selectedRoomId === {{ $room->id }} ? 'border-[#E8451E] shadow-lg shadow-[#E8451E]/20' : 'border-[#2D2B26] hover:border-gray-500'">
                                    <div class="relative aspect-[4/3] overflow-hidden">
                                        <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=600' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $room->name }}">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent p-3 flex flex-col justify-end">
                                            <span class="text-[10px] font-mono text-[#F2C94C] font-bold">{{ $room->theme }}</span>
                                            <p class="font-heading font-bold text-sm text-white">{{ $room->name }}</p>
                                        </div>
                                        <div x-show="selectedRoomId === {{ $room->id }}" class="absolute top-2 right-2 w-6 h-6 rounded-full bg-[#E8451E] flex items-center justify-center">
                                            <i class="fa-solid fa-check text-white text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>
                                <input type="radio" name="room" value="{{ $room->id }}" x-model.number="selectedRoomId" class="sr-only">
                            </label>
                        @endforeach
                    </div>
                    <button @click="step = 3" :disabled="!selectedRoomId"
                            class="w-full py-4 rounded-full bg-[#E8451E] text-white font-bold text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#d03a16] shadow-lg transition-all">
                        Lanjut Pilih Jadwal <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 3: Pilih Jadwal --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <button @click="step = 2" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white text-xs"><i class="fa-solid fa-arrow-left"></i></button>
                        <h2 class="font-heading font-bold text-xl text-white">Pilih Tanggal & Jam</h2>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-300 mb-2 block">Pilih Tanggal Sesi:</label>
                        <input type="date"
                               x-model="selectedDate"
                               :min="minDate"
                               @change="fetchSlots()"
                               class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none">
                    </div>

                    <div x-show="selectedDate">
                        <p class="text-xs font-semibold text-gray-300 mb-3">Pilih Jam Mulai:</p>

                        <div class="flex flex-wrap gap-2 mb-3 text-[10px] font-mono">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-[#1F3A2E] border border-emerald-700 inline-block"></span>Tersedia</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-900/40 border border-rose-700/40 inline-block"></span>Terisi</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-[#2D2B26] inline-block"></span>Dikunci</span>
                        </div>

                        <div x-show="loadingSlots" class="text-center py-8 text-gray-400 text-sm">
                            <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i><br>Memuat slot tersedia...
                        </div>

                        <div x-show="!loadingSlots" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            <template x-for="slot in slots" :key="slot.start_time">
                                <button type="button"
                                        :disabled="!slot.available"
                                        @click="slot.available && selectSlot(slot)"
                                        :class="{
                                            'bg-[#1F3A2E] border-emerald-700/60 text-emerald-300 hover:border-emerald-500': slot.available && selectedSlot?.start_time !== slot.start_time,
                                            'bg-[#E8451E] border-[#E8451E] text-white shadow-lg': selectedSlot?.start_time === slot.start_time,
                                            'bg-rose-900/20 border-rose-800/30 text-rose-600 cursor-not-allowed': !slot.available,
                                        }"
                                        class="px-3 py-3 rounded-xl border text-xs font-mono font-bold transition-all text-center">
                                    <span x-text="slot.start_time.substring(0,5)"></span>
                                    <span x-show="slot.discount_percent > 0" class="block text-[9px] text-[#F2C94C] mt-0.5" x-text="`-${slot.discount_percent}%`"></span>
                                </button>
                            </template>
                        </div>

                        <p x-show="slots.length === 0 && !loadingSlots" class="text-center text-xs text-gray-500 py-6 font-mono">Tidak ada slot tersedia untuk tanggal ini.</p>
                    </div>

                    <div x-show="selectedSlot" class="bg-emerald-950/40 border border-emerald-800/40 rounded-2xl p-4 text-xs font-mono text-emerald-200 flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                        <span>Slot dipilih: <strong x-text="selectedDate"></strong> · Jam <strong x-text="selectedSlot?.start_time?.substring(0,5)"></strong> – <strong x-text="selectedSlot?.end_time?.substring(0,5)"></strong></span>
                    </div>

                    <button @click="step = 4; fetchPrice()" :disabled="!selectedSlot"
                            class="w-full py-4 rounded-full bg-[#E8451E] text-white font-bold text-sm disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#d03a16] shadow-lg transition-all">
                        Lanjut Konfirmasi & Add-On <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 4: Konfirmasi --}}
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <form method="POST" action="{{ route('booking.store') }}" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-6">
                    @csrf
                    <input type="hidden" name="package_id" :value="selectedPackageId">
                    <input type="hidden" name="room_id" :value="selectedRoomId">
                    <input type="hidden" name="booking_date" :value="selectedDate">
                    <input type="hidden" name="start_time" :value="selectedSlot?.start_time">

                    <div class="flex items-center gap-3">
                        <button type="button" @click="step = 3" class="p-2 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white text-xs"><i class="fa-solid fa-arrow-left"></i></button>
                        <h2 class="font-heading font-bold text-xl text-white">Konfirmasi Booking</h2>
                    </div>

                    {{-- People count --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-300 mb-2 block">Jumlah Orang di Sesi:</label>
                        <input type="number" name="people_count" x-model.number="peopleCount" min="1" max="10" @change="fetchPrice()"
                               class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none">
                    </div>

                    {{-- Add-ons --}}
                    @if($addons->isNotEmpty())
                        <div>
                            <p class="text-xs font-semibold text-gray-300 mb-3">Add-on Layanan (opsional):</p>
                            <div class="space-y-3">
                                @foreach($addons as $addon)
                                    <label class="flex items-center justify-between p-3 rounded-2xl border cursor-pointer transition-all"
                                           :class="selectedAddons.includes({{ $addon->id }}) ? 'border-[#E8451E] bg-[#E8451E]/10' : 'border-[#2D2B26] hover:border-gray-500'">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="addons[]" value="{{ $addon->id }}"
                                                   @change="toggleAddon({{ $addon->id }}); fetchPrice()"
                                                   class="text-[#E8451E] rounded focus:ring-0">
                                            <div>
                                                <p class="text-xs font-semibold text-white">{{ $addon->name }}</p>
                                                <p class="text-[10px] font-mono text-gray-500">{{ $addon->description }}</p>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-[#F2C94C] font-mono shrink-0 ml-3">+Rp {{ number_format($addon->price, 0, ',', '.') }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Promo Code --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-300 mb-2 block">Kode Promo (opsional):</label>
                        <div class="flex gap-2">
                            <input type="text" name="promo_code" x-model="promoCode" placeholder="Masukkan kode promo..." @input="fetchPrice()"
                                   class="flex-grow bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none uppercase">
                        </div>
                    </div>

                    {{-- Payment Option --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-300 mb-3">Opsi Pembayaran:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="p-4 rounded-2xl border cursor-pointer transition-all text-center"
                                   :class="paymentOption === 'dp' ? 'border-[#E8451E] bg-[#E8451E]/10' : 'border-[#2D2B26] hover:border-gray-500'">
                                <input type="radio" name="payment_option" value="dp" x-model="paymentOption" class="sr-only">
                                <p class="font-bold text-sm text-white">DP 50%</p>
                                <p class="text-[10px] font-mono text-gray-400 mt-0.5">Lunasi sisanya saat atau setelah sesi</p>
                                <p class="font-heading font-black text-base text-[#F2C94C] mt-1" x-text="'Rp ' + Math.round((pricing?.total_price ?? 0) / 2).toLocaleString('id-ID')"></p>
                            </label>
                            <label class="p-4 rounded-2xl border cursor-pointer transition-all text-center"
                                   :class="paymentOption === 'full' ? 'border-[#E8451E] bg-[#E8451E]/10' : 'border-[#2D2B26] hover:border-gray-500'">
                                <input type="radio" name="payment_option" value="full" x-model="paymentOption" class="sr-only">
                                <p class="font-bold text-sm text-white">Bayar Lunas</p>
                                <p class="text-[10px] font-mono text-gray-400 mt-0.5">Lebih hemat, slot aman 100%</p>
                                <p class="font-heading font-black text-base text-[#F2C94C] mt-1" x-text="'Rp ' + (pricing?.total_price ?? 0).toLocaleString('id-ID')"></p>
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-300 mb-2 block">Catatan Tambahan (opsional):</label>
                        <textarea name="notes" rows="2" placeholder="Tema spesifik, permintaan properti, dll..." class="w-full bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-3 rounded-xl text-sm focus:outline-none resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-full bg-[#E8451E] text-white font-extrabold text-sm hover:bg-[#d03a16] shadow-xl shadow-[#E8451E]/30 transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid fa-lock text-sm"></i>
                        Konfirmasi & Lanjut Pembayaran
                    </button>
                    <p class="text-center text-[10px] font-mono text-gray-500">Slot dikunci 15 menit setelah konfirmasi. Pastikan segera selesaikan pembayaran.</p>
                </form>
            </div>

        </div>

        {{-- RIGHT: Summary Card --}}
        <div class="lg:col-span-5">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5 sticky top-24">
                <h3 class="font-heading font-bold text-base text-white border-b border-[#2D2B26] pb-4">Ringkasan Booking</h3>

                <div class="space-y-3 text-xs font-mono text-gray-300">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Paket</span>
                        <span x-text="selectedPackage?.name ?? '—'" class="font-bold text-white text-right max-w-[60%]"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Ruangan</span>
                        <span x-text="selectedRoom?.name ?? '—'" class="font-bold text-white text-right max-w-[60%]"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tanggal</span>
                        <span x-text="selectedDate ?? '—'" class="text-[#F2C94C] font-bold"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Jam Sesi</span>
                        <span x-text="selectedSlot ? selectedSlot.start_time.substring(0,5) + ' – ' + selectedSlot.end_time.substring(0,5) : '—'" class="font-bold text-white"></span>
                    </div>
                </div>

                <div x-show="pricing" class="border-t border-[#2D2B26] pt-4 space-y-2 text-xs font-mono">
                    <div class="flex justify-between text-gray-400">
                        <span>Subtotal Paket</span>
                        <span x-text="'Rp ' + (pricing?.base_price ?? 0).toLocaleString('id-ID')"></span>
                    </div>
                    <template x-if="pricing?.markup_amount > 0">
                        <div class="flex justify-between text-amber-300">
                            <span>Markup Weekend/Peak</span>
                            <span x-text="'+Rp ' + (pricing?.markup_amount ?? 0).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <template x-if="pricing?.addons_total > 0">
                        <div class="flex justify-between text-gray-300">
                            <span>Total Add-on</span>
                            <span x-text="'+Rp ' + (pricing?.addons_total ?? 0).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <template x-if="pricing?.discount > 0">
                        <div class="flex justify-between text-emerald-400">
                            <span>Diskon Promo</span>
                            <span x-text="'-Rp ' + (pricing?.discount ?? 0).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-bold text-white pt-2 border-t border-[#2D2B26]">
                        <span class="text-base">TOTAL</span>
                        <span x-text="'Rp ' + (pricing?.total_price ?? 0).toLocaleString('id-ID')" class="text-[#F2C94C] font-heading text-xl"></span>
                    </div>
                </div>

                <div x-show="!pricing" class="border-t border-[#2D2B26] pt-4 text-center text-xs font-mono text-gray-500">
                    Lengkapi pilihan untuk melihat estimasi harga
                </div>

                {{-- Hemat Info --}}
                <div x-show="pricing?.cheapest_slot" class="bg-[#1F3A2E] border border-emerald-800/40 rounded-2xl p-3 text-[11px] font-mono text-emerald-200">
                    <i class="fa-solid fa-lightbulb text-[#F2C94C] mr-1.5"></i>
                    <span x-text="pricing?.cheapest_slot_label ?? ''"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bookingWizard(initRoom, initPackage) {
    return {
        step: 1,
        selectedPackageId: initPackage,
        selectedRoomId: initRoom,
        selectedDate: '',
        minDate: new Date().toISOString().split('T')[0],
        selectedSlot: null,
        slots: [],
        loadingSlots: false,
        peopleCount: 1,
        selectedAddons: [],
        promoCode: '',
        paymentOption: 'full',
        pricing: null,
        packages: @json($packages),
        rooms: @json($rooms->load('primaryImage')),

        get selectedPackage() {
            return this.packages.find(p => p.id === this.selectedPackageId);
        },
        get selectedRoom() {
            return this.rooms.find(r => r.id === this.selectedRoomId);
        },

        init() {
            if (this.selectedPackageId && this.selectedRoomId) this.step = 3;
            else if (this.selectedPackageId) this.step = 2;
        },

        selectSlot(slot) {
            this.selectedSlot = slot;
        },

        toggleAddon(id) {
            const idx = this.selectedAddons.indexOf(id);
            if (idx === -1) this.selectedAddons.push(id);
            else this.selectedAddons.splice(idx, 1);
        },

        async fetchSlots() {
            if (!this.selectedDate || !this.selectedRoomId || !this.selectedPackageId) return;
            this.loadingSlots = true;
            this.slots = [];
            this.selectedSlot = null;
            try {
                const res = await fetch(`/api/slots?room_id=${this.selectedRoomId}&date=${this.selectedDate}&package_id=${this.selectedPackageId}`);
                const data = await res.json();
                this.slots = data.slots ?? [];
            } catch(e) {
                this.slots = [];
            }
            this.loadingSlots = false;
        },

        async fetchPrice() {
            if (!this.selectedPackageId || !this.selectedDate || !this.selectedSlot) return;
            try {
                const payload = {
                    package_id: this.selectedPackageId,
                    date: this.selectedDate,
                    start_time: this.selectedSlot.start_time,
                    people_count: this.peopleCount,
                    addons: this.selectedAddons,
                    promo_code: this.promoCode,
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                };
                const res = await fetch('/api/calculate-price', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                this.pricing = data.pricing;
            } catch(e) {}
        },
    }
}
</script>
@endpush
@endsection
