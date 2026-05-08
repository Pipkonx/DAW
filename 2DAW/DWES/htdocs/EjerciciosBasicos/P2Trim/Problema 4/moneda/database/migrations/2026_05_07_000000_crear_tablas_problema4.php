<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cif')->unique();
            $table->string('currency', 3)->default('EUR'); // Moneda local (USD, GBP, etc.)
            $table->timestamps();
        });

        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clientes')->onDelete('cascade');
            $table->string('concept');
            $table->decimal('amount', 10, 2); // Importe en moneda local
            $table->string('currency', 3); // Moneda en la que se emite
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->decimal('eur_amount', 10, 2)->nullable(); // Importe convertido a EUR el día del pago
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas');
        Schema::dropIfExists('clientes');
    }
};
