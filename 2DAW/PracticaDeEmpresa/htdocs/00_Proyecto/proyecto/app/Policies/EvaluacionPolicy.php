<?php

namespace App\Policies;

use App\Models\Evaluacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EvaluacionPolicy
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
        return $user->isTutorCurso() || $user->isTutorPracticas() || $user->isAlumno();
    }

    public function view(User $user, Evaluacion $evaluacion): bool
    {
        if ($user->isTutorCurso()) {
            return true;
        }

        if ($user->isTutorPracticas()) {
            // El tutor solo ve evaluaciones de alumnos donde él es el tutor de prácticas
            return $user->id === $evaluacion->alumno->tutorPracticas->user_id;
        }

        if ($user->isAlumno()) {
            // El alumno solo ve sus propias evaluaciones
            return $evaluacion->alumno->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTutorPracticas() || $user->isTutorCurso();
    }

    public function update(User $user, Evaluacion $evaluacion): bool
    {
        if ($user->isTutorCurso()) {
            return true;
        }

        if ($user->isTutorPracticas()) {
            return $user->id === $evaluacion->alumno->tutorPracticas->user_id;
        }

        return false;
    }

    public function delete(User $user, Evaluacion $evaluacion): bool
    {
        return false; // Solo Admin
    }
}
