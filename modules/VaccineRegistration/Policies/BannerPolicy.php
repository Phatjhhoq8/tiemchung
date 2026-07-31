<?php

namespace Modules\VaccineRegistration\Policies;

use App\Models\User;
use Modules\VaccineRegistration\Models\Banner;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->isSuperAdmin();
    }
}
