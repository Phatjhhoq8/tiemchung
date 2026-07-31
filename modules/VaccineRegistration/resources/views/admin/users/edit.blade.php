@extends('vaccine::layouts.admin')

@section('title', 'Sửa tài khoản')
@section('page_title', 'Sửa Tài Khoản')

@section('admin_content')
<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')
    @include('vaccine::admin.users._form')
    <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:24px;">
        <a href="{{ route('admin.users.index') }}" class="btn-modern btn-modern-secondary" style="text-decoration:none;">Hủy</a>
        <button type="submit" class="btn-modern btn-modern-primary">Lưu thay đổi</button>
    </div>
</form>
@endsection
