<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isDepartmentHod();
    }

    public function view(User $user, Department $department): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->department_id === $department->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Department $department): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->isSuperAdmin();
    }
}
