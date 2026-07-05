<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_api_keys')->default(0);
            $table->unsignedInteger('total_webhooks_received')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_stats');
    }
};
