<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class PermissionMatrix extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.permission-matrix';

    protected static ?string $title = 'Matriz de Permisos (Estilo Discord)';

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

    public function togglePermission($roleId, $permissionId): void
    {
        $role = Role::find($roleId);
        $permission = Permission::find($permissionId);

        if (!$role || !$permission) return;

        if ($role->hasPermissionTo($permission->name)) {
            $role->revokePermissionTo($permission->name);
            $this->matrix[$roleId][$permissionId] = false;
        } else {
            $role->givePermissionTo($permission->name);
            $this->matrix[$roleId][$permissionId] = true;
        }

        Notification::make()
            ->success()
            ->title('Permiso actualizado')
            ->body("Se ha actualizado el permiso '{$permission->name}' para el rol '{$role->name}'.")
            ->send();
    }
}
