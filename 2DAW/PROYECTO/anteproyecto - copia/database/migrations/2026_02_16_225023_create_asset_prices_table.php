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
        Schema::create('asset_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_asset_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('price', 20, 8); // Alta precisión para criptos
            $table->bigInteger('volume')->nullable();
            $table->string('source')->default('manual'); // 'FMP', 'CoinGecko', 'EODHD', 'manual'
            $table->timestamps();

            // Índice único para evitar duplicados por fecha y activo
            $table->unique(['market_asset_id', 'date']);
            // Índice para búsquedas rápidas por fecha
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_prices');
    }
};
