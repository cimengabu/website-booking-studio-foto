@extends('layouts.public')

@section('title', 'Galeri Portfolio — SnapSpace Studio Foto Surabaya')
@section('meta_description', 'Galeri foto asli pelanggan SnapSpace Studio. Browse portfolio wisuda, prewedding, keluarga, self-photo, dan produk. Filter kategori, lightbox preview.')

@section('content')

{{-- ── PAGE HEADER ── --}}
<section class="bg-cream-warm py-20 border-b border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Portfolio</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div>
                <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">100% Foto Asli Pelanggan</p>
                <h1 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-4">Galeri Portfolio Studio</h1>
                <p class="text-ink-muted text-lg max-w-xl">Semua foto diambil di studio SnapSpace oleh pelanggan nyata — bukan stock photo, bukan rekayasa. Ini adalah hasil karya mereka.</p>
            </div>
            <div class="text-right">
                <p class="font-serif font-bold text-4xl text-gold">{{ $portfolios->total() }}+</p>
                <p class="text-xs text-ink-muted mt-1">foto tersedia</p>
            </div>
        </div>
    </div>
</section>

{{-- ── FILTER TABS ── --}}
<div class="sticky top-[72px] z-30 bg-white/95 backdrop-blur-md border-b border-cream-dark shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            @php
                $allCats = [
                    'all'       => ['Semua', 'fa-grid-2'],
                    'self-photo'=> ['Self Photo', 'fa-person'],
                    'prewedding'=> ['Prewedding', 'fa-heart'],
                    'wisuda'    => ['Wisuda', 'fa-graduation-cap'],
                    'keluarga'  => ['Keluarga', 'fa-house-chimney-user'],
                    'produk'    => ['Produk', 'fa-box'],
                ];
                $activeFilter = $category ?? 'all';
            @endphp

            @foreach($allCats as $slug => [$label, $icon])
                <a href="{{ route('portfolio.index', $slug !== 'all' ? ['category' => $slug] : []) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 whitespace-nowrap
                          {{ $activeFilter === $slug || ($slug === 'all' && !$activeFilter)
                             ? 'bg-gold text-white shadow-warm'
                             : 'text-ink-muted hover:text-ink hover:bg-cream' }}">
                    <i class="fa-solid fa-{{ $icon }} text-xs"></i>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── MASONRY PORTFOLIO GRID with LIGHTBOX ── --}}
