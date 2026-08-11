@extends('vaccine::layouts.admin')

@section('title', 'Nhật ký hệ thống')
@section('page_title', 'Nhật Ký Hệ Thống')

@section('admin_content')
<div class="card-modern">
    <div style="margin-bottom:24px;">
        <h2 style="font-family:var(--font-display); font-size:18px; font-weight:800; margin:0 0 6px;">Tra cứu audit log</h2>
        <p style="color:var(--text-muted); margin:0;">Theo dõi các thao tác đã được ghi nhận trên toàn hệ thống.</p>
    </div>

    <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; align-items:end; margin-bottom:24px;">
        <div>
            <label class="form-label-modern" for="from_date">Từ ngày</label>
            <input class="form-control-modern" id="from_date" type="date" name="from_date" value="{{ request('from_date') }}">
        </div>
        <div>
            <label class="form-label-modern" for="to_date">Đến ngày</label>
            <input class="form-control-modern" id="to_date" type="date" name="to_date" value="{{ request('to_date') }}">
        </div>
        <div>
            <label class="form-label-modern" for="actor_id">Người thực hiện</label>
            <select class="form-control-modern" id="actor_id" name="actor_id">
                <option value="">Tất cả</option>
                @foreach($actors as $actor)
                    <option value="{{ $actor->id }}" {{ (string) request('actor_id') === (string) $actor->id ? 'selected' : '' }}>{{ $actor->name }} ({{ $actor->username }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label-modern" for="center_id">Chi nhánh</label>
            <select class="form-control-modern" id="center_id" name="center_id">
                <option value="">Tất cả</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" {{ (string) request('center_id') === (string) $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label-modern" for="action">Hành động</label>
            <select class="form-control-modern" id="action" name="action">
                <option value="">Tất cả</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label-modern" for="resource_type">Loại tài nguyên</label>
            <select class="form-control-modern" id="resource_type" name="resource_type">
                <option value="">Tất cả</option>
                @foreach($resourceTypes as $resourceType)
                    <option value="{{ $resourceType }}" {{ request('resource_type') === $resourceType ? 'selected' : '' }}>{{ $resourceType }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label-modern" for="resource_id">ID tài nguyên</label>
            <input class="form-control-modern" id="resource_id" type="text" name="resource_id" maxlength="50" value="{{ request('resource_id') }}" placeholder="Ví dụ: 123">
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn-modern btn-modern-primary" type="submit"><i data-lucide="search"></i> Lọc</button>
            @if(request()->hasAny(['from_date', 'to_date', 'actor_id', 'center_id', 'action', 'resource_type', 'resource_id']))
                <a class="btn-modern btn-modern-secondary" href="{{ route('admin.audit-logs.index') }}">Xóa lọc</a>
            @endif
        </div>
    </form>

    @if($errors->any())
        <div class="admin-section-hint" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Người thực hiện</th>
                    <th>Chi nhánh</th>
                    <th>Hành động</th>
                    <th>Tài nguyên</th>
                    <th>IP</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $auditLog)
                    <tr>
                        <td style="white-space:nowrap;">{{ $auditLog->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <strong>{{ $auditLog->actor?->name ?? 'Hệ thống' }}</strong>
                            @if($auditLog->actor?->username)
                                <small style="display:block; color:var(--text-muted);">{{ $auditLog->actor->username }}</small>
                            @endif
                        </td>
                        <td>{{ $auditLog->center?->name ?? 'Toàn hệ thống' }}</td>
                        <td><span class="badge-modern badge-modern-info">{{ $auditLog->action }}</span></td>
                        <td>
                            <strong>{{ $auditLog->resource_type }}</strong>
                            <small style="display:block; color:var(--text-muted);">ID: {{ $auditLog->resource_id }}</small>
                        </td>
                        <td>{{ $auditLog->ip_address ?? '-' }}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a class="btn-action-sm" href="{{ route('admin.audit-logs.show', $auditLog) }}">Chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:32px;">Không có audit log phù hợp.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">{{ $auditLogs->links() }}</div>
</div>
@endsection
