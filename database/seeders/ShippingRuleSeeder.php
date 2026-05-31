<?php

namespace Database\Seeders;

use App\Models\ShippingRule;
use Illuminate\Database\Seeder;

class ShippingRuleSeeder extends Seeder
{
    public function run(): void
    {
        ShippingRule::updateOrCreate(
            ['name' => 'شحن مجاني للعيد'],
            [
                'type' => 'free',
                'value' => null,
                'scope' => 'all',
                'governorate_ids' => null,
                'min_order_total' => null,
                'priority' => 10,
                'is_active' => false,
                'starts_at' => now()->startOfMonth(),
                'ends_at' => now()->endOfMonth(),
            ]
        );
    }
}
