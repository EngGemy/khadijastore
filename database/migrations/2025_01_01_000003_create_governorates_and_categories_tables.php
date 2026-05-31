<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // المحافظات المصرية + رسوم الشحن (مرجعي مشترك)
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('shipping_fee')->default(0); // بالجنيه
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // التصنيفات (تابعة للبراند)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['brand_id', 'slug']);
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('governorates');
    }
};
