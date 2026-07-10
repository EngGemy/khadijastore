<?php

use App\Models\HomeBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $productsBlock = HomeBlock::query()->where('type', 'products_grid')->first();

        if ($productsBlock) {
            $data = $productsBlock->data ?? [];

            if (($data['source'] ?? 'featured') === 'featured') {
                $data['source'] = 'best_selling';
            }

            if ((int) ($data['limit'] ?? 8) < 24) {
                $data['limit'] = 48;
            }

            $productsBlock->update([
                'is_active' => true,
                'data' => $data,
            ]);
        } else {
            $filterSort = HomeBlock::query()
                ->where('type', 'brands_filter')
                ->value('sort');

            HomeBlock::create([
                'type' => 'products_grid',
                'title' => null,
                'subtitle' => null,
                'is_active' => true,
                'sort' => $filterSort !== null ? $filterSort + 1 : 40,
                'data' => ['source' => 'best_selling', 'limit' => 48],
            ]);
        }

        forget_home_blocks_cache();
    }

    public function down(): void
    {
        // لا حاجة للتراجع
    }
};
