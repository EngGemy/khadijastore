<?php

namespace Database\Seeders;

use App\Models\HomeBlock;
use Illuminate\Database\Seeder;

class HomeBlockSeeder extends Seeder
{
    public function run(): void
    {
        if (HomeBlock::count() > 0) {
            return;
        }

        $blocks = [
            [
                'type' => 'brands_marquee',
                'title' => null,
                'subtitle' => null,
                'is_active' => true,
                'sort' => 10,
                'data' => ['limit' => null],
            ],
            [
                'type' => 'categories',
                'title' => null,
                'subtitle' => null,
                'is_active' => true,
                'sort' => 20,
                'data' => [
                    'items' => [
                        [
                            'label' => 'موبايلات وأكسسوار',
                            'sublabel' => 'MOBILES',
                            'icon' => '📱',
                            'link' => '/brand/mobile',
                            'bg_style' => 'bg-ink',
                        ],
                        [
                            'label' => 'قطع غيار',
                            'sublabel' => 'SPARE PARTS',
                            'icon' => '🔧',
                            'link' => '/brand/parts',
                            'bg_style' => 'gradient-dark',
                        ],
                        [
                            'label' => 'عطور',
                            'sublabel' => 'PERFUMES',
                            'icon' => '🌸',
                            'link' => '/brand/perfume',
                            'bg_style' => 'gradient-darker',
                        ],
                        [
                            'label' => 'مواد عطارة',
                            'sublabel' => 'HERBS',
                            'icon' => '🌿',
                            'link' => '/brand/attar',
                            'bg_style' => 'gradient-mixed',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'brands_grid',
                'title' => null,
                'subtitle' => null,
                'is_active' => true,
                'sort' => 30,
                'data' => ['limit' => null],
            ],
            [
                'type' => 'brands_filter',
                'title' => 'اختار البراند',
                'subtitle' => 'فلتر حسب المتجر · FILTER BY STORE',
                'is_active' => true,
                'sort' => 35,
                'data' => ['limit' => null],
            ],
            [
                'type' => 'products_grid',
                'title' => null,
                'subtitle' => null,
                'is_active' => true,
                'sort' => 40,
                'data' => ['source' => 'best_selling', 'limit' => 48],
            ],
            [
                'type' => 'banner',
                'title' => null,
                'subtitle' => null,
                'is_active' => true,
                'sort' => 50,
                'data' => [
                    'eyebrow' => 'جاهز تطلب؟ · READY?',
                    'title' => 'اطلب الآن وادفع عند الاستلام',
                    'paragraph' => 'توصيل سريع خلال 24 ساعة لكل المحافظات، مع ضمان استبدال 14 يوم.',
                    'btn_text' => 'ابدأ التسوّق',
                    'btn_link' => '#products',
                ],
            ],
        ];

        foreach ($blocks as $block) {
            HomeBlock::create($block);
        }
    }
}
