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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear Roles y asignar permisos
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $tutorCurso = Role::firstOrCreate(['name' => 'tutor_curso']);
        $tutorCurso->syncPermissions([
            'gestionar_alumnos',
            'ver_propias_observaciones',
            'evaluar_alumnos',
        ]);

        $tutorPracticas = Role::firstOrCreate(['name' => 'tutor_practicas']);
        $tutorPracticas->syncPermissions([
            'ver_alumnos_empresa',
            'ver_propias_observaciones',
            'evaluar_alumnos',
        ]);

        $empresa = Role::firstOrCreate(['name' => 'empresa']);
        $empresa->syncPermissions([
            'ver_alumnos_empresa',
        ]);

        $alumno = Role::firstOrCreate(['name' => 'alumno']);
        $alumno->syncPermissions([
            'ver_propias_observaciones',
            'crear_observaciones',
        ]);
    }
}
