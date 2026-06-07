<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('name');
            $table->string('slug');
            $table->string('name_en')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['brand_id', 'slug']);
            $table->index(['type', 'is_active']);
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32);
            $table->string('name');
            $table->string('slug');
            $table->string('name_en')->nullable();
            $table->text('summary')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();

            // تواصل
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('address_en')->nullable();
            $table->string('governorate')->nullable();
            $table->string('map_url')->nullable();

            // عرض
            $table->json('data')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['brand_id', 'slug']);
            $table->index(['type', 'is_active']);
            $table->index('service_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
        Schema::dropIfExists('service_categories');
    }
};
