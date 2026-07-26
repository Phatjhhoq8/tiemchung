@extends('vaccine::layouts.admin')

@section('title', 'Thêm Vắc Xin Mới - Medicare Cờ Đỏ')
@section('page_title', 'Thêm Vắc Xin Vào Danh Mục')

@section('admin_content')
<div class="card-modern" style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
        <h2 style="font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Nhập thông tin vắc xin</h2>
        <a href="{{ route('admin.vaccines.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-family: var(--font-display);">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.vaccines.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @include('vaccine::admin.vaccines._form')

        <div style="border-top: 1px solid var(--border-color); padding-top: 24px; display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px;">
            <a href="{{ route('admin.vaccines.index') }}" class="btn-modern btn-modern-secondary">Hủy bỏ</a>
            <button type="submit" class="btn-modern btn-modern-primary">
                <i data-lucide="save"></i> Lưu vắc xin
            </button>
        </div>
    </form>
</div>
@endsection
