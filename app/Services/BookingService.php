<?php

namespace App\Services;

use App\Exceptions\SlotUnavailableException;
use App\Models\ActivityLog;
use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Package;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        protected PricingService $pricingService,
        protected NotificationService $notificationService
    ) {}

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $roomId = $data['room_id'];
            $packageId = $data['package_id'];
            $userId = $data['user_id'];
            $bookingDate = $data['booking_date'];
            $startTime = $data['start_time'];
            $peopleCount = (int) ($data['people_count'] ?? 1);
            $paymentOption = $data['payment_option'] ?? 'full'; // dp or full
            $addonSelections = $data['addons'] ?? []; // [addon_id => qty]
            $promoCode = $data['promo_code'] ?? null;
            $redeemPoints = (int) ($data['redeem_points'] ?? 0);
            $notes = $data['notes'] ?? null;

            $package = Package::findOrFail($packageId);
            $duration = $package->duration_minutes;
            $buffer = (int) Setting::getByKey('buffer_minutes', 15);

            $startCarbon = Carbon::parse($bookingDate.' '.$startTime);
            $endCarbon = $startCarbon->copy()->addMinutes($duration);
            $endWithBufferCarbon = $endCarbon->copy()->addMinutes($buffer);

            $endTimeStr = $endCarbon->format('H:i:s');
            $endWithBufferStr = $endWithBufferCarbon->format('H:i:s');

            // Lock & Check for conflicting bookings
            $conflict = Booking::where('room_id', $roomId)
                ->whereDate('booking_date', $bookingDate)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed', 'dp_paid', 'paid', 'checked_in'])
                ->where(function ($query) use ($startTime, $endWithBufferStr) {
                    $query->where('start_time', '<', $endWithBufferStr)
                        ->where('end_time', '>', $startTime);
                })
                ->where(function ($query) {
                    $query->where('status', '!=', 'pending')
                        ->orWhere('expires_at', '>', now());
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new SlotUnavailableException('Slot waktu tersebut baru saja dipesan oleh pelanggan lain. Silakan pilih slot lainnya.');
            }

            // Check blocked slots
            $blocked = BlockedSlot::where(function ($q) use ($roomId) {
                $q->where('room_id', $roomId)->orWhereNull('room_id');
            })
                ->whereDate('date', $bookingDate)
                ->where('start_time', '<', $endTimeStr)
                ->where('end_time', '>', $startTime)
                ->exists();

            if ($blocked) {
                throw new SlotUnavailableException('Slot waktu tersebut sedang diblokir oleh admin.');
            }

            // Validate promo quota with pessimistic lock (Edge case: voucher habis saat checkout)
            $verifiedPromoCode = null;
            if ($promoCode) {
                $promo = Promo::where('code', strtoupper($promoCode))
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (! $promo) {
                    throw new \Exception("Kode voucher '{$promoCode}' tidak ditemukan atau sudah tidak aktif.");
                }

                if ($promo->used_count >= $promo->quota) {
                    throw new \Exception("Mohon maaf, kuota voucher '{$promoCode}' baru saja habis terpakai sebelum pesanan Anda selesai.");
                }

                $verifiedPromoCode = $promo->code;
                $promo->increment('used_count');
            }

            // Validate and deduct loyalty points atomically inside transaction
            $actualRedeemPoints = 0;
            if ($redeemPoints > 0 && $userId) {
                $user = User::where('id', $userId)->lockForUpdate()->first();
                if ($user && $user->loyalty_points > 0) {
                    $actualRedeemPoints = min($redeemPoints, $user->loyalty_points);
                    $user->decrement('loyalty_points', $actualRedeemPoints);
                }
            }

            // Calculate pricing
            $pricing = $this->pricingService->calculate(
                $packageId,
                $bookingDate,
                $startTime,
                $peopleCount,
                $addonSelections,
                $verifiedPromoCode,
                $actualRedeemPoints
            );

            // Generate Booking Code & QR Token
            $dateFormatted = Carbon::parse($bookingDate)->format('Ymd');
            $randomStr = strtoupper(Str::random(4));
            $bookingCode = "SNAP-{$dateFormatted}-{$randomStr}";
            $qrToken = "QR-{$bookingCode}-".Str::random(6);

            $expiryMinutes = (int) Setting::getByKey('booking_expiry_minutes', 30);

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $userId,
                'room_id' => $roomId,
                'package_id' => $packageId,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTimeStr,
                'people_count' => $peopleCount,
                'subtotal' => $pricing['subtotal'],
                'addons_total' => $pricing['addons_total'],
                'markup_amount' => $pricing['markup_amount'],
                'discount' => $pricing['discount'],
                'promo_code' => $verifiedPromoCode,
                'total_price' => $pricing['total_price'],
                'paid_amount' => 0,
                'payment_option' => $paymentOption,
                'status' => 'pending',
                'expires_at' => now()->addMinutes($expiryMinutes),
                'qr_token' => $qrToken,
                'notes' => $notes,
            ]);

            // Save Addons
            foreach ($pricing['addons_breakdown'] as $b) {
                BookingAddon::create([
                    'booking_id' => $booking->id,
                    'addon_id' => $b['addon_id'],
                    'qty' => $b['qty'],
                    'unit_price' => $b['unit_price'],
                    'subtotal' => $b['subtotal'],
                ]);
            }

            ActivityLog::record($userId, 'booking_created', "Membuat booking {$bookingCode}");

            $this->notificationService->sendBookingConfirmation($booking);

            return $booking;
        });
    }

    public function rescheduleBooking(Booking $booking, string $newDate, string $newStartTime): Booking
    {
        if (! $booking->isReschedulable()) {
            throw new \Exception('Booking ini tidak memenuhi syarat untuk reschedule (maksimal 1x dan minimal H-2 hari sebelum sesi).');
        }

        return DB::transaction(function () use ($booking, $newDate, $newStartTime) {
            $roomId = $booking->room_id;
            $duration = $booking->package->duration_minutes;
            $buffer = (int) Setting::getByKey('buffer_minutes', 15);

            $startCarbon = Carbon::parse($newDate.' '.$newStartTime);
            $endCarbon = $startCarbon->copy()->addMinutes($duration);
            $endWithBufferCarbon = $endCarbon->copy()->addMinutes($buffer);

            $endTimeStr = $endCarbon->format('H:i:s');
            $endWithBufferStr = $endWithBufferCarbon->format('H:i:s');

            // Lock & Check for conflicting bookings excluding current booking
            $conflict = Booking::where('room_id', $roomId)
                ->where('id', '!=', $booking->id)
                ->whereDate('booking_date', $newDate)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress', 'completed', 'dp_paid', 'paid', 'checked_in'])
                ->where(function ($query) use ($newStartTime, $endWithBufferStr) {
                    $query->where('start_time', '<', $endWithBufferStr)
                        ->where('end_time', '>', $newStartTime);
                })
                ->where(function ($query) {
                    $query->where('status', '!=', 'pending')
                        ->orWhere('expires_at', '>', now());
                })
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new SlotUnavailableException('Slot jadwal baru tersebut baru saja dipesan oleh pelanggan lain. Silakan pilih jadwal lainnya.');
            }

            // Check blocked slots
            $blocked = BlockedSlot::where(function ($q) use ($roomId) {
                $q->where('room_id', $roomId)->orWhereNull('room_id');
            })
                ->whereDate('date', $newDate)
                ->where('start_time', '<', $endTimeStr)
                ->where('end_time', '>', $newStartTime)
                ->exists();

            if ($blocked) {
                throw new SlotUnavailableException('Slot jadwal baru tersebut sedang diblokir oleh admin studio.');
            }

            $oldDate = $booking->booking_date->toDateString();
            $oldTime = $booking->start_time;

            if (! $booking->original_booking_date) {
                $booking->original_booking_date = $oldDate;
                $booking->original_start_time = $oldTime;
            }

            $booking->booking_date = $newDate;
            $booking->start_time = $newStartTime;
            $booking->end_time = $endTimeStr;
            $booking->reschedule_count = (int) $booking->reschedule_count + 1;
            $booking->save();

            ActivityLog::record(
                $booking->user_id,
                'booking_rescheduled',
                "Reschedule booking {$booking->booking_code} dari {$oldDate} ({$oldTime}) ke {$newDate} ({$newStartTime})"
            );

            $this->notificationService->sendRescheduleNotification($booking, $oldDate, $oldTime);

            return $booking;
        });
    }

    public function cancelBooking(Booking $booking, ?string $reason = null): Booking
    {
        if (! $booking->isCancellable()) {
            throw new \Exception('Booking dengan status ini tidak dapat dibatalkan.');
        }

        return DB::transaction(function () use ($booking, $reason) {
            $calc = $booking->calculateCancellationRefund();

            $booking->status = 'cancelled';
            $booking->cancellation_reason = $reason ?? 'Dibatalkan oleh pelanggan';
            $booking->refund_amount = $calc['refund_amount'];
            $booking->penalty_amount = $calc['penalty_amount'];
            $booking->save();

            if ($booking->promo_code) {
                Promo::where('code', $booking->promo_code)
                    ->where('used_count', '>', 0)
                    ->decrement('used_count');
            }

            ActivityLog::record(
                $booking->user_id,
                'booking_cancelled',
                "Booking {$booking->booking_code} dibatalkan. Refund: Rp ".number_format($calc['refund_amount'], 0, ',', '.')." ({$calc['tier']})"
            );

            $this->notificationService->sendCancellationNotification($booking, $reason);

            return $booking;
        });
    }

    public function expirePendingBookings(): int
    {
        $expired = Booking::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $b) {
            $b->update(['status' => 'expired']);

            if ($b->promo_code) {
                Promo::where('code', $b->promo_code)
                    ->where('used_count', '>', 0)
                    ->decrement('used_count');
            }

            ActivityLog::record($b->user_id, 'booking_expired', "Booking {$b->booking_code} otomatis expired.");
            $count++;
        }

        return $count;
    }
}
