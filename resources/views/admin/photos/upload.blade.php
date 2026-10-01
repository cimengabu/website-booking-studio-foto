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

        <!-- Upload via URL Form (Demo Friendly) -->
        <form method="POST" action="{{ route('admin.photos.store', $booking->id) }}" enctype="multipart/form-data" x-data="{ urlCount: 1 }" class="space-y-6">
            @csrf
            
            <div class="space-y-4">
                <p class="text-xs font-semibold text-gray-300">Masukkan URL Foto (Unsplash / Link Publik):</p>
                <template x-for="i in urlCount" :key="i">
                    <div class="flex gap-3 items-center">
                        <input type="url" :name="`image_urls[${i-1}]`" placeholder="https://images.unsplash.com/..." class="flex-grow bg-[#141311] border border-[#2D2B26] focus:border-[#E8451E] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none">
                    </div>
                </template>
                <button type="button" @click="urlCount++" class="text-xs font-semibold text-[#E8451E] hover:underline flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah URL Foto
                </button>
            </div>

            <div class="border-t border-[#2D2B26] pt-4">
                <p class="text-xs font-semibold text-gray-300 mb-3">Atau Upload File Foto (JPG/PNG/WEBP):</p>
                <input type="file" name="photos[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" class="w-full bg-[#141311] border border-dashed border-[#2D2B26] hover:border-[#E8451E] text-gray-400 text-xs px-4 py-6 rounded-xl text-center cursor-pointer">
                <p class="text-[10px] text-gray-500 font-mono mt-1">*Maks 15MB per file. Format: JPG, PNG, WEBP.</p>
            </div>

            <button type="submit" class="px-8 py-3.5 rounded-full bg-[#E8451E] text-white font-bold text-sm hover:bg-[#d03a16] shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Notifikasi Pelanggan
            </button>
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
