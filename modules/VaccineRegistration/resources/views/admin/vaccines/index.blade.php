@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Vắc Xin - Medicare Cờ Đỏ')
@section('page_title', 'Danh Mục Vắc Xin & Gói Vắc Xin')

@section('admin_content')
<div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 30px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">Danh sách Vắc xin đang hoạt động</h2>
        <a href="{{ route('admin.vaccines.create') }}" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle"></i> Thêm Vắc xin Mới
        </a>
    </div>

    @if($vaccines->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: #94a3b8;"></i>
            <p>Không tìm thấy vắc xin nào trong hệ thống.</p>
        </div>
    @else
        <div class="table-responsive" style="overflow-x: auto; margin-bottom: 24px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; color: #475569;">
                        <th style="padding: 12px 16px; font-weight: 600;">Phân loại</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Tên vắc xin</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Độ tuổi</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Phòng bệnh</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Nguồn gốc</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Mũi tiêm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Giá tiền</th>
                        <th style="padding: 12px 16px; font-weight: 600; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccines as $vac)
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #334155; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 14px 16px;">
                                <span style="padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;
                                    @if($vac->type === 'package') background-color: #fef3c7; color: #d97706;
                                    @else background-color: #e0f2fe; color: #0369a1; @endif">
                                    {{ $vac->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ' }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #1e293b;">{{ $vac->name }}</td>
                            <td style="padding: 14px 16px; font-size: 14px;">{{ $vac->age_group }}</td>
                            <td style="padding: 14px 16px; font-size: 14px;">{{ Str::limit($vac->disease_prevention, 40) }}</td>
                            <td style="padding: 14px 16px;">{{ $vac->origin }}</td>
                            <td style="padding: 14px 16px; text-align: center; font-weight: 600;">{{ $vac->doses }}</td>
                            <td style="padding: 14px 16px; font-weight: 700; color: var(--primary-color);">{{ number_format($vac->price, 0, ',', '.') }} đ</td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <a href="{{ route('admin.vaccines.edit', $vac->id) }}" class="btn-secondary" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background:#ffffff; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; color: #475569;">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.vaccines.destroy', $vac->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vắc xin này? Tất cả dữ liệu liên kết giỏ hàng sẽ bị xóa.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #fbd5d5; background:#fff5f5; color: #c8102e; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination Links -->
        <div class="admin-pagination" style="display: flex; justify-content: center;">
            {{ $vaccines->links() }}
        </div>
    @endif
</div>
@endsection
