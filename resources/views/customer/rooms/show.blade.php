@extends('layouts.public')

@section('title', $room->name.' — SnapSpace Studio')
@section('meta_description', 'Detail ruangan '.$room->name.' di SnapSpace Studio. '.$room->description.' Kapasitas '.$room->capacity.' orang, luas '.$room->area_m2.' m².')

@section('content')

{{-- ── IMAGE GALLERY HERO ── --}}
<section class="bg-cream-dark"
         x-data="{ activeImg: 0, images: {{ json_encode($room->images->pluck('path')->toArray() ?: ['https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=1200']) }} }">

    {{-- Main image --}}
    <div class="relative aspect-[21/9] overflow-hidden bg-cream-dark max-h-[520px]">
        <template x-for="(src, idx) in images" :key="idx">
            <img :src="src" :alt="'{{ $room->name }} foto ' + (idx+1)"
                 class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                 :class="activeImg === idx ? 'opacity-100' : 'opacity-0'"
                 loading="lazy">
        </template>

        {{-- Gradient for text readability --}}
        <div class="absolute inset-0 bg-gradient-to-t from-ink/40 to-transparent pointer-events-none"></div>

        {{-- Nav arrows (show if multiple images) --}}
        @if($room->images->count() > 1)
            <button @click="activeImg = (activeImg - 1 + images.length) % images.length"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/80 backdrop-blur-sm hover:bg-white transition-colors flex items-center justify-center shadow-card">
                <i class="fa-solid fa-chevron-left text-ink text-xs"></i>
            </button>
            <button @click="activeImg = (activeImg + 1) % images.length"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/80 backdrop-blur-sm hover:bg-white transition-colors flex items-center justify-center shadow-card">
                <i class="fa-solid fa-chevron-right text-ink text-xs"></i>
            </button>
        @endif

        {{-- Dots --}}
        @if($room->images->count() > 1)
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5">
                @foreach($room->images as $i => $img)
                    <button @click="activeImg = {{ $i }}"
                            class="w-2 h-2 rounded-full transition-all duration-200"
                            :class="activeImg === {{ $i }} ? 'bg-white w-5' : 'bg-white/50'"></button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Thumbnail strip --}}
    @if($room->images->count() > 1)
        <div class="bg-white border-b border-cream-dark py-3 overflow-x-auto">
            <div class="flex gap-2 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-max sm:w-auto">
                @foreach($room->images as $i => $img)
                    <button @click="activeImg = {{ $i }}"
                            class="w-16 h-12 rounded-xl overflow-hidden flex-shrink-0 border-2 transition-all duration-200"
                            :class="activeImg === {{ $i }} ? 'border-gold' : 'border-transparent opacity-60 hover:opacity-100'">
                        <img src="{{ $img->path }}" alt="" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</section>

{{-- ── BREADCRUMB ── --}}
<div class="bg-white border-b border-cream-dark py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <a href="{{ route('rooms.index') }}" class="hover:text-gold transition-colors">Ruangan</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">{{ $room->name }}</span>
        </nav>
    </div>
</div>

