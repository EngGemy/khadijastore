<?php

use App\Models\HomeBlock;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE home_blocks MODIFY COLUMN type ENUM(
            'categories',
            'banner',
            'rich_text',
            'products_grid',
            'image_cta',
            'brands_marquee',
            'brands_grid',
            'brands_filter'
        ) NOT NULL");

        if (HomeBlock::where('type', 'brands_filter')->exists()) {
            return;
        }

        HomeBlock::create([
            'type' => 'brands_filter',
            'title' => 'اختار البراند',
            'subtitle' => 'فلتر حسب المتجر · FILTER BY STORE',
            'is_active' => true,
            'sort' => 35,
            'data' => ['limit' => null],
        ]);
    }

    public function down(): void
    {
        HomeBlock::where('type', 'brands_filter')->delete();

        DB::statement("ALTER TABLE home_blocks MODIFY COLUMN type ENUM(
            'categories',
            'banner',
            'rich_text',
            'products_grid',
            'image_cta',
            'brands_marquee',
            'brands_grid'
        ) NOT NULL");
    }
};
