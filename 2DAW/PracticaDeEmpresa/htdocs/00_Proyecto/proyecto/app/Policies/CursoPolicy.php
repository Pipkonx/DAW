<?php

namespace App\Policies;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CursoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTutorCurso() || $user->isTutorEmpresa();
    }

    public function view(User $user, Curso $curso): bool
    {
        return $user->isAdmin() || $user->isTutorCurso() || $user->isTutorEmpresa();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Curso $curso): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Curso $curso): bool
    {
        return $user->isAdmin();
    }
}
