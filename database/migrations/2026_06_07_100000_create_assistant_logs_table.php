<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistant_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id')->nullable()->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('ip', 45)->nullable();
            $table->text('query');
            $table->text('reply')->nullable();
            $table->json('products')->nullable();
            $table->unsignedSmallInteger('response_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistant_logs');
    }
};
