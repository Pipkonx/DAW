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
        Schema::table('practices', function (Blueprint $table) {
            // El creador de la práctica
            $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Hacer alumno_id nullable para permitir prácticas compartidas
            $table->foreignId('alumno_id')->nullable()->change();
            
            // Para compartir con un grupo específico (curso)
            $table->foreignId('curso_id')->after('alumno_id')->nullable()->constrained('cursos')->onDelete('cascade');
            
            // Para compartir con un rol específico (alumno, tutor_practicas, tutor_curso)
            $table->string('target_role')->after('curso_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            $table->foreignId('alumno_id')->nullable(false)->change();
            
            $table->dropForeign(['curso_id']);
            $table->dropColumn(['curso_id', 'target_role']);
        });
    }
};
