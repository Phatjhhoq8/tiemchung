<?php

namespace Modules\VaccineRegistration\Policies;

use App\Models\User;
use Modules\VaccineRegistration\Models\Registration;

class RegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Registration $registration): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isBranchAdmin() && (int) $user->center_id === (int) $registration->center_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Registration $registration): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isBranchAdmin() && (int) $user->center_id === (int) $registration->center_id;
    }

    public function delete(User $user, Registration $registration): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isBranchAdmin() && (int) $user->center_id === (int) $registration->center_id;
    }
}
