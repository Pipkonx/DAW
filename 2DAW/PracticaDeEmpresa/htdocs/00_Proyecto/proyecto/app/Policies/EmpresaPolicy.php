<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmpresaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTutorCurso() || $user->isTutorPracticas();
    }

    public function view(User $user, Empresa $empresa): bool
    {
        return $user->isAdmin() || $user->isTutorCurso() || ($user->isTutorPracticas() && $user->id === $empresa->tutoresPracticas()->where('user_id', $user->id)->first()?->user_id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Empresa $empresa): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Empresa $empresa): bool
    {
        return $user->isAdmin();
    }
}
