<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Alumno;
use App\Models\TutorCurso;
use App\Models\TutorPracticas;
use App\Models\Curso;
use App\Models\Empresa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * @class UsuarioPerfilSeeder
 * @brief Seeder encargado de generar usuarios con sus respectivos roles y perfiles vinculados.
 */
class UsuarioPerfilSeeder extends Seeder
{
    /**
     * @brief Ejecuta el sembrado de usuarios y perfiles.
     */
    public function run(): void
    {
        $this->crearUsuariosYPerfiles();
    }

    /**
     * @brief Crea Admin, Tutores, Alumnos y vincula Empresas manteniendo la integridad referencial.
     */
    protected function crearUsuariosYPerfiles(): void
    {
        // 1. Admin
        if (!User::where('email', 'admin@ejemplo.com')->exists()) {
            $admin = User::create([
                'name' => 'Administrador Sistema',
                'email' => 'admin@ejemplo.com',
                'password' => Hash::make('password'),
            ]);
            $admin->assignRole('admin');
        }

        // 2. Tutores de Curso
        $cursos = Curso::all();
        if ($cursos->isEmpty()) {
            // Si no hay cursos (aunque CursoSeeder debería haber corrido), creamos uno
            $cursos = collect([Curso::create([
                'nombre' => 'Desarrollo de Aplicaciones Web',
                'descripcion' => 'Ciclo Formativo DAW',
                'activo' => true
            ])]);
        }

        for ($i = 1; $i <= 3; $i++) {
            $email = "tutorcurso$i@ejemplo.com";
            if (!User::where('email', $email)->exists()) {
                $user = User::create([
                    'name' => "Tutor Curso $i",
                    'email' => $email,
                    'password' => Hash::make('password'),
                ]);
                $user->assignRole('tutor_curso');
                
                $perfil = TutorCurso::create([
                    'user_id' => $user->id,
                    'nombre' => "Tutor",
                    'apellidos' => "Curso $i",
                    'dni' => "0000000{$i}T",
                    'email' => $email,
                    'departamento' => 'Informática',
                    'especialidad' => 'Desarrollo Web',
                    'activo' => true,
                ]);

                $user->update(['reference_id' => $perfil->id]);
            }
        }

        // 3. Vincular Usuarios a Empresas existentes (de EmpresaSeeder)
        $empresas = Empresa::all();
        foreach ($empresas as $index => $empresa) {
            $email = "empresa$index@ejemplo.com";
            if (!User::where('email', $email)->exists()) {
                $user = User::create([
                    'name' => "Empresa {$empresa->nombre}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                ]);
                $user->assignRole('empresa');

                $empresa->update(['user_id' => $user->id]);
                $user->update(['reference_id' => $empresa->id]);
            }
        }

        // 4. Tutores de Empresa (Prácticas)
        foreach ($empresas as $index => $empresa) {
            $email = "tutorempresa$index@ejemplo.com";
            if (!User::where('email', $email)->exists()) {
                $user = User::create([
                    'name' => "Tutor Empresa $index",
                    'email' => $email,
                    'password' => Hash::make('password'),
                ]);
                $user->assignRole('tutor_practicas');
                
                $perfil = TutorPracticas::create([
                    'user_id' => $user->id,
                    'empresa_id' => $empresa->id,
                    'nombre' => "Tutor",
                    'apellidos' => "Empresa $index",
                    'dni' => "1111111{$index}P",
                    'email' => $email,
                    'puesto' => 'Senior Developer',
                ]);

                $user->update(['reference_id' => $perfil->id]);
            }
        }

        // 5. Alumnos con relaciones completas
        $tutoresEmpresa = TutorPracticas::all();
        $tutoresCurso = TutorCurso::all();

        for ($i = 1; $i <= 5; $i++) {
            $email = "alumno$i@ejemplo.com";
            if (!User::where('email', $email)->exists()) {
                $user = User::create([
                    'name' => "Alumno Prueba $i",
                    'email' => $email,
                    'password' => Hash::make('password'),
                ]);
                $user->assignRole('alumno');

                $empresa = $empresas->random();
                $tutorEmpresa = $tutoresEmpresa->where('empresa_id', $empresa->id)->first();

                $perfil = Alumno::create([
                    'user_id' => $user->id,
                    'curso_id' => $cursos->random()->id,
                    'empresa_id' => $empresa->id,
                    'tutor_curso_id' => $tutoresCurso->random()->id,
                    'tutor_practicas_id' => $tutorEmpresa ? $tutorEmpresa->id : null,
                    'nombre' => "Alumno",
                    'apellidos' => "Prueba $i",
                    'dni' => "1234567{$i}Z",
                    'email' => $email,
                    'telefono' => "60000000$i",
                    'fecha_nacimiento' => '2000-01-01',
                ]);

                $user->update(['reference_id' => $perfil->id]);
            }
        }
        // 6. Configuración Académica (Criterios y Capacidades)
        $criterios = [
            ['nombre' => 'Actitud y Comportamiento', 'peso' => 20],
            ['nombre' => 'Capacidad de Aprendizaje', 'peso' => 30],
            ['nombre' => 'Habilidades Técnicas', 'peso' => 50],
        ];

        foreach ($criterios as $c) {
            $criterio = \App\Models\CriterioEvaluacion::firstOrCreate(['nombre' => $c['nombre']], ['peso' => $c['peso'], 'activo' => true]);
            
            \App\Models\CapacidadEvaluacion::firstOrCreate(
                ['nombre' => "Capacidad 1 de {$c['nombre']}"],
                ['criterio_id' => $criterio->id, 'puntuacion_maxima' => 10, 'activo' => true]
            );
            \App\Models\CapacidadEvaluacion::firstOrCreate(
                ['nombre' => "Capacidad 2 de {$c['nombre']}"],
                ['criterio_id' => $criterio->id, 'puntuacion_maxima' => 10, 'activo' => true]
            );
            \App\Models\CapacidadEvaluacion::firstOrCreate(
                ['nombre' => "Capacidad 3 de {$c['nombre']}"],
                ['criterio_id' => $criterio->id, 'puntuacion_maxima' => 10, 'activo' => true]
            );
        }
    }
}
