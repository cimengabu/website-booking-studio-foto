@extends('layouts.admin')

@section('page-title', 'Kelola Booking')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <form method="GET" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode/Nama/HP..." class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>
            <div>
                <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Status</label>
                <select name="status" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl text-xs focus:outline-none">
                    <option value="">Semua Status</option>
                    @foreach([
                        'pending'     => 'Menunggu Pembayaran',
                        'confirmed'   => 'Dikonfirmasi',
                        'in_progress' => 'Sedang Berlangsung',
                        'completed'   => 'Selesai Sesi',
                        'delivered'   => 'Foto Siap (Delivery)',
                        'closed'      => 'Ditutup',
                        'rescheduled' => 'Rescheduled',
                        'cancelled'   => 'Dibatalkan',
                        'no_show'     => 'No-Show (Hangus)',
                        'expired'     => 'Kedaluwarsa',
                    ] as $key => $lbl)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Ruangan</label>
                <select name="room_id" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl text-xs focus:outline-none">
                    <option value="">Semua Ruangan</option>
                    @foreach($rooms as $r)
                        <option value="{{ $r->id }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Tanggal Sesi</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full bg-[#141311] border border-[#2D2B26] text-white px-3 py-2 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16] w-full">
                <i class="fa-solid fa-search mr-1.5"></i> Filter
            </button>
        </div>
    </form>

    <!-- Booking Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase">
                    <tr>
                        @foreach(['Kode Booking', 'Pelanggan', 'Ruangan / Paket', 'Tanggal Sesi', 'Jam', 'Total', 'Dibayar', 'Status', 'Aksi'] as $h)
                            <th class="text-left px-4 py-3">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($bookings as $b)
                        @php
                            $statusBadge = [
                                'pending'     => 'text-amber-300 bg-amber-500/10 border-amber-700/40',
                                'confirmed'   => 'text-emerald-300 bg-emerald-500/10 border-emerald-700/40',
                                'paid'        => 'text-emerald-300 bg-emerald-500/10 border-emerald-700/40',
                                'dp_paid'     => 'text-blue-300 bg-blue-500/10 border-blue-700/40',
                                'in_progress' => 'text-cyan-300 bg-cyan-500/10 border-cyan-700/40',
                                'checked_in'  => 'text-violet-300 bg-violet-500/10 border-violet-700/40',
                                'completed'   => 'text-indigo-300 bg-indigo-500/10 border-indigo-700/40',
                                'delivered'   => 'text-purple-300 bg-purple-500/10 border-purple-700/40',
                                'closed'      => 'text-gray-400 bg-gray-500/10 border-gray-700/40',
                                'rescheduled' => 'text-amber-300 bg-amber-500/10 border-amber-700/40',
                                'cancelled'   => 'text-rose-300 bg-rose-500/10 border-rose-700/40',
                                'no_show'     => 'text-rose-400 bg-rose-950/40 border-rose-800/50',
                                'expired'     => 'text-rose-400 bg-rose-900/20 border-rose-900/40',
                            ];
                        @endphp
                        <tr class="hover:bg-[#141311] transition-colors">
                            <td class="px-4 py-3 font-bold text-[#F2C94C]">{{ $b->booking_code }}</td>
                            <td class="px-4 py-3 text-gray-200">
                                <p class="font-semibold">{{ $b->user->name }}</p>
                                <p class="text-gray-500 text-[10px]">{{ $b->user->phone }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                <p>{{ $b->room->name }}</p>
                                <p class="text-gray-500 text-[10px]">{{ $b->package->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-300">{{ $b->booking_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ substr($b->start_time, 0, 5) }}</td>
                            <td class="px-4 py-3 font-bold text-white">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-bold text-emerald-400">Rp {{ number_format($b->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full border text-[10px] font-bold uppercase {{ $statusBadge[$b->status] ?? 'text-gray-400' }}">{{ $b->status_label }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="px-3 py-1.5 rounded-lg bg-[#141311] border border-[#2D2B26] hover:border-[#E8451E] text-xs font-bold text-gray-300 hover:text-[#E8451E] transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-12 text-gray-500">Tidak ada data booking yang ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#2D2B26]">{{ $bookings->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
