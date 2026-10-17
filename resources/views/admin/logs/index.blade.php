@extends('layouts.admin')

@section('page-title', 'Log Aktivitas (Audit Trail)')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-heading font-black text-white flex items-center gap-2.5">
                <i class="fa-solid fa-list-check text-[#E8451E]"></i>
                Log Aktivitas Sistem (Audit Trail)
            </h2>
            <p class="text-xs text-gray-400 font-mono mt-1">Rekam jejak setiap aksi krusial pengguna: login, perubahan status booking, penugasan fotografer, dan unggah foto.</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26] flex items-center justify-between">
            <h3 class="text-sm font-heading font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-[#F2C94C]"></i>
                Riwayat Jejak Audit ({{ $logs->total() }} Log Tercatat)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-5 py-3.5">Waktu (WIB)</th>
                        <th class="text-left px-5 py-3.5">Aktor / Pengguna</th>
                        <th class="text-left px-5 py-3.5">Jenis Aksi</th>
                        <th class="text-left px-5 py-3.5">Detail Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($logs as $log)
                        @php
                            $actionColors = [
                                'login'               => 'text-blue-300 bg-blue-500/10 border-blue-500/30',
                                'create_booking'      => 'text-emerald-300 bg-emerald-500/10 border-emerald-500/30',
                                'status_update'       => 'text-amber-300 bg-amber-500/10 border-amber-500/30',
                                'assign_photographer' => 'text-purple-300 bg-purple-500/10 border-purple-500/30',
                                'admin_upload_photos' => 'text-cyan-300 bg-cyan-500/10 border-cyan-500/30',
                                'cancel_booking'      => 'text-rose-300 bg-rose-500/10 border-rose-500/30',
                                'reschedule'          => 'text-indigo-300 bg-indigo-500/10 border-indigo-500/30',
                            ];
                        @endphp
                        <tr class="hover:bg-[#141311]/50 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap text-gray-400">
                                <span class="text-white font-bold block">{{ $log->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-gray-500">{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>

                            <td class="px-5 py-4">
                                @if($log->user)
                                    <p class="font-bold text-white font-sans text-xs">{{ $log->user->name }}</p>
                                    <span class="text-[10px] text-gray-500 font-mono">{{ $log->user->email }} · {{ $log->user->role_label }}</span>
                                @else
                                    <span class="text-gray-500 text-xs italic">Sistem Otomatis</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $actionColors[$log->action] ?? 'text-gray-300 bg-gray-500/10 border-gray-600' }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-gray-200 font-sans text-xs">
                                {{ $log->description ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-500">Belum ada catatan log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-[#2D2B26]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
