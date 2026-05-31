<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('mark', 16)->nullable();           // النص المختصر للصورة المؤقتة
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('features')->nullable();              // مصفوفة مميزات
            $table->json('usage_steps')->nullable();           // خطوات الاستخدام

            $table->unsignedInteger('price');                  // السعر الأساسي بالجنيه
            $table->unsignedInteger('compare_price')->nullable(); // السعر قبل الخصم
            $table->string('badge')->nullable();               // الأكثر مبيعًا / جديد...
            $table->string('video_url')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->unsignedBigInteger('sales_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['brand_id', 'slug']);
            $table->index(['brand_id', 'is_active']);
        });

        // باقات المنتج (قطعة / قطعتين + هدية ...)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');                  // "قطعة واحدة"
            $table->string('subtitle')->nullable();  // "وفّر 49 ج.م"
            $table->unsignedInteger('price');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
