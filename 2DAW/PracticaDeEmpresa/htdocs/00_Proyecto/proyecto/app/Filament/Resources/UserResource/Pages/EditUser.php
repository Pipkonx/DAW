<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Alumno;
use App\Models\TutorCurso;
use App\Models\TutorPracticas;
use Filament\Notifications\Notification;

/**
 * @class EditUser
 * @brief Página para la edición de registros de Usuarios.
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario actualizado')
            ->body("Los datos del usuario {$this->record->name} han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Sobrescribe el proceso de actualización para gestionar roles y perfiles.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
            $rol = $data['rol'] ?? null;
            $datosPerfil = $data['datosPerfil'] ?? [];

            // 1. Actualizar datos básicos del usuario
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'avatar_url' => $data['avatar_url'] ?? $record->avatar_url,
            ];

            if (!empty($data['password'])) {
                $userData['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            }

            $record->update($userData);

            // 2. Sincronizar el rol
            if ($rol) {
                $record->syncRoles([$rol]);
            }

            // 3. Gestionar el perfil relacionado
            if ($rol && $rol !== 'admin') {
                $this->actualizarPerfilRelacionado($record, $rol, $datosPerfil);
            }

            return $record;
        });
    }

    /**
     * @brief Lógica ejecutada después de guardar el usuario.
     */
    protected function afterSave(): void
    {
        // La lógica se ha movido a handleRecordUpdate para mayor consistencia
    }

    /**
     * @brief Define las acciones de la cabecera en la página de edición.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @brief Obtiene la URL de redirección tras editar un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @brief Prepara los datos del formulario antes de cargarlos en la página de edición.
     * 
     * @param array $data Datos del registro.
     * @return array Datos formateados para el formulario.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $usuario = $this->record;
        $rol = $usuario->getRoleNames()->first();
        $data['rol'] = $rol;

        // Intentar cargar el perfil usando la relación directa (más fiable que reference_id)
        $perfil = match ($rol) {
            'alumno' => $usuario->alumno,
            'tutor_curso' => $usuario->perfilTutorCurso,
            'tutor_practicas' => $usuario->perfilTutorPracticas,
            'empresa' => $usuario->empresa,
            default => null,
        };

        // Fallback a reference_id si la relación no devolvió nada
        if (!$perfil && $recordId = $this->record->reference_id) {
            $perfil = match ($rol) {
                'alumno' => \App\Models\Alumno::find($recordId),
                'tutor_curso' => \App\Models\TutorCurso::find($recordId),
                'tutor_practicas' => \App\Models\TutorPracticas::find($recordId),
                'empresa' => \App\Models\Empresa::find($recordId),
                default => null,
            };
        }

        if ($perfil) {
            $data['datosPerfil'] = $perfil->toArray();
        }

        return $data;
    }

    /**
     * @brief Actualiza o crea el perfil relacionado según el rol.
     */
    private function actualizarPerfilRelacionado($usuario, $rol, $datosPerfil): void
    {
        // Buscamos primero por reference_id, si no existe, por user_id
        $search = $usuario->reference_id ? ['id' => $usuario->reference_id] : ['user_id' => $usuario->id];
        
        // Aseguramos que el user_id esté presente en los datos del perfil
        $datosPerfil['user_id'] = $usuario->id;

        $perfil = match ($rol) {
            'alumno' => Alumno::updateOrCreate($search, $datosPerfil),
            'tutor_curso' => TutorCurso::updateOrCreate($search, $datosPerfil),
            'tutor_practicas' => TutorPracticas::updateOrCreate($search, $datosPerfil),
            'empresa' => \App\Models\Empresa::updateOrCreate($search, $datosPerfil),
            default => null,
        };

        if ($perfil && $usuario->reference_id !== $perfil->id) {
            $usuario->update(['reference_id' => $perfil->id]);
        }
    }
}