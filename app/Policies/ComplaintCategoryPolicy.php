<?php

namespace App\Policies;

use App\Models\ComplaintCategory;
use App\Models\User;

class ComplaintCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function view(User $user, ComplaintCategory $category): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ComplaintCategory $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ComplaintCategory $category): bool
    {
        return $user->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
