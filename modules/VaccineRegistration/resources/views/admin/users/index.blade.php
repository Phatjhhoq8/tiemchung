@extends('vaccine::layouts.admin')

@section('title', 'Quản lý tài khoản chi nhánh')
@section('page_title', 'Tài Khoản Chi Nhánh')

@section('admin_content')
<div class="card-modern">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <h2 style="font-family:var(--font-display); font-size:18px; font-weight:800; margin:0;">Danh sách tài khoản quản trị</h2>
        <a href="{{ route('admin.users.create') }}" class="btn-modern btn-modern-primary" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
            <i data-lucide="user-plus"></i> Tạo tài khoản
        </a>
    </div>

    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Tài khoản</th>
                    <th>Quyền</th>
                    <th>Chi nhánh</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center; width:180px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td style="font-weight:700;">{{ $user->name }}</td>
                        <td>
                            <div>{{ $user->username }}</div>
                            <small style="color:var(--text-muted);">{{ $user->email }}</small>
                        </td>
                        <td>
                            <span class="badge-modern {{ $user->role === 'super_admin' ? 'badge-modern-danger' : 'badge-modern-info' }}">
                                {{ $user->role === 'super_admin' ? 'Admin gốc' : 'Admin chi nhánh' }}
                            </span>
                        </td>
                        <td>{{ $user->center?->name ?? 'Toàn hệ thống' }}</td>
                        <td>
                            <span class="badge-modern {{ $user->is_active ? 'badge-modern-success' : 'badge-modern-danger' }}">
                                {{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:inline-flex; gap:8px;">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-action-sm"><i data-lucide="edit-3"></i> Sửa</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xóa tài khoản này?')" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-sm btn-action-danger"><i data-lucide="trash-2"></i> Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">{{ $users->links() }}</div>
</div>
@endsection
