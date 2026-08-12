@extends('vaccine::layouts.admin')

@section('title', 'Chi tiết hoạt động #' . $auditLog->id . ' - Medicare')
@section('page_title', 'Chi Tiết Lịch Sử Hoạt Động')

@section('admin_content')
<div style="display:grid; gap:24px;">
    <div class="card-modern">
        <a class="btn-action-sm" href="{{ route('admin.audit-logs.index', request()->query()) }}" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
            <i data-lucide="arrow-left" style="width:14px; height:14px;"></i> Quay lại danh sách
        </a>
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-top:18px;">
            <div>
                <h2 style="font-family:var(--font-display); margin:0 0 8px; font-size: 20px; font-weight: 800; color: #1e293b;">
                    Chi Tiết Hoạt Động #{{ $auditLog->id }}
                </h2>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge-modern {{ $auditLog->action_badge_class }}" style="font-weight: 700; font-size: 13.5px; padding: 5px 14px;">
                        {{ $auditLog->action_label }}
                    </span>
                </div>
            </div>
            <strong style="color:var(--text-muted); font-size: 14px;">
                <i data-lucide="clock" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                {{ $auditLog->created_at?->format('d/m/Y H:i:s') }}
            </strong>
        </div>
    </div>

    <div class="card-modern">
        <h3 style="margin-top:0; font-family:var(--font-display); font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Thông Tin Sự Kiện</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px;">
            <div>
                <span style="display:block; color:var(--text-muted); margin-bottom:5px; font-size: 12.5px;">Người thực hiện</span>
                <strong style="color: #1e293b;">{{ $auditLog->actor?->name ?? 'Hệ thống tự động' }}</strong>
                @if($auditLog->actor?->username)
                    <small style="display:block; color: #64748b;">@ {{ $auditLog->actor->username }}</small>
                @endif
            </div>
            <div>
                <span style="display:block; color:var(--text-muted); margin-bottom:5px; font-size: 12.5px;">Chi nhánh</span>
                <strong>{{ $auditLog->center?->name ?? 'Toàn hệ thống' }}</strong>
            </div>
            <div>
                <span style="display:block; color:var(--text-muted); margin-bottom:5px; font-size: 12.5px;">Mục dữ liệu tác động</span>
                <strong style="color: #0f172a;">{{ $auditLog->resource_display_name }}</strong>
            </div>
            <div>
                <span style="display:block; color:var(--text-muted); margin-bottom:5px; font-size: 12.5px;">Địa chỉ mạng (IP)</span>
                <strong style="font-family: monospace;">{{ $auditLog->ip_address ?? '-' }}</strong>
            </div>
        </div>
        <div style="margin-top:20px; padding-top: 14px; border-top: 1px solid #f1f5f9;">
            <span style="display:block; color:var(--text-muted); margin-bottom:5px; font-size: 12.5px;">Thiết bị & Trình duyệt thực hiện</span>
            <div style="overflow-wrap:anywhere; font-size: 12.5px; color: #475569; background: #f8fafc; padding: 10px 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                {{ $auditLog->user_agent ?? '-' }}
            </div>
        </div>
    </div>

    @php
        $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $oldValuesJson = $auditLog->old_values === null ? null : json_encode($auditLog->old_values, $jsonOptions);
        $newValuesJson = $auditLog->new_values === null ? null : json_encode($auditLog->new_values, $jsonOptions);
    @endphp
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap:24px;">
        <div class="card-modern" style="min-width:0;">
            <h3 style="margin-top:0; font-family:var(--font-display); font-size: 15px; font-weight: 700; color: #64748b;">
                Dữ liệu ban đầu (Trước khi thao tác)
            </h3>
            @if($oldValuesJson !== null)
                <pre style="margin:0; padding:18px; overflow:auto; border-radius:12px; background:#f8fafc; border:1px solid var(--border-color); color:var(--text-primary); white-space:pre-wrap; overflow-wrap:anywhere; font-size: 12.5px;">{{ $oldValuesJson }}</pre>
            @else
                <p style="color:var(--text-muted); margin-bottom:0; font-style: italic;">Không có dữ liệu cũ (Tạo mới).</p>
            @endif
        </div>
        <div class="card-modern" style="min-width:0;">
            <h3 style="margin-top:0; font-family:var(--font-display); font-size: 15px; font-weight: 700; color: var(--primary-color);">
                Dữ liệu mới (Sau khi thực hiện)
            </h3>
            @if($newValuesJson !== null)
                <pre style="margin:0; padding:18px; overflow:auto; border-radius:12px; background:#f8fafc; border:1px solid var(--border-color); color:var(--text-primary); white-space:pre-wrap; overflow-wrap:anywhere; font-size: 12.5px;">{{ $newValuesJson }}</pre>
            @else
                <p style="color:var(--text-muted); margin-bottom:0; font-style: italic;">Không có dữ liệu mới (Đã xóa).</p>
            @endif
        </div>
    </div>
</div>
@endsection
