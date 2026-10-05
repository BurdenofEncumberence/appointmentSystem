<?php

namespace App\Policies;

use App\Models\Court;
use App\Models\User;

class CourtPolicy
{
    /**
     * Determine whether the user can view courts.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can manage/create courts.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can update courts.
     */
    public function update(User $user, Court $court): bool
    {
        return $user->isAdmin() || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can delete courts.
     */
    public function delete(User $user, Court $court): bool
    {
        return $user->isAdmin() || $user->hasRole('manager');
    }
}
