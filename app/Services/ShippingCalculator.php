<?php

namespace App\Services;

use App\Models\Governorate;
use App\Models\ShippingRule;
use Illuminate\Support\Facades\Cache;

class ShippingCalculator
{
    private const CACHE_TTL = 300; // 5 minutes

    public function calculate(string $governorateName, int $subtotal, ?int $brandId = null): array
    {
        // 1) Base fee from governorate
        $gov = Governorate::where('name', $governorateName)->first();
        $baseFee = $gov?->shipping_fee ?? (int) setting('shipping.flat_fallback', 0);

        $fee = $baseFee;
        $appliedRule = null;
        $reason = null;

        // 2) Free-over thresholds
        $globalFreeOver = setting('shipping.free_over', null);
        $govFreeOver = $gov?->free_over ?? null;

        // Governorate-specific free_over wins if lower
        $freeOver = $govFreeOver ?? $globalFreeOver;

        if ($freeOver !== null && $subtotal >= $freeOver) {
            return [
                'fee' => 0,
                'rule' => null,
                'free' => true,
                'reason' => 'شحن مجاني — تجاوزت الحد الأدنى',
            ];
        }

        // 3) Apply active shipping rules (highest priority wins)
        $rule = $this->findMatchingRule($governorateName, $subtotal, $brandId);

        if ($rule) {
            $appliedRule = $rule;
            $fee = match ($rule->type) {
                'free' => 0,
                'flat' => $rule->value ?? 0,
                'percent_off' => (int) round($fee * (1 - (($rule->value ?? 0) / 100))),
                'amount_off' => max(0, $fee - ($rule->value ?? 0)),
                default => $fee,
            };
            $reason = $rule->name;
        }

        return [
            'fee' => max(0, $fee),
            'rule' => $appliedRule,
            'free' => $fee === 0,
            'reason' => $reason,
        ];
    }

    private function findMatchingRule(string $governorateName, int $subtotal, ?int $brandId): ?ShippingRule
    {
        $cacheKey = 'shipping.rules.active.'.($brandId ?? 'global');

        $rules = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($brandId) {
            return ShippingRule::currentlyActive()
                ->where(fn ($q) => $q->whereNull('brand_id')->orWhere('brand_id', $brandId))
                ->orderByDesc('priority')
                ->get();
        });

        foreach ($rules as $rule) {
            // Check governorate scope
            if (! $rule->appliesToGovernorate($governorateName)) {
                continue;
            }

            // Check min_order_total
            if ($rule->min_order_total !== null && $subtotal < $rule->min_order_total) {
                continue;
            }

            // Brand-specific rules take precedence; if brandId matches exactly, it's a strong match
            if ($rule->brand_id === $brandId || $rule->brand_id === null) {
                return $rule;
            }
        }

        return null;
    }

    public function bustCache(): void
    {
        Cache::forget('shipping.rules.active.global');
        \App\Models\Brand::pluck('id')->each(fn ($id) => Cache::forget("shipping.rules.active.{$id}"));
    }
}
