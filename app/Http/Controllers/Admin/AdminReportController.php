<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        $payments = Payment::with(['booking.user', 'booking.room', 'booking.package'])
            ->where('transaction_status', 'settlement')
            ->whereBetween('paid_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->latest('paid_at')
            ->paginate(20);

        $allPayments = Payment::where('transaction_status', 'settlement')
            ->whereBetween('paid_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->get();

        $cancelledCount = Booking::whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->where('status', 'cancelled')
            ->count();

        $summary = [
            'total_revenue' => $allPayments->sum('gross_amount'),
            'total_bookings' => $allPayments->count(),
            'avg_booking' => $allPayments->count() > 0 ? $allPayments->sum('gross_amount') / $allPayments->count() : 0,
            'cancelled_count' => $cancelledCount,
        ];

        // Daily revenue grouped
        $dailyRevenue = $allPayments
            ->groupBy(fn ($p) => Carbon::parse($p->paid_at)->format('d M'))
            ->map(fn ($g) => $g->sum('gross_amount'))
            ->toArray();

        return view('admin.reports.index', compact('payments', 'summary', 'dailyRevenue', 'from', 'to'));
    }

    public function export(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));
        $format = $request->query('format', 'excel');

        $payments = Payment::with(['booking.user', 'booking.room', 'booking.package'])
            ->where('transaction_status', 'settlement')
            ->whereBetween('paid_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->latest('paid_at')
            ->get();

        if ($format === 'excel') {
            $fileName = "Laporan-Keuangan-SnapSpace-{$from}-sd-{$to}.xls";
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $totalRevenue = $payments->sum('gross_amount');

            $content = view('admin.reports.excel', [
                'payments' => $payments,
                'from' => $from,
                'to' => $to,
                'totalRevenue' => $totalRevenue,
            ])->render();

            return response($content, 200, $headers);
        }

        // CSV fallback
        $fileName = "Laporan-Keuangan-SnapSpace-{$from}-sd-{$to}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fwrite($file, "sep=,\r\n");   // Excel delimiter declaration
            fputcsv($file, ['No', 'ID Transaksi', 'Kode Booking', 'Pelanggan', 'Ruangan', 'Paket', 'Tipe', 'Metode', 'Jumlah', 'Tanggal Lunas']);
            $no = 1;
            foreach ($payments as $p) {
                fputcsv($file, [
                    $no++,
                    $p->order_id,
                    $p->booking->booking_code ?? '-',
                    $p->booking->user->name ?? '-',
                    $p->booking->room->name ?? '-',
                    $p->booking->package->name ?? '-',
                    strtoupper($p->payment_kind),
                    strtoupper($p->payment_type ?? 'QRIS'),
                    $p->gross_amount,
                    $p->paid_at?->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
