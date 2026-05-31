<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $global = [
            'store.name' => 'متجر العلامات',
            'store.tagline' => 'أفضل البراندات في مكان واحد',
            'store.currency' => 'EGP',
            'store.support_phone' => '01001234567',
            'store.support_whatsapp' => '201001234567',
            'store.email' => 'support@alamat.shop',
            'store.address' => 'القاهرة، مصر',
            'store.social' => [
                'facebook' => 'https://facebook.com/alamatshop',
                'instagram' => 'https://instagram.com/alamatshop',
                'tiktok' => 'https://tiktok.com/@alamatshop',
            ],
            'store.maintenance_mode' => false,
            'checkout.cod_enabled' => true,
            'checkout.whatsapp_enabled' => true,
            'checkout.transfer_enabled' => true,
            'checkout.min_order_total' => 0,
            'checkout.terms_text' => 'بالضغط على "اطلب الآن" فإنك توافق على شروط البيع وترجع المنتج خلال 14 يوم إذا كان غير مستخدم.',
            'shipping.free_over' => null,
            'shipping.flat_fallback' => 60,
        ];

        foreach ($global as $key => $value) {
            Setting::updateOrCreate(
                ['brand_id' => null, 'key' => $key],
                ['value' => $value]
            );
        }
    }
}
