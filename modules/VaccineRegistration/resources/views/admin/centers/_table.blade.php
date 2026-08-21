@if($centers->isEmpty())
    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
        <p>Chưa có chi nhánh nào trong hệ thống.</p>
    </div>
@else
    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllCenters" onchange="toggleSelectAllCenters(this)" style="width: 16px; height: 16px; cursor: pointer;"></th>
                    <th style="width: 80px; text-align: center;">STT</th>
                    <th>Tên chi nhánh</th>
                    <th>Địa chỉ</th>
                    <th>Hotline</th>
                    <th style="text-align: center; width: 140px;">Trạng thái</th>
                    <th style="text-align: right; width: 160px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($centers as $center)
                    <tr>
                        <td style="text-align: center;"><input type="checkbox" class="center-select-checkbox" value="{{ $center->id }}" onchange="updateCenterBulkDeleteState()" style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary-color);"></td>
                        <td style="font-weight: 600; text-align: center;">{{ ($centers->currentPage() - 1) * $centers->perPage() + $loop->iteration }}</td>
                        <td style="font-weight: 700; color: var(--text-primary);">
                            {{ $center->name }}
                            @if($center->slug)
                                <div style="font-size: 12px; color: var(--text-muted); font-weight: 500;">{{ $center->slug }}</div>
                            @endif
                        </td>
                        <td style="color: var(--text-muted);">{{ $center->address }}</td>
                        <td style="white-space: nowrap; font-weight: 500;">{{ $center->phone ?: 'Chưa cập nhật' }}</td>
                        <td style="text-align: center;">
                            <span class="badge-modern {{ $center->is_active ? 'badge-modern-success' : 'badge-modern-danger' }}">
                                {{ $center->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; gap: 6px; justify-content: flex-end;">
                                <a href="{{ route('admin.centers.edit', $center->id) }}" class="btn-action-sm">Sửa</a>
                                @if($center->is_active)
                                    <form action="{{ route('admin.centers.toggle-status', $center->id) }}" method="POST" data-confirm="Bạn có chắc chắn muốn tạm dừng hoạt động chi nhánh này?" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action-sm" style="background-color: var(--secondary-color, #eaaa00); border-color: var(--secondary-color, #eaaa00); color: #fff;">Ngừng</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.centers.toggle-status', $center->id) }}" method="POST" data-confirm="Bạn có chắc chắn muốn kích hoạt lại hoạt động chi nhánh này?" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-action-sm btn-action-success">Bật lại</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.centers.destroy', $center->id) }}" method="POST" data-confirm="CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN chi nhánh này khỏi hệ thống? Thao tác này sẽ xóa sạch dữ liệu liên quan và không thể khôi phục." style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-sm btn-action-danger">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">
        {{ $centers->links() }}
    </div>
@endif
