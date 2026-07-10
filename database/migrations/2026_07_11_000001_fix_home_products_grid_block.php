<?php

use App\Models\HomeBlock;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        HomeBlock::query()
            ->where('type', 'products_grid')
            ->get()
            ->each(function (HomeBlock $block): void {
                $data = $block->data ?? [];

                if (($data['source'] ?? 'featured') === 'featured') {
                    $data['source'] = 'best_selling';
                }

                if ((int) ($data['limit'] ?? 8) < 24) {
                    $data['limit'] = 48;
                }

                $block->update(['data' => $data]);
            });

        Cache::forget('home.blocks.resolved');
    }

    public function down(): void
    {
        // لا حاجة للتراجع
    }
};
