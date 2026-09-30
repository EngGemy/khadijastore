<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProductPackagePricesCommand extends Command
{
    protected $signature = 'products:sync-package-prices {--id= : Sync a single product id}';

    protected $description = 'Sync each product.price onto its primary package/variant (fixes storefront showing old package prices)';

    public function handle(): int
    {
        $query = Product::query()->with('variants');

        if ($id = $this->option('id')) {
            $query->whereKey($id);
        }

        $synced = 0;
        $query->chunkById(100, function ($products) use (&$synced): void {
            foreach ($products as $product) {
                if ($product->syncPrimaryVariantPrice((int) $product->price)) {
                    $synced++;
                    $this->line("✓ #{$product->id} {$product->name} → {$product->price}");
                }
            }
        });

        forget_home_blocks_cache();
        $this->info("Synced {$synced} product(s). Storefront cache cleared.");

        return self::SUCCESS;
    }
}
