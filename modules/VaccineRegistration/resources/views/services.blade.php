@extends('vaccine::layouts.app')

@section('title', 'Dịch Vụ Tiêm Chủng - Medicare Cờ Đỏ')

@section('content')
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <span style="color: var(--primary-color); font-weight: 600;">Dịch vụ tiêm chủng</span>
    </div>

    <div class="section-title-wrapper" style="text-align: center; margin-bottom: 40px;">
        <span class="section-badge">Dịch Vụ Nổi Bật</span>
        <h2>Các Dịch Vụ Tiêm Chủng Tại Medicare Cờ Đỏ</h2>
        <p>Đáp ứng đầy đủ nhu cầu phòng ngừa dịch bệnh cho trẻ em, người trưởng thành và phụ nữ mang thai.</p>
    </div>

    <!-- Danh sách 4 dịch vụ chính -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 36px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 56px; height: 56px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i data-lucide="baby" style="width: 28px; height: 28px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px;">1. Tiêm Chủng Cho Trẻ Em</h3>
                <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin-bottom: 20px;">Gói dịch vụ tiêm chủng đầy đủ vắc xin thiết yếu dành cho bé từ sơ sinh đến 6 tuổi: Vắc xin 6in1 (Hexaxim/Infanrix), Tiêu chảy Rota (Rotarix/Rotavin), Phế cầu (Prevenar 13/20), Cúm mùa, Viêm não Nhật Bản...</p>
            </div>
            <a href="{{ route('vaccine.index', ['age_group' => 'Trẻ']) }}" class="btn-primary" style="text-align: center; justify-content: center;">Xem danh mục vắc xin trẻ em</a>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 36px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 56px; height: 56px; background-color: rgba(0, 75, 143, 0.08); color: var(--accent-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i data-lucide="user-check" style="width: 28px; height: 28px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px;">2. Tiêm Chủng Cho Người Lớn</h3>
                <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin-bottom: 20px;">Bảo vệ sức khỏe người trưởng thành và người cao tuổi trước các tác nhân nguy hiểm: Cúm mùa Vaxigrip Tetra, Phế cầu khuẩn 13/20, Zona thần kinh Shingrix, Sốt xuất huyết Qdenga, Viêm gan A-B...</p>
            </div>
            <a href="{{ route('vaccine.index', ['age_group' => 'người lớn']) }}" class="btn-primary" style="text-align: center; justify-content: center; background-color: var(--accent-color);">Xem danh mục vắc xin người lớn</a>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 36px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 56px; height: 56px; background-color: rgba(234, 170, 0, 0.08); color: var(--secondary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i data-lucide="package-check" style="width: 28px; height: 28px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px;">3. Gói Vắc Xin Trọn Gói</h3>
                <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin-bottom: 20px;">Đăng ký mua vắc xin trọn gói giúp gia đình tiết kiệm chi phí, cam kết giữ vắc xin 100% không lo thiếu mũi hay tăng giá trong suốt phác đồ tiêm.</p>
            </div>
            <a href="{{ route('vaccine.index', ['type' => 'package']) }}" class="btn-primary" style="text-align: center; justify-content: center; background-color: var(--secondary-color);">Xem các gói vắc xin ưu đãi</a>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 36px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 56px; height: 56px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i data-lucide="heart-pulse" style="width: 28px; height: 28px;"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px;">4. Tiêm Chủng Tiền Hôn Nhân</h3>
                <p style="color: #64748b; font-size: 15px; line-height: 1.7; margin-bottom: 20px;">Chuẩn bị nền tảng sức khỏe vững chắc cho phụ nữ trước khi mang thai với các vắc xin phòng Sởi - Quai bị - Rubella (MMR II), Thủy đậu (Varilrix), HPV Gardasil 9...</p>
            </div>
            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="btn-primary" style="text-align: center; justify-content: center; background-color: var(--primary-color);">Đặt lịch tư vấn miễn phí</a>
        </div>
    </div>
</div>
@endsection
