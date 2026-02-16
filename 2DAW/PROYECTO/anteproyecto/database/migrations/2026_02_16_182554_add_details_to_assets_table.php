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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('sector')->nullable()->after('type'); // Technology, Healthcare, etc.
            $table->string('industry')->nullable()->after('sector'); // Software, Biotech, etc.
            $table->string('region')->nullable()->after('industry'); // North America, Europe, etc.
            $table->string('country')->nullable()->after('region'); // USA, Germany, etc.
            $table->string('currency_code')->default('EUR')->after('country'); // USD, EUR, etc. (Renamed to currency_code to avoid conflict if I used currency)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['sector', 'industry', 'region', 'country', 'currency_code']);
        });
    }
};
