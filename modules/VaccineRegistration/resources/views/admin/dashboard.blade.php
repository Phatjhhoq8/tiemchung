@extends('vaccine::layouts.admin')

@section('title', 'Admin Dashboard - Medicare Cờ Đỏ')
@section('page_title', 'Bảng Điều Khiển Quản Trị')

@section('admin_content')
<!-- Thẻ thống kê Widgets -->
<div class="stats-widgets-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 40px;">
    <!-- Widget 1: Tổng đăng ký -->
    <div class="stat-card" style="background-color: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 14px; font-weight: 600; display: block; margin-bottom: 8px; text-transform: uppercase;">Tổng Đăng Ký</span>
            <strong style="font-size: 28px; color: #1e293b; font-family: 'Roboto', sans-serif;">{{ $totalRegistrations }}</strong>
        </div>
        <div style="background-color: #e2f0fd; color: #0a58ca; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i data-lucide="clipboard-list"></i></div>
    </div>

    <!-- Widget 2: Doanh thu -->
    <div class="stat-card" style="background-color: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 14px; font-weight: 600; display: block; margin-bottom: 8px; text-transform: uppercase;">Doanh Thu (Dự Kiến)</span>
            <strong style="font-size: 24px; color: #10b981; font-family: 'Roboto', sans-serif;">{{ number_format($totalRevenue, 0, ',', '.') }} đ</strong>
        </div>
        <div style="background-color: #ecfdf5; color: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i data-lucide="dollar-sign"></i></div>
    </div>

    <!-- Widget 3: Chờ thanh toán -->
    <div class="stat-card" style="background-color: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 14px; font-weight: 600; display: block; margin-bottom: 8px; text-transform: uppercase;">Chờ Thanh Toán</span>
            <strong style="font-size: 28px; color: #f59e0b; font-family: 'Roboto', sans-serif;">{{ $pendingCount }}</strong>
        </div>
        <div style="background-color: #fffbeb; color: #f59e0b; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i data-lucide="clock"></i></div>
    </div>

    <!-- Widget 4: Đã thanh toán -->
    <div class="stat-card" style="background-color: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <span style="color: #64748b; font-size: 14px; font-weight: 600; display: block; margin-bottom: 8px; text-transform: uppercase;">Đã Hoàn Tất</span>
            <strong style="font-size: 28px; color: #8b5cf6; font-family: 'Roboto', sans-serif;">{{ $completedCount }}</strong>
        </div>
        <div style="background-color: #f5f3ff; color: #8b5cf6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i data-lucide="shield-check"></i></div>
    </div>
</div>

<!-- Khung phụ thống kê nhanh -->
<div style="display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 40px;">
    <div style="flex: 1 1 200px; background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px;">
        <div style="background-color: #fff1f2; color: #e11d48; padding: 12px; border-radius: 8px;"><i data-lucide="syringe"></i></div>
        <div>
            <span style="display:block; color:#64748b; font-size:14px;">Danh mục vắc xin</span>
            <strong style="font-size: 20px; color:#1e293b;">{{ $vaccinesCount }} loại</strong>
        </div>
    </div>
    <div style="flex: 1 1 200px; background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 16px;">
        <div style="background-color: #f0fdf4; color: #16a34a; padding: 12px; border-radius: 8px;"><i data-lucide="map-pin"></i></div>
        <div>
            <span style="display:block; color:#64748b; font-size:14px;">Chi nhánh hoạt động</span>
            <strong style="font-size: 20px; color:#1e293b;">{{ $centersCount }} trung tâm</strong>
        </div>
    </div>
</div>

<!-- Danh sách đăng ký gần đây -->
<div class="recent-registrations-card" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 30px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
        <h2 style="font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">Đơn Đăng Ký Tiêm Mới Nhất</h2>
        <a href="{{ route('admin.registrations.index') }}" style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 14px;">Xem tất cả <i data-lucide="chevron-right" style="width:16px; height:16px; display:inline-block; vertical-align:middle;"></i></a>
    </div>

    @if($recentRegistrations->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: #94a3b8;"></i>
            <p>Chưa có đơn đăng ký tiêm chủng nào được lưu trong hệ thống.</p>
        </div>
    @else
        <div class="table-responsive" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; color: #475569;">
                        <th style="padding: 12px 16px; font-weight: 600;">Mã đơn</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Họ tên người tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Số điện thoại</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Địa điểm tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Ngày tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Tổng tiền</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Trạng thái</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRegistrations as $reg)
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
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('admin.registrations.show', $reg->id) }}" class="btn-primary" style="padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
