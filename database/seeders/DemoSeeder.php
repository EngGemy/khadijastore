<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $super = User::updateOrCreate(
            ['email' => 'super@alamat.test'],
            ['name' => 'السوبر أدمن', 'password' => Hash::make('password'), 'brand_id' => null, 'is_active' => true],
        );
        $super->syncRoles('super_admin');
        // سوبر أدمن: /platform/login — تجار البراند: /merchant/login

        $brands = [
            ['slug' => 'care', 'name' => 'براند العناية', 'mark' => 'ع', 'category_label' => 'العناية والتجميل · BEAUTY', 'whatsapp' => '201001234567', 'vodafone_cash' => '010 1234 5678', 'instapay' => 'care@instapay'],
            ['slug' => 'mobile', 'name' => 'موبايل ستور', 'mark' => 'م', 'category_label' => 'موبايلات وأجهزة · TECH', 'whatsapp' => '201002223333', 'vodafone_cash' => '010 7777 6666', 'instapay' => 'mobile@instapay'],
            ['slug' => 'perfume', 'name' => 'عطور النخبة', 'mark' => 'ع', 'category_label' => 'عطور فاخرة · PERFUMES', 'whatsapp' => '201004445555', 'vodafone_cash' => '010 9999 8888', 'instapay' => 'elite@instapay'],
            ['slug' => 'parts', 'name' => 'قطع غيار برو', 'mark' => 'ق', 'category_label' => 'قطع غيار · PARTS', 'whatsapp' => '201005556666', 'vodafone_cash' => '010 5555 4444', 'instapay' => 'parts@instapay'],
            ['slug' => 'attar', 'name' => 'عطارة الأصالة', 'mark' => 'ط', 'category_label' => 'مواد عطارة · HERBS', 'whatsapp' => '201007778888', 'vodafone_cash' => '010 3333 2222', 'instapay' => 'attar@instapay'],
        ];

        $catalog = [
            'care' => [
                ['qalam-7awageb', 'قلم', 'قلم حواجب شعرة بشعرة', 149, 229, 'الأكثر مبيعًا', true, 4.8, 312],
                ['face-serum', 'سيروم', 'سيروم فيتامين سي للبشرة', 199, 280, 'جديد', true, 4.8, 176],
                ['cream', 'كريم', 'كريم ترطيب مكثف 100مل', 175, 230, null, false, 4.7, 98],
                ['lipstick', 'أحمر', 'أحمر شفاه مات طويل الثبات', 110, 160, 'عرض', false, 4.9, 201],
            ],
            'mobile' => [
                ['power-bank', 'باور', 'باور بانك 20000 mAh', 450, 600, null, true, 4.7, 88],
                ['iphone-case', 'جراب', 'جراب آيفون شفاف مضاد للصدمات', 120, 180, null, true, 4.7, 95],
                ['screen', 'حماية', 'حماية شاشة زجاج 9H', 75, 120, 'عرض', false, 4.5, 67],
            ],
            'perfume' => [
                ['oud-perfume', 'عود', 'عطر عود ملكي 50مل', 340, 450, 'فاخر', true, 4.9, 210],
                ['musk', 'مسك', 'عطر مسك أبيض 30مل', 220, 300, null, false, 4.8, 88],
            ],
            'parts' => [
                ['car-filter', 'فلتر', 'فلتر زيت أصلي', 85, 110, null, true, 4.6, 54],
            ],
            'attar' => [
                ['misk-oil', 'مسك', 'زيت المسك الأبيض الطبيعي', 65, 90, 'طبيعي', true, 4.9, 143],
            ],
        ];

        foreach ($brands as $data) {
            $brand = Brand::updateOrCreate(['slug' => $data['slug']], array_merge($data, [
                'description' => 'منتجات بجودة احترافية وأسعار تنافسية.',
                'working_hours' => ['السبت-الخميس' => '10ص - 10م', 'الجمعة' => 'مغلق'],
                'is_active' => true,
            ]));

            User::updateOrCreate(
                ['email' => 'admin-'.$brand->slug.'@alamat.test'],
                ['name' => 'أدمن '.$brand->name, 'password' => Hash::make('password'), 'brand_id' => $brand->id, 'is_active' => true],
            )->syncRoles('brand_admin');

            User::updateOrCreate(
                ['email' => 'staff-'.$brand->slug.'@alamat.test'],
                ['name' => 'موظف '.$brand->name, 'password' => Hash::make('password'), 'brand_id' => $brand->id, 'is_active' => true],
            )->syncRoles('brand_staff');

            $cat = Category::updateOrCreate(
                ['brand_id' => $brand->id, 'slug' => 'general'],
                ['name' => 'عام', 'is_active' => true],
            );

            foreach ($catalog[$data['slug']] as $sort => [$pslug, $mark, $pname, $price, $compare, $badge, $featured, $rating, $sales]) {
                $product = Product::updateOrCreate(
                    ['slug' => $pslug],
                    [
                        'brand_id' => $brand->id,
                        'category_id' => $cat->id,
                        'name' => $pname,
                        'mark' => $mark,
                        'short_description' => 'منتج عالي الجودة بأفضل سعر.',
                        'price' => $price,
                        'compare_price' => $compare,
                        'badge' => $badge,
                        'is_active' => true,
                        'is_featured' => $featured,
                        'sort' => $sort,
                        'rating' => $rating,
                        'sales_count' => $sales,
                        'features' => ['جودة عالية', 'ضمان أصلي', 'سهل الاستخدام'],
                        'usage_steps' => ['افتح العبوة', 'اتبع التعليمات', 'استمتع بالنتيجة'],
                    ],
                );

                $product->variants()->delete();
                $product->variants()->createMany([
                    ['name' => 'قطعة واحدة', 'subtitle' => 'للتجربة', 'price' => $price, 'is_default' => true, 'sort' => 1],
                    ['name' => 'قطعتان + هدية', 'subtitle' => 'وفّر 50 ج.م', 'price' => $price * 2 - 50, 'is_popular' => true, 'sort' => 2],
                    ['name' => '3 قطع + هديتان', 'subtitle' => 'أفضل قيمة', 'price' => $price * 3 - 150, 'sort' => 3],
                ]);
            }
        }
    }
}
