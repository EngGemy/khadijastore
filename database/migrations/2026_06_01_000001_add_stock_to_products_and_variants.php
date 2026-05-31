<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('sort');
            $table->boolean('track_stock')->default(true)->after('stock');
            $table->unsignedInteger('low_stock_threshold')->default(5)->after('track_stock');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->after('sales_count');
            $table->boolean('track_stock')->default(true)->after('stock');
            $table->unsignedInteger('low_stock_threshold')->default(5)->after('track_stock');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['stock', 'track_stock', 'low_stock_threshold']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'track_stock', 'low_stock_threshold']);
        });
    }
};
