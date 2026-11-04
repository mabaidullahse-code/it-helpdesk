<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     * Only Super Admin can access the complete organization user directory.
     * Department HODs can view only users in their own department.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHod();
    }

    public function view(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentHod()) {
            if ($user->id === $model->id) {
                return true;
            }

            return $model->department_id === $user->department_id 
                && $model->role !== 'super_admin';
        }

        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHod();
    }

    public function update(User $user, User $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentHod()) {
            if ($user->id === $model->id) {
                return true;
            }

            return $model->department_id === $user->department_id 
                && $model->role !== 'super_admin';
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }
}
