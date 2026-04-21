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
            $table->string('isin')->nullable()->after('ticker');
            // We'll store the link to the market asset if it came from there
            $table->foreignId('market_asset_id')->nullable()->after('id')->constrained('market_assets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['market_asset_id']);
            $table->dropColumn(['isin', 'market_asset_id']);
        });
    }
};
