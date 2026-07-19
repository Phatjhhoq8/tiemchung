@extends('vaccine::layouts.admin')

@section('title', 'Thêm Vắc Xin Mới - Medicare Cờ Đỏ')
@section('page_title', 'Thêm Vắc Xin Vào Danh Mục')

@section('admin_content')
<div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 40px; max-width: 800px; margin: 0 auto;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
        <h2 style="font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">Nhập thông tin vắc xin</h2>
        <a href="{{ route('admin.vaccines.index') }}" style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Quay lại danh sách
        </a>
    </div>

    <form action="{{ route('admin.vaccines.store') }}" method="POST">
        @csrf
        
        @include('vaccine::admin.vaccines._form')

        <div style="border-top: 1px solid #e2e8f0; padding-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('admin.vaccines.index') }}" class="btn-secondary" style="padding: 12px 24px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; text-decoration: none; color: #475569; font-weight: 600;">Hủy bỏ</a>
            <button type="submit" class="btn-primary" style="padding: 12px 28px; border-radius: 8px; border: none; color: #ffffff; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="save"></i> Lưu vắc xin
            </button>
        </div>
    </form>
</div>
@endsection
