<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class HomeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // HERO
            'home.hero.eyebrow'           => 'منصة واحدة · آلاف الخيارات · ثقة من أول طلب',
            'home.hero.title_line1'       => 'تسوّق',
            'home.hero.title_highlight'   => 'بثقة',
            'home.hero.title_line2'       => 'من أول طلب',
            'home.hero.paragraph'         => 'منتجات أصلية 100% من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام.',
            'home.hero.primary_btn_text'  => 'اكتشف المنتجات',
            'home.hero.primary_btn_link'  => '#products',
            'home.hero.secondary_btn_text'=> 'تصفّح البراندات',
            'home.hero.secondary_btn_link'=> '#brands',
            'home.hero.stats'             => [
                ['value' => '{brands_count}+', 'label' => 'براندات · Brands'],
                ['value' => '{total_orders}+', 'label' => 'طلب مكتمل'],
                ['value' => '{avg_rating}★',   'label' => 'متوسط التقييم'],
            ],

            // SECTION TITLES
            'home.categories.title'  => 'كل اللي تحتاجه في مكان واحد',
            'home.categories.eyebrow'=> 'تسوّق حسب الفئة · CATEGORIES',
            'home.brands.title'      => 'براندات تثق فيها',
            'home.brands.eyebrow'    => 'شركاؤنا · OUR BRANDS',
            'home.products.title'    => 'منتجات اختارها عملاؤنا',
            'home.products.eyebrow'  => 'الأكثر طلبًا · BESTSELLERS',

            // CTA
            'home.cta.eyebrow'   => 'جاهز تجرب الفرق؟ · READY?',
            'home.cta.title'     => 'اطلب الآن وادفع عند الاستلام',
            'home.cta.paragraph' => 'توصيل سريع خلال 24 ساعة لكل المحافظات، مع ضمان استبدال 14 يوم — جودة تليق بك في كل طلب.'
            'home.cta.btn_text'  => 'ابدأ التسوّق',
            'home.cta.btn_link'  => '#products',

            // SHOW/HIDE TOGGLES
            'home.show_marquee'    => true,
            'home.show_categories' => true,
            'home.show_brands'     => true,
            'home.show_products'   => true,
            'home.show_cta'        => true,

            // PRODUCTS SOURCE
            'home.products.mode'  => 'featured',
            'home.products.limit' => 8,

            // SEO
            'home.seo.title'       => 'متجر العلامات · اختار الأفضل بكل ثقة',
            'home.seo.description' => 'اكتشف منتجات أصلية من أشهر البراندات العالمية والمحلية. توصيل سريع، دفع عند الاستلام، وضمان رضا تام.'
            'home.seo.image'       => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(
                ['brand_id' => null, 'key' => $key],
                ['value' => $value]
            );
        }
    }
}
