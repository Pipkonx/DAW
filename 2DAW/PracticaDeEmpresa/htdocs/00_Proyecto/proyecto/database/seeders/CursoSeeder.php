<?php

namespace Database\Seeders;

use App\Models\Curso;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

/**
 * @class CursoSeeder
 * @brief Seeder encargado de generar los cursos académicos obligatorios (Ciclos Formativos).
 */
class CursoSeeder extends Seeder
{
    /**
     * @brief Ejecuta el sembrado de datos para la tabla cursos.
     */
    public function run(): void
    {
        $this->ejecutarSembrado();
    }

    /**
     * @brief Inserta los registros de cursos específicos solicitados.
     */
    protected function ejecutarSembrado(): void
    {
        $cursos = [
            [
                'nombre' => 'Desarrollo de Aplicaciones Web',
                'descripcion' => 'Ciclo Formativo de Grado Superior centrado en tecnologías web frontend y backend.',
                'fecha_inicio' => Carbon::create(2025, 9, 15),
                'fecha_fin' => Carbon::create(2026, 6, 20),
            ],
            [
                'nombre' => 'Desarrollo de Aplicaciones Multiplataforma',
                'descripcion' => 'Ciclo Formativo de Grado Superior especializado en desarrollo móvil y aplicaciones de escritorio.',
                'fecha_inicio' => Carbon::create(2025, 9, 15),
                'fecha_fin' => Carbon::create(2026, 6, 20),
            ],
            [
                'nombre' => 'Administración de Sistemas Informáticos en Red',
                'descripcion' => 'Ciclo Formativo de Grado Superior enfocado en redes, servidores y seguridad informática.',
                'fecha_inicio' => Carbon::create(2025, 9, 15),
                'fecha_fin' => Carbon::create(2026, 6, 20),
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::create($curso);
        }
    }
}
