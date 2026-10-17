@extends('layouts.admin')

@section('page-title', 'Galeri & Delivery Foto')

@section('content')
<div class="space-y-6">
    <!-- Header Summary Card -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#E8451E]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider
                        {{ auth()->user()->isPhotographer() ? 'bg-amber-500/10 text-amber-300 border border-amber-500/30' : 'bg-[#E8451E]/10 text-[#E8451E] border border-[#E8451E]/30' }}">
                        {{ auth()->user()->role_label }}
                    </span>
                    <span class="text-xs text-gray-400 font-mono">Workflow Hasil Pemotretan</span>
                </div>
                <h2 class="text-xl font-heading font-black text-white">
                    {{ auth()->user()->isPhotographer() ? 'Sesi Pemotretan & Unggah Hasil Saya' : 'Pusat Manajemen Galeri & Delivery Foto' }}
                </h2>
                <p class="text-xs text-gray-400 mt-1 max-w-2xl font-mono">
                    {{ auth()->user()->isPhotographer() 
                        ? 'Daftar booking yang ditugaskan kepada Anda. Unggah hasil foto (file hi-res atau URL) setelah pemotretan selesai agar pelanggan menerima notifikasi WhatsApp otomatis.' 
                        : 'Pantau ketersediaan hasil foto dari tim fotografer, kelola link delivery pelanggan, dan pastikan pengiriman foto tepat waktu.' }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-xs font-mono font-bold text-gray-300 hover:text-white transition-all flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-[#E8451E]"></i> Lihat Semua Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Table of Sessions -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-camera text-amber-400"></i>
                Daftar Sesi Pemotretan & Status Galeri
            </h3>
            <span class="text-xs font-mono text-gray-400">Total: {{ $bookings->total() }} Sesi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-3.5">Kode & Pelanggan</th>
                        <th class="text-left px-4 py-3.5">Studio & Paket</th>
                        <th class="text-left px-4 py-3.5">Jadwal Sesi</th>
                        <th class="text-left px-4 py-3.5">Fotografer Bertugas</th>
                        <th class="text-center px-4 py-3.5">Status Sesi</th>
                        <th class="text-center px-4 py-3.5">Hasil Foto</th>
                        <th class="text-center px-4 py-3.5">Delivery Klien</th>
                        <th class="text-right px-4 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($bookings as $b)
                        @php
                            $photoCount = $b->photoResults->count();
                            $isDelivered = $b->photoDelivery !== null;
                        @endphp
                        <tr class="hover:bg-[#141311]/60 transition-colors">
                            <!-- Kode & Pelanggan -->
                            <td class="px-4 py-3.5">
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="font-bold text-[#F2C94C] hover:underline block">
                                    {{ $b->booking_code }}
                                </a>
                                <p class="text-white font-sans font-medium text-xs mt-0.5">{{ $b->user->name }}</p>
                                <p class="text-[10px] text-gray-500">{{ $b->user->phone ?? $b->user->email }}</p>
                            </td>

                            <!-- Studio & Paket -->
                            <td class="px-4 py-3.5">
                                <span class="text-gray-200 font-sans font-medium block">{{ $b->room->name }}</span>
                                <span class="text-[10px] text-gray-500 font-mono">{{ $b->package->name }}</span>
                            </td>

                            <!-- Jadwal Sesi -->
                            <td class="px-4 py-3.5 text-gray-300">
                                <p class="text-white font-bold">{{ $b->booking_date->format('d M Y') }}</p>
                                <p class="text-[10px] text-gray-500">{{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }} WIB</p>
                            </td>

                            <!-- Fotografer Bertugas -->
                            <td class="px-4 py-3.5">
                                @if($b->photographer)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#141311] border border-[#2D2B26] text-amber-300 text-[11px] font-medium">
                                        <i class="fa-solid fa-camera text-[10px]"></i>
                                        {{ $b->photographer->name }}
                                        @if(auth()->id() === $b->photographer_id)
                                            <span class="text-[9px] bg-amber-500/20 text-amber-300 px-1 rounded">Saya</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#141311] border border-dashed border-[#2D2B26] text-gray-500 text-[10px]">
                                        <i class="fa-solid fa-user-slash text-[9px]"></i> Belum ditugaskan
                                    </span>
                                @endif
                            </td>

                            <!-- Status Sesi -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border
                                    @if($b->status === 'delivered') text-purple-300 bg-purple-500/10 border-purple-700/40
                                    @elseif($b->status === 'completed') text-indigo-300 bg-indigo-500/10 border-indigo-700/40
                                    @elseif($b->status === 'in_progress') text-cyan-300 bg-cyan-500/10 border-cyan-700/40
                                    @else text-gray-400 bg-gray-500/10 border-gray-700/40 @endif">
                                    {{ $b->status_label }}
                                </span>
                            </td>

                            <!-- Jumlah Foto Terunggah -->
                            <td class="px-4 py-3.5 text-center">
                                @if($photoCount > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold text-xs">
                                        <i class="fa-solid fa-images text-[10px]"></i> {{ $photoCount }} File
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-[11px]">
                                        <i class="fa-solid fa-cloud-arrow-up text-[10px]"></i> 0 File
                                    </span>
                                @endif
                            </td>

                            <!-- Status Delivery -->
                            <td class="px-4 py-3.5 text-center">
                                @if($isDelivered)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-bold" title="Terkirim pada {{ $b->photoDelivery->delivered_at?->format('d M Y H:i') }}">
                                        <i class="fa-solid fa-circle-check"></i> Terkirim
                                    </span>
                                    <p class="text-[9px] text-gray-500">Exp: {{ $b->photoDelivery->expires_at?->format('d M Y') }}</p>
                                @else
                                    <span class="text-[11px] text-gray-500">Belum Dikirim</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.photos.upload', $b->id) }}" 
                                       class="px-3 py-1.5 rounded-xl font-bold text-xs transition-all flex items-center gap-1.5
                                       {{ $photoCount > 0 ? 'bg-[#141311] border border-[#2D2B26] text-[#F2C94C] hover:border-[#F2C94C]' : 'bg-[#E8451E] hover:bg-[#d03a16] text-white shadow-sm' }}">
                                        <i class="fa-solid fa-cloud-arrow-up text-[10px]"></i>
                                        <span>{{ $photoCount > 0 ? 'Kelola (' . $photoCount . ')' : 'Unggah Foto' }}</span>
                                    </a>
                                    <a href="{{ route('admin.bookings.show', $b->id) }}" class="p-1.5 rounded-lg bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white hover:border-[#E8451E] transition-all" title="Detail Booking">
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-gray-500">
                                <i class="fa-solid fa-camera text-3xl mb-3 opacity-30"></i>
                                <p class="text-xs">Tidak ada sesi pemotretan aktif yang siap untuk unggah foto.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-[#2D2B26]">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
