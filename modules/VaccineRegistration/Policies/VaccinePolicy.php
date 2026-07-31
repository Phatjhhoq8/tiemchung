<?php

namespace Modules\VaccineRegistration\Policies;

use App\Models\User;
use Modules\VaccineRegistration\Models\Vaccine;

class VaccinePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Vaccine $vaccine): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function updateMasterCatalog(User $user, Vaccine $vaccine): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Vaccine $vaccine): bool
    {
        return $user->isSuperAdmin() || $user->isBranchAdmin();
    }

    public function delete(User $user, Vaccine $vaccine): bool
    {
        return $user->isSuperAdmin();
    }
}
