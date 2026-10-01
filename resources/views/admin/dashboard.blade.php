@extends('layouts.admin')

@section('title', 'Admin Dashboard — SnapSpace')
@section('page-title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center gap-4 shadow-xl">
            <div class="w-12 h-12 rounded-2xl bg-[#E8451E]/20 text-[#E8451E] flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <p class="text-xs font-mono text-gray-400 uppercase">Pendapatan Hari Ini</p>
                <p class="font-heading font-black text-2xl text-white">Rp {{ number_format($incomeToday, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center gap-4 shadow-xl">
            <div class="w-12 h-12 rounded-2xl bg-[#F2C94C]/20 text-[#F2C94C] flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xs font-mono text-gray-400 uppercase">Pendapatan Bulan Ini</p>
                <p class="font-heading font-black text-2xl text-white">Rp {{ number_format($incomeMonth, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center gap-4 shadow-xl">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <p class="text-xs font-mono text-gray-400 uppercase">Total Booking Bulan Ini</p>
                <p class="font-heading font-black text-2xl text-white">{{ $totalBookingsMonth }}</p>
            </div>
        </div>
        <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center gap-4 shadow-xl">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-mono text-gray-400 uppercase">Total Pelanggan</p>
                <p class="font-heading font-black text-2xl text-white">{{ $totalCustomers }}</p>
            </div>
        </div>
    </div>

    <!-- Hourly Heatmap Chart + Top Packages -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
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
                            <span class="w-7 h-7 rounded-full bg-[#141311] text-xs font-mono font-bold text-[#F2C94C] flex items-center justify-center">{{ $loop->iteration }}</span>
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

    <!-- Recent Bookings Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-heading font-bold text-lg text-white">Booking Terbaru</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-[#E8451E] hover:underline">Lihat Semua <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i></a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="text-gray-400 text-[10px] uppercase">
                    <tr class="border-b border-[#2D2B26]">
                        <th class="text-left pb-3 pr-4">Kode Booking</th>
                        <th class="text-left pb-3 pr-4">Pelanggan</th>
                        <th class="text-left pb-3 pr-4">Ruangan</th>
                        <th class="text-left pb-3 pr-4">Tanggal Sesi</th>
                        <th class="text-left pb-3 pr-4">Total</th>
                        <th class="text-left pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @foreach($recentBookings as $b)
                        @php
                            $statusBadge = [
                                'pending' => 'bg-amber-500/10 text-amber-300 border-amber-700/40',
                                'dp_paid' => 'bg-blue-500/10 text-blue-300 border-blue-700/40',
                                'paid' => 'bg-emerald-500/10 text-emerald-300 border-emerald-700/40',
                                'checked_in' => 'bg-violet-500/10 text-violet-300 border-violet-700/40',
                                'completed' => 'bg-gray-500/10 text-gray-300 border-gray-700/40',
                                'cancelled' => 'bg-rose-500/10 text-rose-300 border-rose-700/40',
                                'expired' => 'bg-rose-900/20 text-rose-400 border-rose-900/40',
                            ];
                        @endphp
                        <tr class="hover:bg-[#141311] transition-colors">
                            <td class="py-3 pr-4">
                                <a href="{{ route('admin.bookings.show', $b->id) }}" class="text-[#F2C94C] hover:underline font-bold">{{ $b->booking_code }}</a>
                            </td>
                            <td class="py-3 pr-4 text-gray-200">{{ $b->user->name }}</td>
                            <td class="py-3 pr-4 text-gray-400">{{ $b->room->name }}</td>
                            <td class="py-3 pr-4 text-gray-300">{{ $b->booking_date->format('d M Y') }} · {{ substr($b->start_time, 0, 5) }}</td>
                            <td class="py-3 pr-4 font-bold text-white">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                            <td class="py-3">
                                <span class="px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase {{ $statusBadge[$b->status] ?? '' }}">{{ $b->status }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const hours = ['09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20'];
    const hourlyData = @json($hourlyDistribution);
    const dataPoints = hours.map(h => hourlyData[h] || 0);

    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: {
            labels: hours.map(h => h + ':00'),
            datasets: [{
                label: 'Jumlah Booking',
                data: dataPoints,
                backgroundColor: dataPoints.map(v => {
                    if (v >= 8) return 'rgba(232, 69, 30, 0.85)';
                    if (v >= 4) return 'rgba(242, 201, 76, 0.75)';
                    return 'rgba(31, 58, 46, 0.85)';
                }),
                borderRadius: 10,
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
@endpush
@endsection
