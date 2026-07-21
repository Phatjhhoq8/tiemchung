@extends('vaccine::layouts.app')

@section('title', 'Giới Thiệu Phòng Tiêm Chủng Medicare Cờ Đỏ')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">Giới thiệu phòng khám</span>
    </div>

    <!-- Banner Header -->
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 48px; border-radius: 16px; margin-bottom: 40px; text-align: center;">
        <span style="background-color: var(--primary-color); color: #ffffff; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Về Chúng Tôi</span>
        <h1 style="font-family: 'Roboto', sans-serif; font-size: 36px; font-weight: 800; margin-top: 16px; margin-bottom: 12px;">Phòng Tiêm Chủng Vắc Xin Medicare Cờ Đỏ</h1>
        <p style="color: #94a3b8; font-size: 16px; max-width: 700px; margin: 0 auto; line-height: 1.6;">Đơn vị y tế uy tín hàng đầu chuyên cung cấp giải pháp phòng bệnh toàn diện bằng vắc xin chất lượng cao cho trẻ em và người lớn tại Huyện Cờ Đỏ, TP Cần Thơ.</p>
    </div>

    <!-- Khối Tầm Nhìn & Sứ Mệnh -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; margin-bottom: 50px;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 32px;">
            <div style="width: 48px; height: 48px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i data-lucide="target" style="width: 24px; height: 24px;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Sứ Mệnh Bảo Vệ Sức Khỏe</h3>
            <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin: 0;">Mang lại dịch vụ tiêm chủng an toàn tuyệt đối, nhanh chóng và tiếp cận dễ dàng cho mọi gia đình. Giúp cộng đồng chủ động phòng ngừa các bệnh truyền nhiễm nguy hiểm.</p>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 32px;">
            <div style="width: 48px; height: 48px; background-color: rgba(2, 132, 199, 0.08); color: #0284c7; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Kho Lạnh GSP Đạt Chuẩn</h3>
            <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin: 0;">100% vắc xin được lưu trữ trong kho lạnh dây chuyền lạnh GSP đạt tiêu chuẩn Bộ Y tế, bảo quản nghiêm ngặt ở nhiệt độ 2 - 8°C để duy trì chất lượng vắc xin tối đa.</p>
        </div>
    </div>

    <!-- Khối Đội Ngũ Bác Sĩ & Cơ Sở Vật Chất -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 40px; margin-bottom: 50px;">
        <h2 style="font-size: 24px; font-weight: 800; color: #1e293b; margin-top: 0; margin-bottom: 24px; text-align: center;">Cam Kết Chất Lượng Dịch Vụ</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
            <div style="text-align: center; padding: 20px;">
                <i data-lucide="stethoscope" style="width: 36px; height: 36px; color: var(--primary-color); margin-bottom: 12px;"></i>
                <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Khám Sàng Lọc Kỹ Càng</h4>
                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">100% khách hàng được bác sĩ chuyên khoa khám sàng lọc cẩn thận trước khi tiêm.</p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <i data-lucide="heart-pulse" style="width: 36px; height: 36px; color: var(--primary-color); margin-bottom: 12px;"></i>
                <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Theo Dõi 30 Phút Sau Tiêm</h4>
                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">Phòng theo dõi sau tiêm tiện nghi, trang bị đầy đủ dụng cụ cấp cứu theo quy định.</p>
            </div>
            <div style="text-align: center; padding: 20px;">
                <i data-lucide="award" style="width: 36px; height: 36px; color: var(--primary-color); margin-bottom: 12px;"></i>
                <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Vắc Xin Chính Hãng</h4>
                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">Nhập khẩu chính hãng từ các tập đoàn dược phẩm hàng đầu thế giới (MSD, GSK, Sanofi Pasteur, Pfizer, Takeda...).</p>
            </div>
        </div>
    </div>
</div>
@endsection
