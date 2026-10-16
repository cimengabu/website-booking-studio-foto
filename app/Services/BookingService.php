<?php

namespace App\Services;

use App\Exceptions\SlotUnavailableException;
use App\Models\ActivityLog;
use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Package;
use App\Models\Setting;
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
                ->whereIn('status', ['pending', 'dp_paid', 'paid', 'checked_in'])
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

            // Calculate pricing
            $pricing = $this->pricingService->calculate(
                $packageId,
                $bookingDate,
                $startTime,
                $peopleCount,
                $addonSelections,
                $promoCode
            );

            // Generate Booking Code & QR Token
            $dateFormatted = Carbon::parse($bookingDate)->format('Ymd');
            $randomStr = strtoupper(Str::random(4));
            $bookingCode = "SNAP-{$dateFormatted}-{$randomStr}";
            $qrToken = "QR-{$bookingCode}-".Str::random(6);

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
                'total_price' => $pricing['total_price'],
                'paid_amount' => 0,
                'payment_option' => $paymentOption,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(15), // 15 min lock
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

    public function expirePendingBookings(): int
    {
        $expired = Booking::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $b) {
            $b->update(['status' => 'expired']);
            ActivityLog::record($b->user_id, 'booking_expired', "Booking {$b->booking_code} otomatis expired.");
            $count++;
        }

        return $count;
    }
}
