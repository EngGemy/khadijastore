<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name', 120);
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('governorate')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'is_approved']);
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
