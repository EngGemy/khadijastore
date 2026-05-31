<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('order_no')->unique();          // ALM-2026-00001

            // بيانات العميل
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('governorate');
            $table->text('address');
            $table->text('notes')->nullable();

            // الدفع
            $table->enum('payment_method', ['cod', 'whatsapp', 'transfer'])->default('cod');
            $table->string('receipt_path')->nullable();    // صورة إيصال التحويل

            // الحالة
            $table->enum('status', [
                'pending',      // قيد المراجعة
                'confirmed',    // مؤكد
                'processing',   // قيد التجهيز
                'shipped',      // تم الشحن
                'delivered',    // تم التسليم
                'cancelled',    // ملغي
            ])->default('pending');

            // المبالغ (snapshot لحظة الطلب)
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('shipping')->default(0);
            $table->unsignedInteger('total');

            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['brand_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');     // snapshot
            $table->string('variant_name')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedInteger('line_total');
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
