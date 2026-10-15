@extends('layouts.public')

@section('title', 'Ruangan Favorit Saya — SnapSpace Studio')
@section('meta_description', 'Lihat koleksi studio foto favorit Anda di SnapSpace Surabaya.')

@section('content')

{{-- ── HERO / HEADER ── --}}
<section class="bg-cream-warm border-b border-cream-dark py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-4">
            <a href="{{ route('home') }}" class="hover:text-gold transition-colors">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-ink font-medium">Ruangan Favorit</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-gold uppercase tracking-widest block mb-1">Daftar Impian Sesi Foto</span>
                <h1 class="font-serif font-bold text-3xl sm:text-4xl text-ink">Studio & Ruangan Favorit</h1>
            </div>
            <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-cream-dark text-xs font-bold text-ink hover:border-gold hover:text-gold shadow-xs transition-all self-start sm:self-auto">
                <i class="fa-solid fa-compass"></i>
                <span>Jelajahi Semua Studio</span>
            </a>
        </div>
    </div>
</section>

{{-- ── MAIN CONTENT ── --}}
<section class="py-12 bg-cream min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-8 p-4 rounded-2xl bg-success/10 border border-success/20 text-success text-xs font-medium flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($favorites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($favorites as $fav)
                    @php
                        $room = $fav->room;
                        $img = $room->primaryImage ? asset('storage/'.$room->primaryImage->image_path) : asset('images/hero-studio.jpg');
                    @endphp
                    <div class="bg-white rounded-3xl overflow-hidden border border-cream-dark shadow-card hover:shadow-warm transition-all duration-300 flex flex-col group">
                        {{-- Image & Badges --}}
                        <div class="relative h-64 overflow-hidden bg-ink-soft">
                            <img src="{{ $img }}" alt="{{ $room->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-transparent to-transparent"></div>

                            {{-- Capacity Badge --}}
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[11px] font-medium border border-white/20">
                                    <i class="fa-solid fa-users text-gold mr-1"></i> Kapasitas {{ $room->capacity }} org
                                </span>
                            </div>

                            {{-- Remove Favorite Button --}}
                            <form action="{{ route('favorites.toggle', $room->id) }}" method="POST" class="absolute top-4 right-4">
                                @csrf
                                <button type="submit" title="Hapus dari favorit" class="w-9 h-9 rounded-full bg-white/90 hover:bg-white text-danger flex items-center justify-center shadow-md transition-transform hover:scale-110">
                                    <i class="fa-solid fa-heart text-sm"></i>
                                </button>
                            </form>

                            {{-- Bottom Room Name in overlay --}}
                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="font-serif font-bold text-xl text-white tracking-tight">{{ $room->name }}</h3>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                            <div>
                                <p class="text-xs text-ink-muted line-clamp-2 leading-relaxed mb-4">
                                    {{ $room->description ?? 'Studio foto dengan tata cahaya premium dan dekorasi estetika terkini.' }}
                                </p>

                                {{-- Features chips --}}
                                @if(!empty($room->facilities))
                                    @php
                                        $facilities = is_array($room->facilities) ? $room->facilities : json_decode($room->facilities, true) ?? [];
                                    @endphp
                                    <div class="flex flex-wrap gap-1.5 mb-4">
                                        @foreach(array_slice($facilities, 0, 3) as $fac)
                                            <span class="px-2.5 py-1 rounded-lg bg-cream text-[11px] font-medium text-ink-soft border border-cream-dark">
                                                {{ $fac }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Price display --}}
                                <div class="pt-4 border-t border-cream-dark flex items-baseline justify-between">
                                    <span class="text-xs text-ink-muted">Mulai dari:</span>
                                    <span class="font-serif font-bold text-lg text-ink">
                                        Rp {{ number_format($room->price_per_slot ?? 150000, 0, ',', '.') }}
                                        <span class="font-sans text-[11px] font-normal text-ink-muted">/sesi</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <a href="{{ route('rooms.show', $room->slug) }}" class="py-2.5 px-3 rounded-xl border border-cream-dark hover:border-gold text-center text-xs font-bold text-ink hover:text-gold transition-colors">
                                    Lihat Detail
                                </a>
                                <a href="{{ route('booking.wizard') }}?room_id={{ $room->id }}" class="py-2.5 px-3 rounded-xl bg-gold hover:bg-gold-dark text-white text-center text-xs font-bold shadow-xs transition-colors flex items-center justify-center gap-1.5">
                                    <span>Pesan Slot</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="max-w-md mx-auto text-center py-16 px-4">
                <div class="w-20 h-20 rounded-full bg-cream-warm border border-cream-dark flex items-center justify-center mx-auto mb-6 text-gold">
                    <i class="fa-regular fa-heart text-3xl"></i>
                </div>
                <h3 class="font-serif font-bold text-2xl text-ink mb-2">Belum Ada Studio Favorit</h3>
                <p class="text-xs text-ink-muted leading-relaxed mb-6">
                    Temukan ruangan studio yang sesuai dengan konsep foto impian Anda dan tandai dengan ikon hati untuk akses cepat saat booking.
                </p>
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gold hover:bg-gold-dark text-white text-xs font-bold shadow-warm transition-all">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Eksplorasi Ruangan Studio</span>
                </a>
            </div>
        @endif

    </div>
</section>

@endsection
