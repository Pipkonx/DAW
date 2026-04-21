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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nombre del activo (Bitcoin, SP500, Bono X)
            $table->string('ticker')->nullable(); // Símbolo (BTC, AAPL)
            $table->enum('type', ['stock', 'fund', 'etf', 'bond', 'crypto', 'real_estate', 'other']); // Tipo de activo
            $table->decimal('quantity', 20, 8)->default(0); // Cantidad de unidades (con alta precisión para cripto)
            $table->decimal('avg_buy_price', 20, 8)->default(0); // Precio promedio de compra
            $table->decimal('current_price', 20, 8)->nullable(); // Precio actual (simulado o manual)
            $table->string('color')->nullable(); // Color para gráficas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
