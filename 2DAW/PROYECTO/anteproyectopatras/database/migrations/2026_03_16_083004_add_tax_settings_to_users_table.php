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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('enable_tax_projection')->default(false)->after('investment_return_rate');
            $table->decimal('tax_rate', 5, 2)->default(19.00)->after('enable_tax_projection'); // 19% por defecto (mínimo ahorro en España)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['enable_tax_projection', 'tax_rate']);
        });
    }
};
