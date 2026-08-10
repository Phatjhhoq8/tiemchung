@php
    $leadStatusLabels = $leadStatusLabels ?? ['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
    $leadSourceLabels = $leadSourceLabels ?? [
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

@if($leads->isEmpty())
    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
        <p>Chưa có yêu cầu tư vấn nào.</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ & Tên</th>
                    <th>Số điện thoại</th>
                    <th>Nguồn yêu cầu</th>
                    <th>Trung tâm</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $lead)
                    <tr>
                        <td>#{{ $lead->id }}</td>
                        <td><strong style="color: var(--text-main);">{{ $lead->name }}</strong></td>
                        <td>{{ $lead->phone }}</td>
                        <td><span class="badge-modern badge-modern-secondary">{{ str_starts_with((string) $lead->source, 'Nhóm bệnh:') ? $lead->source : ($leadSourceLabels[$lead->source] ?? 'Không xác định') }}</span></td>
                        <td>{{ $lead->center?->name ?? 'Tất cả / Medicare' }}</td>
                        <td>
                            @if($lead->status === 'new')
                                <span class="badge-modern badge-modern-warning">Mới</span>
                            @elseif($lead->status === 'contacted')
                                <span class="badge-modern badge-modern-info">Đã liên hệ</span>
                            @elseif($lead->status === 'completed')
                                <span class="badge-modern badge-modern-success">Hoàn thành</span>
                            @else
                                <span class="badge-modern badge-modern-secondary">{{ $leadStatusLabels[$lead->status] ?? 'Không xác định' }}</span>
                            @endif
                        </td>
                        <td>{{ $lead->created_at->format('H:i d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn-modern btn-modern-secondary" style="padding: 4px 12px; font-size: 12px;">Xem</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $leads->links() }}
    </div>
@endif
