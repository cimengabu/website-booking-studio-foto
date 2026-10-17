<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings')
            ->with(['bookings' => function ($q) {
                $q->latest()->take(3);
            }])
            ->where('role', 'customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15);

        // Calculate total spent for each customer
        foreach ($customers as $c) {
            $c->total_spent = Booking::where('user_id', $c->id)
                ->whereIn('status', ['confirmed', 'paid', 'dp_paid', 'in_progress', 'completed', 'delivered', 'closed'])
                ->sum('paid_amount');
            $c->is_vip = $c->total_spent >= 750000 || $c->bookings_count >= 3;
        }

        return view('admin.customers.index', compact('customers'));
    }

    public function export()
    {
        $customers = User::withCount('bookings')
            ->where('role', 'customer')
            ->latest()
            ->get();

        $filename = 'customers_snapspace_'.date('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($customers) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($output, ['ID', 'Nama', 'Email', 'No Telepon', 'Jumlah Booking', 'Total Belanja (Rp)', 'Status VIP', 'Terdaftar Pada']);

            foreach ($customers as $c) {
                $spent = Booking::where('user_id', $c->id)
                    ->whereIn('status', ['confirmed', 'paid', 'dp_paid', 'in_progress', 'completed', 'delivered', 'closed'])
                    ->sum('paid_amount');
                $isVip = ($spent >= 750000 || $c->bookings_count >= 3) ? 'VIP' : 'Reguler';

                fputcsv($output, [
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->phone ?: '-',
                    $c->bookings_count,
                    $spent,
                    $isVip,
                    $c->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
