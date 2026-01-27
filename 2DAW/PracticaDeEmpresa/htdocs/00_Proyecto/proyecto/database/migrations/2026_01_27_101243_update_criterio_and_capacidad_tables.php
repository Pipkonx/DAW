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
        Schema::table('criterio_evaluacions', function (Blueprint $table) {
            $table->decimal('peso', 5, 2)->after('descripcion')->nullable(); // Porcentaje sobre la nota final
            $table->boolean('activo')->default(true)->after('peso');
        });

        Schema::table('capacidad_evaluacions', function (Blueprint $table) {
            // Nota: criterio_id ya existe en la tabla original, pero verificamos si se necesita algún cambio
            // Según la instrucción, capacidad tiene criterioId (FK), que ya existe como criterio_id.
            $table->integer('puntuacion_maxima')->default(10)->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criterio_evaluacions', function (Blueprint $table) {
            $table->dropColumn(['peso', 'activo']);
        });

        Schema::table('capacidad_evaluacions', function (Blueprint $table) {
            $table->dropColumn(['puntuacion_maxima']);
        });
    }
};
