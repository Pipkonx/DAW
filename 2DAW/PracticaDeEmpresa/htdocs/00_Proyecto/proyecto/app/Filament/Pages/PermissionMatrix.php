<?php

namespace App\Filament\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class PermissionMatrix extends Page
{
    protected static string $view = 'filament.resources.user-resource.pages.permission-matrix';

    protected static ?string $title = 'Matriz de Permisos';
    
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Permisos';

    protected static ?int $navigationSort = -100;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public Collection $roles;
    public Collection $permissions;
    public array $matrix = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        // Forzamos la creación de permisos base si no existen
        $this->createDefaultPermissions();

        // Cargamos roles (excluyendo admin para seguridad)
        $this->roles = Role::where('name', '!=', 'admin')->get();
        
        // Obtenemos todos los permisos
        $this->permissions = Permission::all();

        foreach ($this->roles as $role) {
            foreach ($this->permissions as $permission) {
                $this->matrix[$role->id][$permission->id] = $role->hasPermissionTo($permission->name);
            }
        }
    }

    protected function createDefaultPermissions(): void
    {
        $basePermissions = [
            'gestionar_todo' => 'Gestionar Todo (Acceso Total)',
            'gestionar_usuarios' => 'Gestionar Usuarios',
            'gestionar_empresas' => 'Gestionar Empresas',
            'gestionar_cursos' => 'Gestionar Cursos',
            'gestionar_alumnos' => 'Gestionar Alumnos',
            'ver_evaluaciones' => 'Ver Evaluaciones',
            'crear_evaluaciones' => 'Crear Evaluaciones',
            'ver_observaciones' => 'Ver Observaciones',
            'crear_observaciones' => 'Crear Observaciones',
            'ver_incidencias' => 'Ver Incidencias',
            'crear_incidencias' => 'Crear Incidencias',
            'resolver_incidencias' => 'Resolver Incidencias',
            'gestionar_backups' => 'Gestionar Backups',
            'gestionar_capacidades' => 'Gestionar Capacidades/Criterios',
            'ver_alumnos_empresa' => 'Ver Alumnos de Empresa',
        ];

        foreach ($basePermissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Limpiar permisos antiguos que ya no están en la lista base
        Permission::whereNotIn('name', array_keys($basePermissions))->delete();
    }

    public function togglePermission(int $roleId, int $permissionId): void
    {
        // Esta función ya no es necesaria con @entangle
    }

    public function saveChanges(): void
    {
        foreach ($this->matrix as $roleId => $permissions) {
            $role = Role::find($roleId);
            if (!$role) continue;

            $permissionNames = [];
            foreach ($permissions as $permissionId => $value) {
                if ($value) {
                    $permission = Permission::find($permissionId);
                    if ($permission) {
                        $permissionNames[] = $permission->name;
                    }
                }
            }
            
            // Sincronizamos todos los permisos del rol de una vez
            $role->syncPermissions($permissionNames);
        }

        Notification::make()
            ->success()
            ->title('Cambios guardados')
            ->body('Se han actualizado todos los permisos correctamente.')
            ->send();
    }
}
