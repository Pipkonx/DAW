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
        Schema::table('capacidad_evaluacions', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('puntuacion_maxima');
        });

        Schema::table('evaluacion_tutors', function (Blueprint $table) {
            $table->text('aspectosPositivos')->nullable()->after('observaciones_finales');
            $table->text('aspectosMejorar')->nullable()->after('aspectosPositivos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capacidad_evaluacions', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        Schema::table('evaluacion_tutors', function (Blueprint $table) {
            $table->dropColumn(['aspectosPositivos', 'aspectosMejorar']);
        });
    }
};
