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
        Schema::table('transactions', function (Blueprint $table) {
            $table->time('time')->nullable()->after('date');
            $table->decimal('fees', 20, 2)->nullable()->after('price_per_unit');
            $table->decimal('exchange_fees', 20, 2)->nullable()->after('fees');
            $table->decimal('tax', 20, 2)->nullable()->after('exchange_fees');
            $table->string('currency', 3)->default('EUR')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['time', 'fees', 'exchange_fees', 'tax', 'currency']);
        });
    }
};
