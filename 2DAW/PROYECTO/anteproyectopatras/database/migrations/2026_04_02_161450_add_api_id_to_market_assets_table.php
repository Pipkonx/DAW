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
        Schema::table('market_assets', function (Blueprint $table) {
            $table->string('api_id')->nullable()->after('ticker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_assets', function (Blueprint $table) {
            $table->dropColumn('api_id');
        });
    }
};
