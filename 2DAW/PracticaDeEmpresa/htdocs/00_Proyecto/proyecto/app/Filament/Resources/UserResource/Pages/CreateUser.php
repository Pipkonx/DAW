<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Alumno;
use App\Models\TutorCurso;
use App\Models\TutorPracticas;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Filament\Notifications\Notification;

/**
 * @class CreateUser
 * @brief Página para la creación de usuarios y sus perfiles relacionados.
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @brief Obtiene la notificación de éxito al crear un usuario.
     * 
     * @return Notification|null Objeto de notificación configurado.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario creado')
            ->body("El usuario {$this->record->name} ha sido creado correctamente.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Obtiene la URL de redirección tras crear un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @brief Sobrescribe el proceso de creación para gestionar perfiles y reference_id.
     * 
     * @param array $data Datos del formulario.
     * @return Model Instancia del usuario creado.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $rol = $data['rol'] ?? null;
            $datosPerfil = $data['datosPerfil'] ?? [];

            // 1. Crear el usuario primero
            $usuario = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'avatar_url' => $data['avatar_url'] ?? null,
            ]);

            // 2. Asignar el rol
            if ($rol) {
                $usuario->assignRole($rol);
            }

            // 3. Crear el perfil específico si no es admin
            if ($rol && $rol !== 'admin') {
                // Aseguramos que el user_id esté en los datos del perfil
                $datosPerfil['user_id'] = $usuario->id;

                $perfil = match ($rol) {
                    'alumno' => \App\Models\Alumno::create($datosPerfil),
                    'tutor_curso' => \App\Models\TutorCurso::create($datosPerfil),
                    'tutor_practicas' => \App\Models\TutorPracticas::create($datosPerfil),
                    'empresa' => \App\Models\Empresa::create($datosPerfil),
                    default => null,
                };

                if ($perfil) {
                    // 4. Actualizar el usuario con el reference_id (ID del perfil)
                    $usuario->update(['reference_id' => $perfil->id]);
                }
            }

            return $usuario;
        });
    }
}
