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
        // 1. Observaciones Diarias
        Schema::create('observacion_diarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->date('fecha');
            $table->text('actividad');
            $table->integer('horas');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Incidencias
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['abierta', 'en proceso', 'resuelta'])->default('abierta');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->timestamp('fecha_resolucion')->nullable();
            $table->text('explicacion_resolucion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Criterios de Evaluación
        Schema::create('criterio_evaluacions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 4. Capacidades de Evaluación
        Schema::create('capacidad_evaluacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterio_id')->constrained('criterio_evaluacions')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 5. Evaluaciones
        Schema::create('evaluacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('tutor_practicas_id')->constrained('tutor_practicas')->onDelete('cascade');
            $table->foreignId('capacidad_id')->nullable()->constrained('capacidad_evaluacions')->onDelete('cascade');
            $table->decimal('nota', 4, 2)->nullable();
            $table->decimal('nota_final', 4, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Evaluación Tutors
        Schema::create('evaluacion_tutors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->onDelete('cascade');
            $table->foreignId('tutor_curso_id')->constrained('tutor_cursos')->onDelete('cascade');
            $table->decimal('nota_final', 4, 2);
            $table->text('observaciones_finales')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Evaluación Detalles
        Schema::create('evaluacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluacions')->onDelete('cascade');
            $table->foreignId('capacidad_id')->constrained('capacidad_evaluacions')->onDelete('cascade');
            $table->decimal('nota', 4, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion_detalles');
        Schema::dropIfExists('evaluacion_tutors');
        Schema::dropIfExists('evaluacions');
        Schema::dropIfExists('capacidad_evaluacions');
        Schema::dropIfExists('criterio_evaluacions');
        Schema::dropIfExists('incidencias');
        Schema::dropIfExists('observacion_diarias');
    }
};
