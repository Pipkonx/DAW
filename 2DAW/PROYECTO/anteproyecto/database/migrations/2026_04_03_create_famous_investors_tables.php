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
        // 1. Perfiles de Inversores Famosos
        Schema::create('famous_investors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('cik')->unique();
            $table->text('description')->nullable();
            $table->string('avatar')->nullable();
            $table->string('location')->nullable();
            $table->string('type')->default('Público');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        // 2. Holdings (Posiciones actuales 13F)
        Schema::create('famous_investor_holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('famous_investor_id')->constrained()->cascadeOnDelete();
            $table->string('symbol');
            $table->string('name');
            $table->decimal('shares_number', 20, 2)->default(0);
            $table->decimal('market_value', 20, 2)->default(0);
            $table->decimal('weight', 8, 4)->default(0);
            $table->timestamps();
        });

        // 3. Trades (Historial comercial)
        Schema::create('famous_investor_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('famous_investor_id')->constrained()->cascadeOnDelete();
            $table->string('symbol');
            $table->string('name')->nullable();
            $table->decimal('change_in_shares', 20, 2)->default(0);
            $table->string('change_type')->nullable(); // buy, sell, new, reduced, etc.
            $table->date('filling_date');
            $table->decimal('percent_of_portfolio', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('famous_investor_trades');
        Schema::dropIfExists('famous_investor_holdings');
        Schema::dropIfExists('famous_investors');
    }
};
