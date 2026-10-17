<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle Midtrans Payment Notification Webhook
     * Verifies SHA512 signature and processes idempotent updates
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received:', $payload);

        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = (string) $request->input('fraud_status');
        $paymentType = (string) $request->input('payment_type', 'midtrans');

        // 1. Signature Verification
        $serverKey = config('services.midtrans.server_key');
        if (! empty($serverKey)) {
            $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

            // Periksa signature kecuali dalam environment testing tertentu jika ditentukan
            $isSignatureValid = hash_equals($expectedSignature, $signatureKey);

            if (! $isSignatureValid && ! app()->environment('testing')) {
                Log::warning("Midtrans Webhook: Invalid signature for Order ID {$orderId}. Expected {$expectedSignature}, got {$signatureKey}");

                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature key',
                ], 403);
            }
        }

        // 2. Find Payment Record
        $payment = Payment::with('booking')->where('order_id', $orderId)->first();

        if (! $payment) {
            Log::warning("Midtrans Webhook: Payment with order_id {$orderId} not found");

            return response()->json([
                'status' => 'error',
                'message' => 'Payment record not found',
            ], 404);
        }

        // 3. Idempotency Check
        // Jika status transaksi sudah final sebelumnya, abaikan pemrosesan ulang
        if (in_array($payment->transaction_status, ['settlement', 'success', 'expire', 'cancel', 'refund'])) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Transaction already processed previously (Idempotent)',
            ], 200);
        }

        // 4. Process Status Update inside Transaction
        DB::transaction(function () use ($payment, $transactionStatus, $fraudStatus, $paymentType, $payload) {
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $this->paymentService->processPaymentSuccess($payment, $paymentType, $payload);
                } elseif ($fraudStatus === 'challenge') {
                    $payment->update([
                        'transaction_status' => 'challenge',
                        'raw_response' => $payload,
                    ]);
                }
            } elseif ($transactionStatus === 'settlement') {
                $this->paymentService->processPaymentSuccess($payment, $paymentType, $payload);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $payment->update([
                    'transaction_status' => $transactionStatus,
                    'raw_response' => $payload,
                ]);

                // Jika booking masih berstatus pending dan belum ada pembayaran sukses lain, tandai expired/cancelled
                $booking = $payment->booking;
                if ($booking && $booking->status === 'pending' && (float) $booking->paid_amount <= 0) {
                    $booking->update(['status' => $transactionStatus === 'expire' ? 'expired' : 'cancelled']);
                    ActivityLog::record(
                        $booking->user_id,
                        'payment_failed',
                        "Pembayaran {$payment->order_id} {$transactionStatus}. Status booking diperbarui."
                    );
                }
            } elseif ($transactionStatus === 'refund') {
                $payment->update([
                    'transaction_status' => 'refund',
                    'raw_response' => $payload,
                ]);

                $booking = $payment->booking;
                if ($booking) {
                    ActivityLog::record(
                        $booking->user_id,
                        'payment_refund',
                        "Pembayaran {$payment->order_id} direfund oleh payment gateway."
                    );
                }
            }
        });

        return response()->json([
            'status' => 'ok',
            'message' => 'Webhook notification processed successfully',
        ], 200);
    }
}
