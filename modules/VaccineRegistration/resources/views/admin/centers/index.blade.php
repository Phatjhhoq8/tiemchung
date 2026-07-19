@extends('vaccine::layouts.admin')

@section('title', 'Quản lý Trung Tâm - Medicare Cờ Đỏ')
@section('page_title', 'Hệ Thống Trung Tâm Tiêm Chủng')

@section('admin_content')
<div style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; padding: 30px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <h2 style="font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">Danh sách các trung tâm tiêm chủng</h2>
        <a href="{{ route('admin.centers.create') }}" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus-circle"></i> Thêm Chi Nhánh Mới
        </a>
    </div>

    @if($centers->isEmpty())
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: #94a3b8;"></i>
            <p>Chưa có chi nhánh trung tâm nào trong hệ thống.</p>
        </div>
    @else
        <div class="table-responsive" style="overflow-x: auto; margin-bottom: 24px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid #cbd5e1; color: #475569;">
                        <th style="padding: 12px 16px; font-weight: 600; width: 80px;">ID</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Tên trung tâm</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Địa chỉ</th>
                        <th style="padding: 12px 16px; font-weight: 600;">Hotline</th>
                        <th style="padding: 12px 16px; font-weight: 600; text-align: center;">Trạng thái</th>
                        <th style="padding: 12px 16px; font-weight: 600; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($centers as $center)
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #334155; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 14px 16px;">{{ $center->id }}</td>
                            <td style="padding: 14px 16px; font-weight: 700; color: #1e293b;">{{ $center->name }}</td>
                            <td style="padding: 14px 16px; font-size: 14px; color: #475569;">{{ $center->address }}</td>
                            <td style="padding: 14px 16px;">{{ $center->phone ?: 'Chưa cập nhật' }}</td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <span class="badge" style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block;
                                    @if($center->is_active) background-color: #def7ec; color: #03543f;
                                    @else background-color: #fde8e8; color: #9b1c1c; @endif">
                                    {{ $center->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <a href="{{ route('admin.centers.edit', $center->id) }}" class="btn-secondary" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background:#ffffff; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; color: #475569;">
                                        <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.centers.destroy', $center->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa trung tâm này?')">
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

        <div style="display: flex; justify-content: center;">
            {{ $centers->links() }}
        </div>
    @endif
</div>
@endsection
