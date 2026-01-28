<?php

namespace App\Policies;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * @class AlumnoPolicy
 * @brief Política de seguridad para la gestión de alumnos.
 * 
 * Implementa las reglas de negocio para el acceso a datos de alumnos.
 */
class AlumnoPolicy
{
    /**
     * @brief Determina si el usuario puede ver el listado de alumnos.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @return bool Verdadero si tiene acceso, falso de lo contrario.
     */
    public function verCualquiera(User $usuario): bool
    {
        return $usuario->isAdmin() || $usuario->isTutorCurso() || $usuario->isTutorPracticas() || $usuario->isAlumno();
    }

    /**
     * @brief Determina si el usuario puede ver un alumno específico.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @param Alumno $alumno Instancia del alumno que se desea consultar.
     * @return bool Verdadero si el acceso está permitido.
     */
    public function ver(User $usuario, Alumno $alumno): bool
    {
        if ($usuario->isAdmin() || $usuario->isTutorCurso() || $usuario->isTutorPracticas()) {
            return true;
        }

        if ($usuario->isAlumno()) {
            return $usuario->id === $alumno->user_id;
        }

        return false;
    }

    /**
     * @brief Determina si el usuario puede registrar nuevos alumnos.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @return bool Verdadero para administradores, tutores de curso y tutores de prácticas.
     */
    public function crear(User $usuario): bool
    {
        return $usuario->isAdmin() || $usuario->isTutorCurso() || $usuario->isTutorPracticas();
    }

    /**
     * @brief Determina si el usuario puede editar la ficha de un alumno.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @param Alumno $alumno Instancia del alumno a modificar.
     * @return bool Verdadero según las restricciones de rol.
     */
    public function actualizar(User $usuario, Alumno $alumno): bool
    {
        return $usuario->isAdmin() || $usuario->isTutorCurso() || $usuario->isTutorPracticas();
    }

    /**
     * @brief Determina si el usuario puede dar de baja a un alumno.
     * 
     * @param User $usuario Instancia del usuario que realiza la acción.
     * @param Alumno $alumno Instancia del alumno a eliminar.
     * @return bool Permitido para administradores y tutores de prácticas.
     */
    public function eliminar(User $usuario, Alumno $alumno): bool
    {
        return $usuario->isAdmin() || $usuario->isTutorPracticas();
    }
}
