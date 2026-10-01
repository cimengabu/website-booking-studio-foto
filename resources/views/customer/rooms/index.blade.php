@extends('layouts.app')

@section('title', 'Pilihan Ruangan Studio — SnapSpace')

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4">
    <div class="text-center space-y-3 mb-12">
        <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">PILIH TEMA AMBIENCE FAVORIT KAMU</span>
        <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Ruangan & Tema Studio</h1>
        <p class="text-xs text-gray-400 max-w-xl mx-auto">Dari Korean aesthetic hingga vintage darkroom — semua dilengkapi pencahayaan profesional, remote shutter, dan properti foto pilihan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($rooms as $room)
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl overflow-hidden group hover:border-[#E8451E] transition-all shadow-xl flex flex-col">
                <div class="relative aspect-[16/10] overflow-hidden bg-gray-900">
                    <img src="{{ $room->primaryImage?->path ?? 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=800' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $room->name }}">
                    <span class="absolute top-4 left-4 bg-[#141311]/80 backdrop-blur-md px-3 py-1 rounded-full text-[#F2C94C] text-[10px] font-mono font-bold uppercase">{{ $room->theme }}</span>
                    @if($room->images->count() > 1)
                        <span class="absolute top-4 right-4 bg-[#141311]/80 backdrop-blur-md px-2.5 py-1 rounded-full text-gray-300 text-[10px] font-mono">
                            <i class="fa-solid fa-images mr-1"></i> {{ $room->images->count() }} foto
                        </span>
                    @endif
                </div>
                <div class="p-6 space-y-4 flex flex-col justify-between flex-grow">
                    <div class="space-y-3">
                        <h2 class="font-heading font-extrabold text-xl text-white group-hover:text-[#E8451E] transition-colors">{{ $room->name }}</h2>
                        <p class="text-xs text-gray-400 leading-relaxed">{{ $room->description }}</p>
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-[#2D2B26] text-xs font-mono text-gray-300">
                            <div class="flex items-center gap-2"><i class="fa-solid fa-users text-[#E8451E]"></i> {{ $room->capacity }} Orang</div>
                            <div class="flex items-center gap-2"><i class="fa-solid fa-expand text-[#F2C94C]"></i> {{ $room->area_m2 }} m²</div>
                            <div class="flex items-center gap-2"><i class="fa-solid fa-bolt text-blue-400"></i> Remote Shutter</div>
                            <div class="flex items-center gap-2"><i class="fa-solid fa-lightbulb text-emerald-400"></i> Lighting Soft Box</div>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('rooms.show', $room->slug) }}" class="flex-1 py-2.5 rounded-full bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-gray-200 font-semibold text-xs text-center transition-all">
                            Lihat Detail
                        </a>
                        <a href="{{ route('booking.wizard', ['room_id' => $room->id]) }}" class="flex-1 py-2.5 rounded-full bg-[#E8451E] text-white font-bold text-xs text-center hover:bg-[#d03a16] shadow-lg shadow-[#E8451E]/20 transition-all">
                            Booking Studio
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
