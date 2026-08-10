<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\AdminPasswordPolicy;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền quản lý tài khoản quản trị.');

        $query = User::with('center');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('username', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15)->withQueryString();

        return view('vaccine::admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo tài khoản quản trị.');

        $user = new User(['role' => 'branch_admin', 'is_active' => true]);
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();

        return view('vaccine::admin.users.create', compact('user', 'centers'));
    }

    public function store(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo tài khoản quản trị.');

        $validated = $this->validateUser($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['must_change_password'] = true;
        $validated['password_changed_at'] = null;
        if ($validated['role'] === 'super_admin') {
            $validated['center_id'] = null;
        }

        $user = User::create($validated);
        AuditLogger::log(
            'admin_user.created',
            'admin_user',
            $user->id,
            newValues: $user->only(['name', 'username', 'email', 'role', 'center_id', 'is_active', 'status']),
            centerId: $user->center_id,
            resolveCenter: false
        );

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản chi nhánh thành công.');
    }

    public function edit(User $user)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa tài khoản quản trị.');

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();

        return view('vaccine::admin.users.edit', compact('user', 'centers'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa tài khoản quản trị.');

        $validated = $this->validateUser($request, $user);
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['must_change_password'] = true;
            $validated['password_changed_at'] = null;
        }
        $validated['is_active'] = $request->boolean('is_active');
        if ($validated['role'] === 'super_admin') {
            $validated['center_id'] = null;
        }

        $oldValues = $user->only(['name', 'username', 'email', 'role', 'center_id', 'is_active', 'status']);
        $user->update($validated);
        AuditLogger::log(
            'admin_user.updated',
            'admin_user',
            $user->id,
            $oldValues,
            $user->fresh()->only(array_keys($oldValues)),
            $user->center_id,
            resolveCenter: false
        );

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(User $user)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền vô hiệu hóa tài khoản quản trị.');

        if ($user->id === AdminContext::user()?->id) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        $oldValues = $user->only(['is_active', 'status']);
        $user->is_active = false;
        $user->status = 'inactive';
        $user->save();
        AuditLogger::log(
            'admin_user.deactivated',
            'admin_user',
            $user->id,
            $oldValues,
            $user->only(['is_active', 'status']),
            $user->center_id,
            resolveCenter: false
        );

        return redirect()->route('admin.users.index')->with('success', 'Đã vô hiệu hóa tài khoản.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $id = $user?->id ?: 'NULL';

        $passwordRules = [$user ? 'nullable' : 'required', 'string', AdminPasswordPolicy::rule()];

        return $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$id,
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'password' => $passwordRules,
            'role' => 'required|in:super_admin,branch_admin',
            'center_id' => 'required_if:role,branch_admin|nullable|exists:centers,id',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
