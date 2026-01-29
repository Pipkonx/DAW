<?php

namespace App\Policies;

use App\Models\Practice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PracticePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtrado por getEloquentQuery en el recurso
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Practice $practice): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Si es el creador
        if ($practice->user_id === $user->id) {
            return true;
        }

        // Si es alumno y está asignado a él, su grupo o su rol
        if ($user->isAlumno()) {
            return $practice->alumno_id === $user->alumno?->id ||
                   $practice->curso_id === $user->alumno?->curso_id ||
                   $practice->target_role === 'alumno';
        }

        // Si es tutor de empresa y está asignado a su rol o a uno de sus alumnos
        if ($user->isTutorPracticas()) {
            if ($practice->target_role === 'tutor_practicas') {
                return true;
            }
            
            if ($practice->alumno_id) {
                return $practice->alumno->tutor_practicas_id === $user->perfilTutorPracticas?->id;
            }
        }

        // Si es tutor de curso y está asignado a su rol
        if ($user->isTutorCurso()) {
            return $practice->target_role === 'tutor_curso';
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return !$user->isAlumno();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Practice $practice): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Solo el creador puede actualizar
        return $practice->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Practice $practice): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Alumnos y Tutores de Empresa no pueden eliminar nunca
        if ($user->isAlumno() || $user->isTutorPracticas()) {
            return false;
        }

        // Otros (como Tutor de Curso) solo pueden eliminar lo que ellos crearon
        return $practice->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Practice $practice): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Practice $practice): bool
    {
        return $user->isAdmin();
    }
}
