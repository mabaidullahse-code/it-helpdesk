<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isItStaff() || $user->isDepartmentHod();
    }

    public function view(User $user, Asset $asset): bool
    {
        if ($user->isSuperAdmin() || $user->isItStaff()) {
            return true;
        }

        if ($user->isDepartmentHod()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
