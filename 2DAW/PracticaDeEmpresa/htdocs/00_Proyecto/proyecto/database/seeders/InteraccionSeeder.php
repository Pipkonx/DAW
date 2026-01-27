<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\ObservacionDiaria;
use App\Models\Incidencia;
use App\Models\Evaluacion;
use App\Models\TutorPracticas;
use Illuminate\Database\Seeder;

/**
 * @class InteraccionSeeder
 * @brief Seeder encargado de generar interacciones (observaciones, incidencias y evaluaciones) para los alumnos.
 */
class InteraccionSeeder extends Seeder
{
    /**
     * @brief Ejecuta el sembrado de interacciones.
     */
    public function run(): void
    {
        $this->generarInteraccionesPrueba();
    }

    /**
     * @brief Crea observaciones, incidencias y evaluaciones para cada alumno existente.
     */
    protected function generarInteraccionesPrueba(): void
    {
        $alumnos = Alumno::all();
        
        if ($alumnos->isEmpty()) {
            return;
        }

        foreach ($alumnos as $alumno) {
            // 1. Crear un par de observaciones diarias
            ObservacionDiaria::create([
                'alumno_id' => $alumno->id,
                'fecha' => now()->subDays(2),
                'actividades' => 'Análisis de requerimientos y diseño de base de datos.',
                'explicaciones' => 'Se revisaron los requisitos con el tutor.',
                'observacionesAlumno' => 'Aprendí mucho sobre normalización.',
                'observacionesTutor' => 'Buen progreso.',
                'horasRealizadas' => 6,
            ]);

            ObservacionDiaria::create([
                'alumno_id' => $alumno->id,
                'fecha' => now()->subDays(1),
                'actividades' => 'Implementación de controladores y modelos iniciales.',
                'explicaciones' => 'Se implementó el CRUD básico.',
                'observacionesAlumno' => 'Problemas menores con migraciones.',
                'observacionesTutor' => 'Ayudar con debugging.',
                'horasRealizadas' => 7,
            ]);

            // 2. Crear una incidencia para cada alumno
            $tutorPracticas = TutorPracticas::where('empresa_id', $alumno->empresa_id)->first() ?? TutorPracticas::first();
            
            Incidencia::create([
                'alumno_id' => $alumno->id,
                'tutor_practicas_id' => $tutorPracticas ? $tutorPracticas->id : null,
                'fecha' => now()->subDays(5),
                'tipo' => 'OTROS',
                'descripcion' => 'El alumno presenta problemas para configurar el servidor local debido a permisos de administrador.',
                'estado' => 'ABIERTA',
            ]);

            // 3. Crear una evaluación final
            Evaluacion::create([
                'alumno_id' => $alumno->id,
                'tutor_practicas_id' => $tutorPracticas ? $tutorPracticas->id : null,
                'nota_final' => rand(7, 10),
                'observaciones' => 'Excelente desempeño durante las prácticas.',
            ]);
        }
    }
}
