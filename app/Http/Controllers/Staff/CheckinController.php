<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckinController extends Controller
{
    public function showScan()
    {
        return view('staff.scan');
    }

    public function processCheckin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ], [
            'token.required' => 'QR Token / Kode Booking wajib diisi.',
        ]);

        $token = trim($request->token);

        $booking = Booking::with(['user', 'room', 'package'])
            ->where('qr_token', $token)
            ->orWhere('booking_code', $token)
            ->first();

        if (! $booking) {
            return back()->withInput()->withErrors(['checkin' => 'Tiket / Kode Booking tidak ditemukan.']);
        }

        if (in_array($booking->status, ['checked_in', 'completed'])) {
            return back()->with('info', "Pelanggan {$booking->user->name} sudah check-in sebelumnya pada {$booking->checked_in_at?->format('H:i, d M Y')}.");
        }

        if ($booking->status === 'cancelled' || $booking->status === 'expired') {
            return back()->withErrors(['checkin' => "Booking ini berstatus {$booking->status} dan tidak berlaku."]);
        }

        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        ActivityLog::record(Auth::id(), 'staff_checkin', "Staff check-in booking {$booking->booking_code} milik {$booking->user->name}");

        return redirect()->route('staff.today')->with('success', "CHECK-IN BERHASIL! {$booking->user->name} ({$booking->room->name} - {$booking->start_time})");
    }

    public function todaySchedule()
    {
        $today = now()->format('Y-m-d');
        $bookings = Booking::with(['user', 'room', 'package'])
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['paid', 'dp_paid', 'checked_in', 'completed'])
            ->orderBy('start_time')
            ->get();

        return view('staff.today', compact('bookings'));
    }

    public function markCompleted(int $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'completed']);

        ActivityLog::record(Auth::id(), 'staff_completed', "Staff menyelesaikan sesi foto {$booking->booking_code}");

        return back()->with('success', "Sesi foto untuk {$booking->booking_code} telah diselesaikan!");
    }
}
