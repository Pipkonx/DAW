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
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('localidad')->nullable()->after('direccion');
            $table->string('provincia')->nullable()->after('localidad');
            $table->string('codigo_postal')->nullable()->after('provincia');
            $table->string('web')->nullable()->after('email');
            $table->boolean('activa')->default(true)->after('sector');
            $table->timestamp('fecha_creacion')->nullable()->after('activa');
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre');
            $table->integer('duracion')->nullable()->after('descripcion');
            $table->date('fecha_inicio')->nullable()->after('duracion');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->foreignId('tutor_curso_id')->nullable()->after('fecha_fin')->constrained('tutor_cursos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['localidad', 'provincia', 'codigo_postal', 'web', 'activa', 'fecha_creacion']);
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeign(['tutor_curso_id']);
            $table->dropColumn(['descripcion', 'duracion', 'fecha_inicio', 'fecha_fin', 'tutor_curso_id']);
        });
    }
};