{{-- ── MAIN CONTENT + STICKY SIDEBAR ── --}}
<section class="py-12 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- ── LEFT: Room Detail ── --}}
            <div class="lg:col-span-7 space-y-8">

                {{-- Title --}}
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-3 py-1.5 rounded-full bg-teal/10 text-teal text-xs font-bold border border-teal/20">
                            {{ $room->theme }}
                        </span>
                        @if($room->is_active)
                            <span class="px-3 py-1.5 rounded-full bg-success/10 text-success text-xs font-bold border border-success/20 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-success animate-pulse"></span>
                                Tersedia
                            </span>
                        @endif
                    </div>
                    <h1 class="font-serif font-bold text-4xl text-ink mb-3">{{ $room->name }}</h1>
                    <p class="text-ink-muted text-lg leading-relaxed">{{ $room->description }}</p>
                </div>

                {{-- Specs Grid --}}
                <div class="bg-white rounded-2xl p-6 border border-cream-dark shadow-card">
                    <h2 class="font-serif font-semibold text-lg text-ink mb-5">Spesifikasi Ruangan</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach([
                            ['fa-users', '#C8A27A', 'Kapasitas', $room->capacity.' Orang'],
                            ['fa-expand', '#2F4F4F', 'Luas', $room->area_m2.' m²'],
                            ['fa-camera', '#C8A27A', 'Kamera', 'Full-Frame DSLR'],
                            ['fa-bolt', '#2E9E6B', 'Remote', 'Wireless Shutter'],
                            ['fa-lightbulb', '#C8A27A', 'Lighting', 'Softbox Godox'],
                            ['fa-shield-halved', '#2F4F4F', 'Privasi', '100% Tertutup'],
                        ] as [$icon, $color, $label, $val])
                            <div class="flex items-start gap-3 p-4 rounded-xl bg-cream border border-cream-dark">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $color }}15;">
                                    <i class="fa-solid fa-{{ $icon }} text-sm" style="color: {{ $color }}"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-ink-muted">{{ $label }}</p>
                                    <p class="font-semibold text-sm text-ink mt-0.5">{{ $val }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- What's Included --}}
                <div class="bg-white rounded-2xl p-6 border border-cream-dark shadow-card">
                    <h2 class="font-serif font-semibold text-lg text-ink mb-5">Yang Tersedia di Ruangan Ini</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach([
                            'Kamera DSLR/Mirrorless Full-Frame terkalibrasi',
                            'Tripod sturdy + remote shutter wireless',
                            'Lighting softbox profesional (Godox)',
                            'Hair light & fill light',
                            'Pilihan background warna (3–6 opsi)',
                            'Properti foto: kursi, frame, bunga, dll.',
                            'Cermin full-length untuk cek penampilan',
                            'Ruang ganti privat',
                            'AC + WiFi kencang',
                            'Tissue & cermin kecil makeup',
                        ] as $item)
                            <div class="flex items-center gap-3 text-sm text-ink-soft">
                                <div class="w-5 h-5 rounded-full bg-success/15 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-check text-success text-[10px]"></i>
                                </div>
                                {{ $item }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Portfolio from this room --}}
                @if($room->portfolios->isNotEmpty())
                    <div>
                        <h2 class="font-serif font-semibold text-xl text-ink mb-5">Foto dari Ruangan Ini</h2>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($room->portfolios->take(6) as $port)
                                <div class="aspect-square rounded-2xl overflow-hidden bg-cream-dark border border-cream-dark card-lift">
                                    <img src="{{ $port->image_path }}"
                                         alt="{{ $port->title }}"
                                         class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                         loading="lazy">
                                </div>
                            @endforeach
                        </div>
                        @if($room->portfolios->count() > 6)
                            <a href="{{ route('portfolio.index') }}" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-gold hover:underline">
                                Lihat semua portfolio <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Reviews --}}
                @if(isset($reviews) && $reviews->isNotEmpty())
                    <div>
                        <h2 class="font-serif font-semibold text-xl text-ink mb-5">Ulasan Pelanggan</h2>
                        <div class="space-y-4">
                            @foreach($reviews as $rev)
                                <div class="bg-white rounded-2xl p-5 border border-cream-dark shadow-card">
                                    <div class="flex gap-1 text-gold text-sm mb-3">
                                        @for($i=1;$i<=$rev->rating;$i++)<i class="fa-solid fa-star"></i>@endfor
                                    </div>
                                    <p class="text-sm text-ink-soft leading-relaxed mb-4">"{{ $rev->comment }}"</p>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-teal text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($rev->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-ink">{{ $rev->user->name }}</p>
                                            <p class="text-[11px] text-ink-muted">{{ $rev->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── RIGHT: Sticky Booking Card ── --}}
            <div class="lg:col-span-5">
                <div class="sticky top-24 space-y-4">

                    {{-- Booking Card --}}
                    <div class="bg-white rounded-3xl border border-cream-dark shadow-warm p-7">
                        <div class="text-center mb-6 pb-6 border-b border-cream-dark">
                            <p class="text-xs text-ink-muted mb-1">Mulai booking dari</p>
                            <p class="font-serif font-bold text-4xl text-gold">Rp 99.000</p>
                            <p class="text-xs text-ink-muted mt-1">*Harga bergantung paket & waktu pilihan</p>
                        </div>

                        {{-- Select Package to book --}}
                        <div class="space-y-3 mb-5">
                            <p class="text-xs font-bold text-ink-muted uppercase tracking-wide">Pilih paket untuk ruangan ini:</p>
                            @php $packages = App\Models\Package::where('is_active', true)->orderBy('price')->get(); @endphp
                            @foreach($packages as $pkg)
                                <a href="{{ route('booking.wizard', ['room_id' => $room->id, 'package_id' => $pkg->id]) }}"
                                   class="flex items-center justify-between p-4 rounded-xl border border-cream-dark hover:border-gold bg-cream hover:bg-gold/5 transition-all group">
                                    <div>
                                        <p class="font-semibold text-sm text-ink group-hover:text-gold transition-colors">{{ $pkg->name }}</p>
                                        <p class="text-xs text-ink-muted">{{ $pkg->duration_minutes }} menit · Maks. {{ $pkg->max_people }} orang</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-serif font-bold text-base text-gold">Rp {{ number_format($pkg->price, 0, ',', '.') }}</p>
                                        <i class="fa-solid fa-arrow-right text-gold/60 text-xs group-hover:translate-x-1 transition-transform"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <a href="{{ route('booking.wizard', ['room_id' => $room->id]) }}"
                           class="btn-gold btn-bounce flex items-center justify-center gap-2 w-full py-4 rounded-2xl text-white font-bold text-base shadow-warm">
                            <i class="fa-solid fa-calendar-plus"></i>
                            Booking Ruangan Ini
                        </a>

                        @auth
                            <form method="POST" action="{{ route('favorites.toggle', $room->id) }}" class="mt-3">
                                @csrf
                                <button type="submit"
                                        class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl border border-cream-dark text-ink-soft text-sm font-semibold hover:border-gold hover:text-gold transition-all">
                                    <i class="fa-{{ auth()->user()->favorites->contains('room_id', $room->id) ? 'solid text-red-400' : 'regular' }} fa-heart"></i>
                                    {{ auth()->user()->favorites->contains('room_id', $room->id) ? 'Hapus dari Favorit' : 'Simpan ke Favorit' }}
                                </button>
                            </form>
                        @endauth
                    </div>

                    {{-- Info card --}}
                    <div class="bg-cream rounded-2xl border border-cream-dark p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gold/15 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-circle-info text-gold text-sm"></i>
                            </div>
                            <div class="text-xs text-ink-muted leading-relaxed space-y-1">
                                <p><strong class="text-ink">Jam buka:</strong> Setiap hari 09.00–21.00 WIB</p>
                                <p><strong class="text-ink">Buffer antar sesi:</strong> 15 menit (kebersihan & setup)</p>
                                <p><strong class="text-ink">Konfirmasi:</strong> Instan — tiket QR langsung aktif</p>
                            </div>
                        </div>
                    </div>

                    {{-- WhatsApp --}}
                    <a href="https://wa.me/6281234567890?text=Halo%20SnapSpace%2C%20saya%20mau%20tanya%20tentang%20ruangan%20{{ urlencode($room->name) }}"
                       target="_blank" rel="noopener"
                       class="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-success/10 border border-success/30 text-success text-sm font-semibold hover:bg-success/20 transition-colors">
                        <i class="fa-brands fa-whatsapp text-base"></i> Tanya via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── RELATED ROOMS ── --}}
<section class="py-14 bg-white border-t border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <h2 class="font-serif font-semibold text-2xl text-ink">Ruangan Lainnya</h2>
            <a href="{{ route('rooms.index') }}" class="text-sm font-semibold text-gold hover:underline">Lihat Semua</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach($otherRooms as $other)
                <a href="{{ route('rooms.show', $other->slug) }}"
                   class="group flex items-center gap-4 p-4 bg-cream rounded-2xl border border-cream-dark hover:border-gold card-lift transition-all">
                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-cream-dark flex-shrink-0">
                        <img src="{{ $other->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=200' }}"
                             alt="{{ $other->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-sm text-ink group-hover:text-gold transition-colors truncate">{{ $other->name }}</p>
                        <p class="text-xs text-ink-muted">{{ $other->theme }}</p>
                        <p class="text-xs text-ink-muted mt-0.5">Maks. {{ $other->capacity }} orang · {{ $other->area_m2 }} m²</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gold/50 text-xs ml-auto flex-shrink-0"></i>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
