<?php

namespace Modules\VaccineRegistration\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Center;

class AdminContext
{
    public const SELECTED_CENTER_SESSION_KEY = 'admin_selected_center_id';
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

        if ($requestedCenterId) {
            return (int) Center::active()->findOrFail($requestedCenterId)->id;
        }

        $selectedCenterId = session(self::SELECTED_CENTER_SESSION_KEY);
        if (!$selectedCenterId) {
            return null;
        }

        $centerId = Center::active()->whereKey($selectedCenterId)->value('id');
        if (!$centerId) {
            session()->forget(self::SELECTED_CENTER_SESSION_KEY);
        }

        return $centerId ? (int) $centerId : null;
    }

    public static function resolveListCenterId(Request $request): ?int
    {
        if (self::isBranchAdmin()) {
            if ($request->has('center_id') && $request->input('center_id') !== null && $request->input('center_id') !== ''
                && (int) $request->input('center_id') !== (int) self::centerId()) {
                abort(403, 'Bạn không có quyền truy cập dữ liệu của chi nhánh khác.');
            }

            return self::centerId();
        }

        if (!$request->has('center_id')) {
            return self::selectedCenterId();
        }

        $centerId = $request->input('center_id');
        $center = self::setSelectedCenter($centerId === null || $centerId === '' ? null : (int) $centerId);

        return $center?->id;
    }

    public static function setSelectedCenter(?int $centerId): ?Center
    {
        if (self::isBranchAdmin()) {
            abort_unless($centerId !== null && $centerId === self::centerId(), 403, 'Bạn không có quyền đổi chi nhánh quản trị.');
        }

        if ($centerId === null) {
            session()->forget(self::SELECTED_CENTER_SESSION_KEY);

            return null;
        }

        $center = Center::active()->findOrFail($centerId);
        session([self::SELECTED_CENTER_SESSION_KEY => $center->id]);

        return $center;
    }

    public static function canManageCenter(int $centerId): bool
    {
        return self::isSuperAdmin()
            || (self::isBranchAdmin() && (int) self::centerId() === $centerId);
    }

    public static function assertCanManageCenter(int $centerId): void
    {
        abort_unless(self::canManageCenter($centerId), 403, 'Bạn không có quyền quản lý chi nhánh này.');
    }

    public static function applyCenterScope(Builder $query, string $column = 'center_id'): Builder
    {
        if (self::isBranchAdmin()) {
            if (!self::centerId()) {
                return $query->whereRaw('1 = 0');
            }

            $query->where($column, self::centerId());
        }

        return $query;
    }
}
