<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function createPayment(Booking $booking, string $kind = 'full'): Payment
    {
        $amount = 0.0;
        if ($kind === 'dp') {
            $dpPercent = (float) Setting::getByKey('dp_percent', 50);
            $amount = ((float) $booking->total_price * $dpPercent) / 100;
        } elseif ($kind === 'remaining') {
            $amount = (float) $booking->remaining_balance;
        } else {
            $amount = (float) $booking->total_price - (float) $booking->paid_amount;
        }

        $orderId = "SNAP-{$booking->booking_code}-".strtoupper(Str::random(4));

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'order_id' => $orderId,
            'payment_kind' => $kind,
            'payment_type' => 'midtrans_qris',
            'gross_amount' => $amount,
            'transaction_status' => 'pending',
            'snap_token' => 'SIMULATED-SNAP-'.Str::uuid(),
        ]);

        return $payment;
    }

    public function processPaymentSuccess(Payment $payment, string $paymentType = 'QRIS', array $raw = []): void
    {
        if ($payment->transaction_status === 'settlement' || $payment->transaction_status === 'success') {
            return; // Idempotent check
        }

        $payment->update([
            'transaction_status' => 'settlement',
            'payment_type' => $paymentType,
            'paid_at' => now(),
            'raw_response' => $raw,
        ]);

        $booking = $payment->booking;
        $newPaidAmount = (float) $booking->paid_amount + (float) $payment->gross_amount;
        $booking->paid_amount = $newPaidAmount;

        if ($newPaidAmount >= (float) $booking->total_price) {
            $booking->status = 'paid';
        } else {
            $booking->status = 'dp_paid';
        }

        $booking->save();

        ActivityLog::record($booking->user_id, 'payment_success', "Pembayaran {$payment->payment_kind} untuk {$booking->booking_code} sebesar Rp ".number_format($payment->gross_amount, 0, ',', '.').' berhasil.');

        if ($payment->payment_kind === 'remaining') {
            $this->notificationService->sendSettlementReceived($payment);
        } else {
            $this->notificationService->sendPaymentReceived($payment);
        }
    }
}
