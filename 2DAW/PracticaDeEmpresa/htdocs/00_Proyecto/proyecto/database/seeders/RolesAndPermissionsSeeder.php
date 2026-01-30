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
            'gestionar_usuarios',
            'gestionar_empresas',
            'gestionar_cursos',
            'gestionar_alumnos',
            'ver_evaluaciones',
            'crear_evaluaciones',
            'ver_observaciones',
            'crear_observaciones',
            'ver_incidencias',
            'crear_incidencias',
            'resolver_incidencias',
            'gestionar_backups',
            'gestionar_capacidades',
            'ver_alumnos_empresa',
            'gestionar_practicas',
            'gestionar_mensajes',
            'gestionar_configuracion',
            'ver_estadisticas_globales',
            'exportar_datos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Eliminar permisos antiguos que ya no se usan
        Permission::whereNotIn('name', $permissions)->delete();

        // Crear Roles y asignar permisos
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $tutorCurso = Role::firstOrCreate(['name' => 'tutor_curso']);
        $tutorCurso->syncPermissions([
            'gestionar_alumnos',
            'gestionar_cursos',
            'gestionar_practicas',
            'ver_evaluaciones',
            'crear_evaluaciones',
            'ver_observaciones',
            'crear_observaciones',
            'ver_incidencias',
            'crear_incidencias',
            'resolver_incidencias',
            'gestionar_mensajes',
            'ver_estadisticas_globales',
        ]);

        $tutorPracticas = Role::firstOrCreate(['name' => 'tutor_practicas']);
        $tutorPracticas->syncPermissions([
            'ver_alumnos_empresa',
            'gestionar_practicas',
            'ver_evaluaciones',
            'crear_evaluaciones',
            'ver_observaciones',
            'crear_observaciones',
            'gestionar_capacidades',
            'gestionar_mensajes',
        ]);

        $empresa = Role::firstOrCreate(['name' => 'empresa']);
        $empresa->syncPermissions([
            'ver_alumnos_empresa',
            'gestionar_mensajes',
        ]);

        $alumno = Role::firstOrCreate(['name' => 'alumno']);
        $alumno->syncPermissions([
            'ver_evaluaciones',
            'ver_observaciones',
            'crear_observaciones',
            'ver_incidencias',
            'crear_incidencias',
            'gestionar_mensajes',
        ]);
    }
}
