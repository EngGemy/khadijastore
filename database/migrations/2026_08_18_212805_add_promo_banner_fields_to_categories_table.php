<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('promo_headline')->nullable()->after('is_active');
            $table->string('promo_cta_text')->nullable()->after('promo_headline');
            $table->string('promo_cta_url')->nullable()->after('promo_cta_text');
            $table->boolean('is_promo_active')->default(false)->after('promo_cta_url');

            $table->index(['is_promo_active', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_promo_active', 'is_active']);
            $table->dropColumn([
                'promo_headline',
                'promo_cta_text',
                'promo_cta_url',
                'is_promo_active',
            ]);
        });
    }
};
