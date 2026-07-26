@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Đơn Đăng Ký - Medicare Cờ Đỏ')
@section('page_title', 'Danh Sách Đăng Ký Tiêm Chủng')

@section('admin_content')
<div class="card-modern">
    
    <!-- Bộ lọc & Tìm kiếm -->
    <div style="margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
        <form action="{{ route('admin.registrations.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            <!-- Tìm kiếm -->
            <div style="flex: 1 1 250px;">
                <label for="search" class="form-label-modern">Tìm kiếm nhanh</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nhập mã đơn, tên bệnh nhân, SĐT..." class="form-control-modern">
            </div>

            <!-- Trạng thái -->
            <div style="width: 200px;">
                <label for="status" class="form-label-modern">Trạng thái</label>
                <select name="status" id="status" class="form-control-modern" style="background-image: none;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Chờ thanh toán" {{ request('status') === 'Chờ thanh toán' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="Đã thanh toán" {{ request('status') === 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="Đã tiêm" {{ request('status') === 'Đã tiêm' ? 'selected' : '' }}>Đã tiêm</option>
                    <option value="Đã hủy" {{ request('status') === 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="Chờ tư vấn" {{ request('status') === 'Chờ tư vấn' ? 'selected' : '' }}>Chờ tư vấn</option>
                    <option value="Đã tư vấn" {{ request('status') === 'Đã tư vấn' ? 'selected' : '' }}>Đã tư vấn</option>
                </select>
            </div>

            <!-- Nút Lọc -->
            <button type="submit" class="btn-modern btn-modern-primary" style="padding: 10px 24px; border-radius: 8px;">
                <i data-lucide="filter" style="width: 14px; height: 14px;"></i> Lọc đơn
            </button>
            
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.registrations.index') }}" class="btn-modern btn-modern-secondary" style="padding: 10px 20px; border-radius: 8px;">Xóa bộ lọc</a>
            @endif

            <a href="{{ route('admin.registrations.export.csv') }}" class="btn-modern btn-modern-secondary" style="padding: 10px 20px; border-radius: 8px; border-color: var(--accent-color); color: var(--accent-color);">
                <i data-lucide="download" style="width: 14px; height: 14px;"></i> Xuất CSV
            </a>
        </form>
    </div>

    @if($registrations->isEmpty())
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
            <p>Không tìm thấy hồ sơ đăng ký tiêm chủng nào.</p>
        </div>
    @else
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Họ tên người tiêm</th>
                        <th>Số điện thoại</th>
                        <th>Địa điểm tiêm</th>
                        <th>Ngày hẹn tiêm</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th style="text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary-color);">{{ $reg->registration_code }}</td>
                            <td style="font-weight: 600;">{{ $reg->patient_name }}</td>
                            <td>{{ $reg->patient_phone }}</td>
                            <td>{{ $reg->center_name }}</td>
                            <td>{{ date('d/m/Y', strtotime($reg->injection_date)) }}</td>
                            <td style="font-weight: 600;">{{ number_format($reg->total_price, 0, ',', '.') }} đ</td>
                            <td>
                                <span class="badge-modern 
                                    @if($reg->status === 'Đã thanh toán') badge-modern-success
                                    @elseif($reg->status === 'Đã tiêm') badge-modern-info
                                    @elseif($reg->status === 'Đã hủy') badge-modern-danger
                                    @elseif($reg->status === 'Đã tư vấn') badge-modern-success
                                    @elseif($reg->status === 'Chờ tư vấn') badge-modern-warning
                                    @else badge-modern-warning @endif">
                                    {{ $reg->status }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.registrations.show', $reg->id) }}" class="btn-action-sm">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination Links -->
        <div style="display: flex; justify-content: center; margin-top: 24px;">
            {{ $registrations->links() }}
        </div>
    @endif
</div>
@endsection
