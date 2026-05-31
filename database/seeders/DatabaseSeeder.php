<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            GovernorateSeeder::class,
            ThemeSeeder::class,
            SettingsSeeder::class,
            ShippingRuleSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
