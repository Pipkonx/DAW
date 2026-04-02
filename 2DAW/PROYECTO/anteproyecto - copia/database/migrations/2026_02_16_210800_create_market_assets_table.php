<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_assets', function (Blueprint $table) {
            $table->id();
            $table->string('ticker')->unique(); // BTC, AAPL
            $table->string('name');
            $table->string('isin')->nullable(); // Optional for crypto
            $table->enum('type', ['stock', 'etf', 'crypto', 'fund', 'bond', 'other']);
            $table->string('currency_code')->default('USD');
            $table->string('sector')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_assets');
    }
};
