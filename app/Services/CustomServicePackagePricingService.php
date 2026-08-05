<?php

namespace App\Services;

use App\Models\ServicePackagePricingRule;
use InvalidArgumentException;

class CustomServicePackagePricingService
{
    public static function calculate(ServicePackagePricingRule $rule, int $sessions): array
    {
        if (! $rule->is_active) {
            throw new InvalidArgumentException('خيار التسعير غير متاح حالياً.');
        }

        $min = max(1, (int) $rule->min_sessions);
        $max = max($min, (int) $rule->max_sessions);
        $step = max(1, (int) $rule->session_step);

        if ($sessions < $min || $sessions > $max || (($sessions - $min) % $step !== 0)) {
            throw new InvalidArgumentException("عدد الحصص يجب أن يكون بين {$min} و{$max} وبزيادة {$step}.");
        }

        $unitPrice = round((float) $rule->price_per_session, 2);
        $originalAmount = round($sessions * $unitPrice, 2);
        $discountPercent = self::discountPercent($rule, $sessions);
        $discountAmount = round($originalAmount * $discountPercent / 100, 2);
        $amount = round($originalAmount - $discountAmount, 2);

        return [
            'pricing_rule_id' => $rule->id,
            'name' => 'باقة مخصصة — '.$rule->name,
            'scope' => $rule->scope,
            'scope_label' => $rule->scopeLabel(),
            'sessions' => $sessions,
            'session_minutes' => (int) $rule->session_minutes,
            'duration_days' => (int) $rule->duration_days,
            'price_per_session' => $unitPrice,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'original_amount' => $originalAmount,
            'amount' => $amount,
            'final_price_per_session' => $sessions > 0 ? round($amount / $sessions, 2) : 0,
            'currency' => 'USD',
        ];
    }

    private static function discountPercent(ServicePackagePricingRule $rule, int $sessions): float
    {
        $discount = 0.0;

        foreach ($rule->discount_tiers ?? [] as $tier) {
            $minimum = (int) ($tier['min_sessions'] ?? 0);
            $percent = min(100, max(0, (float) ($tier['discount_percent'] ?? 0)));
            if ($sessions >= $minimum && $minimum > 0) {
                $discount = max($discount, $percent);
            }
        }

        return $discount;
    }
}
