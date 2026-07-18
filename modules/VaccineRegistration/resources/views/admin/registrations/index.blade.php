@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Đơn Đăng Ký - Medicare Cờ Đỏ')
@section('page_title', 'Danh Sách Đăng Ký Tiêm Chủng')

@section('admin_content')
<div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 30px;">
    
    <!-- Bộ lọc & Tìm kiếm -->
    <div style="margin-bottom: 30px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0;">
        <form action="{{ route('admin.registrations.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            <!-- Tìm kiếm -->
            <div style="flex: 1 1 250px;">
                <label for="search" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #475569;">Tìm kiếm nhanh</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nhập mã đơn, tên bệnh nhân, SĐT..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none;">
            </div>

            <!-- Trạng thái -->
            <div style="width: 200px;">
                <label for="status" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #475569;">Trạng thái</label>
                <select name="status" id="status" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #ffffff;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Chờ thanh toán" {{ request('status') === 'Chờ thanh toán' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="Đã thanh toán" {{ request('status') === 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="Đã tiêm" {{ request('status') === 'Đã tiêm' ? 'selected' : '' }}>Đã tiêm</option>
                    <option value="Đã hủy" {{ request('status') === 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            <!-- Nút Lọc -->
            <button type="submit" class="btn-primary" style="padding: 10px 24px; border-radius: 8px; border: none; color: #ffffff; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <i data-lucide="filter"></i> Lọc đơn
            </button>
            
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.registrations.index') }}" class="btn-secondary" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; text-decoration: none; color: #475569; font-weight: 600;">Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    @if($registrations->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: #94a3b8;"></i>
            <p>Không tìm thấy hồ sơ đăng ký tiêm chủng nào.</p>
        </div>
    @else
        <div class="table-responsive" style="overflow-x: auto; margin-bottom: 24px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; color: #475569;">
                        <th style="padding: 12px 16px; font-weight: 600;">Mã đơn</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Họ tên người tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Số điện thoại</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Địa điểm tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Ngày hẹn tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Tổng tiền</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Trạng thái</th>
                        <th style="padding: 12px 16px; text-align: center; font-weight: 600;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #334155; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 14px 16px; font-weight: 700; color: var(--primary-color);">{{ $reg->registration_code }}</td>
                            <td style="padding: 14px 16px; font-weight: 600;">{{ $reg->patient_name }}</td>
                            <td style="padding: 14px 16px;">{{ $reg->patient_phone }}</td>
                            <td style="padding: 14px 16px; font-size: 14px;">{{ $reg->center_name }}</td>
                            <td style="padding: 14px 16px;">{{ date('d/m/Y', strtotime($reg->injection_date)) }}</td>
                            <td style="padding: 14px 16px; font-weight: 600;">{{ number_format($reg->total_price, 0, ',', '.') }} đ</td>
                            <td style="padding: 14px 16px;">
                                <span class="badge" style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block;
                                    @if($reg->status === 'Đã thanh toán') background-color: #def7ec; color: #03543f;
                                    @elseif($reg->status === 'Đã tiêm') background-color: #e1effe; color: #1e429f;
                                    @elseif($reg->status === 'Đã hủy') background-color: #fde8e8; color: #9b1c1c;
                                    @else background-color: #fef08a; color: #713f12; @endif">
                                    {{ $reg->status }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="{{ route('admin.registrations.show', $reg->id) }}" class="btn-primary" style="padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination Links -->
        <div style="display: flex; justify-content: center;">
            {{ $registrations->links() }}
        </div>
    @endif
</div>
@endsection
