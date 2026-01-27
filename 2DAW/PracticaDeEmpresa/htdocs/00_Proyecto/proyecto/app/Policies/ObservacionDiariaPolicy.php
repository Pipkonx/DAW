<?php

namespace App\Policies;

use App\Models\ObservacionDiaria;
use App\Models\User;
use App\Models\Alumno;
use Illuminate\Auth\Access\Response;

class ObservacionDiariaPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // Controlado por Global Scope en el Resource
    }

    public function view(User $user, ObservacionDiaria $observacion): bool
    {
        if ($user->isTutorCurso()) {
            return true;
        }

        if ($user->isTutorPracticas()) {
            // Solo puede gestionar observaciones de sus alumnos asignados
            return $user->id === $observacion->alumno->tutorPracticas->user_id;
        }

        if ($user->isAlumno()) {
            return $observacion->alumno->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAlumno() || $user->isTutorCurso() || $user->isTutorPracticas();
    }

    public function update(User $user, ObservacionDiaria $observacion): bool
    {
        if ($user->isTutorCurso()) {
            return true;
        }

        if ($user->isTutorPracticas()) {
            return $user->id === $observacion->alumno->tutorPracticas->user_id;
        }

        if ($user->isAlumno()) {
            return $observacion->alumno->user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, ObservacionDiaria $observacion): bool
    {
        return false; // Solo Admin
    }
}
