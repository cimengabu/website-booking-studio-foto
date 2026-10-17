<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Package;
use App\Models\PricingRule;
use App\Models\Promo;
use App\Models\Setting;
use Carbon\Carbon;

class PricingService
{
    public function calculate(
        int $packageId,
        string $date,
        string $startTime,
        int $peopleCount = 1,
        array $addonSelections = [], // [addon_id => qty]
        ?string $promoCode = null,
        int $redeemPoints = 0
    ): array {
        $package = Package::findOrFail($packageId);
        $carbonDate = Carbon::parse($date);
        $time = Carbon::parse($date.' '.$startTime);

        // Package Base Price
        $packagePrice = (float) $package->price;

        // Extra Person Charge
        $extraPeople = max(0, $peopleCount - $package->max_people);
        $extraPersonRate = (float) Setting::getByKey('extra_person_fee', 25000);
        $extraPeopleTotal = $extraPeople * $extraPersonRate;

        $subtotal = $packagePrice + $extraPeopleTotal;

        // Addons Total
        $addonsTotal = 0;
        $addonsBreakdown = [];

        if (! empty($addonSelections)) {
            $addons = Addon::whereIn('id', array_keys($addonSelections))->where('is_active', true)->get();
            foreach ($addons as $addon) {
                $qty = max(1, (int) ($addonSelections[$addon->id] ?? 1));
                $unitPrice = (float) $addon->price;
                $lineSubtotal = 0;

                if ($addon->type === 'flat') {
                    $lineSubtotal = $unitPrice;
                } elseif ($addon->type === 'per_person') {
                    $lineSubtotal = $unitPrice * $peopleCount;
                } else { // per_unit
                    $lineSubtotal = $unitPrice * $qty;
                }

                $addonsTotal += $lineSubtotal;
                $addonsBreakdown[] = [
                    'addon_id' => $addon->id,
                    'name' => $addon->name,
                    'type' => $addon->type,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ];
            }
        }

        // Markup (Weekend / Peak Hour Rules)
        $markupPercent = 0;
        $isWeekend = $carbonDate->isWeekend();

        $activeRules = PricingRule::where('is_active', true)->get();
        foreach ($activeRules as $rule) {
            if ($rule->day_type === 'weekend' && ! $isWeekend) {
                continue;
            }
            if ($rule->day_type === 'weekday' && $isWeekend) {
                continue;
            }

            if ($rule->start_time && $rule->end_time) {
                $ruleStart = Carbon::parse($date.' '.$rule->start_time);
                $ruleEnd = Carbon::parse($date.' '.$rule->end_time);
                if ($time->gte($ruleStart) && $time->lte($ruleEnd)) {
                    $markupPercent = max($markupPercent, (float) $rule->markup_percent);
                }
            } else {
                $markupPercent = max($markupPercent, (float) $rule->markup_percent);
            }
        }

        // Automatic Saver Discount (e.g. Tuesday/Wednesday mornings)
        $saverDiscountPercent = 0;
        if (($carbonDate->isTuesday() || $carbonDate->isWednesday()) && (int) $time->format('H') < 12) {
            $saverDiscountPercent = 15; // 15% off subtotal
        }

        $markupAmount = ($subtotal * $markupPercent) / 100;
        $saverDiscountAmount = ($subtotal * $saverDiscountPercent) / 100;

        // Promo Code Discount
        $promoDiscount = 0;
        $appliedPromo = null;
        if ($promoCode) {
            $promo = Promo::where('code', strtoupper($promoCode))->where('is_active', true)->first();
            if ($promo && $promo->isValidForAmount($subtotal + $addonsTotal)) {
                $promoDiscount = $promo->calculateDiscount($subtotal + $addonsTotal);
                $appliedPromo = [
                    'code' => $promo->code,
                    'discount' => $promoDiscount,
                ];
            }
        }

        // Loyalty Points Discount (1 pt = Rp 100, max 50% of subtotal)
        $pointsDiscount = 0;
        if ($redeemPoints > 0) {
            $pointValue = 100; // 1 point = Rp 100
            $maxPointsDiscount = ($subtotal + $addonsTotal) * 0.5;
            $pointsDiscount = min($redeemPoints * $pointValue, $maxPointsDiscount);
        }

        $totalDiscount = $saverDiscountAmount + $promoDiscount + $pointsDiscount;
        $totalPrice = max(0, $subtotal + $addonsTotal + $markupAmount - $totalDiscount);

        // DP Calculation (50%)
        $dpPercent = (float) Setting::getByKey('dp_percent', 50);
        $dpAmount = ($totalPrice * $dpPercent) / 100;

        return [
            'package_price' => $packagePrice,
            'extra_people' => $extraPeople,
            'extra_people_total' => $extraPeopleTotal,
            'subtotal' => $subtotal,
            'addons_total' => $addonsTotal,
            'addons_breakdown' => $addonsBreakdown,
            'markup_percent' => $markupPercent,
            'markup_amount' => $markupAmount,
            'saver_discount_percent' => $saverDiscountPercent,
            'saver_discount_amount' => $saverDiscountAmount,
            'applied_promo' => $appliedPromo,
            'points_discount' => $pointsDiscount,
            'points_redeemed' => $redeemPoints,
            'discount' => $totalDiscount,
            'total_price' => $totalPrice,
            'dp_amount' => $dpAmount,
            'remaining_amount' => max(0, $totalPrice - $dpAmount),
        ];
    }
}
