<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\ObservacionDiaria;
use App\Models\Incidencia;
use Illuminate\Database\Seeder;

/**
 * @class InteraccionSeeder
 * @brief Seeder encargado de generar interacciones (observaciones e incidencias) para los alumnos.
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
     * @brief Crea observaciones e incidencias para cada alumno existente.
     */
    protected function generarInteraccionesPrueba(): void
    {
        $alumnos = Alumno::all();

        foreach ($alumnos as $alumno) {
            // 1. Crear un par de observaciones diarias
            ObservacionDiaria::create([
                'alumno_id' => $alumno->id,
                'fecha' => now()->subDays(2),
                'actividad' => 'Análisis de requerimientos y diseño de base de datos.',
                'horas' => 6,
            ]);

            ObservacionDiaria::create([
                'alumno_id' => $alumno->id,
                'fecha' => now()->subDays(1),
                'actividad' => 'Implementación de controladores y modelos iniciales.',
                'horas' => 7,
            ]);

            // 2. Crear una incidencia para cada alumno
            Incidencia::create([
                'alumno_id' => $alumno->id,
                'titulo' => 'Dificultad con el entorno de desarrollo',
                'descripcion' => 'El alumno presenta problemas para configurar el servidor local debido a permisos de administrador.',
                'estado' => 'abierta',
                'prioridad' => 'media',
            ]);
        }
    }
}
