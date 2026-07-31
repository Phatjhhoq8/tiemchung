<?php

namespace Modules\VaccineRegistration\Policies;

use App\Models\User;
use Modules\VaccineRegistration\Models\Article;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Article $article): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Article $article): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->isSuperAdmin();
    }
}
