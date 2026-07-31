<?php

namespace Modules\VaccineRegistration\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\VaccineRegistration\Models\Center;

class AdminContext
{
    public static function user(): ?User
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return \Illuminate\Support\Facades\Auth::user();
        }

        $userId = session('admin_user_id');
        if ($userId) {
            $user = User::with('center')->find($userId);
            if ($user) {
                \Illuminate\Support\Facades\Auth::setUser($user);
            }
            return $user;
        }

        if (session('admin_logged_in') === true) {
            $user = User::where('role', 'super_admin')->where('is_active', true)->first();
            if ($user) {
                \Illuminate\Support\Facades\Auth::setUser($user);
            }
            return $user;
        }

        return null;
    }

    public static function isSuperAdmin(): bool
    {
        return self::user()?->isSuperAdmin() === true;
    }

    public static function isBranchAdmin(): bool
    {
        return self::user()?->isBranchAdmin() === true;
    }

    public static function centerId(): ?int
    {
        $user = self::user();
        return $user?->isBranchAdmin() ? $user->center_id : null;
    }

    public static function selectedCenterId(?int $requestedCenterId = null): ?int
    {
        if (self::isBranchAdmin()) {
            return self::centerId();
        }

        return $requestedCenterId ?: Center::active()->orderBy('sort_order')->orderBy('id')->value('id');
    }

    public static function applyCenterScope(Builder $query, string $column = 'center_id'): Builder
    {
        if (self::isBranchAdmin() && self::centerId()) {
            $query->where($column, self::centerId());
        }

        return $query;
    }
}
