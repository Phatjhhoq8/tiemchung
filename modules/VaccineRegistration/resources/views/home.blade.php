@extends('vaccine::layouts.app')

@section('title', 'Hệ Thống Tiêm Chủng Medicare - Đăng Ký Tiêm Chủng')

@section('content')
    <div class="home-container">
        <!-- Slider Banner (Động từ CSDL) -->
        <section class="banner-slider" id="hero-banner">
            @if($banners->isEmpty())
                <div class="slide active" style="position: relative; min-height: 480px; display: flex; align-items: center; padding: 0; width: 100%; overflow: hidden;">
                    <!-- Anh nen tran man hinh -->
                    <img src="{{ asset('images/banners/banner_family.jpg') }}" alt="Hệ Thống Medicare" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
                    <!-- Overlay che phu lam noi bat chu -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(90deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.7) 45%, rgba(255,255,255,0.1) 100%); z-index: 2;"></div>

                    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; position: relative; z-index: 3;">
                        <div class="slide-content" style="max-width: 550px; padding: 40px 0;">
                            <h1 style="font-family: 'Roboto', sans-serif; font-size: 42px; font-weight: 800; color: #1e293b; margin-bottom: 16px; line-height: 1.2;">Hệ Thống Tiêm Chủng Medicare</h1>
                            <p style="color: #475569; font-size: 16px; margin-bottom: 28px; line-height: 1.6; font-weight: 500;">Hệ thống trung tâm tiêm chủng vắc xin an toàn, chất lượng hàng đầu cho trẻ em và người lớn.</p>
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
                                    <span class="slide-badge" style="background-color: var(--primary-color); color: #ffffff; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px; display: inline-block; letter-spacing: 0.05em;">Hệ Thống Medicare</span>
                                    <h2 style="font-family: 'Roboto', sans-serif; font-size: 42px; font-weight: 800; color: #1e293b; margin-bottom: 16px; line-height: 1.2;">{{ $banner->title }}</h2>
                                    <p style="color: #475569; font-size: 16px; margin-bottom: 28px; line-height: 1.6; font-weight: 500;">{{ $banner->subtitle }}</p>
                                    <a href="{{ $banner->link_url ?: route('vaccine.index') }}" class="btn-cta" style="background-color: var(--primary-color); color: #ffffff; padding: 14px 28px; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(200, 16, 46, 0.2);" onmouseover="this.style.backgroundColor='var(--primary-hover)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.backgroundColor='var(--primary-color)'; this.style.transform='translateY(0)';">Tìm hiểu thêm <i data-lucide="arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- VNVC Quick Action Toolbar (Bảng thao tác nhanh tiện ích) -->
        <div style="max-width: 1200px; margin: -30px auto 40px auto; padding: 0 20px; position: relative; z-index: 20;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 16px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);">
                <a href="{{ route('vaccine.index') }}" style="display: flex; align-items: center; gap: 14px; padding: 14px; border-radius: 12px; background-color: var(--bg-main); border: 1px solid var(--border-color); text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary-color)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="syringe" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 14.5px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">Đặt Mua Vắc Xin Online</div>
                        <div style="font-size: 12.5px; color: #64748b;">Giữ vắc xin 100%, không lo thiếu mũi</div>
                    </div>
                </a>

                <a href="{{ route('register.show') }}" style="display: flex; align-items: center; gap: 14px; padding: 14px; border-radius: 12px; background-color: var(--bg-main); border: 1px solid var(--border-color); text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='#0284c7'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background-color: rgba(2, 132, 199, 0.08); color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="calendar-check-2" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 14.5px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">Đăng Ký Tiêm Chủng</div>
                        <div style="font-size: 12.5px; color: #64748b;">Chọn chi nhánh & ngày giờ hẹn trước</div>
                    </div>
                </a>

                <a href="{{ route('vaccine.index') }}" style="display: flex; align-items: center; gap: 14px; padding: 14px; border-radius: 12px; background-color: var(--bg-main); border: 1px solid var(--border-color); text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='#eaaa00'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background-color: rgba(234, 170, 0, 0.08); color: #eaaa00; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="badge-percent" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 14.5px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">Bảng Giá Vắc Xin</div>
                        <div style="font-size: 12.5px; color: #64748b;">Giá niêm yết công khai, bình ổn</div>
                    </div>
                </a>

                <a href="{{ route('contact') }}" style="display: flex; align-items: center; gap: 14px; padding: 14px; border-radius: 12px; background-color: var(--bg-main); border: 1px solid var(--border-color); text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='#16a34a'; this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)'">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background-color: rgba(22, 163, 74, 0.08); color: #16a34a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="map-pin" style="width: 22px; height: 22px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 14.5px; font-weight: 700; color: #1e293b; margin-bottom: 2px;">Tìm Chi Nhánh Gần Bạn</div>
                        <div style="font-size: 12.5px; color: #64748b;">Chi nhánh Cờ Đỏ & Thới Lai</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Khuyến nghị quan trọng (Từ PDF: Cúm, Phế cầu, Zona và Qdenga) -->
        <section class="recommendations-section" id="recommendations-section">
            <div class="section-title-wrapper">
                <span class="section-badge">Lời khuyên Y khoa</span>
                <h2>{{ $settings['rec_section_title'] ?? 'Vắc Xin Khuyến Nghị Cho Người Cao Tuổi & Bệnh Nền' }}</h2>
                <p>{{ $settings['rec_section_desc'] ?? 'Chủ động bảo vệ sức khỏe trước các tác nhân gây suy giảm hệ miễn dịch nguy hiểm.' }}</p>
            </div>

            <div class="recommendations-grid">
                <div class="rec-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="rec-card-img" style="height: 180px; width: 100%; overflow: hidden; border-bottom: 1px solid var(--border-color); background: var(--bg-main); position: relative;">
                            <img src="{{ asset('images/vaccines/vaxigrip.jpg') }}" alt="Vắc Xin Cúm Mùa" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="rec-card-body" style="padding: 24px;">
                            <h3 style="font-family: 'Roboto', sans-serif; font-size: 19px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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
                        <div class="rec-card-img" style="height: 180px; width: 100%; overflow: hidden; border-bottom: 1px solid var(--border-color); background: var(--bg-main); position: relative;">
                            <img src="{{ asset('images/vaccines/prevenar13.jpg') }}" alt="Vắc Xin Phế Cầu" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="rec-card-body" style="padding: 24px;">
                            <h3 style="font-family: 'Roboto', sans-serif; font-size: 19px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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
                        <div class="rec-card-img" style="height: 180px; width: 100%; overflow: hidden; border-bottom: 1px solid var(--border-color); background: var(--bg-main); position: relative;">
                            <img src="{{ asset('images/vaccines/shingrix.jpg') }}" alt="Vắc Xin Zona Thần Kinh" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="rec-card-body" style="padding: 24px;">
                            <h3 style="font-family: 'Roboto', sans-serif; font-size: 19px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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
        <section class="qdenga-promo" id="qdenga-promo">
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
        <section class="home-vaccines" id="vaccine-catalog">
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
                                <div class="vaccine-card-img" style="height: 180px; width: 100%; background: var(--bg-main); display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color);">
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
                        <div class="vaccine-card-footer" style="padding: 20px; border-top: 1px solid var(--border-color); background: var(--bg-card);">
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

        @if($vaccinePackages->isNotEmpty())
            <!-- Các Gói Vắc Xin Gia Đình -->
            <section class="home-packages" id="package-catalog">
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
                                    <div class="package-card-img" style="height: 180px; width: 100%; background: var(--bg-main); display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color);">
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
                            <div class="package-footer" style="padding: 24px; border-top: 1px solid var(--border-color); background: var(--bg-card); margin-top: 0;">
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
        @endif

        <!-- Quy trình 5 Bước Tiêm Chủng An Toàn Chuẩn Y Tế (VNVC Standard) -->
        <section id="process-section" style="padding: 60px 0; background-color: var(--bg-card); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div class="section-title-wrapper" style="text-align: center;">
                    <span class="section-badge">Chuẩn Y Tế An Toàn</span>
                    <h2>{{ $settings['process_section_title'] ?? 'Quy Trình Tiêm Chủng 5 Bước An Toàn Mẫu Mực' }}</h2>
                    <p>{{ $settings['process_section_desc'] ?? 'Medicare áp dụng nghiêm ngặt quy trình tiêm chủng an toàn bảo vệ sức khỏe tối đa cho khách hàng.' }}</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 40px;">
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px; padding: 28px 16px; text-align: center; position: relative; transition: all 0.3s ease;" onmouseover="this.style.borderColor='var(--primary-color)'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                        <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); width: 32px; height: 32px; background-color: var(--primary-color); color: #ffffff; border-radius: 50%; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 3px solid var(--bg-card);">1</div>
                        <div style="width: 52px; height: 52px; margin: 10px auto 16px auto; background: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="clipboard-signature" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #1e293b;">1. Đăng Ký & Khám Sàng Lọc</h4>
                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.6; margin: 0;">Bác sĩ chuyên khoa khám sàng lọc cẩn thận miễn phí trước khi tiêm.</p>
                    </div>

                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px; padding: 28px 16px; text-align: center; position: relative; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#0284c7'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                        <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); width: 32px; height: 32px; background-color: #0284c7; color: #ffffff; border-radius: 50%; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 3px solid var(--bg-card);">2</div>
                        <div style="width: 52px; height: 52px; margin: 10px auto 16px auto; background: rgba(2, 132, 199, 0.08); color: #0284c7; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="stethoscope" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #1e293b;">2. Tư Vấn Phác Đồ</h4>
                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.6; margin: 0;">Tư vấn kỹ càng loại vắc xin, liều lượng, phác đồ phù hợp và chi phí niêm yết.</p>
                    </div>

                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px; padding: 28px 16px; text-align: center; position: relative; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#eaaa00'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                        <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); width: 32px; height: 32px; background-color: #eaaa00; color: #ffffff; border-radius: 50%; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 3px solid var(--bg-card);">3</div>
                        <div style="width: 52px; height: 52px; margin: 10px auto 16px auto; background: rgba(234, 170, 0, 0.08); color: #eaaa00; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="syringe" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #1e293b;">3. Tiêm Vắc Xin Chuẩn GSP</h4>
                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.6; margin: 0;">Điều dưỡng thực hiện tiêm đúng kỹ thuật nhẹ nhàng, không đau.</p>
                    </div>

                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px; padding: 28px 16px; text-align: center; position: relative; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#16a34a'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                        <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); width: 32px; height: 32px; background-color: #16a34a; color: #ffffff; border-radius: 50%; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 3px solid var(--bg-card);">4</div>
                        <div style="width: 52px; height: 52px; margin: 10px auto 16px auto; background: rgba(22, 163, 74, 0.08); color: #16a34a; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="timer" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #1e293b;">4. Theo Dõi 30 Phút</h4>
                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.6; margin: 0;">Theo dõi phản ứng sau tiêm 30 phút tại phòng chờ hiện đại.</p>
                    </div>

                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 16px; padding: 28px 16px; text-align: center; position: relative; transition: all 0.3s ease;" onmouseover="this.style.borderColor='#9333ea'; this.style.transform='translateY(-4px)';" onmouseout="this.style.borderColor='var(--border-color)'; this.style.transform='translateY(0)';">
                        <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); width: 32px; height: 32px; background-color: #9333ea; color: #ffffff; border-radius: 50%; font-size: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; border: 3px solid var(--bg-card);">5</div>
                        <div style="width: 52px; height: 52px; margin: 10px auto 16px auto; background: rgba(147, 51, 234, 0.08); color: #9333ea; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="shield-check" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #1e293b;">5. Kiểm Tra & Ra Về</h4>
                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.6; margin: 0;">Kiểm tra vết tiêm, đo lại thân nhiệt, dặn dò và ra về an toàn.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="services-section" id="services-section" style="padding: 60px 0; background-color: var(--bg-card); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div class="section-title-wrapper">
                    <span class="section-badge">Dịch vụ chính</span>
                    <h2>{{ $settings['service_section_title'] ?? 'Dịch Vụ Tiêm Chủng Tại Phòng Khám' }}</h2>
                    <p>{{ $settings['service_section_desc'] ?? 'Giải pháp phòng ngừa bệnh tật toàn diện dành cho mọi lứa tuổi và gia đình.' }}</p>
                </div>

                <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; margin-top: 36px;">
                    <div class="service-card" style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 28px 24px; transition: all 0.3s ease;">
                        <div style="width: 52px; height: 52px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i data-lucide="baby" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Tiêm Chủng Trẻ Em</h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">Cung cấp đầy đủ các loại vắc xin quan trọng cho bé từ sơ sinh đến 6 tuổi với quy trình tiêm nhẹ nhàng, không đau.</p>
                        <a href="{{ route('vaccine.index', ['age_group' => 'Trẻ']) }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">Xem vắc xin trẻ em <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i></a>
                    </div>

                    <div class="service-card" style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 28px 24px; transition: all 0.3s ease;">
                        <div style="width: 52px; height: 52px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i data-lucide="user-check" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Tiêm Chủng Người Lớn</h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">Bảo vệ người trưởng thành và người cao tuổi trước Cúm mùa, Phế cầu, Phế cầu 20, Zona thần kinh, Sốt xuất huyết Qdenga...</p>
                        <a href="{{ route('vaccine.index', ['age_group' => 'người lớn']) }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">Xem vắc xin người lớn <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i></a>
                    </div>

                    <div class="service-card" style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 28px 24px; transition: all 0.3s ease;">
                        <div style="width: 52px; height: 52px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i data-lucide="package-check" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Gói Tiêm Trọn Gói</h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">Tiết kiệm chi phí, cam kết giữ vắc xin không lo thiếu hàng hay tăng giá trong suốt phác đồ tiêm chủng.</p>
                        <a href="{{ route('vaccine.index', ['type' => 'package']) }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">Xem các gói tiêm <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i></a>
                    </div>

                    <div class="service-card" style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 28px 24px; transition: all 0.3s ease;">
                        <div style="width: 52px; height: 52px; background-color: rgba(200, 16, 46, 0.08); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i data-lucide="stethoscope" style="width: 26px; height: 26px;"></i>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Tư Vấn & Khám Sàng Lọc</h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">Miễn phí khám sàng lọc bởi bác sĩ chuyên khoa trước khi tiêm và theo dõi sức khỏe cẩn thận sau tiêm.</p>
                        <a href="{{ route('register.show') }}" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">Đặt lịch hẹn ngay <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Tin Tức / Kiến Thức Tiêm Chủng (Nạp động 100% từ CSDL) -->
        <section class="news-section" id="news-section" style="padding: 60px 0;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div class="section-title-wrapper">
                    <span class="section-badge">Góc y khoa</span>
                    <h2>{{ $settings['news_section_title'] ?? 'Kiến Thức Tiêm Chủng & Tin Tức' }}</h2>
                    <p>{{ $settings['news_section_desc'] ?? 'Cập nhật những thông tin y tế chính thống và lời khuyên hữu ích từ chuyên gia.' }}</p>
                </div>

                <div class="news-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px; margin-top: 36px;">
                    @foreach($articles as $article)
                        <article class="news-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column;">
                            <div style="height: 190px; overflow: hidden;">
                                <img src="{{ asset('images/vaccines/' . ($article->image ?: 'default_vaccine.jpg')) }}" alt="{{ $article->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </div>
                            <div style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <span style="font-size: 12px; color: var(--primary-color); font-weight: 700; text-transform: uppercase; background: rgba(200,16,46,0.08); padding: 4px 10px; border-radius: 4px;">{{ $article->category }}</span>
                                    <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 12px 0 10px 0; line-height: 1.4;">{{ $article->title }}</h3>
                                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 0;">{{ $article->summary }}</p>
                                </div>
                                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 12.5px; color: #94a3b8; display: flex; align-items: center; gap: 4px;"><i data-lucide="calendar" style="width: 14px; height: 14px;"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '21/07/2026' }}</span>
                                    <a href="{{ route('vaccine.index') }}" style="color: var(--primary-color); font-weight: 700; font-size: 13px; text-decoration: none;">Đọc chi tiết →</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 8. Khám Phá Mạng Lưới Chi Nhánh Trung Tâm -->
        <section class="contact-map-section" id="contact-section" style="padding: 60px 0; background-color: var(--bg-card); border-top: 1px solid var(--border-color);">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 48px; border-radius: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 32px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    <div style="max-width: 650px;">
                        <span style="background-color: var(--primary-color); color: #ffffff; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 16px;">Mạng Lưới Chi Nhánh</span>
                        <h2 style="font-family: 'Roboto', sans-serif; font-size: 32px; font-weight: 800; margin-top: 0; margin-bottom: 16px; color: #ffffff;">Phục Vụ Quý Khách Tại 2 Chi Nhánh Trung Tâm</h2>
                        <p style="color: #94a3b8; font-size: 16px; line-height: 1.7; margin: 0;">Hệ thống tiêm chủng Medicare sẵn sàng phục vụ tại <strong>Chi nhánh 1 (Medicare Cờ Đỏ)</strong> và <strong>Chi nhánh 2 (Medicare Thới Lai)</strong> với đầy đủ 40 loại vắc xin chất lượng cao.</p>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 16px; min-width: 220px;">
                        <a href="{{ route('contact') }}" class="btn-primary" style="padding: 14px 28px; text-align: center; justify-content: center; font-size: 15px; font-weight: 700;">
                            <i data-lucide="map-pin"></i> Xem địa chỉ & bản đồ chi nhánh
                        </a>
                        <a href="{{ route('register.show') }}" style="color: #94a3b8; text-align: center; text-decoration: none; font-size: 14px; font-weight: 600;">
                            Đăng ký tiêm chọn chi nhánh →
                        </a>
                </div>
            </div>
        </section>
    </div>
@endsection
