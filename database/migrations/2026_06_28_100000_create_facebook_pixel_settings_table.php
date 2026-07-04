<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_pixel_settings', function (Blueprint $table) {
            $table->id();
            // Each brand acts as an independent store tenant.
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('pixel_id', 32);
            $table->text('access_token');
            $table->string('test_event_code')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->boolean('track_pageview')->default(true);
            $table->boolean('track_viewcontent')->default(true);
            $table->boolean('track_addtocart')->default(true);
            $table->boolean('track_initiatecheckout')->default(true);
            $table->boolean('track_purchase')->default(true);
            $table->boolean('track_lead')->default(true);
            $table->boolean('capi_enabled')->default(true);
            $table->timestamps();

            $table->unique('brand_id');
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_pixel_settings');
    }
};
