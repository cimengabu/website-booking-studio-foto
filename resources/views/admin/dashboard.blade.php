@extends('layouts.admin')

@section('title', 'Dashboard — SnapSpace Admin')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── 1. FOTOGRAFER DASHBOARD VIEW ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(Auth::user()->isPhotographer())
        {{-- Photographer Greeting --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-[#1E1D1A] via-[#1A1917] to-[#1E1D1A] border border-[#2D2B26] p-6 rounded-3xl shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#F2C94C]/20 text-[#F2C94C] flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div>
                    <h2 class="font-heading font-bold text-xl text-white">Halo, {{ Auth::user()->name }} 📸</h2>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">Ruang kerja fotografer SnapSpace Studio</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.photos.index') }}" class="px-4 py-2.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white text-xs font-bold transition-all shadow-md">
                    <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> Upload Hasil Foto
                </a>
                <a href="{{ route('admin.calendar.index') }}" class="px-4 py-2.5 rounded-xl border border-[#2D2B26] bg-[#141311] hover:text-white text-gray-300 text-xs font-bold font-mono transition-colors">
                    <i class="fa-solid fa-calendar mr-1.5"></i> Kalender
                </a>
            </div>
        </div>

        {{-- Photographer Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#F2C94C]/20 text-[#F2C94C] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <span class="text-[10px] font-mono text-gray-500 uppercase">Hari Ini</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Sesi Saya Hari Ini</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $photographerStats['today_sessions'] ?? 0 }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="text-[10px] font-mono text-amber-500 uppercase font-bold">Perlu Aksi</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Menunggu Upload Foto</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $photographerStats['pending_uploads'] ?? 0 }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <span class="text-[10px] font-mono text-gray-500 uppercase">Total</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Jadwal Mendatang</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $photographerStats['upcoming_sessions'] ?? 0 }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-500 uppercase font-bold">Selesai</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Sesi Sudah Diupload</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $photographerStats['completed_uploads'] ?? 0 }}</p>
            </div>
        </div>

        {{-- Sesi Menunggu Upload Foto Table --}}
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-lg text-white flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-[#E8451E]"></i> Sesi Menunggu Upload Foto
                    </h3>
                    <p class="text-xs text-gray-400 font-mono">Sesi yang telah selesai dan membutuhkan upload file foto hasil jepretan</p>
                </div>
                <a href="{{ route('admin.photos.index') }}" class="text-xs font-semibold text-[#E8451E] hover:underline font-mono">
                    Semua Galeri <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs font-sans">
                    <thead class="bg-[#141311] text-gray-400 font-mono text-[10px] uppercase border-b border-[#2D2B26]">
                        <tr>
                            <th class="text-left px-4 py-3">Kode Booking</th>
                            <th class="text-left px-4 py-3">Pelanggan</th>
                            <th class="text-left px-4 py-3">Ruangan & Paket</th>
                            <th class="text-left px-4 py-3">Tanggal & Jam</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-right px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2D2B26]">
                        @forelse($myPendingUploads as $b)
                            <tr class="hover:bg-[#171614] transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-[#F2C94C]">{{ $b->booking_code }}</td>
                                <td class="px-4 py-3 font-medium text-white">{{ $b->user?->name ?? 'Guest' }}</td>
                                <td class="px-4 py-3 text-gray-300">
                                    <p>{{ $b->room?->name }}</p>
                                    <p class="text-[10px] text-gray-500 font-mono">{{ $b->package?->name }}</p>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-300">
                                    {{ date('d M Y', strtotime($b->booking_date)) }} · {{ substr($b->start_time, 0, 5) }} WIB
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                        Perlu Upload
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.photos.upload', $b->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#E8451E] hover:bg-[#d03a16] text-white text-xs font-bold transition-all shadow-sm">
                                        <i class="fa-solid fa-upload text-[10px]"></i> Upload Foto
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-gray-500 font-mono text-xs">
                                    <i class="fa-solid fa-circle-check text-2xl text-emerald-500/40 mb-2 block"></i>
                                    Hebat! Semua sesi foto yang selesai telah diunggah hasilnya.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── 2. STAFF / KASIR DASHBOARD VIEW ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @elseif(Auth::user()->isStaff())
        {{-- Staff Operational Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <span class="text-[10px] font-mono text-gray-500 uppercase">Hari Ini</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Sesi Hari Ini</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $todayBookings->count() }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-500 uppercase font-bold">Checked-In</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Sudah Check-In</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $checkedInTodayCount }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <span class="text-[10px] font-mono text-amber-500 uppercase font-bold">Pending</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Menunggu Pembayaran</p>
                <p class="font-heading font-black text-2xl sm:text-3xl text-white mt-1">{{ $pendingPaymentsCount }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#E8451E]/20 text-[#E8451E] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <span class="text-[10px] font-mono text-gray-500 uppercase">Aksi</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Booking Walk-In</p>
                <a href="{{ route('admin.bookings.walkin') }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-[#E8451E] hover:underline">
                    Buat Sekarang <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── 3. OWNER / ADMIN FULL ANALYTICS VIEW ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @else
        <!-- Financial Stats Cards Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#E8451E]/20 text-[#E8451E] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">Hari Ini</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Pendapatan Hari Ini</p>
                <p class="font-heading font-black text-xl sm:text-2xl text-white mt-1">Rp {{ number_format($incomeToday, 0, ',', '.') }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#F2C94C]/20 text-[#F2C94C] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <span class="text-[10px] font-mono text-[#F2C94C] font-bold bg-[#F2C94C]/10 px-2 py-0.5 rounded-full border border-[#F2C94C]/20">Bulan Ini</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Pendapatan Bulan Ini</p>
                <p class="font-heading font-black text-xl sm:text-2xl text-white mt-1">Rp {{ number_format($incomeMonth, 0, ',', '.') }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <span class="text-[10px] font-mono text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-full border border-blue-500/20">Booking</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Total Booking Bulan Ini</p>
                <p class="font-heading font-black text-xl sm:text-2xl text-white mt-1">{{ $totalBookingsMonth }}</p>
            </div>

            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">Member</span>
                </div>
                <p class="text-xs font-mono text-gray-400 uppercase">Total Pelanggan</p>
                <p class="font-heading font-black text-xl sm:text-2xl text-white mt-1">{{ $totalCustomers }}</p>
            </div>
        </div>

        <!-- Hourly Heatmap Chart + Top Packages -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 sm:gap-8">
            <!-- Heatmap Chart -->
            <div class="xl:col-span-8 bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Heatmap Jam Tersibuk</h3>
                        <p class="text-xs text-gray-400 font-mono">Distribusi booking berdasarkan jam operasional</p>
                    </div>
                </div>
                <canvas id="hourlyChart" height="100"></canvas>
            </div>

            <!-- Top Packages Leaderboard -->
            <div class="xl:col-span-4 bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl">
                <h3 class="font-heading font-bold text-lg text-white mb-6">Paket Terlaris</h3>
                <div class="space-y-4">
                    @foreach($topPackages as $pkg)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-full bg-[#141311] text-xs font-mono font-bold text-[#F2C94C] flex items-center justify-center border border-[#2D2B26]">{{ $loop->iteration }}</span>
                                <p class="text-xs font-semibold text-white">{{ $pkg->name }}</p>
                            </div>
                            <span class="font-mono text-xs font-bold text-gray-300">{{ $pkg->total_bookings }}x</span>
                        </div>
                        <div class="h-1.5 bg-[#141311] rounded-full overflow-hidden">
                            <div class="h-full bg-[#E8451E] rounded-full" style="width: {{ min(100, ($pkg->total_bookings / max(1, $topPackages->max('total_bookings'))) * 100) }}%"></div>
                        </div>
                    @endforeach
                    @if($topPackages->isEmpty())
                        <p class="text-xs text-gray-500 font-mono text-center py-4">Belum ada data booking</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── TABEL JADWAL HARI INI & BOOKING TERBARU (DENSE & CLEAN) ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-heading font-bold text-lg text-white">
                    {{ Auth::user()->isPhotographer() ? 'Jadwal Sesi Foto Saya' : 'Booking & Sesi Terbaru' }}
                </h3>
                <p class="text-xs text-gray-400 font-mono">Daftar booking aktif yang memerlukan monitoring dan operasional</p>
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-[#E8451E] hover:underline font-mono">
                Lihat Semua Booking <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-sans">
                <thead class="bg-[#141311] text-gray-400 font-mono text-[10px] uppercase border-b border-[#2D2B26]">
                    <tr>
                        <th class="text-left px-4 py-3">Kode Booking</th>
                        <th class="text-left px-4 py-3">Pelanggan</th>
                        <th class="text-left px-4 py-3">Ruangan & Paket</th>
                        <th class="text-left px-4 py-3">Tanggal & Jam</th>
                        <th class="text-left px-4 py-3">Fotografer</th>
                        <th class="text-left px-4 py-3">Total Biaya</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @php
                        $displayBookings = Auth::user()->isPhotographer() ? $myPhotographerBookings : $recentBookings;
                    @endphp
                    @forelse($displayBookings as $b)
                        @php
                            $statusBadge = [
                                'pending'     => 'bg-amber-500/10 text-amber-300 border-amber-700/40',
                                'confirmed'   => 'bg-emerald-500/10 text-emerald-300 border-emerald-700/40',
                                'paid'        => 'bg-emerald-500/10 text-emerald-300 border-emerald-700/40',
                                'dp_paid'     => 'bg-blue-500/10 text-blue-300 border-blue-700/40',
                                'in_progress' => 'bg-cyan-500/10 text-cyan-300 border-cyan-700/40',
                                'checked_in'  => 'bg-violet-500/10 text-violet-300 border-violet-700/40',
                                'completed'   => 'bg-indigo-500/10 text-indigo-300 border-indigo-700/40',
                                'delivered'   => 'bg-purple-500/10 text-purple-300 border-purple-700/40',
                                'closed'      => 'bg-gray-500/10 text-gray-400 border-gray-700/40',
                                'rescheduled' => 'bg-amber-500/10 text-amber-300 border-amber-700/40',
                                'cancelled'   => 'bg-rose-500/10 text-rose-300 border-rose-700/40',
                                'no_show'     => 'bg-rose-950/40 text-rose-400 border-rose-800/50',
                                'expired'     => 'bg-rose-900/20 text-rose-400 border-rose-900/40',
                            ];
                        @endphp
                        <tr class="hover:bg-[#171614] transition-colors">
                            <td class="px-4 py-3 font-mono font-bold">
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="text-[#F2C94C] hover:underline">
                                    {{ $b->booking_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-white text-xs">{{ $b->user?->name ?? 'Tamu Walk-in' }}</p>
                                <p class="text-[10px] text-gray-500 font-mono">{{ $b->user?->phone ?: '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                <p class="font-medium text-xs">{{ $b->room?->name }}</p>
                                <p class="text-[10px] text-gray-500 font-mono">{{ $b->package?->name }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-300 text-xs">
                                <p>{{ date('d M Y', strtotime($b->booking_date)) }}</p>
                                <p class="text-[10px] text-[#F2C94C]">{{ substr($b->start_time, 0, 5) }} — {{ substr($b->end_time, 0, 5) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($b->photographer)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-gray-300 font-medium">
                                        <i class="fa-solid fa-camera text-[#F2C94C] text-[10px]"></i>
                                        {{ $b->photographer->name }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-gray-600 font-mono italic">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-white text-xs">
                                Rp {{ number_format($b->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full border text-[10px] font-mono font-bold uppercase {{ $statusBadge[$b->status] ?? 'text-gray-400 border-gray-700' }}">
                                    {{ $b->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.bookings.show', $b->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-[#2D2B26] hover:bg-[#383630] text-gray-300 hover:text-white text-xs font-mono font-semibold transition-colors"
                                       title="Lihat Detail">
                                        Detail
                                    </a>
                                    @if(Auth::user()->isPhotographer() || Auth::user()->isOwner())
                                        <a href="{{ route('admin.photos.upload', $b->id) }}"
                                           class="p-1.5 rounded-lg text-[#F2C94C] hover:bg-[#F2C94C]/10 transition-colors"
                                           title="Upload Foto">
                                            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-gray-500 font-mono text-xs">
                                Tidak ada jadwal booking terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
@if(Auth::user()->isOwner() && !empty($hourlyDistribution))
<script>
    const hours = ['09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'];
    const hourlyData = @json($hourlyDistribution);
    const dataPoints = hours.map(h => hourlyData[h] || 0);

    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: {
            labels: hours.map(h => h + ':00'),
            datasets: [{
                label: 'Jumlah Sesi',
                data: dataPoints,
                backgroundColor: dataPoints.map(v => {
                    if (v >= 8) return 'rgba(232, 69, 30, 0.85)';
                    if (v >= 4) return 'rgba(242, 201, 76, 0.75)';
                    return 'rgba(31, 58, 46, 0.85)';
                }),
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E1D1A',
                    bodyColor: '#F3EFE6',
                    titleColor: '#F2C94C',
                    borderColor: '#2D2B26',
                    borderWidth: 1,
                }
            },
            scales: {
                x: { grid: { color: '#2D2B26' }, ticks: { color: '#8A857A', font: { family: 'JetBrains Mono', size: 11 } } },
                y: { grid: { color: '#2D2B26' }, ticks: { color: '#8A857A', font: { family: 'JetBrains Mono', size: 11 } } }
            }
        }
    });
</script>
@endif
@endpush
@endsection
