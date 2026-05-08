<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('provider_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('password')->nullable()->change(); // Permitir password nulo para login social
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['provider_id', 'provider_name']);
        });
    }
};
