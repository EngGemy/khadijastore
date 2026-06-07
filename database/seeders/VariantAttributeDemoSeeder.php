<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class VariantAttributeDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Attributes
        $colorAttr = Attribute::updateOrCreate(
            ['code' => 'color'],
            [
                'brand_id' => null,
                'name' => 'اللون',
                'code' => 'color',
                'input_type' => 'color',
                'sort' => 1,
            ]
        );

        $sizeAttr = Attribute::updateOrCreate(
            ['code' => 'size'],
            [
                'brand_id' => null,
                'name' => 'الحجم',
                'code' => 'size',
                'input_type' => 'select',
                'sort' => 2,
            ]
        );

        // 2. Create Attribute Values
        $colorValues = [
            ['label' => 'أحمر', 'value' => 'red', 'color_hex' => '#DC2626', 'sort' => 1],
            ['label' => 'أزرق', 'value' => 'blue', 'color_hex' => '#2563EB', 'sort' => 2],
            ['label' => 'أسود', 'value' => 'black', 'color_hex' => '#171717', 'sort' => 3],
        ];

        $sizeValues = [
            ['label' => '100 جرام', 'value' => '100g', 'sort' => 1],
            ['label' => '250 جرام', 'value' => '250g', 'sort' => 2],
            ['label' => '500 جرام', 'value' => '500g', 'sort' => 3],
        ];

        $colorValueMap = [];
        foreach ($colorValues as $cv) {
            $v = AttributeValue::updateOrCreate(
                ['attribute_id' => $colorAttr->id, 'value' => $cv['value']],
                array_merge($cv, ['attribute_id' => $colorAttr->id])
            );
            $colorValueMap[$cv['value']] = $v->id;
        }

        $sizeValueMap = [];
        foreach ($sizeValues as $sv) {
            $v = AttributeValue::updateOrCreate(
                ['attribute_id' => $sizeAttr->id, 'value' => $sv['value']],
                array_merge($sv, ['attribute_id' => $sizeAttr->id])
            );
            $sizeValueMap[$sv['value']] = $v->id;
        }

        // 3. Get the attar brand and misk-oil product
        $brand = Brand::where('slug', 'attar')->first();
        if (! $brand) {
            $this->command->warn('Brand with slug "attar" not found. Run DemoSeeder first.');
            return;
        }

        $product = Product::where('slug', 'misk-oil')->where('brand_id', $brand->id)->first();
        if (! $product) {
            $this->command->warn('Product "misk-oil" not found. Run DemoSeeder first.');
            return;
        }

        // 4. Delete old variants and create new ones with option_values
        $product->variants()->delete();

        $variants = [
            // Red
            ['name' => 'مسك أحمر 100جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['red']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['100g']]], 'price' => 65, 'stock' => 50, 'sort' => 1],
            ['name' => 'مسك أحمر 250جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['red']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['250g']]], 'price' => 120, 'stock' => 30, 'sort' => 2],
            ['name' => 'مسك أحمر 500جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['red']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['500g']]], 'price' => 220, 'stock' => 15, 'sort' => 3],
            // Blue
            ['name' => 'مسك أزرق 100جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['blue']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['100g']]], 'price' => 65, 'stock' => 40, 'sort' => 4],
            ['name' => 'مسك أزرق 250جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['blue']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['250g']]], 'price' => 120, 'stock' => 25, 'sort' => 5],
            ['name' => 'مسك أزرق 500جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['blue']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['500g']]], 'price' => 220, 'stock' => 10, 'sort' => 6],
            // Black
            ['name' => 'مسك أسود 100جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['black']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['100g']]], 'price' => 70, 'stock' => 20, 'sort' => 7],
            ['name' => 'مسك أسود 250جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['black']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['250g']]], 'price' => 130, 'stock' => 12, 'sort' => 8],
            ['name' => 'مسك أسود 500جرام', 'option_values' => [['attribute_id' => $colorAttr->id, 'value_id' => $colorValueMap['black']], ['attribute_id' => $sizeAttr->id, 'value_id' => $sizeValueMap['500g']]], 'price' => 240, 'stock' => 5, 'sort' => 9],
        ];

        foreach ($variants as $v) {
            ProductVariant::create([
                'product_id' => $product->id,
                'name' => $v['name'],
                'price' => $v['price'],
                'stock' => $v['stock'],
                'track_stock' => true,
                'low_stock_threshold' => 5,
                'sort' => $v['sort'],
                'option_values' => $v['option_values'],
            ]);
        }

        // 5. Add price tiers for this product
        $product->priceTiers()->delete();
        $product->priceTiers()->createMany([
            ['label' => 'القطاعي', 'min_qty' => 1, 'price' => 65, 'is_active' => true, 'sort' => 1],
            ['label' => 'نص جملة', 'min_qty' => 10, 'price' => 55, 'is_active' => true, 'sort' => 2],
            ['label' => 'جملة', 'min_qty' => 50, 'price' => 45, 'is_active' => true, 'sort' => 3],
        ]);

        $this->command->info("Created attributes + 9 variants with color/size for product: {$product->name}");
        $this->command->info("Visit: /product/misk-oil to test the new attribute selectors.");
    }
}
