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
        // Modificar tabla incidencias
        Schema::table('incidencias', function (Blueprint $table) {
            // Renombrar o añadir columnas
            $table->foreignId('tutor_practicas_id')->after('alumno_id')->nullable()->constrained('tutor_practicas')->onDelete('cascade');
            $table->date('fecha')->after('tutor_practicas_id')->nullable();
            
            // tipo ENUM: FALTA, RETRASO, PROBLEMA_ACTITUD, OTROS
            // Usamos string para enum por flexibilidad o enum nativo si DB lo soporta bien. Usaremos enum de Laravel
            $table->enum('tipo', ['FALTA', 'RETRASO', 'PROBLEMA_ACTITUD', 'OTROS'])->default('OTROS')->after('fecha');
            
            // resolucion (renombrar explicacion_resolucion si existe, o crear)
            // En migracion anterior era 'explicacion_resolucion'. Vamos a renombrarla.
            $table->renameColumn('explicacion_resolucion', 'resolucion');
            
            // Modificar enum estado para match user requirements: ABIERTA, EN_PROCESO, RESUELTA
            // DB change for ENUM can be tricky. We might drop and re-add or just alter.
            // Para SQLite/MySQL simple, a veces es mejor drop y add si no hay datos criticos, o change().
            // Asumimos MySQL/MariaDB.
            // Primero eliminamos el default antiguo si es necesario, luego cambiamos.
            // Nota: cambiar columnas enum es complejo en algunas DBs.
            // Vamos a intentar modificarlo. Si falla, asumimos que los valores antiguos se mapean o se truncan.
            // Dado que es desarrollo, podemos permitirnos dropColumn y addColumn para 'estado' si queremos asegurar los valores,
            // pero intentaremos change() primero.
            // Actually, let's drop 'titulo' and 'prioridad' as they are not in the new list.
            $table->dropColumn(['titulo', 'prioridad']);
        });

        // Como modificar ENUMs es complejo y varia por DB, lo hacemos en una sentencia raw o drop/add
        // Para mayor seguridad en desarrollo:
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
        Schema::table('incidencias', function (Blueprint $table) {
            $table->enum('estado', ['ABIERTA', 'EN_PROCESO', 'RESUELTA'])->default('ABIERTA')->after('resolucion');
        });

        // Modificar tabla observacion_diarias
        Schema::table('observacion_diarias', function (Blueprint $table) {
            // Renombrar actividad -> actividades
            $table->renameColumn('actividad', 'actividades');
            
            // Añadir explicaciones
            $table->text('explicaciones')->nullable()->after('actividades');
            
            // Renombrar observaciones -> observacionesAlumno (asumiendo que 'observaciones' era general, ahora especificamos)
            $table->renameColumn('observaciones', 'observacionesAlumno');
            
            // Añadir observacionesTutor
            $table->text('observacionesTutor')->nullable()->after('observacionesAlumno');
            
            // Renombrar horas -> horasRealizadas
            $table->renameColumn('horas', 'horasRealizadas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign(['tutor_practicas_id']);
            $table->dropColumn(['tutor_practicas_id', 'fecha', 'tipo']);
            $table->renameColumn('resolucion', 'explicacion_resolucion');
            $table->string('titulo')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->dropColumn('estado');
        });
        
        Schema::table('incidencias', function (Blueprint $table) {
            $table->enum('estado', ['abierta', 'en proceso', 'resuelta'])->default('abierta');
        });

        Schema::table('observacion_diarias', function (Blueprint $table) {
            $table->renameColumn('actividades', 'actividad');
            $table->dropColumn('explicaciones');
            $table->renameColumn('observacionesAlumno', 'observaciones');
            $table->dropColumn('observacionesTutor');
            $table->renameColumn('horasRealizadas', 'horas');
        });
    }
};
