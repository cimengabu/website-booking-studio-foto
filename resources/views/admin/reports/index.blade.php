@extends('layouts.admin')

@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="space-y-8">
    <!-- Filter Period -->
    <form method="GET" class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-5 shadow-xl">
        <div class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from', now()->startOfMonth()->format('Y-m-d')) }}" class="bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>
            <div>
                <label class="block text-[10px] font-mono font-bold text-gray-400 uppercase mb-1.5">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}" class="bg-[#141311] border border-[#2D2B26] text-white px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:border-[#E8451E]">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#E8451E] text-white font-bold text-xs hover:bg-[#d03a16]">
                <i class="fa-solid fa-search mr-1.5"></i> Tampilkan
            </button>
            <a href="{{ route('admin.reports.export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="px-5 py-2.5 rounded-xl bg-[#1F3A2E] text-[#F2C94C] font-bold text-xs hover:bg-[#152820] flex items-center gap-2">
                <i class="fa-solid fa-table"></i> Export Excel
            </a>
            <a href="{{ route('admin.reports.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="px-5 py-2.5 rounded-xl bg-rose-900/60 text-rose-200 font-bold text-xs hover:bg-rose-900 flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </form>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach([
            ['Pendapatan Bruto', 'Rp ' . number_format($summary['total_revenue'], 0, ',', '.'), '#E8451E', 'fa-sack-dollar'],
            ['Total Booking Selesai', $summary['total_bookings'], '#F2C94C', 'fa-calendar-check'],
            ['Rata-rata per Booking', 'Rp ' . number_format($summary['avg_booking'], 0, ',', '.'), '#60A5FA', 'fa-calculator'],
            ['Booking Dibatalkan', $summary['cancelled_count'], '#EF4444', 'fa-ban'],
        ] as [$label, $value, $color, $icon])
            <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 flex items-center gap-4 shadow-xl">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0" style="background-color: {{ $color }}22; color: {{ $color }}">
                    <i class="fa-solid fa-{{ $icon }}"></i>
                </div>
                <div>
                    <p class="text-[10px] font-mono text-gray-400 uppercase">{{ $label }}</p>
                    <p class="font-heading font-black text-2xl text-white">{{ $value }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Revenue Chart -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl p-6 shadow-xl">
        <h3 class="font-heading font-bold text-lg text-white mb-6">Grafik Pendapatan Harian</h3>
        <canvas id="revenueChart" height="80"></canvas>
    </div>

    <!-- Breakdown Table -->
    <div class="bg-[#1E1D1A] border border-[#2D2B26] rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-[#2D2B26]">
            <h3 class="font-heading font-bold text-lg text-white">Rincian Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs font-mono">
                <thead class="bg-[#141311] text-gray-400 text-[10px] uppercase">
                    <tr>
                        @foreach(['Tanggal', 'Kode Booking', 'Pelanggan', 'Paket', 'Metode Bayar', 'Jenis', 'Jumlah'] as $h)
                            <th class="text-left px-4 py-3">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D2B26]">
                    @forelse($payments as $p)
                        <tr class="hover:bg-[#141311]">
                            <td class="px-4 py-3 text-gray-300">{{ $p->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-[#F2C94C] font-bold">{{ $p->booking->booking_code }}</td>
                            <td class="px-4 py-3 text-gray-200">{{ $p->booking->user->name }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $p->booking->package->name }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $p->payment_type }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full {{ $p->payment_kind === 'dp' ? 'bg-blue-500/10 text-blue-300 border-blue-700/40' : 'bg-emerald-500/10 text-emerald-300 border-emerald-700/40' }} border text-[10px] font-bold uppercase">
                                    {{ $p->payment_kind }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-white">Rp {{ number_format($p->gross_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-12 text-gray-500">Tidak ada transaksi dalam periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#2D2B26]">{{ $payments->withQueryString()->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    const dailyData = @json($dailyRevenue);
    const labels = Object.keys(dailyData);
    const values = Object.values(dailyData);
    
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: values,
                fill: true,
                borderColor: '#E8451E',
                backgroundColor: 'rgba(232, 69, 30, 0.08)',
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#E8451E',
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
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: { grid: { color: '#2D2B26' }, ticks: { color: '#8A857A', font: { family: 'JetBrains Mono', size: 10 } } },
                y: { grid: { color: '#2D2B26' }, ticks: { color: '#8A857A', font: { family: 'JetBrains Mono', size: 10 }, callback: v => 'Rp ' + v.toLocaleString('id-ID') } }
            }
        }
    });
</script>
@endpush
@endsection
