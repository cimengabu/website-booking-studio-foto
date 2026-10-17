@extends('layouts.admin')

@section('page-title', 'Data Pelanggan (CRM)')

@section('content')
<div class="space-y-8" x-data="{
    detailModal: false,
    selectedCustomer: null,
    openHistory(c) {
        this.selectedCustomer = c;
        this.detailModal = true;
    }
}">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-users text-[#E8451E]"></i>
                Manajemen Pelanggan & Riwayat Booking (CRM)
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Pantau loyalitas pelanggan, riwayat transaksi, status VIP, serta akses kontak cepat WhatsApp.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.export') }}" class="px-5 py-2.5 rounded-xl bg-[#1F3A2E] text-[#F2C94C] font-bold text-xs hover:bg-[#152820] shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Ekspor Data Pelanggan (.CSV)
            </a>
        </div>
    </div>

    <!-- Search & Filter Filter Bar -->
    <form method="GET" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-grow relative">
                <i class="fa-solid fa-search absolute left-4 top-3.5 text-gray-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau nomor HP pelanggan..." class="w-full bg-[#141311] border border-[#2D2B26] text-white pl-10 pr-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white font-bold text-xs">
                Cari Pelanggan
            </button>
            @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white text-xs flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Table of Customers -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-address-book text-[#F2C94C]"></i>
                Daftar Pelanggan Terdaftar ({{ $customers->total() }} Orang)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3.5">Pelanggan</th>
                        <th class="text-left px-5 py-3.5">Kontak</th>
                        <th class="text-center px-5 py-3.5">Tier / Tag</th>
                        <th class="text-center px-5 py-3.5">Total Booking</th>
                        <th class="text-right px-5 py-3.5">Total Belanja (LTV)</th>
                        <th class="text-left px-5 py-3.5">Bergabung</th>
                        <th class="text-right px-5 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($customers as $c)
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $c->phone ?? '');
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                        @endphp
                        <tr class="hover:bg-[#141311]/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-[#E8451E]/10 border border-[#E8451E]/20 text-[#E8451E] font-bold flex items-center justify-center font-heading text-sm shrink-0">
                                        {{ strtoupper(substr($c->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-white font-sans text-sm">{{ $c->name }}</p>
                                        <span class="text-[10px] text-gray-500 font-mono">ID: #{{ $c->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-300">
                                <p>{{ $c->email }}</p>
                                @if($c->phone)
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-gray-400">{{ $c->phone }}</span>
                                        <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($c->name) }},%20terima%20kasih%20telah%20menghubungi%20SnapSpace%20Studio" target="_blank" class="text-emerald-400 hover:text-emerald-300 text-[11px]" title="Chat via WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-[10px] text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($c->is_vip)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-500/10 text-amber-300 border border-amber-500/30">
                                        <i class="fa-solid fa-crown text-[9px]"></i> VIP Gold
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-500/10 text-gray-400 border border-gray-700">
                                        Reguler
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-white text-xs px-2.5 py-1 rounded-lg bg-[#141311] border border-[#2D2B26]">
                                    {{ $c->bookings_count }}x
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-emerald-400 text-sm">
                                Rp {{ number_format($c->total_spent, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-gray-400">
                                {{ $c->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openHistory({{ $c }})" class="px-3 py-1.5 rounded-xl bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-gray-300 hover:text-white font-bold text-xs transition-all flex items-center gap-1.5">
                                        <i class="fa-solid fa-clock-rotate-left text-[10px] text-[#F2C94C]"></i> Riwayat
                                    </button>
                                    <a href="{{ route('admin.bookings.index', ['search' => $c->email]) }}" class="p-1.5 rounded-lg bg-[#141311] border border-[#2D2B26] text-gray-400 hover:text-white hover:border-[#E8451E] transition-all" title="Filter Booking">
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500">Tidak ada pelanggan yang cocok dengan pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-[#2D2B26]">
                {{ $customers->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Riwayat Booking Pelanggan -->
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" @keydown.escape.window="detailModal = false">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl relative" @click.away="detailModal = false">
            <div class="flex items-center justify-between border-b border-[#2D2B26] pb-4">
                <div>
                    <h3 class="font-heading font-bold text-lg text-white" x-text="selectedCustomer ? selectedCustomer.name : 'Detail Pelanggan'"></h3>
                    <p class="text-[11px] text-gray-400 font-mono mt-0.5" x-text="selectedCustomer ? (selectedCustomer.email + ' · ' + (selectedCustomer.phone || '-')) : ''"></p>
                </div>
                <button @click="detailModal = false" class="text-gray-500 hover:text-white p-1"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 text-xs font-mono">
                    <div class="p-3 rounded-2xl bg-[#141311] border border-[#2D2B26]">
                        <span class="text-[10px] text-gray-500 uppercase block">Total Booking</span>
                        <strong class="text-white text-base" x-text="selectedCustomer ? (selectedCustomer.bookings_count + ' Pesanan') : '0'"></strong>
                    </div>
                    <div class="p-3 rounded-2xl bg-[#141311] border border-[#2D2B26]">
                        <span class="text-[10px] text-gray-500 uppercase block">Total Belanja (LTV)</span>
                        <strong class="text-emerald-400 text-base" x-text="selectedCustomer ? ('Rp ' + new Intl.NumberFormat('id-ID').format(selectedCustomer.total_spent)) : '0'"></strong>
                    </div>
                </div>

                <div class="border-t border-[#2D2B26] pt-3">
                    <h4 class="text-xs font-mono font-bold text-gray-400 uppercase mb-2">3 Pesanan Terakhir</h4>
                    <template x-if="selectedCustomer && selectedCustomer.bookings && selectedCustomer.bookings.length > 0">
                        <div class="space-y-2">
                            <template x-for="b in selectedCustomer.bookings" :key="b.id">
                                <div class="p-3 rounded-xl bg-[#141311] border border-[#2D2B26] flex items-center justify-between text-xs font-mono">
                                    <div>
                                        <a :href="`/admin/bookings/${b.id}`" class="text-[#F2C94C] font-bold hover:underline" x-text="b.booking_code"></a>
                                        <p class="text-[10px] text-gray-400" x-text="b.booking_date + ' · ' + (b.start_time ? b.start_time.substring(0,5) : '')"></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-emerald-400 font-bold block" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(b.paid_amount || 0)"></span>
                                        <span class="text-[9px] uppercase px-2 py-0.5 rounded bg-gray-800 text-gray-300" x-text="b.status"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!selectedCustomer || !selectedCustomer.bookings || selectedCustomer.bookings.length === 0">
                        <p class="text-xs text-gray-500 italic py-2">Belum ada riwayat booking.</p>
                    </template>
                </div>
            </div>

            <div class="pt-4 border-t border-[#2D2B26] flex justify-end">
                <button type="button" @click="detailModal = false" class="px-5 py-2.5 rounded-xl bg-[#141311] border border-[#2D2B26] text-gray-300 hover:text-white text-xs font-mono font-bold">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
