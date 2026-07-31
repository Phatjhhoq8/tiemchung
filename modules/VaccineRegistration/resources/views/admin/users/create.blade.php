@extends('vaccine::layouts.admin')

@section('title', 'Tạo tài khoản chi nhánh')
@section('page_title', 'Tạo Tài Khoản Chi Nhánh')

@section('admin_content')
<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf
    @include('vaccine::admin.users._form')
    <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:24px;">
        <a href="{{ route('admin.users.index') }}" class="btn-modern btn-modern-secondary" style="text-decoration:none;">Hủy</a>
        <button type="submit" class="btn-modern btn-modern-primary">Tạo tài khoản</button>
    </div>
</form>
@endsection
