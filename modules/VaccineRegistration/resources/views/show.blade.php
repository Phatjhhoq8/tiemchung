@extends('vaccine::layouts.app')

@section('title', $vaccine->name . ' (Chính Hãng) - Thông Tin Vắc Xin - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vaccines.css') }}">
@endsection

@section('content')
<div class="news-catalog-page vaccine-detail-page">
    <!-- Breadcrumb Standard (Sleek Modern Medical Style) -->
    <nav class="news-breadcrumb-bar" data-aos="fade-down" aria-label="Đường dẫn điều hướng">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-separator"><i data-lucide="chevron-right"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('vaccine.index') }}">Danh mục vắc xin</a></li>
            <li class="breadcrumb-separator"><i data-lucide="chevron-right"></i></li>
            <li class="breadcrumb-item active" aria-current="page"><span>{{ $vaccine->name }}</span></li>
        </ol>
    </nav>

    <!-- Main Detail Header 2-Column Card -->
    <div class="vaccine-header-card" style="flex: 1 1 100%; margin-bottom: 32px;" data-aos="fade-up">
        <div class="vaccine-detail-grid" style="display: grid; grid-template-columns: minmax(0, 380px) minmax(0, 1fr); gap: 32px; align-items: start;">
            <!-- Left Media Column -->
            <div class="vaccine-detail-media" style="position: relative; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc; height: 360px;">
                <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')) }}" alt="{{ $vaccine->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';">
            </div>

            <!-- Right Summary Info Column -->
            <div class="vaccine-detail-info" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                <div>
                    <h1 class="article-detail-title" style="margin-top: 0; font-size: 28px; line-height: 1.3;">{{ $vaccine->name }}</h1>
                    
                    <p style="font-size: 15px; color: #475569; line-height: 1.65; margin-bottom: 20px; text-align: justify;">
                        {{ $vaccine->description ?: 'Thông tin mô tả chi tiết của sản phẩm chưa được cập nhật từ nguồn đã xác minh.' }}
                    </p>

                    <!-- Specs Table Grid -->
                    <div class="vaccine-specs-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: #f8fafc; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="shield-alert" style="width: 13px; height: 13px; vertical-align: middle;"></i> Phòng ngừa bệnh
                            </span>
                            @if($vaccine->disease_prevention)
                                <a href="{{ route('vaccine.index', ['disease' => $vaccine->disease_prevention]) }}" style="font-size: 14px; color: #0f172a; display: block; text-align: justify; font-weight: 700;">{{ $vaccine->disease_prevention }}</a>
                            @else
                                <span style="font-size: 14px; color: #64748b;">Chưa có dữ liệu xác minh</span>
                            @endif
                        </div>
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="globe" style="width: 13px; height: 13px; vertical-align: middle;"></i> Nhà sản xuất (Xuất xứ)
                            </span>
                            @if($vaccine->manufacturer || $vaccine->origin)
                                <strong style="font-size: 14px; color: #0f172a;">{{ collect([$vaccine->manufacturer, $vaccine->origin])->filter()->implode(' - ') }}</strong>
                            @else
                                <span style="font-size: 14px; color: #64748b;">Chưa có dữ liệu xác minh</span>
                            @endif
                        </div>
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="users" style="width: 13px; height: 13px; vertical-align: middle;"></i> Đối tượng chỉ định
                            </span>
                            <strong style="font-size: 14px; color: #0f172a;">{{ $vaccine->age_group ?: 'Cần được nhân viên y tế tư vấn theo độ tuổi.' }}</strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="syringe" style="width: 13px; height: 13px; vertical-align: middle;"></i> Đường dùng
                            </span>
                            <strong style="font-size: 14px; color: #0f172a;">{{ $vaccine->administration_route ?: 'Cần được xác nhận khi khám sàng lọc.' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Price and Action Buttons -->
                <div class="vaccine-action-bar" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-top: 16px; border-top: 1px dashed #cbd5e1;">
                    <div>
                        <span style="display: block; font-size: 12.5px; color: #64748b;">Giá tại chi nhánh đang chọn:</span>
                        @if($vaccine->hasSalePrice())
                            <span style="display:block; color:#64748b; text-decoration:line-through; font-size:13px;">{{ number_format($vaccine->price, 0, ',', '.') }} đ</span>
                        @endif
                        <strong style="font-size: 28px; color: var(--primary-color, #c8102e); font-weight: 800;">{{ number_format($vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price, 0, ',', '.') }} đ</strong>
                    </div>

                    <div class="vaccine-action-buttons" style="display: flex; gap: 10px; align-items: center;">
                        <a href="{{ route('vaccine.index') }}" class="btn-secondary desktop-only-back-btn" style="padding: 10px 18px; border-radius: 30px; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #ffffff; transition: all 0.2s ease;">
                            <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                            <span>Quay lại danh mục</span>
                        </a>
                        <button class="btn-select-detail {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" data-id="{{ $vaccine->id }}" onclick="toggleCart({{ $vaccine->id }})" {{ $vaccine->stock_quantity <= 0 ? 'disabled' : '' }} style="padding: 12px 24px; border-radius: 30px; border: none; color: #ffffff; font-weight: 800; font-size: 15px; cursor: {{ $vaccine->stock_quantity <= 0 ? 'not-allowed' : 'pointer' }}; opacity:{{ $vaccine->stock_quantity <= 0 ? '.55' : '1' }}; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease; background-color: {{ isset($cart[$vaccine->id]) ? 'var(--secondary-color, #eaaa00)' : 'var(--primary-color, #c8102e)' }}; box-shadow: 0 4px 15px rgba(200, 16, 46, 0.22);">
                            <i data-lucide="{{ isset($cart[$vaccine->id]) ? 'check' : 'plus' }}" style="width: 18px; height: 18px;"></i>
                            <span>{{ $vaccine->stock_quantity <= 0 ? 'Hết hàng tại chi nhánh' : (isset($cart[$vaccine->id]) ? 'Đã chọn vắc xin' : 'Đăng ký tiêm chủng') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Medical Details Layout (Left Big Main Content 75% + Right Small Sidebar 25%) -->
    <div class="vaccine-detail-layout" data-aos="fade-up">
        <!-- Main Content Column (75% Width - Left Big Column) -->
        <main class="article-main-content" style="flex: 0 0 calc(75% - 16px);">

            <!-- Mobile Top TOC (Collapsible Accordion identical to News Detail page) -->
            <div class="vaccine-toc-widget mobile-article-toc" id="mobileAutoTocWidget">
                <div class="widget-title" onclick="toggleMobileTocAccordion(event)" style="cursor: pointer; justify-content: space-between; display: flex; align-items: center;">
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="list" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        <span>Mục Lục Nội Dung</span>
                    </span>
                    <i data-lucide="chevron-down" id="mobileTocChevronIcon" style="width: 16px; height: 16px; color: #64748b; transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);"></i>
                </div>
                <nav style="display: none; flex-direction: column;" id="mobileAutoTocNav">
                    <!-- Dynamic links generated via JS -->
                </nav>
            </div>

            <!-- Section 1: Thông tin vắc xin -->
            <section id="sec-thong-tin" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    1. Thông tin vắc xin & Nhà sản xuất
                </h2>
                <div class="article-body-content">
                    @if($vaccine->description)
                        <p style="text-align: justify; white-space: pre-line;">{{ $vaccine->description }}</p>
                    @else
                        <p>Thông tin mô tả chi tiết của sản phẩm chưa được cập nhật từ nguồn đã xác minh.</p>
                    @endif

                    @if($vaccine->manufacturer || $vaccine->origin || $vaccine->dosage)
                        <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 20px 0 10px 0;">Thông tin sản phẩm</h3>
                        <ul style="padding-left: 20px; line-height: 1.8; color: #334155;">
                            @if($vaccine->manufacturer)<li><strong>Nhà sản xuất:</strong> {{ $vaccine->manufacturer }}</li>@endif
                            @if($vaccine->origin)<li><strong>Xuất xứ:</strong> {{ $vaccine->origin }}</li>@endif
                            @if($vaccine->dosage)<li><strong>Quy cách:</strong> {{ $vaccine->dosage }}</li>@endif
                        </ul>
                    @endif
                </div>
            </section>

            <!-- Section 2: Đối tượng chỉ định -->
            <section id="sec-doi-tuong" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    2. Đối tượng chỉ định tiêm chủng
                </h2>
                <div class="article-body-content">
                    @if($vaccine->age_group)
                        <p style="text-align: justify;"><strong>Độ tuổi / đối tượng ghi nhận:</strong> {{ $vaccine->age_group }}</p>
                    @else
                        <p>Đối tượng sử dụng chưa được cập nhật từ nguồn đã xác minh. Vui lòng khám sàng lọc để được tư vấn.</p>
                    @endif
                </div>
            </section>

            <!-- Section 3: Phác đồ & Lịch tiêm -->
            <section id="sec-phac-do" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    3. Phác đồ & Lịch tiêm chi tiết
                </h2>
                <div class="article-body-content">
                    @if($vaccine->administration_route)
                        <p style="text-align: justify;"><strong>Đường dùng:</strong> {{ $vaccine->administration_route }}</p>
                    @endif
                    @if($vaccine->detailed_schedule)
                        <p style="text-align: justify; white-space: pre-line;">{{ $vaccine->detailed_schedule }}</p>
                    @else
                        <p>Phác đồ chi tiết chưa được cập nhật từ nguồn đã xác minh. Lịch tiêm cần được nhân viên y tế xác nhận theo độ tuổi và lịch sử tiêm chủng.</p>
                    @endif
                </div>
            </section>

            <!-- Section 4: Chống chỉ định và cảnh báo -->
            <section id="sec-than-trong" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    4. Chống chỉ định & Cảnh báo
                </h2>
                <div class="article-body-content">
                    @if($vaccine->contraindications)
                        <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 10px;">Chống chỉ định</h3>
                        <p style="text-align: justify; white-space: pre-line;">{{ $vaccine->contraindications }}</p>
                    @endif
                    @if($vaccine->warnings)
                        <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 20px 0 10px;">Cảnh báo và thận trọng</h3>
                        <p style="text-align: justify; white-space: pre-line;">{{ $vaccine->warnings }}</p>
                    @endif
                    @if(!$vaccine->contraindications && !$vaccine->warnings)
                        <p>Thông tin chống chỉ định và cảnh báo của sản phẩm chưa được cập nhật từ nguồn đã xác minh. Cần khai báo đầy đủ tình trạng sức khỏe khi khám sàng lọc.</p>
                    @endif
                </div>
            </section>

            <!-- Section 5: Phản ứng bất lợi -->
            <section id="sec-phan-ung" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    5. Phản ứng bất lợi & Theo dõi sau tiêm
                </h2>
                <div class="article-body-content">
                    @if($vaccine->adverse_effects)
                        <p style="text-align: justify; white-space: pre-line;">{{ $vaccine->adverse_effects }}</p>
                    @else
                        <p>Thông tin phản ứng bất lợi của sản phẩm chưa được cập nhật từ nguồn đã xác minh. Vui lòng tuân thủ hướng dẫn theo dõi sau tiêm của nhân viên y tế.</p>
                    @endif
                </div>
            </section>

            <section id="sec-nguon" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    6. Nguồn tham khảo
                </h2>
                <div class="article-body-content">
                    @if($vaccine->source_reference_url)
                        <p><a href="{{ $vaccine->source_reference_url }}" target="_blank" rel="noopener noreferrer nofollow">Xem nguồn / tài liệu tham khảo của sản phẩm</a></p>
                    @else
                        <p>Chưa có liên kết nguồn công khai cho nội dung chuyên môn chi tiết.</p>
                    @endif
                    @if($vaccine->source_review_date)
                        <p><strong>Ngày rà soát nguồn:</strong> {{ $vaccine->source_review_date->format('d/m/Y') }}</p>
                    @endif
                </div>
            </section>
        </main>

        <!-- Right Sidebar Column (25% Width - Right Small Sticky Column) -->
        <aside class="article-sidebar" style="flex: 0 0 calc(25% - 16px);">
            <div class="sticky-sidebar-container">
                <!-- TOC Widget -->
                <div class="vaccine-toc-widget" id="autoTocWidget">
                    <div class="widget-title">
                        <i data-lucide="list" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        Mục Lục Nội Dung
                    </div>
                    <nav style="display: flex; flex-direction: column;" id="vaccineTocNav">
                        <a href="#sec-thong-tin" class="toc-link-item active">
                            <i data-lucide="chevron-right"></i> 1. Thông tin vắc xin
                        </a>
                        <a href="#sec-doi-tuong" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 2. Đối tượng chỉ định
                        </a>
                        <a href="#sec-phac-do" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 3. Phác đồ & Lịch tiêm
                        </a>
                        <a href="#sec-than-trong" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 4. Chống chỉ định & Cảnh báo
                        </a>
                        <a href="#sec-phan-ung" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 5. Phản ứng sau tiêm
                        </a>
                        <a href="#sec-nguon" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 6. Nguồn tham khảo
                        </a>
                    </nav>
                </div>

                <!-- Callout CTA Widget -->
                <div class="sidebar-cta-widget">
                    <i data-lucide="headset" class="cta-widget-icon"></i>
                    <h3>Tư Vấn Y Khoa 24/7</h3>
                    <p>Đội ngũ bác sĩ chuyên khoa Medicare luôn sẵn sàng hỗ trợ tra cứu lịch tiêm và tư vấn phác đồ phù hợp cho gia đình bạn.</p>
                    <a href="tel:0938603839" class="cta-widget-btn">Hotline: 0938 60 38 39</a>
                </div>
            </div>
        </aside>
    </div>

    <!-- Related Vaccine Products Slider (Isolated White Card Section 100% Width) -->
    @if(isset($relatedVaccines) && $relatedVaccines->isNotEmpty())
        <section class="suggested-news-section" data-aos="fade-up">
            <div class="suggested-news-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <div>
                    <h2>
                        <i data-lucide="package" style="width: 22px; height: 22px; color: var(--primary-color, #c8102e);"></i>
                        Vắc Xin Tham Khảo Khác
                    </h2>
                    <p style="text-align: justify; margin-top: 4px; color: #64748b;">Xem các sản phẩm đang có tại chi nhánh được chọn.</p>
                </div>

                <!-- Slider Navigation Arrow Buttons (< and >) -->
                <div style="display: flex; gap: 8px; align-items: center; flex-shrink: 0;">
                    <button type="button" class="slider-nav-btn" onclick="scrollRelatedSlider(-1)" title="Xem trước">
                        <i data-lucide="chevron-left" style="width: 18px; height: 18px;"></i>
                    </button>
                    <button type="button" class="slider-nav-btn" onclick="scrollRelatedSlider(1)" title="Xem tiếp">
                        <i data-lucide="chevron-right" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>

            <!-- Horizontal Smooth Scroll Container -->
            <div class="related-slider-container" id="relatedVaccineSlider">
                @foreach($relatedVaccines as $relVac)
                    @php
                        $relPrice = $relVac->hasSalePrice() ? $relVac->sale_price : $relVac->price;
                    @endphp
                    <div class="related-slider-card">
                        <article class="catalog-product-card {{ isset($cart[$relVac->id]) ? 'selected' : '' }}" data-id="{{ $relVac->id }}" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                            <a href="{{ route('vaccine.show', $relVac->id) }}" class="catalog-product-media" style="display: block; text-decoration: none;">
                                <span class="origin-badge"><i data-lucide="map-pin"></i>{{ $relVac->origin ?: 'Chính hãng' }}</span>
                                <img src="{{ asset('images/vaccines/' . ($relVac->image ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="{{ $relVac->name }}" loading="lazy">
                            </a>

                            <div class="catalog-product-body">
                                <a href="{{ route('vaccine.show', $relVac->id) }}" class="catalog-product-title" style="display: block; text-decoration: none; text-align: justify;">
                                    {{ $relVac->name }}
                                </a>
                                <a href="{{ route('vaccine.index', ['disease' => $relVac->disease_prevention]) }}" class="catalog-product-disease" style="display: block; text-align: justify; text-decoration: none;">
                                    {{ $relVac->disease_prevention }}
                                </a>
                                <div class="catalog-product-meta">
                                    <span><i data-lucide="syringe"></i>{{ $relVac->doses ?: 1 }} liều</span>
                                    @if($relVac->manufacturer)
                                        <span><i data-lucide="factory"></i>{{ $relVac->manufacturer }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="catalog-product-footer" style="display: flex; flex-direction: column; gap: 10px; align-items: stretch; width: 100%;">
                                <div class="catalog-price-block" style="display: flex; align-items: baseline; gap: 4px;">
                                    <strong style="font-size: 18px; color: var(--primary-color, #c8102e); font-weight: 800;">{{ number_format($relPrice, 0, ',', '.') }}đ</strong>
                                    <span style="font-size: 12px; color: #64748b;">/ liều</span>
                                </div>
                                <div class="catalog-action-group" style="display: flex; gap: 8px; align-items: center; width: 100%;">
                                    <a href="{{ route('vaccine.show', $relVac->id) }}" class="btn-detail-link" style="flex: 1; text-align: center; padding: 7px 10px; border-radius: 20px; border: 1px solid var(--primary-color, #c8102e); color: var(--primary-color, #c8102e); font-size: 12.5px; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap;">
                                        Xem chi tiết
                                    </a>
                                    <button class="btn-select-vaccine {{ isset($cart[$relVac->id]) ? 'btn-selected' : '' }}" onclick="toggleCart({{ $relVac->id }})" style="flex: 1; text-align: center; padding: 7px 10px; justify-content: center; display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; white-space: nowrap;">
                                        <i data-lucide="{{ isset($cart[$relVac->id]) ? 'x' : 'plus' }}"></i>
                                        <span>{{ isset($cart[$relVac->id]) ? 'Hủy chọn' : 'Chọn tiêm' }}</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Bottom Advice Banner -->
    <section class="catalog-advice-banner" style="margin-top: 40px;" data-aos="fade-up">
        <div>
            <span>Tư vấn tiêm chủng trực tiếp</span>
            <h2>Bạn cần tư vấn thêm về phác đồ vắc xin {{ $vaccine->name }}?</h2>
            <p style="text-align: justify;">Liên hệ ngay đội ngũ bác sĩ chuyên khoa Medicare Cờ Đỏ để được hỗ trợ khám sàng lọc và đặt lịch hẹn ưu tiên.</p>
        </div>
        <a href="tel:0938603839" class="catalog-advice-btn">
            Gọi Hotline 0938 60 38 39 <i data-lucide="phone-call"></i>
        </a>
    </section>
</div>
@endsection

@section('scripts')
<script>
    // Hàm cuộn trượt danh sách vắc xin liên quan khi bấm mũi tên < và >
    function scrollRelatedSlider(direction) {
        const slider = document.getElementById('relatedVaccineSlider');
        if (!slider) return;
        const scrollAmount = 300;
        slider.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>
@endsection
