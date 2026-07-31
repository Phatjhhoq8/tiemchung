<?php

namespace Modules\VaccineRegistration\Policies;

use App\Models\User;
use Modules\VaccineRegistration\Models\Center;

class CenterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Center $center): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isBranchAdmin() && (int) $user->center_id === (int) $center->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Center $center): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Center $center): bool
    {
        return $user->isSuperAdmin();
    }
}
