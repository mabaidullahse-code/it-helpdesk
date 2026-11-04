<?php

namespace App\Policies;

use App\Models\KnowledgeArticle;
use App\Models\User;

class KnowledgeArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, KnowledgeArticle $article): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isItStaff();
    }

    public function update(User $user, KnowledgeArticle $article): bool
    {
        return $user->isSuperAdmin() || $user->isItStaff();
    }

    public function delete(User $user, KnowledgeArticle $article): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isItHod();
    }
}
