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
        // Cargamos roles (excluyendo admin para seguridad, o incluyéndolo si prefieres)
        $this->roles = Role::where('name', '!=', 'admin')->get();
        
        // Obtenemos todos los permisos actuales o definimos una lista base
        $this->permissions = Permission::all();

        // Si no hay permisos en la DB, podríamos querer crearlos o mostrar vacíos
        if ($this->permissions->isEmpty()) {
            $this->createDefaultPermissions();
            $this->permissions = Permission::all();
        }

        foreach ($this->roles as $role) {
            foreach ($this->permissions as $permission) {
                $this->matrix[$role->id][$permission->id] = $role->hasPermissionTo($permission->name);
            }
        }
    }

    protected function createDefaultPermissions(): void
    {
        $basePermissions = [
            'view_incidencias' => 'Ver Incidencias',
            'create_incidencias' => 'Crear Incidencias',
            'resolve_incidencias' => 'Resolver Incidencias',
            'view_practices' => 'Ver Prácticas',
            'create_practices' => 'Crear Prácticas',
            'view_evaluations' => 'Ver Evaluaciones',
            'create_evaluations' => 'Crear Evaluaciones',
            'manage_users' => 'Gestionar Usuarios',
        ];

        foreach ($basePermissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
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
