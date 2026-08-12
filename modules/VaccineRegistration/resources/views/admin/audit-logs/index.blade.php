@extends('vaccine::layouts.admin')

@section('title', 'Nhật Ký Hệ Thống - Medicare')
@section('page_title', 'Nhật Ký Hoạt Động & Thao Tác Hệ Thống')

@section('admin_content')
<div class="card-modern">
    <!-- Header -->
    <div style="margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-family: var(--font-display); font-size: 19px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">
                Tra Cứu Lịch Sử Hoạt Động
            </h2>
            <p style="color: #64748b; margin: 0; font-size: 13.5px;">
                Theo dõi, kiểm tra chi tiết các thao tác (thêm, sửa, xóa, đăng nhập, xuất file...) của nhân viên và quản trị viên trên toàn hệ thống.
            </p>
        </div>
    </div>

    <!-- Bộ Lọc Tìm Kiếm Chuyên Nghiệp Cho Người Non-Tech -->
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 26px;">
        <div style="font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="filter" style="width: 14px; height: 14px; color: var(--primary-color);"></i> Bộ Lọc Tìm Kiếm Nâng Cao
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
            <!-- Từ ngày -->
            <div>
                <label class="form-label-modern" for="from_date" style="font-size: 12.5px; font-weight: 700; color: #334155;">Từ ngày</label>
                <input class="form-control-modern" id="from_date" type="date" name="from_date" value="{{ request('from_date') }}" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
            </div>

            <!-- Đến ngày -->
            <div>
                <label class="form-label-modern" for="to_date" style="font-size: 12.5px; font-weight: 700; color: #334155;">Đến ngày</label>
                <input class="form-control-modern" id="to_date" type="date" name="to_date" value="{{ request('to_date') }}" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
            </div>

            <!-- Người thực hiện -->
            <div>
                <label class="form-label-modern" for="actor_id" style="font-size: 12.5px; font-weight: 700; color: #334155;">Người thực hiện</label>
                <select class="form-control-modern" id="actor_id" name="actor_id" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
                    <option value="">-- Tất cả người dùng --</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" {{ (string) request('actor_id') === (string) $actor->id ? 'selected' : '' }}>
                            {{ $actor->name }} ({{ $actor->username }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Chi nhánh -->
            <div>
                <label class="form-label-modern" for="center_id" style="font-size: 12.5px; font-weight: 700; color: #334155;">Chi nhánh</label>
                <select class="form-control-modern" id="center_id" name="center_id" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
                    <option value="">-- Toàn hệ thống --</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ (string) request('center_id') === (string) $center->id ? 'selected' : '' }}>
                            {{ $center->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
            <!-- Hành động / Chức năng -->
            <div>
                <label class="form-label-modern" for="action" style="font-size: 12.5px; font-weight: 700; color: #334155;">Chức năng / Hành động</label>
                <select class="form-control-modern" id="action" name="action" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
                    <option value="">-- Tất cả chức năng --</option>
                    @foreach($actions as $act)
                        @php
                            $actLabel = \App\Models\AuditLog::actionLabels()[$act] ?? $act;
                        @endphp
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ $actLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Loại dữ liệu -->
            <div>
                <label class="form-label-modern" for="resource_type" style="font-size: 12.5px; font-weight: 700; color: #334155;">Mục tác động</label>
                <select class="form-control-modern" id="resource_type" name="resource_type" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
                    <option value="">-- Tất cả mục dữ liệu --</option>
                    @foreach($resourceTypes as $resType)
                        @php
                            $resLabel = \App\Models\AuditLog::resourceTypeLabels()[$resType] ?? $resType;
                        @endphp
                        <option value="{{ $resType }}" {{ request('resource_type') === $resType ? 'selected' : '' }}>
                            {{ $resLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ID tài nguyên -->
            <div>
                <label class="form-label-modern" for="resource_id" style="font-size: 12.5px; font-weight: 700; color: #334155;">Mã số dữ liệu (ID)</label>
                <input class="form-control-modern" id="resource_id" type="text" name="resource_id" maxlength="50" value="{{ request('resource_id') }}" placeholder="VD: 123 hoặc mã số" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 8px 12px; font-size: 13px;">
            </div>

            <!-- Cụm nút bấm Lọc & Xóa Lọc -->
            <div style="display: flex; gap: 8px; align-items: center;">
                <button type="submit" style="padding: 9px 20px; border-radius: 8px; background: var(--primary-color, #c8102e); color: #ffffff; border: none; font-weight: 700; font-size: 13.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(200, 16, 46, 0.2); transition: all 0.2s;">
                    <i data-lucide="search" style="width: 15px; height: 15px;"></i> Lọc Dữ Liệu
                </button>
                @if(request()->hasAny(['from_date', 'to_date', 'actor_id', 'center_id', 'action', 'resource_type', 'resource_id']))
                    <a href="{{ route('admin.audit-logs.index') }}" style="padding: 9px 16px; border-radius: 8px; background: #ffffff; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                        <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Đặt Lại
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if($errors->any())
        <div class="admin-section-hint" role="alert" style="margin-bottom: 20px; background: #fef2f2; color: #991b1b; border-color: #fecaca;">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Bảng Danh Sách Nhật Ký Hoạt Động -->
    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">STT</th>
                    <th style="width: 150px;">Thời gian</th>
                    <th>Người thực hiện</th>
                    <th>Chi nhánh</th>
                    <th>Hành động đã làm</th>
                    <th>Mục dữ liệu</th>
                    <th style="width: 110px;">Địa chỉ mạng (IP)</th>
                    <th style="text-align: right; width: 100px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $auditLog)
                    <tr>
                        <!-- STT -->
                        <td style="text-align: center; font-weight: 700; color: var(--text-muted);">
                            {{ $auditLogs->firstItem() + $loop->index }}
                        </td>

                        <!-- Thời gian -->
                        <td style="white-space: nowrap; color: #334155; font-size: 13px;">
                            <div style="font-weight: 700; color: #0f172a;">{{ $auditLog->created_at?->format('d/m/Y') }}</div>
                            <div style="font-size: 11.5px; color: #64748b;">{{ $auditLog->created_at?->format('H:i:s') }}</div>
                        </td>

                        <!-- Người thực hiện -->
                        <td>
                            <strong style="color: #1e293b; display: block;">{{ $auditLog->actor?->name ?? 'Hệ thống tự động' }}</strong>
                            @if($auditLog->actor?->username)
                                <small style="display: block; color: #64748b; font-size: 11.5px;">@ {{ $auditLog->actor->username }}</small>
                            @endif
                        </td>

                        <!-- Chi nhánh -->
                        <td>
                            @if($auditLog->center)
                                <span style="font-weight: 600; color: #0f172a;">{{ $auditLog->center->name }}</span>
                            @else
                                <span style="color: #64748b; font-style: italic;">Toàn hệ thống</span>
                            @endif
                        </td>

                        <!-- Hành động đã làm (100% tiếng Việt trong sáng) -->
                        <td>
                            <span class="badge-modern {{ $auditLog->action_badge_class }}" style="font-weight: 700; padding: 5px 12px; font-size: 12.5px; display: inline-block;">
                                {{ $auditLog->action_label }}
                            </span>
                        </td>

                        <!-- Mục dữ liệu tác động -->
                        <td>
                            <strong style="color: #1e293b; font-size: 13px; display: block;">{{ $auditLog->resource_display_name }}</strong>
                        </td>

                        <!-- IP -->
                        <td style="color: #64748b; font-family: monospace; font-size: 12px;">
                            {{ $auditLog->ip_address ?? '-' }}
                        </td>

                        <!-- Thao tác -->
                        <td style="text-align: right; white-space: nowrap;">
                            <a class="btn-action-sm" href="{{ route('admin.audit-logs.show', $auditLog) }}" style="font-weight: 600;">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                            <i data-lucide="inbox" style="width: 36px; height: 36px; color: #cbd5e1; margin-bottom: 8px; display: inline-block;"></i>
                            <p style="margin: 0; font-size: 14px; font-weight: 600;">Không tìm thấy lịch sử hoạt động nào phù hợp với bộ lọc.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
        {{ $auditLogs->links() }}
    </div>
</div>
@endsection
