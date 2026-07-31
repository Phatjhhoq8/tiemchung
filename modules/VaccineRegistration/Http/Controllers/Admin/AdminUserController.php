<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminUserController extends Controller
{
    public function index()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $users = User::with('center')->orderBy('role')->orderBy('name')->paginate(15);
        return view('vaccine::admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $user = new User(['role' => 'branch_admin', 'is_active' => true]);
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        return view('vaccine::admin.users.create', compact('user', 'centers'));
    }

    public function store(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $validated = $this->validateUser($request);
        $validated['password'] = $validated['password'];
        $validated['is_active'] = $request->boolean('is_active');
        if ($validated['role'] === 'super_admin') {
            $validated['center_id'] = null;
        }

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản chi nhánh thành công.');
    }

    public function edit(User $user)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        return view('vaccine::admin.users.edit', compact('user', 'centers'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $validated = $this->validateUser($request, $user);
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        $validated['is_active'] = $request->boolean('is_active');
        if ($validated['role'] === 'super_admin') {
            $validated['center_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(User $user)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        if ($user->id === AdminContext::user()?->id) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa tài khoản.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $id = $user?->id ?: 'NULL';

        return $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => ($user ? 'nullable' : 'required') . '|string|min:6',
            'role' => 'required|in:super_admin,branch_admin',
            'center_id' => 'required_if:role,branch_admin|nullable|exists:centers,id',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
