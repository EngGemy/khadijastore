<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

class CatalogRichSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $brandLogos = [
        'care' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
        'mobile' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
        'perfume' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop',
        'parts' => 'https://images.unsplash.com/photo-1486262715619-67b85e44308f?w=400&h=400&fit=crop&q=80',
        'attar' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=400&h=400&fit=crop',
    ];

    public function run(): void
    {
        $this->seedBrandLogos();
        $this->seedMobileAccessoriesCatalog();
        $this->seedAttarCatalog();
        $this->seedCareCatalog();
        $this->fixMissingMedia();

        $this->command?->info('CatalogRichSeeder: تم إضافة البراندات والمنتجات والصور.');
    }

    private function seedBrandLogos(): void
    {
        foreach ($this->brandLogos as $slug => $url) {
            $brand = Brand::where('slug', $slug)->first();
            if (! $brand) {
                continue;
            }

            if ($brand->getFirstMedia('logo')) {
                continue;
            }

            $this->attachFromUrl($brand, 'logo', $url);
            $this->command?->info("Logo: {$brand->name}");
        }
    }

    private function seedMobileAccessoriesCatalog(): void
    {
        $brand = Brand::where('slug', 'mobile')->first();
        if (! $brand) {
            return;
        }

        $tree = [
            'headphones' => [
                'name' => 'سماعات',
                'sort' => 1,
                'children' => [
                    'anker' => 'أنكر',
                    'samsung' => 'سامسونج',
                    'jbl' => 'JBL',
                ],
            ],
            'chargers' => [
                'name' => 'شواحن وكابلات',
                'sort' => 2,
                'children' => [
                    'anker-chargers' => 'أنكر',
                    'belkin' => 'بيلكن',
                ],
            ],
            'computer-accessories' => [
                'name' => 'إكسسوارات كمبيوتر',
                'sort' => 3,
                'children' => [
                    'logitech' => 'لوجيتك',
                    'razer' => 'رازر',
                ],
            ],
            'phone-accessories' => [
                'name' => 'إكسسوارات موبايل',
                'sort' => 4,
                'children' => [
                    'apple-acc' => 'أبل',
                    'samsung-acc' => 'سامسونج',
                ],
            ],
        ];

        $categories = $this->seedCategoryTree($brand, $tree);

        $products = [
            [
                'slug' => 'anker-soundcore-q30',
                'category' => 'anker',
                'mark' => 'أنكر',
                'name' => 'سماعة أنكر Soundcore Q30 — عزل ضوضاء',
                'price' => 2890,
                'compare' => 3490,
                'badge' => 'الأكثر مبيعًا',
                'featured' => true,
                'rating' => 4.8,
                'sales' => 412,
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=900&h=900&fit=crop',
                'desc' => 'عزل نشط للضوضاء، بطارية 40 ساعة، صوت Hi-Res.',
            ],
            [
                'slug' => 'samsung-galaxy-buds2',
                'category' => 'samsung',
                'mark' => 'سام',
                'name' => 'سماعة سامسونج Galaxy Buds2',
                'price' => 3200,
                'compare' => 3990,
                'badge' => 'جديد',
                'featured' => true,
                'rating' => 4.7,
                'sales' => 198,
                'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=900&h=900&fit=crop',
                'desc' => 'إيربودز لاسلكية مع عزل محيطي ذكي.',
            ],
            [
                'slug' => 'jbl-tune-510',
                'category' => 'jbl',
                'mark' => 'JBL',
                'name' => 'سماعة JBL Tune 510BT',
                'price' => 1450,
                'compare' => 1890,
                'badge' => null,
                'featured' => false,
                'rating' => 4.6,
                'sales' => 267,
                'image' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=900&h=900&fit=crop',
                'desc' => 'بلوتوث خفيفة الوزن مع باس JBL Pure Bass.',
            ],
            [
                'slug' => 'anker-powerbank-20000',
                'category' => 'anker-chargers',
                'mark' => 'أنكر',
                'name' => 'باور بانك أنكر 20000mAh — شحن سريع',
                'price' => 890,
                'compare' => 1150,
                'badge' => 'عرض',
                'featured' => true,
                'rating' => 4.9,
                'sales' => 530,
                'image' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=900&h=900&fit=crop',
                'desc' => 'شحن سريع PD 22.5W، منفذين USB.',
            ],
            [
                'slug' => 'belkin-usb-c-cable',
                'category' => 'belkin',
                'mark' => 'بيل',
                'name' => 'كابل بيلكن USB-C 2م — شحن ونقل بيانات',
                'price' => 320,
                'compare' => 450,
                'badge' => null,
                'featured' => false,
                'rating' => 4.5,
                'sales' => 189,
                'image' => 'https://images.unsplash.com/photo-1625948515291-69613efd202f?w=900&h=900&fit=crop',
                'desc' => 'كابل معتمد بسرعة 60W للشحن السريع.',
            ],
            [
                'slug' => 'logitech-mx-master',
                'category' => 'logitech',
                'mark' => 'لوج',
                'name' => 'ماوس لوجيتك MX Master 3S',
                'price' => 4200,
                'compare' => 4990,
                'badge' => 'احترافي',
                'featured' => true,
                'rating' => 4.9,
                'sales' => 156,
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=900&h=900&fit=crop',
                'desc' => 'ماوس لاسلكي للمحترفين مع تتبع على أي سطح.',
            ],
            [
                'slug' => 'logitech-k380-keyboard',
                'category' => 'logitech',
                'mark' => 'لوج',
                'name' => 'كيبورد لوجيتك K380 متعدد الأجهزة',
                'price' => 1850,
                'compare' => 2200,
                'badge' => null,
                'featured' => false,
                'rating' => 4.7,
                'sales' => 134,
                'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=900&h=900&fit=crop',
                'desc' => 'يتصل بـ 3 أجهزة ويتحول بينها بضغطة زر.',
            ],
            [
                'slug' => 'razer-deathadder',
                'category' => 'razer',
                'mark' => 'راز',
                'name' => 'ماوس رازر DeathAdder V3',
                'price' => 3100,
                'compare' => 3600,
                'badge' => 'جيمنج',
                'featured' => true,
                'rating' => 4.8,
                'sales' => 221,
                'image' => 'https://images.unsplash.com/photo-1615663245857-ac9bb9c323b7?w=900&h=900&fit=crop',
                'desc' => 'ماوس ألعاب خفيف الوزن مع استشعار 30K DPI.',
            ],
            [
                'slug' => 'iphone-15-case-clear',
                'category' => 'apple-acc',
                'mark' => 'أبل',
                'name' => 'جراب آيفون 15 شفاف مضاد للصدمات',
                'price' => 180,
                'compare' => 280,
                'badge' => null,
                'featured' => false,
                'rating' => 4.4,
                'sales' => 302,
                'image' => 'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=900&h=900&fit=crop',
                'desc' => 'حماية شفافة مع حواف سيليكون مضادة للانزلاق.',
            ],
            [
                'slug' => 'samsung-25w-charger',
                'category' => 'samsung-acc',
                'mark' => 'سام',
                'name' => 'شاحن سامسونج 25W أصلي',
                'price' => 450,
                'compare' => 600,
                'badge' => 'أصلي',
                'featured' => true,
                'rating' => 4.7,
                'sales' => 388,
                'image' => 'https://images.unsplash.com/photo-1591290619762-d2b1b9e1fd4b?w=900&h=900&fit=crop',
                'desc' => 'شحن سريع متوافق مع جالاكسي وUSB-C.',
            ],
            [
                'slug' => 'usb-hub-7port',
                'category' => 'logitech',
                'mark' => 'USB',
                'name' => 'موزع USB 7 منافذ للابتوب',
                'price' => 650,
                'compare' => 850,
                'badge' => null,
                'featured' => false,
                'rating' => 4.5,
                'sales' => 97,
                'image' => 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=900&h=900&fit=crop',
                'desc' => 'يو إس بي 3.0 مع منفذ طاقة إضافي.',
            ],
            [
                'slug' => 'laptop-stand-alu',
                'category' => 'logitech',
                'mark' => 'ستاند',
                'name' => 'ستاند لابتوب ألومنيوم قابل للطي',
                'price' => 520,
                'compare' => 720,
                'badge' => null,
                'featured' => false,
                'rating' => 4.6,
                'sales' => 143,
                'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=900&h=900&fit=crop',
                'desc' => 'وضعية مريحة للرقبة مع تبريد أفضل للجهاز.',
            ],
        ];

        $this->seedProducts($brand, $categories, $products);

        // تحديث المنتجات القديمة في موبايل ستور
        $this->updateExistingMobileProducts($brand, $categories);
    }

    private function updateExistingMobileProducts(Brand $brand, array $categories): void
    {
        $map = [
            'power-bank' => [
                'category' => 'anker-chargers',
                'image' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=900&h=900&fit=crop',
            ],
            'iphone-case' => [
                'category' => 'apple-acc',
                'image' => 'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=900&h=900&fit=crop',
            ],
            'screen' => [
                'category' => 'samsung-acc',
                'image' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=900&h=900&fit=crop',
            ],
        ];

        foreach ($map as $slug => $data) {
            $product = Product::where('slug', $slug)->where('brand_id', $brand->id)->first();
            if (! $product) {
                continue;
            }

            if (isset($categories[$data['category']])) {
                $product->update(['category_id' => $categories[$data['category']]->id]);
            }

            $this->attachProductCover($product, $data['image']);
        }
    }

    private function seedAttarCatalog(): void
    {
        $brand = Brand::where('slug', 'attar')->first();
        if (! $brand) {
            return;
        }

        $tree = [
            'oils' => [
                'name' => 'زيوت عطرية',
                'sort' => 1,
                'children' => [
                    'misk' => 'مسك',
                    'oud' => 'عود',
                ],
            ],
            'spices' => [
                'name' => 'بهارات وأعشاب',
                'sort' => 2,
                'children' => [
                    'herbs' => 'أعشاب طبيعية',
                    'spice-mix' => 'خلطات بهارات',
                ],
            ],
        ];

        $categories = $this->seedCategoryTree($brand, $tree);

        $products = [
            [
                'slug' => 'hindi-oud-oil',
                'category' => 'oud',
                'mark' => 'عود',
                'name' => 'دهن عود هندي أصلي — 12 مل',
                'price' => 450,
                'compare' => 580,
                'badge' => 'فاخر',
                'featured' => true,
                'rating' => 4.9,
                'sales' => 187,
                'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=900&h=900&fit=crop',
                'desc' => 'عود هندي طبيعي مركز، ثبات يدوم طوال اليوم.',
            ],
            [
                'slug' => 'amber-misk',
                'category' => 'misk',
                'mark' => 'مسك',
                'name' => 'مسك عنبر فاخر — 30 مل',
                'price' => 120,
                'compare' => 160,
                'badge' => null,
                'featured' => false,
                'rating' => 4.8,
                'sales' => 234,
                'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=900&h=900&fit=crop',
                'desc' => 'مسك عنبر ناعم برائحة دافئة فاخرة.',
            ],
            [
                'slug' => 'green-cardamom',
                'category' => 'herbs',
                'mark' => 'هيل',
                'name' => 'هيل أخضر فاخر — 250 جرام',
                'price' => 185,
                'compare' => 240,
                'badge' => 'طبيعي',
                'featured' => true,
                'rating' => 4.9,
                'sales' => 312,
                'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=900&h=900&fit=crop',
                'desc' => 'هيل أخضر كبير الحبة من أفضل المزارع.',
            ],
            [
                'slug' => 'iranian-saffron',
                'category' => 'herbs',
                'mark' => 'زعفر',
                'name' => 'زعفران إيراني نگین — 1 جرام',
                'price' => 320,
                'compare' => 400,
                'badge' => 'نادر',
                'featured' => true,
                'rating' => 5.0,
                'sales' => 156,
                'image' => 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?w=900&h=900&fit=crop',
                'desc' => 'زعفران سوبر نگین معتمد بأعلى درجة لون.',
            ],
            [
                'slug' => 'mixed-spices',
                'category' => 'spice-mix',
                'mark' => 'بهار',
                'name' => 'خلطة بهارات مشكلة — 500 جرام',
                'price' => 95,
                'compare' => 130,
                'badge' => 'الأكثر مبيعًا',
                'featured' => true,
                'rating' => 4.8,
                'sales' => 445,
                'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=900&h=900&fit=crop',
                'desc' => 'خلطة جاهزة للكبسة واللحوم والشوربة.',
            ],
            [
                'slug' => 'black-seed-oil',
                'category' => 'herbs',
                'mark' => 'حبة',
                'name' => 'زيت حبة البركة الباردة — 125 مل',
                'price' => 75,
                'compare' => 100,
                'badge' => 'طبيعي',
                'featured' => false,
                'rating' => 4.7,
                'sales' => 278,
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=900&h=900&fit=crop',
                'desc' => 'عصر على البارد بدون مواد حافظة.',
            ],
        ];

        $this->seedProducts($brand, $categories, $products);

        $misk = Product::where('slug', 'misk-oil')->where('brand_id', $brand->id)->first();
        if ($misk && isset($categories['misk'])) {
            $misk->update([
                'category_id' => $categories['misk']->id,
                'short_description' => 'زيت مسك أبيض طبيعي مركز — رائحة ثابتة وناعمة.',
            ]);
            $this->attachProductCover($misk, 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=900&h=900&fit=crop');
        }
    }

    private function seedCareCatalog(): void
    {
        $brand = Brand::where('slug', 'care')->first();
        if (! $brand) {
            return;
        }

        $tree = [
            'skincare' => [
                'name' => 'العناية بالبشرة',
                'sort' => 1,
                'children' => [
                    'loreal' => 'لوريال',
                    'neutrogena' => 'نيوتروجينا',
                    'the-ordinary' => 'ذا أورديناري',
                ],
            ],
            'makeup' => [
                'name' => 'المكياج',
                'sort' => 2,
                'children' => [
                    'maybelline' => 'ميبيلين',
                    'mac' => 'MAC',
                ],
            ],
        ];

        $categories = $this->seedCategoryTree($brand, $tree);

        $products = [
            [
                'slug' => 'loreal-hyaluron-serum',
                'category' => 'loreal',
                'mark' => 'لور',
                'name' => 'سيروم لوريال هايلورون — ترطيب مكثف',
                'price' => 420,
                'compare' => 550,
                'badge' => 'جديد',
                'featured' => true,
                'rating' => 4.8,
                'sales' => 289,
                'image' => 'https://images.unsplash.com/photo-1620916569338-8f1d96201def?w=900&h=900&fit=crop',
                'desc' => 'سيروم حمض هايلورونيك لبشرة ممتلئة ومشرقة.',
            ],
            [
                'slug' => 'neutrogena-sunscreen',
                'category' => 'neutrogena',
                'mark' => 'نيو',
                'name' => 'واقي شمس نيوتروجينا SPF 50',
                'price' => 310,
                'compare' => 390,
                'badge' => 'أساسي',
                'featured' => true,
                'rating' => 4.7,
                'sales' => 356,
                'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=900&h=900&fit=crop',
                'desc' => 'حماية واسعة الطيف خفيفة غير لزجة.',
            ],
            [
                'slug' => 'ordinary-niacinamide',
                'category' => 'the-ordinary',
                'mark' => 'TO',
                'name' => 'ذا أورديناري نياسيناميد 10%',
                'price' => 280,
                'compare' => 350,
                'badge' => null,
                'featured' => false,
                'rating' => 4.9,
                'sales' => 412,
                'image' => 'https://images.unsplash.com/photo-1570194065650-d99fb4c57f14?w=900&h=900&fit=crop',
                'desc' => 'يقلل المسام ويوحد لون البشرة.',
            ],
            [
                'slug' => 'maybelline-fit-me',
                'category' => 'maybelline',
                'mark' => 'ميب',
                'name' => 'فاونديشن ميبيلين Fit Me',
                'price' => 245,
                'compare' => 320,
                'badge' => 'الأكثر مبيعًا',
                'featured' => true,
                'rating' => 4.6,
                'sales' => 523,
                'image' => 'https://images.unsplash.com/photo-1631214524020-7e18db9a8f92?w=900&h=900&fit=crop',
                'desc' => 'تغطية طبيعية تناسب كل درجات البشرة.',
            ],
            [
                'slug' => 'mac-ruby-woo',
                'category' => 'mac',
                'mark' => 'MAC',
                'name' => 'أحمر شفاه MAC Ruby Woo',
                'price' => 650,
                'compare' => 780,
                'badge' => 'أيقونة',
                'featured' => true,
                'rating' => 4.9,
                'sales' => 198,
                'image' => 'https://images.unsplash.com/photo-1586495777744-4416fdd442c4?w=900&h=900&fit=crop',
                'desc' => 'لون أحمر كلاسيكي مات طويل الثبات.',
            ],
        ];

        $this->seedProducts($brand, $categories, $products);

        $existing = [
            'qalam-7awageb' => ['category' => 'maybelline', 'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=900&h=900&fit=crop'],
            'face-serum' => ['category' => 'the-ordinary', 'image' => 'https://images.unsplash.com/photo-1620916569338-8f1d96201def?w=900&h=900&fit=crop'],
            'cream' => ['category' => 'neutrogena', 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=900&h=900&fit=crop'],
            'lipstick' => ['category' => 'mac', 'image' => 'https://images.unsplash.com/photo-1586495777744-4416fdd442c4?w=900&h=900&fit=crop'],
        ];

        foreach ($existing as $slug => $data) {
            $product = Product::where('slug', $slug)->where('brand_id', $brand->id)->first();
            if (! $product) {
                continue;
            }

            if (isset($categories[$data['category']])) {
                $product->update(['category_id' => $categories[$data['category']]->id]);
            }

            $this->attachProductCover($product, $data['image']);
        }
    }

    /**
     * @param  array<string, array{name: string, sort: int, children: array<string, string>}>  $tree
     * @return array<string, Category>
     */
    private function seedCategoryTree(Brand $brand, array $tree): array
    {
        $map = [];

        foreach ($tree as $parentSlug => $parentData) {
            $parent = Category::updateOrCreate(
                ['brand_id' => $brand->id, 'slug' => $parentSlug],
                ['name' => $parentData['name'], 'sort' => $parentData['sort'], 'is_active' => true, 'parent_id' => null],
            );

            $map[$parentSlug] = $parent;

            foreach ($parentData['children'] as $childSlug => $childName) {
                $child = Category::updateOrCreate(
                    ['brand_id' => $brand->id, 'slug' => $childSlug],
                    ['name' => $childName, 'sort' => 0, 'is_active' => true, 'parent_id' => $parent->id],
                );
                $map[$childSlug] = $child;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array<int, array<string, mixed>>  $products
     */
    private function seedProducts(Brand $brand, array $categories, array $products): void
    {
        foreach ($products as $sort => $data) {
            $category = $categories[$data['category']] ?? null;

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'brand_id' => $brand->id,
                    'category_id' => $category?->id,
                    'name' => $data['name'],
                    'mark' => $data['mark'],
                    'short_description' => $data['desc'],
                    'price' => $data['price'],
                    'compare_price' => $data['compare'],
                    'badge' => $data['badge'],
                    'is_active' => true,
                    'is_featured' => $data['featured'],
                    'sort' => $sort + 1,
                    'rating' => $data['rating'],
                    'sales_count' => $data['sales'],
                    'stock' => 50,
                    'track_stock' => true,
                    'low_stock_threshold' => 5,
                    'features' => ['جودة أصلية', 'ضمان الوكيل', 'شحن سريع'],
                    'usage_steps' => ['اطلب المنتج', 'استلم خلال 24-48 ساعة', 'استمتع بالجودة'],
                ],
            );

            $product->variants()->delete();
            $product->variants()->createMany([
                ['name' => 'قطعة واحدة', 'subtitle' => 'للتجربة', 'price' => $data['price'], 'is_default' => true, 'sort' => 1, 'stock' => 30, 'track_stock' => true],
                ['name' => 'قطعتان', 'subtitle' => 'وفّر 10%', 'price' => (int) ($data['price'] * 1.8), 'is_popular' => true, 'sort' => 2, 'stock' => 20, 'track_stock' => true],
            ]);

            $this->attachProductCover($product, $data['image']);
        }
    }

    private function attachProductCover(Product $product, string $url): void
    {
        if ($product->getFirstMedia('cover')) {
            return;
        }

        $this->attachFromUrl($product, 'cover', $url);
    }

    private function attachFromUrl(HasMedia $model, string $collection, string $url, ?string $fallbackSeed = null): void
    {
        $seed = $fallbackSeed ?? Str::slug(parse_url($url, PHP_URL_PATH) ?: 'image');
        $sources = array_unique([
            $url,
            "https://picsum.photos/seed/{$seed}/800/800",
        ]);

        foreach ($sources as $source) {
            if ($this->downloadToMedia($model, $collection, $source, $seed)) {
                return;
            }
        }

        $this->command?->warn("تعذّر تحميل الصورة: {$url}");
    }

    private function downloadToMedia(HasMedia $model, string $collection, string $url, string $name): bool
    {
        try {
            $response = Http::timeout(45)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; AlamatShopSeeder/1.0)',
                    'Accept' => 'image/*,*/*',
                ])
                ->get($url);

            if (! $response->successful()) {
                return false;
            }

            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif') => 'gif',
                default => 'jpg',
            };

            $temp = tempnam(sys_get_temp_dir(), 'catalog_seed_').'.'.$extension;
            file_put_contents($temp, $response->body());

            $model->addMedia($temp)
                ->usingName(Str::slug($collection.'-'.$name))
                ->toMediaCollection($collection, 'public');

            @unlink($temp);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function fixMissingMedia(): void
    {
        foreach ($this->brandLogos as $slug => $url) {
            $brand = Brand::where('slug', $slug)->first();
            if ($brand && ! $brand->getFirstMedia('logo')) {
                $this->attachFromUrl($brand, 'logo', $url, 'brand-'.$slug);
            }
        }

        Product::query()->each(function (Product $product) {
            if ($product->getFirstMedia('cover')) {
                return;
            }

            $this->attachFromUrl(
                $product,
                'cover',
                "https://picsum.photos/seed/product-{$product->slug}/800/800",
                'product-'.$product->slug,
            );
        });
    }
}
