@extends('vaccine::layouts.admin')

@section('title', 'Chi Tiết Yêu Cầu Tư Vấn #' . $lead->id)
@section('page_title', 'Chi Tiết Yêu Cầu Tư Vấn #' . $lead->id)

@section('admin_content')
@php
    $leadSourceLabels = [
        'Website' => 'Trang web',
        'Website Empty Cart Form (Online)' => 'Biểu mẫu tư vấn khi danh sách trống (Trực tuyến)',
        'Website Empty Cart Form (Offline)' => 'Biểu mẫu tư vấn khi danh sách trống (Tại trung tâm)',
        'SPA Modal Empty Cart Form (Online)' => 'Hộp thoại tư vấn khi danh sách trống (Trực tuyến)',
        'SPA Modal Empty Cart Form (Offline)' => 'Hộp thoại tư vấn khi danh sách trống (Tại trung tâm)',
        'Biểu mẫu tư vấn khi danh sách trống (Trực tuyến)' => 'Biểu mẫu tư vấn khi danh sách trống (Trực tuyến)',
        'Biểu mẫu tư vấn khi danh sách trống (Tại trung tâm)' => 'Biểu mẫu tư vấn khi danh sách trống (Tại trung tâm)',
        'Hộp thoại tư vấn khi danh sách trống (Trực tuyến)' => 'Hộp thoại tư vấn khi danh sách trống (Trực tuyến)',
        'Hộp thoại tư vấn khi danh sách trống (Tại trung tâm)' => 'Hộp thoại tư vấn khi danh sách trống (Tại trung tâm)',
    ];
@endphp
<div class="card-modern">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('admin.leads.index') }}" class="btn-modern btn-modern-secondary" style="padding: 8px 16px;">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <h3 style="margin-top: 0;">Thông tin Khách hàng</h3>
            <table class="table-modern">
                <tr>
                    <th style="width: 150px;">Họ & Tên</th>
                    <td><strong>{{ $lead->name }}</strong></td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $lead->phone }}</td>
                </tr>
                <tr>
                    <th>Nguồn yêu cầu</th>
                    <td>{{ str_starts_with((string) $lead->source, 'Nhóm bệnh:') ? $lead->source : ($leadSourceLabels[$lead->source] ?? 'Không xác định') }}</td>
                </tr>
                <tr>
                    <th>Chi nhánh mong muốn</th>
                    <td>{{ $lead->center?->name ?? 'Tất cả / Medicare' }}</td>
                </tr>
                <tr>
                    <th>Ghi chú / Nội dung</th>
                    <td>{{ $lead->note ?? 'Không có ghi chú' }}</td>
                </tr>
                <tr>
                    <th>Ngày khởi tạo</th>
                    <td>{{ $lead->created_at->format('H:i:s d/m/Y') }}</td>
                </tr>
            </table>
        </div>

        <div>
            <h3 style="margin-top: 0;">Cập nhật trạng thái</h3>
            <form action="{{ route('admin.leads.status', $lead->id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div style="margin-bottom: 16px;">
                    <label for="status" class="form-label-modern">Trạng thái</label>
                    <select name="status" id="status" class="form-control-modern">
                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>Mới</option>
                        <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Đã liên hệ</option>
                        <option value="completed" {{ $lead->status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ $lead->status === 'cancelled' ? 'selected' : '' }}>Hủy bỏ</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="note" class="form-label-modern">Ghi chú xử lý</label>
                    <textarea name="note" id="note" rows="4" class="form-control-modern">{{ old('note', $lead->note) }}</textarea>
                </div>

                <button type="submit" class="btn-modern btn-modern-primary" style="width: 100%; padding: 10px;">
                    Lưu trạng thái
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
