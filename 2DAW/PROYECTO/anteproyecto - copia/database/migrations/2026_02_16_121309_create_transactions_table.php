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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relación opcional con un activo (si es compra/venta/dividendo)
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('type', [
                'income',       // Ingreso (Sueldo, etc.)
                'expense',      // Gasto
                'buy',          // Compra de inversión
                'sell',         // Venta de inversión
                'dividend',     // Dividendo
                'reward',       // Recompensa (ej. staking)
                'gift',         // Regalo
                'transfer_in',  // Transferencia entrante (ahorro)
                'transfer_out'  // Transferencia saliente
            ]);
            
            $table->decimal('amount', 20, 2); // Monto total de la transacción en moneda base
            $table->date('date'); // Fecha de la transacción
            $table->string('category')->nullable(); // Categoría (Comida, Sueldo, Ahorro, etc.)
            $table->text('description')->nullable(); // Notas adicionales
            
            // Campos específicos de inversión
            $table->decimal('quantity', 20, 8)->nullable(); // Cantidad de unidades compradas/vendidas
            $table->decimal('price_per_unit', 20, 8)->nullable(); // Precio por unidad en el momento
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
