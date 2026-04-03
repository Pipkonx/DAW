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
            if (!Schema::hasColumn('market_assets', 'ter')) {
                $table->decimal('ter', 5, 2)->default(0)->nullable();
            }
            if (!Schema::hasColumn('market_assets', 'volume')) {
                $table->decimal('volume', 15, 2)->default(0)->nullable();
            }
            if (!Schema::hasColumn('market_assets', 'is_distributing')) {
                $table->boolean('is_distributing')->default(false);
            }
            if (!Schema::hasColumn('market_assets', 'country')) {
                $table->string('country')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_assets', function (Blueprint $table) {
            //
        });
    }
};
