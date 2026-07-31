<?php

namespace Modules\VaccineRegistration\Policies;

use App\Models\User;
use Modules\VaccineRegistration\Models\CenterVaccine;

class CenterVaccinePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(User $user, CenterVaccine $centerVaccine): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isBranchAdmin() && (int) $user->center_id === (int) $centerVaccine->center_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBranchAdmin();
    }

    public function update(User $user, CenterVaccine $centerVaccine): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isBranchAdmin() && (int) $user->center_id === (int) $centerVaccine->center_id;
    }

    public function delete(User $user, CenterVaccine $centerVaccine): bool
    {
        return $user->isSuperAdmin();
    }
}
