@if ($errors->any())
    <div class="alert alert-danger" style="margin-bottom:24px; padding:16px; border-radius:8px; background:#fde8e8; color:#9b1c1c; border:1px solid #fbd5d5;">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card-modern">
    <div class="form-grid-2">
        <div class="form-group-modern" style="margin-bottom:0;">
            <label class="form-label-modern" for="name">Tên hiển thị *</label>
            <input class="form-control-modern" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="form-group-modern" style="margin-bottom:0;">
            <label class="form-label-modern" for="username">Tên đăng nhập *</label>
            <input class="form-control-modern" id="username" name="username" value="{{ old('username', $user->username) }}" required>
        </div>
        <div class="form-group-modern" style="margin-bottom:0;">
            <label class="form-label-modern" for="email">Email *</label>
            <input class="form-control-modern" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="form-group-modern" style="margin-bottom:0;">
            <label class="form-label-modern" for="password">Mật khẩu {{ $user->exists ? '(để trống nếu không đổi)' : '*' }}</label>
            <input class="form-control-modern" type="password" id="password" name="password" {{ $user->exists ? '' : 'required' }} minlength="12" autocomplete="new-password">
            <small style="display:block; margin-top:8px; color:#64748b;">Ít nhất 12 ký tự, gồm chữ hoa, chữ thường, số và ký hiệu. Người dùng sẽ phải đổi mật khẩu tạm này khi đăng nhập.</small>
        </div>
        <div class="form-group-modern" style="margin-bottom:0;">
            <label class="form-label-modern" for="role">Quyền *</label>
            <select class="form-control-modern" id="role" name="role" required onchange="toggleCenterSelect()">
                <option value="branch_admin" {{ old('role', $user->role) === 'branch_admin' ? 'selected' : '' }}>Quản trị viên chi nhánh</option>
                <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Quản trị viên cấp cao</option>
            </select>
        </div>
        <div class="form-group-modern" style="margin-bottom:0;" id="centerField">
            <label class="form-label-modern" for="center_id">Chi nhánh *</label>
            <select class="form-control-modern" id="center_id" name="center_id">
                <option value="">-- Chọn chi nhánh --</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) old('center_id', $user->center_id) === (string) $center->id ? 'selected' : '' }}>{{ $center->name }} - {{ $center->phone }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-modern" style="grid-column:span 2; margin-bottom:0;">
            <label style="display:inline-flex; align-items:center; gap:10px; font-weight:700; color:#475569;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--primary-color);">
                Tài khoản đang hoạt động
            </label>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function toggleCenterSelect() {
        const role = document.getElementById('role')?.value;
        const field = document.getElementById('centerField');
        const center = document.getElementById('center_id');
        if (!field || !center) return;
        field.style.display = role === 'branch_admin' ? 'block' : 'none';
        center.required = role === 'branch_admin';
    }
    document.addEventListener('DOMContentLoaded', toggleCenterSelect);
</script>
@endsection
