@extends('layouts.dashboard')

@section('title', 'Galeri Foto Saya — SnapSpace Studio')
@section('breadcrumb', 'Galeri Foto Saya')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8" data-aos="fade-up">
    <div>
        <h1 class="font-heading font-bold text-2xl sm:text-3xl text-white">Galeri Foto Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Koleksi hasil foto sesi studio Anda</p>
    </div>
    @if($bookings->isNotEmpty())
    <div class="flex items-center gap-2 bg-[#1E1D1A] border border-[#2D2B26] rounded-xl px-4 py-2.5">
        <i class="fa-solid fa-images text-[#E8451E] text-sm"></i>
        <span class="text-sm font-mono text-gray-400">
            {{ $bookings->sum(fn($b) => $b->photoResults->count()) }} foto dari {{ $bookings->count() }} sesi
        </span>
    </div>
    @endif
</div>

@if($bookings->isNotEmpty())

{{-- Sessions with photos --}}
<div class="space-y-8">
    @foreach($bookings as $booking)
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl overflow-hidden" data-aos="fade-up">

        {{-- Session header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 border-b border-[#2D2B26]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                    <img src="{{ $booking->room->images->first()?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=100' }}"
                         alt="{{ $booking->room->name }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs text-[#F2C94C] font-bold">{{ $booking->booking_code }}</span>
                        <span class="text-gray-700 text-xs">•</span>
                        <span class="text-xs text-gray-500">{{ $booking->photoResults->count() }} foto</span>
                    </div>
                    <p class="text-sm font-semibold text-white">{{ $booking->package->name }} — {{ $booking->room->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <i class="fa-solid fa-calendar text-[10px] mr-1"></i>{{ $booking->booking_date->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Download actions --}}
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                @if($booking->status === 'delivered' || $booking->remaining_balance <= 0)
                    <a href="{{ route('photos.zip', $booking->booking_code) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-[#E8451E] text-white rounded-xl text-xs font-semibold hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/20 transition-all hover:scale-[1.02]">
                        <i class="fa-solid fa-download text-[10px]"></i>Unduh Semua (ZIP)
                    </a>
                @else
                    <div class="flex items-center gap-2 px-4 py-2 bg-amber-500/10 border border-amber-500/25 text-amber-400 rounded-xl text-xs font-semibold">
                        <i class="fa-solid fa-lock text-[10px]"></i>Lunasi untuk mengunduh
                    </div>
                @endif

                @if($booking->photoDelivery)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-white rounded-xl text-xs font-semibold transition-colors">
                            <i class="fa-solid fa-share-nodes text-[10px]"></i>Bagikan
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute right-0 mt-2 w-64 bg-[#1E1D1A] border border-[#2D2B26] rounded-xl p-4 shadow-2xl z-20">
                            <p class="text-xs font-semibold text-white mb-2">Link Berbagi</p>
                            <div class="flex gap-2">
                                <input type="text" readonly
                                       value="{{ url('/gallery/share/' . $booking->photoDelivery->share_token) }}"
                                       class="flex-1 bg-[#141311] border border-[#2D2B26] rounded-lg px-2 py-1.5 text-[10px] text-gray-400 font-mono outline-none">
                                <button onclick="navigator.clipboard.writeText('{{ url('/gallery/share/' . $booking->photoDelivery->share_token) }}')"
                                        class="px-2.5 py-1.5 bg-[#E8451E]/15 text-[#E8451E] rounded-lg text-xs hover:bg-[#E8451E]/25 transition-colors">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                            @if($booking->photoDelivery->expires_at)
                            <p class="text-[10px] text-gray-600 mt-2 font-mono">
                                Berlaku hingga: {{ \Carbon\Carbon::parse($booking->photoDelivery->expires_at)->translatedFormat('d M Y') }}
                            </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Photo grid --}}
        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3"
                 x-data="{ lightbox: null, photos: {{ $booking->photoResults->map(fn($p) => ['id' => $p->id, 'url' => Storage::url($p->path), 'name' => $p->original_name])->toJson() }} }">

                @foreach($booking->photoResults as $idx => $photo)
                <div class="relative group aspect-square rounded-2xl overflow-hidden cursor-pointer bg-[#141311]"
                     @click="lightbox = {{ $idx }}">
                    <img src="{{ Storage::url($photo->path) }}"
                         alt="{{ $photo->original_name }}"
                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                         loading="lazy">

                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <button class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition-colors">
                            <i class="fa-solid fa-expand text-white text-xs"></i>
                        </button>
                        @if($booking->status === 'delivered' || $booking->remaining_balance <= 0)
                        <a href="{{ route('photos.download', $photo->id) }}"
                           class="w-8 h-8 rounded-full bg-[#E8451E]/80 backdrop-blur-sm flex items-center justify-center hover:bg-[#E8451E] transition-colors"
                           @click.stop>
                            <i class="fa-solid fa-download text-white text-xs"></i>
                        </a>
                        @endif
                    </div>

                    {{-- Photo number badge --}}
                    <span class="absolute bottom-2 left-2 px-1.5 py-0.5 bg-black/60 rounded text-[10px] font-mono text-white">{{ $idx + 1 }}</span>
                </div>
                @endforeach

                {{-- Lightbox --}}
                <div x-show="lightbox !== null" x-transition
                     class="fixed inset-0 z-50 bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
                     @keydown.escape.window="lightbox = null"
                     @click.self="lightbox = null">
                    <div class="relative max-w-4xl w-full" @click.stop>
                        <img :src="photos[lightbox]?.url" :alt="photos[lightbox]?.name"
                             class="w-full max-h-[80vh] object-contain rounded-2xl">

                        {{-- Nav arrows --}}
                        <button @click="lightbox = (lightbox - 1 + photos.length) % photos.length"
                                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-left text-white"></i>
                        </button>
                        <button @click="lightbox = (lightbox + 1) % photos.length"
                                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-right text-white"></i>
                        </button>

                        {{-- Close --}}
                        <button @click="lightbox = null"
                                class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-white"></i>
                        </button>

                        {{-- Caption + Download --}}
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-black/60 backdrop-blur-sm rounded-full px-4 py-2">
                            <span class="text-sm text-white font-mono" x-text="(lightbox + 1) + ' / ' + photos.length"></span>
                            @if($booking->status === 'delivered' || $booking->remaining_balance <= 0)
                            <a :href="'/photos/download/' + photos[lightbox]?.id"
                               class="text-xs text-[#E8451E] hover:text-[#F2C94C] font-semibold transition-colors" @click.stop>
                                <i class="fa-solid fa-download mr-1"></i>Unduh
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session footer --}}
        @if($booking->remaining_balance > 0)
        <div class="mx-5 sm:mx-6 mb-5 px-4 py-3 bg-amber-500/8 border border-amber-500/20 rounded-xl flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-amber-400 text-xs">
                <i class="fa-solid fa-lock text-[10px]"></i>
                <span>Sisa tagihan: <strong>Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</strong> — Lunasi untuk mengunduh foto</span>
            </div>
            <a href="{{ route('my-bookings.show', $booking->booking_code) }}"
               class="flex-shrink-0 px-4 py-1.5 bg-amber-500/15 border border-amber-500/30 text-amber-400 rounded-lg text-xs font-semibold hover:bg-amber-500/25 transition-colors">
                Lunasi →
            </a>
        </div>
        @endif
    </div>
    @endforeach
</div>

@else
{{-- Empty state --}}
<div class="bg-[#1E1D1A] border border-[#2D2B26] border-dashed rounded-3xl p-16 text-center" data-aos="fade-up">
    <div class="w-20 h-20 rounded-2xl bg-[#141311] border border-[#2D2B26] flex items-center justify-center mx-auto mb-5">
        <i class="fa-solid fa-images text-4xl text-gray-700"></i>
    </div>
    <h3 class="font-heading font-bold text-2xl text-white mb-2">Belum Ada Foto</h3>
    <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto leading-relaxed">
        Foto hasil sesi studio Anda akan muncul di sini setelah editor kami selesai memprosesnya.
        Status akan berubah menjadi <strong class="text-purple-400">"Foto Siap Diunduh"</strong> saat sudah tersedia.
    </p>
    <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('booking.wizard') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-[#E8451E] text-white rounded-2xl font-semibold text-sm hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/25 transition-all hover:scale-[1.02]">
            <i class="fa-solid fa-camera text-xs"></i> Booking Sesi Foto
        </a>
        <a href="{{ route('my-bookings.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-[#1E1D1A] border border-[#2D2B26] text-gray-300 rounded-2xl font-semibold text-sm hover:border-[#E8451E]/30 transition-colors">
            <i class="fa-solid fa-ticket text-xs"></i> Lihat Pesanan
        </a>
    </div>
</div>
@endif

@endsection
