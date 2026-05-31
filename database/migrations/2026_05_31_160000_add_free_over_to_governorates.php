<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->unsignedInteger('free_over')->nullable()->after('shipping_fee')
                ->comment('شحن مجاني إذا تجاوز الطلب هذا المبلغ');
        });
    }

    public function down(): void
    {
        Schema::table('governorates', function (Blueprint $table) {
            $table->dropColumn('free_over');
        });
    }
};
