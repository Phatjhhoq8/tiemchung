@extends('vaccine::layouts.admin')

@section('title', 'Đổi mật khẩu quản trị')
@section('page_title', 'Đổi Mật Khẩu')

@section('admin_content')
<div class="card-modern" style="max-width:680px; margin:0 auto;">
    <p style="margin-top:0; color:#475569;">Để bảo vệ tài khoản, hãy đặt mật khẩu mới trước khi tiếp tục sử dụng hệ thống.</p>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:24px; padding:16px; border-radius:8px; background:#fde8e8; color:#9b1c1c; border:1px solid #fbd5d5;">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group-modern">
            <label class="form-label-modern" for="current_password">Mật khẩu hiện tại *</label>
            <input class="form-control-modern" type="password" id="current_password" name="current_password" required autocomplete="current-password" autofocus>
        </div>

        <div class="form-group-modern">
            <label class="form-label-modern" for="password">Mật khẩu mới *</label>
            <input class="form-control-modern" type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <small style="display:block; margin-top:8px; color:#64748b;">Ít nhất 8 ký tự; không được trùng mật khẩu hiện tại.</small>
        </div>

        <div class="form-group-modern">
            <label class="form-label-modern" for="password_confirmation">Xác nhận mật khẩu mới *</label>
            <input class="form-control-modern" type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:24px;">
            <button type="submit" class="btn-modern btn-modern-primary">Đổi mật khẩu</button>
        </div>
    </form>
</div>
@endsection
