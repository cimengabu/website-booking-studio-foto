@extends('layouts.app')

@section('title', 'Galeri Portofolio — SnapSpace Studio')

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4">
    <div class="text-center space-y-3 mb-12">
        <span class="text-[#E8451E] font-mono text-xs uppercase font-bold tracking-widest">HASIL JEPRETAN REAL PELANGGAN KAMI</span>
        <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Galeri Portofolio</h1>
        <p class="text-xs text-gray-400 max-w-xl mx-auto">Semua foto diambil di studio SnapSpace oleh pelanggan asli — bukan stock photo, bukan rekayasa.</p>
    </div>

    <!-- Category Filter -->
    <div class="flex flex-wrap justify-center gap-3 mb-10">
        <a href="{{ route('portfolio.index') }}" class="px-5 py-2 rounded-full text-xs font-mono font-bold transition-all {{ !$category || $category === 'all' ? 'bg-[#E8451E] text-white' : 'bg-[#1E1D1A] border border-[#2D2B26] text-gray-300 hover:border-[#E8451E] hover:text-white' }}">
            Semua Kategori
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('portfolio.index', ['category' => $cat]) }}" class="px-5 py-2 rounded-full text-xs font-mono font-bold transition-all {{ $category === $cat ? 'bg-[#E8451E] text-white' : 'bg-[#1E1D1A] border border-[#2D2B26] text-gray-300 hover:border-[#E8451E] hover:text-white' }}">
                {{ ucfirst($cat) }}
            </a>
        @endforeach
    </div>

    <!-- Portfolio Grid -->
    <div class="columns-2 sm:columns-3 md:columns-4 gap-4 space-y-4">
        @foreach($portfolios as $port)
            <div class="break-inside-avoid bg-[#1E1D1A] rounded-2xl overflow-hidden border border-[#2D2B26] group hover:border-[#E8451E] transition-all">
                <div class="relative overflow-hidden">
                    <img src="{{ $port->image_path }}" class="w-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $port->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <span class="text-[10px] font-mono font-bold text-[#F2C94C] uppercase">{{ $port->category }}</span>
                        <p class="text-xs font-bold text-white mt-0.5">{{ $port->title }}</p>
                        @if($port->room)
                            <p class="text-[10px] text-gray-300 font-mono">{{ $port->room->name }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($portfolios->isEmpty())
        <div class="text-center py-20 text-gray-500 space-y-3">
            <i class="fa-solid fa-images text-5xl text-gray-700"></i>
            <p class="text-sm font-medium">Belum ada portofolio untuk kategori ini.</p>
        </div>
    @endif

    <div class="mt-10">{{ $portfolios->links() }}</div>
</div>
@endsection
