@extends('layouts.public')

@section('title', 'Pilihan Ruangan & Tema Studio — SnapSpace')
@section('meta_description', 'Pilih tema ruangan studio foto favoritmu di SnapSpace. Korean minimalist, vintage, boho, classic — semuanya dilengkapi lighting profesional dan remote shutter.')

@section('content')

{{-- ── PAGE HEADER ── --}}
<section class="bg-cream-warm py-20 border-b border-cream-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Ruangan & Tema</span>
        </nav>
        <div class="max-w-2xl">
            <p class="text-xs font-bold text-gold uppercase tracking-widest mb-3">Pilih Atmosfer Favoritmu</p>
            <h1 class="font-serif font-bold text-4xl sm:text-5xl text-ink mb-4">Ruangan & Tema Studio</h1>
            <p class="text-ink-muted text-lg leading-relaxed">Dari Korean aesthetic hingga vintage darkroom — semua ruangan dilengkapi lighting profesional, remote shutter nirkabel, dan properti foto pilihan.</p>
        </div>
    </div>
</section>

{{-- ── ROOMS GRID ── --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($rooms->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach($rooms as $room)
                    <article class="group bg-cream rounded-3xl overflow-hidden border border-cream-dark card-lift shadow-card flex flex-col reveal"
                             data-delay="{{ ($loop->index % 3) * 100 }}">

                        {{-- Image --}}
                        <a href="{{ route('rooms.show', $room->slug) }}" class="block relative aspect-[4/3] overflow-hidden bg-cream-dark flex-shrink-0">
                            <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800' }}"
                                 alt="{{ $room->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                 loading="lazy">

                            {{-- Theme badge --}}
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur-sm text-teal text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-white/50">
                                    {{ $room->theme }}
                                </span>
                            </div>

                            {{-- Photo count --}}
                            @if($room->images->count() > 1)
                                <div class="absolute top-4 right-4">
                                    <span class="bg-ink/60 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <i class="fa-solid fa-images text-[10px]"></i> {{ $room->images->count() }}
                                    </span>
                                </div>
                            @endif

                            {{-- Hover overlay CTA --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
                                <span class="inline-flex items-center gap-1.5 bg-white text-ink text-xs font-semibold px-3 py-1.5 rounded-full">
                                    <i class="fa-solid fa-eye text-gold text-[10px]"></i> Lihat Detail
                                </span>
                            </div>
                        </a>

                        {{-- Content --}}
                        <div class="p-5 flex flex-col flex-1">
                            <a href="{{ route('rooms.show', $room->slug) }}">
                                <h2 class="font-serif font-semibold text-xl text-ink group-hover:text-gold transition-colors mb-2">{{ $room->name }}</h2>
                            </a>
                            <p class="text-sm text-ink-muted leading-relaxed line-clamp-2 mb-4 flex-1">{{ $room->description }}</p>

                            {{-- Specs --}}
                            <div class="grid grid-cols-2 gap-2 mb-4 pt-4 border-t border-cream-dark">
                                @foreach([
                                    ['fa-users', 'text-gold', $room->capacity.' orang'],
                                    ['fa-expand', 'text-teal', $room->area_m2.' m²'],
                                    ['fa-camera-retro', 'text-success', 'Remote Shutter'],
                                    ['fa-bolt', 'text-gold', 'Lighting Studio'],
                                ] as [$icon, $color, $label])
                                    <div class="flex items-center gap-2 text-xs text-ink-muted">
                                        <i class="fa-solid fa-{{ $icon }} {{ $color }} flex-shrink-0"></i>
                                        {{ $label }}
                                    </div>
                                @endforeach
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-2">
                                <a href="{{ route('rooms.show', $room->slug) }}"
                                   class="flex-1 py-2.5 rounded-xl border border-cream-dark text-ink text-xs font-semibold text-center hover:border-gold hover:text-gold transition-all">
                                    Lihat Detail
                                </a>
                                <a href="{{ route('booking.wizard', ['room_id' => $room->id]) }}"
                                   class="flex-1 py-2.5 rounded-xl btn-gold text-white text-xs font-bold text-center btn-bounce shadow-warm">
                                    Booking Studio
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-2xl bg-cream border border-cream-dark flex items-center justify-center mb-4">
                    <i class="fa-solid fa-door-open text-2xl text-gold/50"></i>
                </div>
                <p class="font-serif font-semibold text-xl text-ink mb-2">Belum ada ruangan tersedia</p>
                <p class="text-sm text-ink-muted">Ruangan sedang dalam persiapan. Silakan cek kembali nanti.</p>
            </div>
        @endif
    </div>
</section>

{{-- ── WHY CHOOSE OUR ROOMS ── --}}
<section class="py-14 bg-cream border-t border-cream-dark">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 reveal">
            <h2 class="font-serif font-bold text-3xl text-ink">Semua Ruangan Dilengkapi</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 reveal" data-delay="100">
            @foreach([
                ['fa-camera', '#C8A27A', 'Kamera Full-Frame'],
                ['fa-bolt', '#2F4F4F', 'Lighting Godox'],
                ['fa-wifi', '#2E9E6B', 'WiFi Kencang'],
                ['fa-snowflake', '#3B82F6', 'AC & Nyaman'],
                ['fa-person-dress', '#C8A27A', 'Ruang Ganti'],
                ['fa-shield-halved', '#2F4F4F', 'Sesi Privat'],
            ] as [$icon, $color, $label])
                <div class="flex flex-col items-center gap-3 p-5 bg-white rounded-2xl border border-cream-dark text-center shadow-card">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: {{ $color }}15;">
                        <i class="fa-solid fa-{{ $icon }} text-base" style="color: {{ $color }}"></i>
                    </div>
                    <p class="text-xs font-semibold text-ink leading-snug">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
