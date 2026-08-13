@extends('vaccine::layouts.admin')
@php
    $isSuperAdmin = $isSuperAdminAllCenters ?? $isSuperAdmin;
@endphp

@section('title', 'Chỉnh Sửa Vắc Xin - Medicare')
@section('page_title', 'Chỉnh Sửa Thông Tin Vắc Xin')

@section('admin_content')
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card-modern" style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; padding: 20px 24px;">
        <div>
            <h2 style="font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0;">Chỉnh sửa thông tin vắc xin</h2>
            <p style="margin: 4px 0 0 0; font-size: 13px; color: var(--text-muted);">Cập nhật chi tiết danh mục, giá cả và tồn kho chi nhánh.</p>
        </div>
        <a href="{{ route('admin.vaccines.index') }}" class="btn-modern btn-modern-secondary" style="font-size: 13.5px;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.vaccines.update', $vaccine->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        @include('vaccine::admin.vaccines._form')

        <div class="card-modern" style="margin-top: 24px; padding: 20px 24px; display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('admin.vaccines.index') }}" class="btn-modern btn-modern-secondary">Hủy bỏ</a>
            <button type="submit" class="btn-modern btn-modern-primary">
                <i data-lucide="save"></i> Cập nhật vắc xin
            </button>
        </div>
    </form>
</div>
@endsection
