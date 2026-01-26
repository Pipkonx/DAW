<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * @class UserPolicy
 * @brief Política de seguridad para la gestión de usuarios.
 * 
 * Controla el acceso a las operaciones CRUD del modelo User.
 */
class UserPolicy
{
    /**
     * @brief Determina si el usuario puede ver el listado de usuarios.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @return bool Verdadero si tiene permiso, falso de lo contrario.
     */
    public function verCualquiera(User $usuario): bool
    {
        return $usuario->isAdmin();
    }

    /**
     * @brief Determina si el usuario puede ver un usuario específico.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @param User $modelo Instancia del usuario que se desea consultar.
     * @return bool Verdadero si tiene permiso, falso de lo contrario.
     */
    public function ver(User $usuario, User $modelo): bool
    {
        return $usuario->isAdmin();
    }

    /**
     * @brief Determina si el usuario puede crear nuevos usuarios.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @return bool Verdadero si tiene permiso, falso de lo contrario.
     */
    public function crear(User $usuario): bool
    {
        return $usuario->isAdmin();
    }

    /**
     * @brief Determina si el usuario puede actualizar un usuario.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @param User $modelo Instancia del usuario que se desea editar.
     * @return bool Verdadero si tiene permiso, falso de lo contrario.
     */
    public function actualizar(User $usuario, User $modelo): bool
    {
        return $usuario->isAdmin();
    }

    /**
     * @brief Determina si el usuario puede eliminar un usuario.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @param User $modelo Instancia del usuario que se desea borrar.
     * @return bool Verdadero si tiene permiso, falso de lo contrario.
     */
    public function eliminar(User $usuario, User $modelo): bool
    {
        return $usuario->isAdmin();
    }
}
