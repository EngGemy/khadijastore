<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 32);
            $table->enum('input_type', ['select', 'color'])->default('select');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['brand_id', 'code']);
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
            $table->string('label');
            $table->string('value', 64)->nullable();
            $table->string('color_hex', 9)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index('attribute_id');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->json('option_values')->nullable()->after('sort');
            $table->string('sku', 64)->nullable()->after('option_values');
        });

        Schema::create('product_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->unsignedInteger('min_qty');
            $table->unsignedInteger('price');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'is_active', 'min_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_price_tiers');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['option_values', 'sku']);
        });

        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
