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
        // 1. Cursos
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        // 2. Empresas
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nombre');
            $table->string('cif')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('persona_contacto')->nullable();
            $table->string('sector')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. TutorCurso
        Schema::create('tutor_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('dni')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('departamento')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. TutorPracticas
        Schema::create('tutor_practicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('empresa_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('dni')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('puesto')->nullable();
            $table->string('horario')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Alumnos
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('curso_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('empresa_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tutor_curso_id')->nullable()->constrained('tutor_cursos')->onDelete('set null');
            $table->foreignId('tutor_practicas_id')->nullable()->constrained('tutor_practicas')->onDelete('set null');
            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('dni')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('duracion_practicas')->nullable();
            $table->string('horario')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
        Schema::dropIfExists('tutor_practicas');
        Schema::dropIfExists('tutor_cursos');
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('cursos');
    }
};
