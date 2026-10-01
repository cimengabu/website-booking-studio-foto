@extends('layouts.app')

@section('title', '{{ $room->name }} — SnapSpace Studio')

@section('content')
<div class="py-12 max-w-5xl mx-auto px-4 space-y-8">
    <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-4 font-medium">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Semua Ruangan
    </a>

    <!-- Image Gallery -->
    <div class="grid {{ $room->images->count() > 1 ? 'grid-cols-2' : 'grid-cols-1' }} gap-4 rounded-3xl overflow-hidden">
        @foreach($room->images->take(2) as $img)
            <div class="aspect-[4/3] bg-gray-900 overflow-hidden">
                <img src="{{ $img->path }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" alt="{{ $room->name }}">
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-7 space-y-6">
            <div>
                <span class="text-[#E8451E] font-mono text-xs font-bold uppercase">{{ $room->theme }}</span>
                <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white mt-1">{{ $room->name }}</h1>
            </div>
            <p class="text-gray-300 text-sm leading-relaxed">{{ $room->description }}</p>

            <!-- Specs -->
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['fa-users', '#E8451E', "Kapasitas {$room->capacity} Orang"],
                    ['fa-expand', '#F2C94C', "Luas Ruangan {$room->area_m2} m²"],
                    ['fa-camera', '#60A5FA', 'Remote Shutter Nirkabel'],
                    ['fa-lightbulb', '#34D399', 'Softbox Lighting Profesional'],
                    ['fa-shield-halved', '#A78BFA', 'Sesi 100% Privat'],
                    ['fa-wind', '#F9A8D4', 'AC & Ruangan Bersih'],
                ] as [$icon, $color, $label])
                    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-2xl p-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: {{ $color }}22; color: {{ $color }}">
                            <i class="fa-solid fa-{{ $icon }} text-xs"></i>
                        </div>
                        <span class="text-xs font-medium text-gray-200">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Portfolio From This Room -->
            @if($room->portfolios->isNotEmpty())
                <div class="space-y-4">
                    <h3 class="font-heading font-bold text-xl text-white">Portofolio Ruangan Ini</h3>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($room->portfolios->take(6) as $port)
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-900">
                                <img src="{{ $port->image_path }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" alt="{{ $port->title }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sticky Booking Card -->
        <div class="lg:col-span-5">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 space-y-5 sticky top-28 shadow-2xl">
                <div class="text-center space-y-1">
                    <span class="text-xs font-mono text-gray-400 uppercase">Mulai Booking dari</span>
                    <p class="font-heading font-black text-3xl text-[#F2C94C]">Rp 99.000</p>
                    <p class="text-[11px] text-gray-500 font-mono">*Harga bergantung paket & jam pilihan</p>
                </div>
                <a href="{{ route('booking.wizard', ['room_id' => $room->id]) }}" class="block w-full py-4 rounded-full bg-[#E8451E] text-white font-extrabold text-sm text-center hover:bg-[#d03a16] shadow-xl shadow-[#E8451E]/30 transition-all">
                    <i class="fa-solid fa-calendar-plus mr-2"></i> Booking Ruangan Ini
                </a>
                @auth
                    <form method="POST" action="{{ route('favorites.toggle', $room->id) }}">
                        @csrf
                        <button type="submit" class="block w-full py-3 rounded-full bg-[#141311] border border-[#2D2B26] text-gray-300 hover:border-[#E8451E] hover:text-[#E8451E] font-semibold text-xs text-center transition-all">
                            <i class="fa-solid fa-heart mr-1.5"></i> Simpan ke Favorit
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
