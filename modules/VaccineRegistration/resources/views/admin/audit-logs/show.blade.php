@extends('vaccine::layouts.admin')

@section('title', 'Audit log #' . $auditLog->id)
@section('page_title', 'Chi Tiết Audit Log')

@section('admin_content')
<div style="display:grid; gap:24px;">
    <div class="card-modern">
        <a class="btn-action-sm" href="{{ route('admin.audit-logs.index', request()->query()) }}">
            <i data-lucide="arrow-left" style="width:14px; height:14px;"></i> Quay lại danh sách
        </a>
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-top:18px;">
            <div>
                <h2 style="font-family:var(--font-display); margin:0 0 8px;">Audit log #{{ $auditLog->id }}</h2>
                <span class="badge-modern badge-modern-info">{{ $auditLog->action }}</span>
            </div>
            <strong style="color:var(--text-muted);">{{ $auditLog->created_at?->format('d/m/Y H:i:s') }}</strong>
        </div>
    </div>

    <div class="card-modern">
        <h3 style="margin-top:0; font-family:var(--font-display);">Thông tin sự kiện</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px;">
            <div><span style="display:block; color:var(--text-muted); margin-bottom:5px;">Người thực hiện</span><strong>{{ $auditLog->actor?->name ?? 'Hệ thống' }}</strong>@if($auditLog->actor?->username)<small style="display:block;">{{ $auditLog->actor->username }}</small>@endif</div>
            <div><span style="display:block; color:var(--text-muted); margin-bottom:5px;">Chi nhánh</span><strong>{{ $auditLog->center?->name ?? 'Toàn hệ thống' }}</strong></div>
            <div><span style="display:block; color:var(--text-muted); margin-bottom:5px;">Tài nguyên</span><strong>{{ $auditLog->resource_type }} #{{ $auditLog->resource_id }}</strong></div>
            <div><span style="display:block; color:var(--text-muted); margin-bottom:5px;">Địa chỉ IP</span><strong>{{ $auditLog->ip_address ?? '-' }}</strong></div>
        </div>
        <div style="margin-top:20px;">
            <span style="display:block; color:var(--text-muted); margin-bottom:5px;">User agent</span>
            <div style="overflow-wrap:anywhere;">{{ $auditLog->user_agent ?? '-' }}</div>
        </div>
    </div>

    @php
        $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $oldValuesJson = $auditLog->old_values === null ? null : json_encode($auditLog->old_values, $jsonOptions);
        $newValuesJson = $auditLog->new_values === null ? null : json_encode($auditLog->new_values, $jsonOptions);
    @endphp
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap:24px;">
        <div class="card-modern" style="min-width:0;">
            <h3 style="margin-top:0; font-family:var(--font-display);">Giá trị cũ</h3>
            @if($oldValuesJson !== null)
                <pre style="margin:0; padding:18px; overflow:auto; border-radius:12px; background:#f8fafc; border:1px solid var(--border-color); color:var(--text-primary); white-space:pre-wrap; overflow-wrap:anywhere;">{{ $oldValuesJson }}</pre>
            @else
                <p style="color:var(--text-muted); margin-bottom:0;">Không có dữ liệu.</p>
            @endif
        </div>
        <div class="card-modern" style="min-width:0;">
            <h3 style="margin-top:0; font-family:var(--font-display);">Giá trị mới</h3>
            @if($newValuesJson !== null)
                <pre style="margin:0; padding:18px; overflow:auto; border-radius:12px; background:#f8fafc; border:1px solid var(--border-color); color:var(--text-primary); white-space:pre-wrap; overflow-wrap:anywhere;">{{ $newValuesJson }}</pre>
            @else
                <p style="color:var(--text-muted); margin-bottom:0;">Không có dữ liệu.</p>
            @endif
        </div>
    </div>
</div>
@endsection
