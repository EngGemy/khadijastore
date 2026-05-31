<?php

namespace App\Observers;

use App\Models\ShippingRule;
use App\Services\ShippingCalculator;

class ShippingRuleObserver
{
    public function __construct(private readonly ShippingCalculator $calculator) {}

    public function saved(ShippingRule $rule): void
    {
        $this->calculator->bustCache();
    }

    public function deleted(ShippingRule $rule): void
    {
        $this->calculator->bustCache();
    }
}
