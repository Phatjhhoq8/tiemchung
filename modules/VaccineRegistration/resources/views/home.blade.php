@extends('vaccine::layouts.app')

@section('title', 'Phòng Tiêm Chủng Medicare Cờ Đỏ - Đăng Ký Tiêm Chủng')

@section('content')
<div class="home-container">
    <!-- Slider Banner (Động từ CSDL) -->
    <section class="banner-slider">
        @if($banners->isEmpty())
            <div class="slide active" style="position: relative; min-height: 480px; display: flex; align-items: center; padding: 0; width: 100%; overflow: hidden;">
                <!-- Anh nen tran man hinh -->
                <img src="{{ asset('images/banners/banner_family.jpg') }}" alt="Medicare Cờ Đỏ" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
                <!-- Overlay che phu lam noi bat chu -->
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 45%, rgba(255,255,255,0.1) 100%); z-index: 2;"></div>
                
                <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; position: relative; z-index: 3;">
                    <div class="slide-content" style="max-width: 550px; padding: 40px 0;">
                        <h1 style="font-family: 'Outfit', sans-serif; font-size: 42px; font-weight: 800; color: #1e293b; margin-bottom: 16px; line-height: 1.2;">Phòng Tiêm Chủng Medicare Cờ Đỏ</h1>
                        <p style="color: #475569; font-size: 16px; margin-bottom: 28px; line-height: 1.6; font-weight: 500;">Dịch vụ tiêm chủng vắc xin an toàn, chất lượng hàng đầu cho trẻ em và người lớn.</p>
                        <a href="{{ route('vaccine.index') }}" class="btn-cta" style="background-color: var(--primary-color); color: #ffffff; padding: 14px 28px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(200, 16, 46, 0.2);" onmouseover="this.style.backgroundColor='var(--primary-hover)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.backgroundColor='var(--primary-color)'; this.style.transform='translateY(0)';">Xem bảng giá vắc xin <i data-lucide="arrow-right"></i></a>
                    </div>
                </div>
            </div>
        @else
            <div class="slider-wrapper">
                @foreach($banners as $index => $banner)
                    <div class="slide {{ $index === 0 ? 'active' : '' }}" id="slide-{{ $banner->id }}" style="position: relative; min-height: 480px; display: {{ $index === 0 ? 'flex' : 'none' }}; align-items: center; padding: 0; width: 100%; overflow: hidden;">
                        <!-- Anh nen tran man hinh -->
                        <img src="{{ $banner->image_path ? asset($banner->image_path) : asset('images/banners/banner_family.jpg') }}" alt="{{ $banner->title }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
                        <!-- Overlay che phu lam noi bat chu -->
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 45%, rgba(255,255,255,0.1) 100%); z-index: 2;"></div>
                        
                        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; position: relative; z-index: 3;">
                            <div class="slide-content" style="max-width: 550px; padding: 40px 0;">
                                <span class="slide-badge" style="background-color: var(--primary-color); color: #ffffff; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px; display: inline-block; letter-spacing: 0.05em;">Medicare Cờ Đỏ</span>
                                <h2 style="font-family: 'Outfit', sans-serif; font-size: 42px; font-weight: 800; color: #1e293b; margin-bottom: 16px; line-height: 1.2;">{{ $banner->title }}</h2>
                                <p style="color: #475569; font-size: 16px; margin-bottom: 28px; line-height: 1.6; font-weight: 500;">{{ $banner->subtitle }}</p>
                                <a href="{{ $banner->link_url ?: route('vaccine.index') }}" class="btn-cta" style="background-color: var(--primary-color); color: #ffffff; padding: 14px 28px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(200, 16, 46, 0.2);" onmouseover="this.style.backgroundColor='var(--primary-hover)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.backgroundColor='var(--primary-color)'; this.style.transform='translateY(0)';">Tìm hiểu thêm <i data-lucide="arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Khuyến nghị quan trọng (Từ PDF: Cúm, Phế cầu, Zona và Qdenga) -->
    <section class="recommendations-section">
        <div class="section-title-wrapper">
            <span class="section-badge">Lời khuyên Y khoa</span>
            <h2>Vắc Xin Khuyến Nghị Cho Người Cao Tuổi & Bệnh Nền</h2>
            <p>Chủ động bảo vệ sức khỏe trước các tác nhân gây suy giảm hệ miễn dịch nguy hiểm.</p>
        </div>
        
        <div class="recommendations-grid">
            <div class="rec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="rec-card-img" style="height: 180px; width: 100%; overflow: hidden; border-bottom: 1px solid var(--border-color); background: #f8fafc; position: relative;">
                        <img src="{{ asset('images/vaccines/vaxigrip.jpg') }}" alt="Vắc Xin Cúm Mùa" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="rec-card-body" style="padding: 24px;">
                        <h3 style="font-family: 'Outfit', sans-serif; font-size: 19px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 50%; font-size: 13px;"><i data-lucide="thermometer-snowflake" style="width:14px;height:14px;"></i></span>
                            1. Vắc Xin Cúm Mùa
                        </h3>
                        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin-bottom: 0;">Phòng bệnh cúm mùa - bệnh hô hấp dễ gây biến chứng viêm phổi nặng ở người lớn tuổi. Cần tiêm nhắc lại hàng năm.</p>
                    </div>
                </div>
                <div style="padding: 0 24px 24px 24px;">
                    <a href="{{ route('vaccine.index', ['search' => 'Cúm']) }}" style="color: var(--primary-color); text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.2s;" onmouseover="this.style.gap='8px'" onmouseout="this.style.gap='4px'">Xem chi tiết & Đăng ký <i data-lucide="arrow-right" style="width:14px;height:14px;"></i></a>
                </div>
            </div>
            <div class="rec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="rec-card-img" style="height: 180px; width: 100%; overflow: hidden; border-bottom: 1px solid var(--border-color); background: #f8fafc; position: relative;">
                        <img src="{{ asset('images/vaccines/prevenar13.jpg') }}" alt="Vắc Xin Phế Cầu" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="rec-card-body" style="padding: 24px;">
                        <h3 style="font-family: 'Outfit', sans-serif; font-size: 19px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 50%; font-size: 13px;"><i data-lucide="shield-alert" style="width:14px;height:14px;"></i></span>
                            2. Vắc Xin Phế Cầu
                        </h3>
                        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin-bottom: 0;">Ngăn ngừa hiệu quả các thể bệnh xâm lấn nguy kịch: Viêm phổi nặng, viêm màng não và nhiễm trùng huyết do phế cầu.</p>
                    </div>
                </div>
                <div style="padding: 0 24px 24px 24px;">
                    <a href="{{ route('vaccine.index', ['search' => 'Phế cầu']) }}" style="color: var(--primary-color); text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.2s;" onmouseover="this.style.gap='8px'" onmouseout="this.style.gap='4px'">Xem chi tiết & Đăng ký <i data-lucide="arrow-right" style="width:14px;height:14px;"></i></a>
                </div>
            </div>
            <div class="rec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="rec-card-img" style="height: 180px; width: 100%; overflow: hidden; border-bottom: 1px solid var(--border-color); background: #f8fafc; position: relative;">
                        <img src="{{ asset('images/vaccines/shingrix.jpg') }}" alt="Vắc Xin Zona Thần Kinh" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="rec-card-body" style="padding: 24px;">
                        <h3 style="font-family: 'Outfit', sans-serif; font-size: 19px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <span style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 50%; font-size: 13px;"><i data-lucide="zap" style="width:14px;height:14px;"></i></span>
                            3. Vắc Xin Zona Thần Kinh
                        </h3>
                        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin-bottom: 0;">Phòng bệnh Zona thần kinh (giời leo) và biến chứng đau dây thần kinh sau Zona dai dẳng đau đớn ở người cao tuổi.</p>
                    </div>
                </div>
                <div style="padding: 0 24px 24px 24px;">
                    <a href="{{ route('vaccine.index', ['search' => 'Zona']) }}" style="color: var(--primary-color); text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; transition: gap 0.2s;" onmouseover="this.style.gap='8px'" onmouseout="this.style.gap='4px'">Xem chi tiết & Đăng ký <i data-lucide="arrow-right" style="width:14px;height:14px;"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner Qdenga Sốt Xuất Huyết (Từ PDF) -->
    <section class="qdenga-promo">
        <div class="promo-container">
            <div class="promo-image">
                <img src="{{ asset('images/vaccines/qdenga.jpg') }}" alt="Vắc Xin Sốt Xuất Huyết Qdenga">
            </div>
            <div class="promo-content">
                <span class="new-tag">MỚI CÓ SẴN</span>
                <h2>Vắc Xin Sốt Xuất Huyết Qdenga (Nhật Bản)</h2>
                <p class="promo-desc">
                    Medicare Cờ Đỏ tự hào cung cấp vắc xin sốt xuất huyết Qdenga thế hệ mới.
                    Giảm nguy cơ nhiễm bệnh sốt xuất huyết lên đến <strong>80%</strong> và giảm tỷ lệ nhập viện do bệnh lên đến <strong>90%</strong>.
                </p>
                <div class="promo-features">
                    <p><i data-lucide="check-circle-2"></i> <strong>Đối tượng:</strong> Cho trẻ từ 4 tuổi trở lên và người lớn</p>
                    <p><i data-lucide="check-circle-2"></i> <strong>Phác đồ:</strong> Tiêm 2 mũi cách nhau 3 tháng</p>
                </div>
                <a href="{{ route('vaccine.index', ['search' => 'Qdenga']) }}" class="btn-secondary">Đăng ký tiêm ngay</a>
            </div>
        </div>
    </section>

    <!-- Danh sách Vắc xin Nổi bật (Động từ CSDL) -->
    <section class="home-vaccines">
        <div class="section-title-wrapper">
            <span class="section-badge">Danh mục vắc xin</span>
            <h2>Vắc Xin Lẻ Nổi Bật</h2>
            <p>Các loại vắc xin thiết yếu đang được đăng ký tiêm nhiều nhất tại trung tâm.</p>
        </div>

        <div class="home-grid">
            @foreach($featuredVaccines as $vaccine)
                <div class="vaccine-card" style="display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
                    <a href="{{ route('vaccine.show', $vaccine->id) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div>
                            <div class="vaccine-card-img" style="height: 180px; width: 100%; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color);">
                                <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'default_vaccine.jpg')) }}" alt="{{ $vaccine->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="vaccine-card-body" style="padding: 20px;">
                            <div style="display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary-color); background-color: rgba(200, 16, 46, 0.08); padding: 4px 10px; border-radius: 4px; margin-bottom: 10px; letter-spacing: 0.05em;">
                                {{ $vaccine->origin }}
                            </div>
                            <h3 class="vaccine-name" style="margin-top: 0; padding-right: 0;">{{ $vaccine->name }}</h3>
                            <div class="vaccine-prevent"><strong>Phòng bệnh:</strong> {{ $vaccine->disease_prevention }}</div>
                            <div class="vaccine-age"><strong>Độ tuổi:</strong> {{ $vaccine->age_group }}</div>
                        </div>
                    </a>
                    <div class="vaccine-card-footer" style="padding: 20px; border-top: 1px solid var(--border-color); background: #fafafa;">
                        <div class="vaccine-price"><strong>{{ number_format($vaccine->price, 0, ',', '.') }} đ</strong></div>
                        <a href="{{ route('vaccine.show', $vaccine->id) }}" class="btn-view-detail">Chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center" style="margin-top: 30px; text-align: center;">
            <a href="{{ route('vaccine.index', ['type' => 'single']) }}" class="btn-primary">Xem tất cả vắc xin lẻ <i data-lucide="chevron-right"></i></a>
        </div>
    </section>

    <!-- Các Gói Vắc Xin Gia Đình (Động từ CSDL) -->
    <section class="home-packages">
        <div class="section-title-wrapper">
            <span class="section-badge">Gói vắc xin trọn gói</span>
            <h2>Gói Vắc Xin Ưu Đãi</h2>
            <p>Bảo vệ toàn diện cho những người thân yêu với chi phí tối ưu, cam kết không tăng giá.</p>
        </div>

        <div class="packages-grid">
            @foreach($vaccinePackages as $package)
                <div class="package-promo-card" style="padding: 0; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
                    <a href="{{ route('vaccine.show', $package->id) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div>
                            <div class="package-card-img" style="height: 180px; width: 100%; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color);">
                                <img src="{{ asset('images/vaccines/' . ($package->image ?: 'default_vaccine.jpg')) }}" alt="{{ $package->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="package-body" style="padding: 24px;">
                            <div style="display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--secondary-color); background-color: rgba(234, 170, 0, 0.08); padding: 4px 10px; border-radius: 4px; margin-bottom: 12px; letter-spacing: 0.05em;">
                                GÓI TRỌN GÓI
                            </div>
                            <h3 style="margin-top: 0;">{{ $package->name }}</h3>
                            <p class="package-desc" style="margin-bottom: 16px;">{{ $package->description }}</p>
                            <div class="package-info-row">
                                <span><i data-lucide="shield-check"></i> Phòng ngừa: {{ Str::limit($package->disease_prevention, 50) }}</span>
                                <span><i data-lucide="milestone"></i> Phác đồ: {{ $package->doses }} mũi tiêm</span>
                            </div>
                        </div>
                    </a>
                    <div class="package-footer" style="padding: 24px; border-top: 1px solid var(--border-color); background: #fafafa; margin-top: 0;">
                        <div class="package-price">
                            <span>Giá trọn gói:</span>
                            <strong>{{ number_format($package->price, 0, ',', '.') }} đ</strong>
                        </div>
                        <a href="{{ route('vaccine.show', $package->id) }}" class="btn-select-package">Xem chi tiết gói</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Tại sao chọn Medicare Cờ Đỏ? -->
    <section class="why-us">
        <div class="section-title-wrapper">
            <h2>Tại Sao Chọn Medicare Cờ Đỏ?</h2>
            <p>Chúng tôi cam kết mang lại dịch vụ tiêm chủng chất lượng cao, an toàn tuyệt đối cho cộng đồng.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="snowflake"></i></div>
                <h4>Hệ thống kho lạnh GSP đạt chuẩn</h4>
                <p>Vắc xin luôn được bảo quản trong điều kiện nhiệt độ chuẩn từ 2 - 8 độ C, đảm bảo chất lượng sinh học tối đa.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="heart-handshake"></i></div>
                <h4>Đội ngũ bác sĩ giàu kinh nghiệm</h4>
                <p>Khám sàng lọc kỹ càng trước tiêm, tư vấn phác đồ phù hợp, theo dõi chặt chẽ sau tiêm.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i data-lucide="sparkles"></i></div>
                <h4>Không gian tiêm chủng sạch sẽ</h4>
                <p>Phòng tiêm thoáng mát, thân thiện, mang lại cảm giác thoải mái và an tâm cho cả gia đình.</p>
            </div>
        </div>
    </section>
</div>
@endsection
