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
        Schema::table('cursos', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('fecha_fin');
        });

        Schema::table('tutor_cursos', function (Blueprint $table) {
            $table->string('especialidad')->nullable()->after('user_id');
            $table->boolean('activo')->default(true)->after('especialidad');
        });

        Schema::table('tutor_practicas', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        Schema::table('tutor_cursos', function (Blueprint $table) {
            $table->dropColumn(['especialidad', 'activo']);
        });

        Schema::table('tutor_practicas', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
};
