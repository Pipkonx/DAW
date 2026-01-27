<?php

namespace App\Policies;

use App\Models\User;

class BackupPolicy
{
    /**
     * Determine whether the user can view the backup page.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create a backup.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can download a backup.
     */
    public function download(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a backup.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
