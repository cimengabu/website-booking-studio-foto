@extends('layouts.admin')

@section('page-title', 'Upload Hasil Foto — ' . $booking->booking_code)

@section('content')
<div class="space-y-6 max-w-4xl">
    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white font-medium">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Detail Booking
    </a>

    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
        <h2 class="font-heading font-bold text-xl text-white">
            Unggah Hasil Foto — {{ $booking->booking_code }}
        </h2>
        <p class="text-xs text-gray-400 font-mono">
            Pelanggan: {{ $booking->user->name }} · {{ $booking->room->name }} · {{ $booking->booking_date->format('d M Y') }} · {{ substr($booking->start_time, 0, 5) }}
        </p>

        <!-- Upload Form with Delivery Options -->
        <form method="POST" action="{{ route('admin.photos.store', $booking->id) }}" enctype="multipart/form-data" x-data="{ urlCount: 1, watermarkEnabled: true, isDragging: false }" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-gray-300">Masukkan URL Foto (Unsplash / Link Cloud):</p>
                    <button type="button" @click="urlCount++" class="text-xs font-semibold text-[#E8451E] hover:underline flex items-center gap-1.5">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kolom URL
                    </button>
                </div>
                <template x-for="i in urlCount" :key="i">
                    <div class="flex gap-3 items-center">
                        <input type="url" :name="`image_urls[${i-1}]`" placeholder="https://images.unsplash.com/photo-..." class="flex-grow bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none">
                    </div>
                </template>
            </div>

            <div class="border-t border-[#2D2B26] pt-4">
                <p class="text-xs font-semibold text-gray-300 mb-2">Upload File Foto Langsung (JPG/PNG/WEBP):</p>
                <div 
                    @dragover.prevent="isDragging = true" 
                    @dragleave.prevent="isDragging = false" 
                    @drop="isDragging = false"
                    :class="isDragging ? 'border-[#E8451E] bg-[#E8451E]/5' : 'border-[#2D2B26] bg-[#141311]'"
                    class="relative border-2 border-dashed rounded-2xl p-6 text-center transition-all">
                    <input type="file" name="photos[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
                        <div class="w-12 h-12 rounded-full bg-[#1E1D1A] flex items-center justify-center text-[#E8451E]">
                            <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-300">Drag & drop foto ke sini, atau <span class="text-[#E8451E] underline">pilih dari file</span></p>
                        <p class="text-[10px] text-gray-500 font-mono">Format didukung: JPG, PNG, WEBP (Maks 15MB/file)</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Settings: Watermark & Expiration -->
            <div class="border-t border-[#2D2B26] pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-[#141311] border border-[#2D2B26] rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-gray-300 flex items-center gap-2">
                            <i class="fa-solid fa-stamp text-amber-400"></i> Watermark Preview
                        </label>
                        <input type="checkbox" name="watermark_enabled" value="1" x-model="watermarkEnabled" class="accent-[#E8451E] w-4 h-4 rounded">
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed">
                        Tampilkan watermark transparan di foto pratinjau sebelum file diunduh lunas.
                    </p>
                    <div x-show="watermarkEnabled" class="pt-1">
                        <label class="block text-[10px] font-mono text-gray-400 mb-1">Teks Watermark:</label>
                        <input type="text" name="watermark_text" value="{{ $booking->photoDelivery?->watermark_text ?? 'SNAPSPACE STUDIO PREVIEW' }}" class="w-full bg-[#1E1D1A] border border-[#2D2B26] focus:border-[#E8451E] text-white px-3 py-1.5 rounded-lg text-xs font-mono">
                    </div>
                </div>

                <div class="bg-[#141311] border border-[#2D2B26] rounded-2xl p-4 space-y-3">
                    <label class="block text-xs font-semibold text-gray-300 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-sky-400"></i> Masa Aktif Link Unduh
                    </label>
                    <p class="text-[11px] text-gray-500 leading-relaxed">
                        Batas waktu pelanggan dapat mengunduh foto beresolusi penuh.
                    </p>
                    <select name="expires_days" class="w-full bg-[#1E1D1A] border border-[#2D2B26] focus:border-[#E8451E] text-white px-3 py-2 rounded-xl text-xs font-mono">
                        <option value="7">7 Hari (1 Minggu)</option>
                        <option value="14">14 Hari (2 Minggu)</option>
                        <option value="30" selected>30 Hari (Rekomendasi 1 Bulan)</option>
                        <option value="60">60 Hari (2 Bulan)</option>
                        <option value="90">90 Hari (3 Bulan)</option>
                        <option value="0">Selamanya (Tanpa Batas Waktu)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-[#2D2B26] pt-4">
                <label class="flex items-center gap-2.5 text-xs text-gray-300 cursor-pointer">
                    <input type="checkbox" name="send_notification" value="1" checked class="accent-[#E8451E] w-4 h-4 rounded">
                    <span>Kirim notifikasi otomatis WhatsApp & Email ke pelanggan bahwa foto sudah siap</span>
                </label>
                <button type="submit" class="px-8 py-3 rounded-full bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16] shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Simpan & Kirim Foto
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Photos -->
    @if($booking->photoResults->isNotEmpty())
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <h3 class="font-heading font-bold text-lg text-white">Foto Sudah Diunggah ({{ $booking->photoResults->count() }} foto)</h3>
                @if($booking->photoDelivery)
                    <span class="px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-700/40 text-emerald-300 font-mono text-[10px] font-bold uppercase">
                        <i class="fa-solid fa-circle-check mr-1"></i> Terkirim ke Pelanggan
                    </span>
                @endif
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($booking->photoResults as $photo)
                    <div class="group relative aspect-square rounded-2xl overflow-hidden bg-gray-900 border border-[#2D2B26]">
                        <img src="{{ $photo->path }}" class="w-full h-full object-cover" alt="{{ $photo->original_name }}">
                        <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2">
                            <p class="text-[10px] font-mono text-center text-white px-2 break-all">{{ $photo->original_name }}</p>
                            <form method="POST" action="{{ route('admin.photos.destroy', $photo->id) }}" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-[10px] font-bold hover:bg-rose-500">
                                    <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
