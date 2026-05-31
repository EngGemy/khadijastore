<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('mark', 8)->nullable();          // حرف اللوجو المختصر
            $table->string('logo_path')->nullable();
            $table->string('category_label')->nullable();    // وصف الفئة (عربي/إنجليزي)
            $table->text('description')->nullable();

            // قنوات التواصل والدفع المنفصلة لكل براند
            $table->string('whatsapp')->nullable();
            $table->string('vodafone_cash')->nullable();
            $table->string('instapay')->nullable();

            // المواعيد / ساعات العمل المنفصلة
            $table->json('working_hours')->nullable();        // {sat:{from,to,closed}, ...}
            $table->string('timezone')->default('Africa/Cairo');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