<section class="py-12 bg-white"
         x-data="lightbox()"
         @keydown.escape.window="close()"
         @keydown.arrow-left.window="prev()"
         @keydown.arrow-right.window="next()">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($portfolios->isNotEmpty())
            {{-- CSS columns masonry --}}
            <div class="columns-2 sm:columns-3 lg:columns-4 gap-4 space-y-4">
                @foreach($portfolios as $idx => $port)
                    <div class="break-inside-avoid mb-4 group relative overflow-hidden rounded-2xl bg-cream-dark border border-cream-dark card-lift cursor-zoom-in reveal"
                         data-delay="{{ min(($idx % 8) * 50, 300) }}"
                         @click="open({{ $idx }}, '{{ addslashes($port->image_path) }}', '{{ addslashes($port->title) }}', '{{ addslashes($port->category) }}', '{{ addslashes($port->room?->name ?? '') }}')">

                        <img src="{{ $port->image_path }}"
                             alt="{{ $port->title }}"
                             class="w-full object-cover group-hover:scale-105 transition-transform duration-700"
                             loading="lazy">

                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-gold/90 text-white mb-1.5 w-fit">
                                {{ $port->category }}
                            </span>
                            <p class="text-sm font-semibold text-white line-clamp-1">{{ $port->title }}</p>
                            @if($port->room)
                                <p class="text-xs text-white/70 mt-0.5">{{ $port->room->name }}</p>
                            @endif
                            <div class="mt-2 w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <i class="fa-solid fa-expand text-white text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Store images for lightbox JS --}}
                    <template x-if="false">
                        <span data-src="{{ $port->image_path }}"
                              data-title="{{ $port->title }}"
                              data-category="{{ $port->category }}"
                              data-room="{{ $port->room?->name }}"></span>
                    </template>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-12 flex justify-center reveal">
                {{ $portfolios->appends(request()->query())->links('vendor.pagination.portfolio') }}
            </div>

        @else
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-24 text-center reveal">
                <div class="w-20 h-20 rounded-3xl bg-cream border border-cream-dark flex items-center justify-center mb-6">
                    <i class="fa-regular fa-images text-3xl text-gold/50"></i>
                </div>
                <h3 class="font-serif font-semibold text-xl text-ink mb-2">Belum ada portfolio</h3>
                <p class="text-sm text-ink-muted max-w-xs">Portfolio untuk kategori ini belum tersedia. Coba kategori lain atau lihat semua portfolio.</p>
                <a href="{{ route('portfolio.index') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gold text-white text-sm font-semibold">
                    Lihat Semua Portfolio
                </a>
            </div>
        @endif
    </div>

    {{-- ── LIGHTBOX MODAL ── --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-ink/90 backdrop-blur-sm p-4"
         @click.self="close()"
         role="dialog" aria-modal="true">

        {{-- Close --}}
        <button @click="close()"
                class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors z-10"
                aria-label="Tutup">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        {{-- Prev --}}
        <button @click="prev()"
                class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                aria-label="Sebelumnya">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        {{-- Image --}}
        <div class="relative max-w-4xl w-full mx-auto flex flex-col items-center">
            <img :src="currentSrc"
                 :alt="currentTitle"
                 class="max-h-[80vh] w-auto object-contain rounded-2xl shadow-2xl"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

            <div class="mt-4 text-center">
                <span class="inline-block px-3 py-1 rounded-full bg-gold/20 text-gold text-xs font-bold uppercase tracking-wide mb-2" x-text="currentCategory"></span>
                <p class="text-white font-semibold" x-text="currentTitle"></p>
                <p class="text-white/60 text-sm" x-text="currentRoom" x-show="currentRoom"></p>
            </div>
        </div>

        {{-- Next --}}
        <button @click="next()"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                aria-label="Berikutnya">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</section>

{{-- ── CTA ── --}}
<section class="py-14 bg-cream border-t border-cream-dark">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <h2 class="font-serif font-bold text-3xl text-ink mb-4">Ingin Hasilmu Ada di Sini?</h2>
        <p class="text-ink-muted mb-7">Booking sesi foto dan buat kenangan yang akan Anda simpan selamanya.</p>
        <a href="{{ route('booking.wizard') }}"
           class="btn-gold btn-bounce inline-flex items-center gap-3 px-8 py-4 rounded-2xl text-white font-bold shadow-warm-lg">
            <i class="fa-solid fa-camera-retro"></i>
            Booking Studio Sekarang
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Collect all portfolio images on page for lightbox navigation
    const portfolioImages = @json($portfolios->map(fn($p) => [
        'src'      => $p->image_path,
        'title'    => $p->title,
        'category' => $p->category,
        'room'     => $p->room?->name ?? '',
    ])->values());

    function lightbox() {
        return {
            isOpen: false,
            currentIndex: 0,
            currentSrc: '',
            currentTitle: '',
            currentCategory: '',
            currentRoom: '',
            images: portfolioImages,

            open(idx, src, title, category, room) {
                this.currentIndex   = idx;
                this.currentSrc     = src;
                this.currentTitle   = title;
                this.currentCategory = category;
                this.currentRoom    = room;
                this.isOpen         = true;
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.isOpen = false;
                document.body.style.overflow = '';
            },

            prev() {
                if (!this.isOpen) return;
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                this._loadCurrent();
            },

            next() {
                if (!this.isOpen) return;
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                this._loadCurrent();
            },

            _loadCurrent() {
                const img = this.images[this.currentIndex];
                if (!img) return;
                this.currentSrc      = img.src;
                this.currentTitle    = img.title;
                this.currentCategory = img.category;
                this.currentRoom     = img.room;
            }
        };
    }
</script>
@endpush
