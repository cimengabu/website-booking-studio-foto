<?php

namespace App\Services;

use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Room;
use App\Models\Setting;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Get available time slots for a specific room, date, and package duration.
     */
    public function getSlots(int $roomId, string $date, int $packageId): array
    {
        $package = Package::findOrFail($packageId);
        $durationMinutes = $package->duration_minutes;
        $bufferMinutes = (int) Setting::getByKey('buffer_minutes', 15);
        $openTimeStr = Setting::getByKey('open_time', '09:00');
        $closeTimeStr = Setting::getByKey('close_time', '21:00');

        $carbonDate = Carbon::parse($date);
        $startDay = Carbon::parse($date.' '.$openTimeStr);
        $endDay = Carbon::parse($date.' '.$closeTimeStr);

        // Fetch existing bookings for this room & date
        $existingBookings = Booking::where('room_id', $roomId)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'dp_paid', 'paid', 'checked_in'])
            ->where(function ($query) {
                $query->where('status', '!=', 'pending')
                    ->orWhere('expires_at', '>', now());
            })
            ->get();

        // Fetch blocked slots for this room & date
        $blockedSlots = BlockedSlot::where(function ($q) use ($roomId) {
            $q->where('room_id', $roomId)->orWhereNull('room_id');
        })
            ->whereDate('date', $date)
            ->get();

        $slots = [];
        $current = $startDay->copy();

        while ($current->copy()->addMinutes($durationMinutes)->lte($endDay)) {
            $slotStart = $current->format('H:i');
            $slotEndObj = $current->copy()->addMinutes($durationMinutes);
            $slotEndWithBufferObj = $slotEndObj->copy()->addMinutes($bufferMinutes);
            $slotEnd = $slotEndObj->format('H:i');
            $slotEndWithBuffer = $slotEndWithBufferObj->format('H:i');

            // Check collision with bookings
            $isBooked = false;
            foreach ($existingBookings as $b) {
                $bStart = Carbon::parse($date.' '.$b->start_time);
                $bEndWithBuffer = Carbon::parse($date.' '.$b->end_time)->addMinutes($bufferMinutes);

                if ($current->lt($bEndWithBuffer) && $slotEndWithBufferObj->gt($bStart)) {
                    $isBooked = true;
                    break;
                }
            }

            // Check collision with blocked slots
            $isBlocked = false;
            if (! $isBooked) {
                foreach ($blockedSlots as $block) {
                    $blStart = Carbon::parse($date.' '.$block->start_time);
                    $blEnd = Carbon::parse($date.' '.$block->end_time);

                    if ($current->lt($blEnd) && $slotEndObj->gt($blStart)) {
                        $isBlocked = true;
                        break;
                    }
                }
            }

            // Also check if time has already passed today
            $isPast = $carbonDate->isToday() && $current->lt(now());

            $status = 'available';
            if ($isBooked) {
                $status = 'booked';
            } elseif ($isBlocked || $isPast) {
                $status = 'blocked';
            }

            // Discount recommendation flag (e.g. Tuesday morning hours)
            $isOffPeak = $carbonDate->isTuesday() || $carbonDate->isWednesday();
            $isMorning = (int) $current->format('H') < 12;
            $hasSaverDiscount = $isOffPeak && $isMorning;

            $slots[] = [
                'start' => $slotStart,
                'end' => $slotEnd,
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'status' => $status,
                'available' => ($status === 'available'),
                'has_saver_discount' => $hasSaverDiscount,
                'saver_note' => $hasSaverDiscount ? 'Hemat 15% Jam Hemat Midweek!' : null,
            ];

            // Advance by duration + buffer
            $current->addMinutes($durationMinutes + $bufferMinutes);
        }

        return $slots;
    }
}
