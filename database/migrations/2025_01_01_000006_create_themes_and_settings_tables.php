<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ثيمات المناسبات (العيد، رمضان، الجمعة البيضاء...)
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();               // eid, ramadan, default...
            $table->string('name');
            $table->enum('scope', ['global', 'brand'])->default('global');
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();

            // توكنات التصميم (تُحقن كـ CSS variables بدون تغيير الواجهة)
            $table->json('tokens');                        // {accent, ink, paper, font, banner_text, badge...}

            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('priority')->default(0); // أعلى = أولوية أعلى
            $table->timestamp('starts_at')->nullable();    // جدولة تلقائية
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['scope', 'is_active']);
            $table->index(['brand_id', 'is_active']);
        });

        // إعدادات عامة وللبراند
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();

            $table->unique(['brand_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('themes');
    }
};
