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

    /**
     * Tampilan E-Tiket publik yang dapat dibuka langsung saat QR code dipindai oleh kamera HP atau resepsionis
     */
    public function showPublicTicket(string $code)
    {
        $cleanCode = trim($code);
        if (preg_match('/(SNAP-[A-Za-z0-9\-]+)/i', $cleanCode, $m)) {
            $cleanCode = strtoupper($m[1]);
        } elseif (preg_match('/(QR-[A-Za-z0-9\-]+)/i', $cleanCode, $m)) {
            $cleanCode = strtoupper($m[1]);
        }

        $booking = Booking::with(['user', 'room.images', 'package', 'payments', 'bookingAddons.addon'])
            ->where('booking_code', $cleanCode)
            ->orWhere('qr_token', $cleanCode)
            ->firstOrFail();

        return view('customer.booking.ticket', compact('booking'));
    }

    /**
     * Konfirmasi check-in langsung dari layar e-tiket
     */
    public function confirmArrivalTicket(Request $request, string $code)
    {
        $cleanCode = trim($code);
        if (preg_match('/(SNAP-[A-Za-z0-9\-]+)/i', $cleanCode, $m)) {
            $cleanCode = strtoupper($m[1]);
        }

        $booking = Booking::with(['user', 'room', 'package'])
            ->where('booking_code', $cleanCode)
            ->orWhere('qr_token', $cleanCode)
            ->firstOrFail();

        if (in_array($booking->status, ['in_progress', 'checked_in', 'completed', 'delivered'])) {
            return back()->with('info', "Pelanggan {$booking->user->name} sudah tercatat hadir pada {$booking->checked_in_at?->format('H:i, d M Y')}.");
        }

        if (in_array($booking->status, ['cancelled', 'expired', 'no_show', 'closed'])) {
            return back()->withErrors(['checkin' => "Booking ini berstatus {$booking->status_label} dan tidak berlaku."]);
        }

        $booking->update([
            'status' => 'in_progress',
            'checked_in_at' => now(),
        ]);

        $actorId = Auth::id() ?? $booking->user_id;
        ActivityLog::record($actorId, 'checkin_confirmed', "Check-in kehadiran studio untuk {$booking->booking_code} ({$booking->user->name})");

        return back()->with('success', "CHECK-IN BERHASIL! Selamat datang di SnapSpace Studio, {$booking->user->name} ({$booking->room->name}).");
    }

    /**
     * Proses Check-in dari Scanner Staff / Resepsionis
     * Mendukung input berupa: Kode Booking (SNAP-...), QR Token (QR-...), atau URL Penuh hasil scan kamera
     */
    public function processCheckin(Request $request)
    {
        $raw = trim($request->input('token', $request->input('qr_token', '')));

        if (empty($raw)) {
            return back()->withInput()->withErrors(['checkin' => 'QR Token / Kode Booking wajib diisi.']);
        }

        // Ekstraksi pintar jika scanner membaca URL penuh (misal: https://snapspace.web.id/ticket/SNAP-20261117-XXXX)
        $token = $raw;
        if (preg_match('/(SNAP-[A-Za-z0-9\-]+)/i', $raw, $matches)) {
            $token = strtoupper($matches[1]);
        } elseif (preg_match('/(QR-[A-Za-z0-9\-]+)/i', $raw, $matches)) {
            $token = strtoupper($matches[1]);
        }

        $booking = Booking::with(['user', 'room', 'package'])
            ->where('qr_token', $token)
            ->orWhere('booking_code', $token)
            ->first();

        if (! $booking) {
            return back()->withInput()->withErrors(['checkin' => "Tiket / Kode Booking '{$token}' tidak ditemukan di database."]);
        }

        if (in_array($booking->status, ['in_progress', 'checked_in', 'completed', 'delivered'])) {
            return back()->with('info', "Pelanggan {$booking->user->name} sudah check-in sebelumnya pada {$booking->checked_in_at?->format('H:i, d M Y')}.");
        }

        if (in_array($booking->status, ['cancelled', 'expired', 'no_show', 'closed'])) {
            return back()->withErrors(['checkin' => "Booking ini berstatus {$booking->status_label} dan tidak berlaku untuk check-in."]);
        }

        if ($booking->status === 'pending' && (float) $booking->paid_amount <= 0) {
            return back()->withErrors(['checkin' => "Booking {$booking->booking_code} belum dibayar (Status: Menunggu Pembayaran). Pelanggan wajib menyelesaikan pembayaran sebelum check-in."]);
        }

        $booking->update([
            'status' => 'in_progress',
            'checked_in_at' => now(),
        ]);

        ActivityLog::record(Auth::id(), 'staff_checkin', "Staff check-in booking {$booking->booking_code} milik {$booking->user->name}");

        $dpNotice = '';
        if ($booking->payment_option === 'dp' && $booking->remaining_balance > 0) {
            $dpNotice = ' — PERHATIAN: Memiliki sisa tagihan DP Rp '.number_format($booking->remaining_balance, 0, ',', '.').' yang perlu dilunasi di kasir.';
        }

        return redirect()->route('staff.today')->with('success', "CHECK-IN BERHASIL! {$booking->user->name} ({$booking->room->name} - {$booking->start_time}){$dpNotice}");
    }

    public function todaySchedule()
    {
        $today = now()->format('Y-m-d');
        $bookings = Booking::with(['user', 'room', 'package'])
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['confirmed', 'in_progress', 'completed', 'delivered', 'paid', 'dp_paid', 'checked_in'])
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
