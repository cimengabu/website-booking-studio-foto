<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * 1. Konfirmasi Booking Baru
     */
    public function sendBookingConfirmation(Booking $booking): array
    {
        $user = $booking->user;
        $room = $booking->room;
        $package = $booking->package;
        $phone = $user?->phone ?? '';

        $payUrl = route('booking.pay', $booking->booking_code);
        $checkUrl = route('booking.check', ['code' => $booking->booking_code]);

        $message = "📸 *KONFIRMASI BOOKING — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Terima kasih telah melakukan pemesanan sesi foto di SnapSpace Studio! Berikut rincian booking Anda:\n\n"
            ."• *Kode Booking:* {$booking->booking_code}\n"
            ."• *Paket:* {$package->name}\n"
            ."• *Ruangan:* {$room->name} ({$room->theme})\n"
            .'• *Tanggal:* '.date('d M Y', strtotime($booking->booking_date))."\n"
            ."• *Jam Sesi:* {$booking->start_time} - {$booking->end_time} WIB\n"
            ."• *Jumlah Orang:* {$booking->people_count} orang\n"
            .'• *Total Biaya:* Rp '.number_format($booking->total_price, 0, ',', '.')."\n"
            ."• *Status:* Menunggu Pembayaran\n\n"
            ."Silakan selesaikan pembayaran Anda melalui tautan berikut:\n"
            ."🔗 {$payUrl}\n\n"
            ."Cek detail booking kapan saja:\n"
            ."🔗 {$checkUrl}\n\n"
            .'_Harap selesaikan pembayaran sebelum batas waktu berakhir._';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_booking_confirmation',
            "Terkirim notifikasi konfirmasi booking untuk {$booking->booking_code}"
        );

        Log::info("Booking confirmation notification sent for {$booking->booking_code}");

        return [
            'type' => 'booking_confirmation',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * 2. Notifikasi Pembayaran Diterima (DP / Full)
     */
    public function sendPaymentReceived(Payment $payment): array
    {
        $booking = $payment->booking;
        $user = $booking->user;
        $phone = $user?->phone ?? '';

        $isDp = $payment->payment_kind === 'dp';
        $remaining = (float) $booking->remaining_balance;
        $checkUrl = route('booking.check', ['code' => $booking->booking_code]);

        $statusText = $isDp ? 'DP 50% Berhasil Diterima' : 'Pembayaran Lunas (100%)';

        $message = "✅ *PEMBAYARAN DITERIMA — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Pembayaran untuk booking *{$booking->booking_code}* telah kami terima dengan sukses!\n\n"
            .'• *Jumlah Dibayar:* Rp '.number_format($payment->gross_amount, 0, ',', '.')."\n"
            ."• *Metode:* {$payment->payment_type}\n"
            ."• *Status Booking:* {$statusText}\n";

        if ($isDp && $remaining > 0) {
            $message .= '• *Sisa Pelunasan:* Rp '.number_format($remaining, 0, ',', '.')." (Dapat dilunasi sebelum/saat sesi foto)\n";
        }

        $message .= "\n📍 *Lokasi Studio:*\n"
            ."SnapSpace Studio, Jl. Tunjungan No. 88, Surabaya\n\n"
            ."Tunjukkan kode booking atau QR check-in saat tiba di studio:\n"
            ."🔗 {$checkUrl}\n\n"
            .'Sampai jumpa di studio!';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_payment_received',
            "Notifikasi pembayaran {$payment->payment_kind} diterima untuk {$booking->booking_code}"
        );

        return [
            'type' => 'payment_received',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * 3. Notifikasi Pelunasan (Jika sebelumnya DP)
     */
    public function sendSettlementReceived(Payment $payment): array
    {
        $booking = $payment->booking;
        $user = $booking->user;
        $phone = $user?->phone ?? '';
        $checkUrl = route('booking.check', ['code' => $booking->booking_code]);

        $message = "🎉 *PELUNASAN SELESAI — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Pelunasan untuk booking *{$booking->booking_code}* sebesar Rp "
            .number_format($payment->gross_amount, 0, ',', '.')." telah berhasil!\n\n"
            ."• *Status:* LUNAS (Paid in Full)\n"
            .'• *Jadwal Sesi:* '.date('d M Y', strtotime($booking->booking_date))." pukul {$booking->start_time} WIB\n"
            ."• *Ruangan:* {$booking->room->name}\n\n"
            ."Cek tiket booking & QR Code Anda:\n"
            ."🔗 {$checkUrl}\n\n"
            .'Mohon hadir 10 menit sebelum sesi dimulai. Terima kasih!';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_settlement_received',
            "Notifikasi pelunasan DP terkirim untuk {$booking->booking_code}"
        );

        return [
            'type' => 'settlement_received',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * 4. Pengingat H-1 (Sehari Sebelum Sesi)
     */
    public function sendReminderHMinus1(Booking $booking): array
    {
        $user = $booking->user;
        $phone = $user?->phone ?? '';
        $checkUrl = route('booking.check', ['code' => $booking->booking_code]);

        $message = "⏰ *PENGINGAT SESI FOTO BESOK (H-1) — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Ini pengingat bahwa sesi foto Anda di SnapSpace Studio dijadwalkan untuk *BESOK*:\n\n"
            .'• *Tanggal:* '.date('d M Y', strtotime($booking->booking_date))."\n"
            ."• *Jam Sesi:* {$booking->start_time} - {$booking->end_time} WIB\n"
            ."• *Ruangan:* {$booking->room->name}\n"
            ."• *Paket:* {$booking->package->name}\n\n"
            ."📌 *Tips Persiapan:*\n"
            ."1. Hadir 10–15 menit sebelum sesi untuk persiapan kostum & briefing.\n"
            ."2. Siapkan konsep pose atau properti favorit kamu.\n"
            ."3. Tunjukkan tiket booking digital: {$checkUrl}\n\n"
            ."📍 Lokasi: Jl. Tunjungan No. 88, Surabaya\n"
            .'Hubungi kami jika ada kendala perjalanan: wa.me/6281234567890';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_reminder_h1',
            "Pengingat H-1 terkirim untuk booking {$booking->booking_code}"
        );

        return [
            'type' => 'reminder_h1',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * 5. Pengingat H-2 Jam (Sebelum Sesi Dimulai)
     */
    public function sendReminderHMinus2Hours(Booking $booking): array
    {
        $user = $booking->user;
        $phone = $user?->phone ?? '';
        $checkUrl = route('booking.check', ['code' => $booking->booking_code]);

        $message = "⚡ *SESI FOTO DALAM 2 JAM! — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Sesi foto Anda di ruangan *{$booking->room->name}* akan dimulai pukul *{$booking->start_time} WIB* (kurang lebih 2 jam lagi).\n\n"
            ."• *Kode Booking:* {$booking->booking_code}\n"
            ."• *Lokasi:* Jl. Tunjungan No. 88, Surabaya\n"
            ."• *QR Check-in:* {$checkUrl}\n\n"
            .'Harap pastikan Anda sudah dalam perjalanan agar tidak mengurangi durasi sesi foto. Tim kami siap menyambut Anda!';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_reminder_h2_hours',
            "Pengingat H-2 jam terkirim untuk booking {$booking->booking_code}"
        );

        return [
            'type' => 'reminder_h2_hours',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * Alias for sendReminderHMinus1
     */
    public function sendH1Reminder(Booking $booking): array
    {
        return $this->sendReminderHMinus1($booking);
    }

    /**
     * Alias for sendReminderHMinus2Hours
     */
    public function sendH2Reminder(Booking $booking): array
    {
        return $this->sendReminderHMinus2Hours($booking);
    }

    /**
     * 6. Reschedule / Pembatalan
     */
    public function sendRescheduleNotification(Booking $booking, string $oldDate, string $oldTime): array
    {
        $user = $booking->user;
        $phone = $user?->phone ?? '';
        $checkUrl = route('booking.check', ['code' => $booking->booking_code]);

        $message = "🗓️ *JADWAL BOOKING DIPERBARUI (Reschedule) — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Jadwal booking foto Anda telah berhasil diubah:\n\n"
            ."• *Kode Booking:* {$booking->booking_code}\n"
            .'• *Jadwal Lama:* '.date('d M Y', strtotime($oldDate))." ({$oldTime} WIB)\n"
            .'• *JADWAL BARU:* '.date('d M Y', strtotime($booking->booking_date))." ({$booking->start_time} - {$booking->end_time} WIB)\n"
            ."• *Ruangan:* {$booking->room->name}\n\n"
            ."Cek detail tiket booking Anda:\n"
            ."🔗 {$checkUrl}\n\n"
            .'Terima kasih atas pengertian Anda!';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_reschedule',
            "Notifikasi reschedule terkirim untuk booking {$booking->booking_code}"
        );

        return [
            'type' => 'reschedule',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    public function sendCancellationNotification(Booking $booking, ?string $reason = null): array
    {
        $user = $booking->user;
        $phone = $user?->phone ?? '';

        $message = "❌ *BOOKING DIBATALKAN — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Pemesanan sesi foto dengan kode *{$booking->booking_code}* telah dibatalkan.\n\n"
            .($reason ? "• *Alasan:* {$reason}\n" : '')
            ."Jika Anda tidak merasa mengajukan pembatalan atau membutuhkan bantuan refund/pertanyaan, silakan hubungi tim kami:\n"
            ."wa.me/6281234567890\n\n"
            .'Terima kasih.';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_cancelled',
            "Notifikasi pembatalan terkirim untuk booking {$booking->booking_code}"
        );

        return [
            'type' => 'cancellation',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * 7. Foto Siap Diunduh
     */
    public function sendPhotosReadyNotification(Booking $booking): array
    {
        $user = $booking->user;
        $phone = $user?->phone ?? '';
        $galleryUrl = route('my-bookings.show', $booking->booking_code);

        $message = "✨ *FOTO KAMU SUDAH SIAP DIUNDUH! — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Kabar gembira! Hasil foto sesi Anda di SnapSpace Studio ({$booking->booking_code}) telah selesai diproses dan siap diunduh!\n\n"
            ."• *Paket:* {$booking->package->name}\n"
            ."• *Ruangan:* {$booking->room->name}\n"
            ."• *Masa Aktif Tautan:* 30 hari ke depan\n\n"
            ."Lihat & unduh foto resolusi tinggi di galeri digital Anda:\n"
            ."🔗 {$galleryUrl}\n\n"
            .'Selamat menikmati hasil fotonya! Jangan lupa tag Instagram kami @snapspace.studio ya! 📸';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_photos_ready',
            "Notifikasi foto siap unduh terkirim untuk booking {$booking->booking_code}"
        );

        return [
            'type' => 'photos_ready',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * 8. Permintaan Ulasan Setelah Sesi Foto
     */
    public function sendReviewRequestNotification(Booking $booking): array
    {
        $user = $booking->user;
        $phone = $user?->phone ?? '';
        $reviewUrl = route('my-bookings.show', $booking->booking_code);

        $message = "⭐ *BAGAIMANA PENGALAMAN FOTO KAMU? — SnapSpace Studio*\n\n"
            ."Halo *{$user->name}*,\n"
            ."Terima kasih banyak telah mempercayakan momen spesial kamu di SnapSpace Studio!\n\n"
            ."Kami sangat senang bisa mengabadikan senyum Anda. Boleh luangkan waktu 1 menit untuk memberi ulasan & bintang?\n"
            ."Masukan Anda sangat berharga bagi peningkatan layanan kami:\n\n"
            ."🔗 {$reviewUrl}\n\n"
            .'Sebagai apresiasi, kamu akan mendapatkan kode promo diskon 15% untuk booking sesi foto berikutnya! 🎁';

        $waUrl = $this->buildWhatsAppUrl($phone, $message);

        ActivityLog::record(
            $user?->id,
            'notification_review_request',
            "Permintaan ulasan terkirim untuk booking {$booking->booking_code}"
        );

        return [
            'type' => 'review_request',
            'phone' => $phone,
            'message' => $message,
            'wa_url' => $waUrl,
        ];
    }

    /**
     * Build WhatsApp Direct Click Link
     */
    public function buildWhatsAppUrl(string $phone, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }

        return 'https://wa.me/'.$cleanPhone.'?text='.urlencode($message);
    }
}
