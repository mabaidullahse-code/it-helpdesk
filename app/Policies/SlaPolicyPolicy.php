<?php

namespace App\Policies;

use App\Models\SlaPolicy;
use App\Models\User;

class SlaPolicyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function view(User $user, SlaPolicy $slaPolicy): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, SlaPolicy $slaPolicy): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, SlaPolicy $slaPolicy): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
