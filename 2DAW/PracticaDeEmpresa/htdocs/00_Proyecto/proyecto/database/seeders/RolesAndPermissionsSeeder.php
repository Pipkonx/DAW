<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear Permisos
        $permissions = [
            'gestionar_todo',
            'ver_propias_observaciones',
            'crear_observaciones',
            'evaluar_alumnos',
            'ver_alumnos_empresa',
            'gestionar_alumnos',
            'gestionar_empresas',
            'gestionar_cursos',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear Roles y asignar permisos
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $tutorCurso = Role::create(['name' => 'tutor_curso']);
        $tutorCurso->givePermissionTo([
            'gestionar_alumnos',
            'ver_propias_observaciones',
            'evaluar_alumnos',
        ]);

        $tutorEmpresa = Role::create(['name' => 'tutor_empresa']);
        $tutorEmpresa->givePermissionTo([
            'ver_alumnos_empresa',
            'ver_propias_observaciones',
            'evaluar_alumnos',
        ]);

        $alumno = Role::create(['name' => 'alumno']);
        $alumno->givePermissionTo([
            'ver_propias_observaciones',
            'crear_observaciones',
        ]);

        // Crear Usuarios de Prueba
        $this->createUser('Admin User', 'admin@example.com', 'admin', 'admin');
        $this->createUser('Tutor Curso', 'tutor.curso@example.com', 'tutor_curso', 'password');
        $this->createUser('Tutor Empresa', 'tutor.empresa@example.com', 'tutor_empresa', 'password');
        $this->createUser('Alumno Prueba', 'alumno@example.com', 'alumno', 'password');
    }

    private function createUser($name, $email, $role, $password)
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $user->assignRole($role);
        return $user;
    }
}
