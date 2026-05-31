<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['free', 'flat', 'percent_off', 'amount_off'])->default('free');
            $table->unsignedInteger('value')->nullable();
            $table->enum('scope', ['all', 'selected'])->default('all');
            $table->json('governorate_ids')->nullable();
            $table->unsignedInteger('min_order_total')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'brand_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rules');
    }
};
