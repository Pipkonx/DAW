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
     * @brief Crea Admin, Tutores y Alumnos manteniendo la integridad referencial.
     */
    protected function crearUsuariosYPerfiles(): void
    {
        // 1. Admin
        $admin = User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@ejemplo.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // 2. Tutores de Curso
        $cursos = Curso::all();
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'name' => "Tutor Curso $i",
                'email' => "tutorcurso$i@ejemplo.com",
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('tutor_curso');
            
            TutorCurso::create([
                'user_id' => $user->id,
                'nombre' => "Tutor",
                'apellidos' => "Curso $i",
                'departamento' => 'Informática',
            ]);
        }

        // 3. Tutores de Empresa (Prácticas)
        $empresas = Empresa::all();
        foreach ($empresas->take(2) as $index => $empresa) {
            $user = User::create([
                'name' => "Tutor Empresa $index",
                'email' => "tutorempresa$index@ejemplo.com",
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('tutor_empresa');
            
            TutorPracticas::create([
                'user_id' => $user->id,
                'empresa_id' => $empresa->id,
                'nombre' => "Tutor",
                'apellidos' => "Empresa $index",
                'puesto' => 'Senior Developer',
            ]);
        }

        // 4. Alumnos con relaciones completas
        $tutoresEmpresa = TutorPracticas::all();
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Alumno Prueba $i",
                'email' => "alumno$i@ejemplo.com",
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('alumno');

            $empresa = $empresas->random();
            $tutorEmpresa = $tutoresEmpresa->where('empresa_id', $empresa->id)->first();

            Alumno::create([
                'user_id' => $user->id,
                'curso_id' => $cursos->random()->id,
                'empresa_id' => $empresa->id,
                'tutor_curso_id' => TutorCurso::all()->random()->id,
                'tutor_practicas_id' => $tutorEmpresa ? $tutorEmpresa->id : null,
                'nombre' => "Alumno",
                'apellidos' => "Prueba $i",
                'dni' => "1234567{$i}Z",
                'telefono' => "60000000$i",
                'fecha_nacimiento' => '2000-01-01',
            ]);
        }
    }
}
