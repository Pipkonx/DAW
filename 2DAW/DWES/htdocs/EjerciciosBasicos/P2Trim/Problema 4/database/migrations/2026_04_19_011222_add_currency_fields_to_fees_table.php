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
        Schema::table('fees', function (Blueprint $table) {
            // Guardamos el importe en euros calculado al momento del pago
            $table->decimal('amount_eur', 10, 2)->nullable()->after('amount');
            // Guardamos la tasa de cambio que se aplicó
            $table->decimal('exchange_rate', 10, 6)->nullable()->after('amount_eur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn(['amount_eur', 'exchange_rate']);
        });
    }
};
