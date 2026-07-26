@extends('vaccine::layouts.app')

@section('title', $vaccine->name . ' (Chính Hãng) - Thông Tin Vắc Xin - Medicare Cờ Đỏ')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vaccines.css') }}">
@endsection

@section('content')
<div class="news-catalog-page vaccine-detail-page">
    <!-- Breadcrumb Standard -->
    <div class="news-breadcrumb" data-aos="fade-down">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('vaccine.index') }}">Danh mục vắc xin</a>
        <i data-lucide="chevron-right"></i>
        <span>{{ $vaccine->name }}</span>
    </div>

    <!-- Main Detail Header 2-Column Card -->
    <div class="article-main-content" style="flex: 1 1 100%; margin-bottom: 32px;" data-aos="fade-up">
        <div class="vaccine-detail-grid" style="display: grid; grid-template-columns: minmax(0, 380px) minmax(0, 1fr); gap: 32px; align-items: start;">
            <!-- Left Media Column -->
            <div class="vaccine-detail-media" style="position: relative; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc; height: 360px;">
                <span class="news-category-badge" style="position: absolute; top: 16px; left: 16px; z-index: 2; padding: 6px 14px; font-size: 12px; background-color: {{ $vaccine->type === 'package' ? '#fff9e6' : 'var(--primary-color, #c8102e)' }}; color: {{ $vaccine->type === 'package' ? '#d49800' : '#ffffff' }}; border: {{ $vaccine->type === 'package' ? '1px solid rgba(234, 170, 0, 0.4)' : 'none' }};">
                    {{ $vaccine->type === 'package' ? 'Gói vắc xin trọn gói' : 'Vắc xin lẻ chính hãng' }}
                </span>
                <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')) }}" alt="{{ $vaccine->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';">
            </div>

            <!-- Right Summary Info Column -->
            <div class="vaccine-detail-info" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                <div>
                    <h1 class="article-detail-title" style="margin-top: 0; font-size: 28px; line-height: 1.3;">{{ $vaccine->name }}</h1>
                    
                    <p style="font-size: 15px; color: #475569; line-height: 1.65; margin-bottom: 20px; text-align: justify;">
                        {{ $vaccine->description ?: 'Vắc xin chính hãng 100% nhập khẩu bảo quản theo tiêu chuẩn Dây chuyền lạnh GSP (2 - 8°C) nghiêm ngặt tại hệ thống Trung tâm Tiêm chủng Medicare Cờ Đỏ.' }}
                    </p>

                    <!-- Specs Table Grid -->
                    <div class="vaccine-specs-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: #f8fafc; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="shield-alert" style="width: 13px; height: 13px; vertical-align: middle;"></i> Phòng ngừa bệnh
                            </span>
                            <strong style="font-size: 14px; color: #0f172a; display: block; text-align: justify;">{{ $vaccine->disease_prevention ?: 'Cúm mùa, Bệnh truyền nhiễm' }}</strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="globe" style="width: 13px; height: 13px; vertical-align: middle;"></i> Nhà sản xuất (Xuất xứ)
                            </span>
                            <strong style="font-size: 14px; color: #0f172a;">{{ $vaccine->manufacturer ?: 'Sanofi' }} ({{ $vaccine->origin ?: 'Pháp' }})</strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="users" style="width: 13px; height: 13px; vertical-align: middle;"></i> Đối tượng chỉ định
                            </span>
                            <strong style="font-size: 14px; color: #0f172a;">{{ $vaccine->age_group ?: 'Trẻ từ 6 tháng tuổi & Người lớn' }}</strong>
                        </div>
                        <div>
                            <span style="display: block; font-size: 11.5px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
                                <i data-lucide="syringe" style="width: 13px; height: 13px; vertical-align: middle;"></i> Đường tiêm & Phác đồ
                            </span>
                            <strong style="font-size: 14px; color: #0f172a;">Tiêm bắp ({{ $vaccine->doses ?: 1 }} mũi tiêm)</strong>
                        </div>
                    </div>
                </div>

                <!-- Price and Action Buttons -->
                <div class="vaccine-action-bar" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-top: 16px; border-top: 1px dashed #cbd5e1;">
                    <div>
                        <span style="display: block; font-size: 12.5px; color: #64748b;">Giá tiêm lẻ niêm yết:</span>
                        <strong style="font-size: 28px; color: var(--primary-color, #c8102e); font-weight: 800;">{{ number_format($vaccine->price, 0, ',', '.') }} đ</strong>
                    </div>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="{{ route('vaccine.index') }}" class="btn-secondary" style="padding: 10px 18px; border-radius: 30px; border: 1px solid #cbd5e1; color: #475569; font-weight: 700; font-size: 13.5px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: #ffffff; transition: all 0.2s ease;">
                            <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                            <span>Quay lại danh mục</span>
                        </a>
                        <button class="btn-select-detail {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCartDetail({{ $vaccine->id }})" style="padding: 10px 24px; border-radius: 30px; border: none; color: #ffffff; font-weight: 800; font-size: 14.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; background-color: {{ isset($cart[$vaccine->id]) ? 'var(--secondary-color, #eaaa00)' : 'var(--primary-color, #c8102e)' }};">
                            <i data-lucide="{{ isset($cart[$vaccine->id]) ? 'check' : 'plus' }}" style="width: 17px; height: 17px;"></i>
                            <span>{{ isset($cart[$vaccine->id]) ? 'Đã chọn vắc xin' : 'Đăng ký tiêm chủng' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Medical Details Layout (Sidebar TOC + Main Detailed Content) -->
    <div class="vaccine-detail-layout" data-aos="fade-up">
        <!-- Sidebar Column (25% Width): Unified Sticky Container (top: 100px) -->
        <aside class="article-sidebar" style="flex: 0 0 calc(25% - 16px);">
            <div class="sticky-sidebar-container">
                <!-- TOC Widget -->
                <div class="vaccine-toc-widget">
                    <div class="widget-title">
                        <i data-lucide="list" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
                        Mục Lục Nội Dung
                    </div>
                    <nav style="display: flex; flex-direction: column;">
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
                            <i data-lucide="chevron-right"></i> 4. Thận trọng & Thai kỳ
                        </a>
                        <a href="#sec-phan-ung" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 5. Phản ứng sau tiêm
                        </a>
                        <a href="#sec-bao-quan" class="toc-link-item">
                            <i data-lucide="chevron-right"></i> 6. Bảo quản Dây chuyền lạnh
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

        <!-- Main Content Column (75% Width): Clean Natural Dynamic HTML Body (Support Markdown/AI Output) -->
        <main class="article-main-content" style="flex: 0 0 calc(75% - 16px);">
            <!-- Section 1: Thông tin vắc xin -->
            <section id="sec-thong-tin" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    1. Thông tin vắc xin & Nhà sản xuất
                </h2>
                <div class="article-body-content">
                    <p style="text-align: justify;">Vắc xin <strong>{{ $vaccine->name }}</strong> là dòng vắc xin cúm tam giá thế hệ mới, được chỉ định tiêm phòng cho trẻ em từ 6 tháng tuổi trở lên và người lớn nhằm ngăn ngừa hiệu quả 3 chủng virus cúm mùa phổ biến bao gồm: 2 chủng cúm A (A/H1N1, A/H3N2) và 1 dòng cúm B (B/Victoria).</p>
                    
                    <p style="text-align: justify;">So với các dòng vắc xin cúm trước đây, sản phẩm đã cập nhật loại bỏ chủng cúm B/Yamagata theo đúng khuyến cáo mới nhất của Tổ chức Y tế Thế giới (WHO), do chủng virus này đã không còn lưu hành tự nhiên trên toàn cầu.</p>

                    <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 20px 0 10px 0;">Nhà sản xuất & Nguồn gốc</h3>
                    <p style="text-align: justify;">Vắc xin được nghiên cứu, phát triển và sản xuất bởi Tập đoàn Dược phẩm hàng đầu thế giới <strong>{{ $vaccine->manufacturer ?: 'Sanofi' }} ({{ $vaccine->origin ?: 'Pháp' }})</strong>. Toàn bộ các lô vắc xin nhập khẩu về Việt Nam đều trải qua kiểm định nghiêm ngặt của Viện Kiểm định Quốc gia Vắc xin và Sinh phẩm Y tế trước khi đưa vào sử dụng tại hệ thống Medicare Cờ Đỏ.</p>

                    <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 20px 0 10px 0;">Đường tiêm & Chống chỉ định</h3>
                    <p style="text-align: justify;">Vắc xin được sử dụng theo đường <strong>tiêm bắp (ưu tiên)</strong> hoặc tiêm dưới da. Chống chỉ định tiêm đối với người có tiền sử phản ứng quá mẫn nặng với bất kỳ thành phần nào của vắc xin, hoặc người đang có tình trạng sốt cao cấp tính (cần hoãn tiêm cho đến khi hết sốt).</p>
                </div>
            </section>

            <!-- Section 2: Đối tượng chỉ định -->
            <section id="sec-doi-tuong" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    2. Đối tượng chỉ định tiêm chủng
                </h2>
                <div class="article-body-content">
                    <p style="text-align: justify;">Vắc xin <strong>{{ $vaccine->name }}</strong> được khuyến cáo tiêm phòng rộng rãi cho các nhóm đối tượng sau:</p>
                    <ul style="padding-left: 20px; line-height: 1.8; color: #334155; margin-bottom: 20px;">
                        <li style="text-align: justify;"><strong>Trẻ em từ 6 tháng tuổi trở lên</strong>: Giúp hệ miễn dịch còn non nớt của trẻ tạo kháng thể chủ động phòng ngừa các biến chứng nguy hiểm của cúm như viêm phổi, viêm tai giữa.</li>
                        <li style="text-align: justify;"><strong>Người lớn & Người cao tuổi (trên 65 tuổi)</strong>: Giảm thiểu nguy cơ nhập viện và tử vong do biến chứng tim mạch, hô hấp khi mắc cúm.</li>
                        <li style="text-align: justify;"><strong>Phụ nữ chuẩn bị mang thai & Đang mang thai</strong>: Bảo vệ an toàn cho cả mẹ và thai nhi khỏi nguy cơ dị tật hoặc sinh nhẹ cân do cúm mùa.</li>
                        <li style="text-align: justify;"><strong>Người có bệnh nền mạn tính</strong>: Bệnh nhân tiểu đường, hen suyễn, bệnh tim mạch, suy giảm miễn dịch cần tiêm phòng hằng năm.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 3: Phác đồ & Lịch tiêm (100% Pure Clean HTML Typography - No Artificial Gray Box) -->
            <section id="sec-phac-do" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    3. Phác đồ & Lịch tiêm chi tiết
                </h2>
                <div class="article-body-content">
                    <p style="text-align: justify;">Lịch tiêm vắc xin <strong>{{ $vaccine->name }}</strong> được phân chia cụ thể theo độ tuổi như sau:</p>
                    
                    <h3 style="font-size: 17px; font-weight: 700; color: var(--primary-color, #c8102e); margin: 20px 0 10px 0;">
                        A. Trẻ em từ 6 tháng tuổi đến dưới 9 tuổi (Chưa từng tiêm cúm):
                    </h3>
                    <ul style="padding-left: 22px; margin-bottom: 20px; line-height: 1.8; color: #334155;">
                        <li style="text-align: justify;"><strong>Mũi 1</strong>: Lần tiêm đầu tiên.</li>
                        <li style="text-align: justify;"><strong>Mũi 2</strong>: Tiêm cách Mũi 1 tối thiểu <strong>4 tuần</strong> (28 ngày).</li>
                        <li style="text-align: justify;"><strong>Tiêm nhắc lại</strong>: Tiêm 1 mũi hằng năm vào trước mùa dịch cúm.</li>
                    </ul>

                    <h3 style="font-size: 17px; font-weight: 700; color: var(--accent-color, #004b8f); margin: 20px 0 10px 0;">
                        B. Trẻ từ 9 tuổi trở lên & Người lớn:
                    </h3>
                    <ul style="padding-left: 22px; margin-bottom: 20px; line-height: 1.8; color: #334155;">
                        <li style="text-align: justify;"><strong>Mũi cơ bản</strong>: Tiêm <strong>1 mũi duy nhất</strong>.</li>
                        <li style="text-align: justify;"><strong>Tiêm nhắc lại</strong>: Tiêm nhắc <strong>1 mũi hằng năm</strong> để duy trì lượng kháng thể bảo vệ cao nhất trước sự biến đổi chủng virus cúm.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 4: Thận trọng & Thai kỳ -->
            <section id="sec-than-trong" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    4. Thận trọng khi sử dụng & Khuyến cáo thai kỳ
                </h2>
                <div class="article-body-content">
                    <p style="text-align: justify;"><strong>Phụ nữ mang thai & Cho con bú:</strong> Phụ nữ mang thai là đối tượng dễ gặp biến chứng nặng khi mắc cúm. Vắc xin <strong>{{ $vaccine->name }}</strong> là vắc xin bất hoạt nên an toàn sử dụng cho thai phụ ở mọi giai đoạn thai kỳ (3 tháng đầu, 3 tháng giữa và 3 tháng cuối) cũng như phụ nữ đang cho con bú. Việc tiêm phòng cho mẹ còn giúp truyền kháng thể thụ động bảo vệ trẻ sơ sinh trong 6 tháng đầu đời.</p>

                    <p style="text-align: justify;"><strong>Rối loạn đông máu:</strong> Người bị giảm tiểu cầu hoặc rối loạn đông máu cần thông báo cho bác sĩ để được đánh giá cẩn thận trước khi tiêm bắp nhằm tránh nguy cơ chảy máu tại chỗ tiêm.</p>
                </div>
            </section>

            <!-- Section 5: Phản ứng sau tiêm -->
            <section id="sec-phan-ung" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    5. Phản ứng phụ có thể xảy ra sau tiêm & Hướng dẫn theo dõi
                </h2>
                <div class="article-body-content">
                    <p style="text-align: justify;">Tương tự như tất cả các vắc xin khác, vắc xin <strong>{{ $vaccine->name }}</strong> có thể gây ra một số phản ứng nhẹ sau tiêm. Đây là dấu hiệu bình thường phản ánh hệ miễn dịch của cơ thể đang chủ động tạo kháng thể:</p>

                    <ul style="padding-left: 20px; line-height: 1.8; color: #334155; margin-bottom: 20px;">
                        <li style="text-align: justify;"><strong>Phản ứng tại chỗ tiêm</strong>: Sưng nhẹ, đỏ hoặc đau tại vị trí tiêm (tự hết sau 1-2 ngày).</li>
                        <li style="text-align: justify;"><strong>Phản ứng toàn thân nhẹ</strong>: Sốt nhẹ, mệt mỏi, đau đầu hoặc đau cơ nhẹ. Khách hàng có thể uống thuốc hạ sốt paracetamol theo chỉ dẫn của bác sĩ nếu sốt trên 38.5°C.</li>
                    </ul>

                    <p style="text-align: justify;"><strong>Theo dõi sau tiêm:</strong> Người tiêm chủng ở lại theo dõi ít nhất 30 phút tại trung tâm Medicare Cờ Đỏ. Sau khi về nhà, tiếp tục theo dõi sức khỏe trong vòng 24-48 giờ tiếp theo. Nếu có bất kỳ dấu hiệu bất thường như sốt cao không hạ, khó thở, vội liên hệ ngay Hotline 0938 60 38 39.</p>
                </div>
            </section>

            <!-- Section 6: Bảo quản GSP -->
            <section id="sec-bao-quan" style="margin-bottom: 36px;">
                <h2 style="font-size: 21px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-color, #c8102e);">
                    6. Bảo quản tiêu chuẩn Dây chuyền lạnh GSP
                </h2>
                <div class="article-body-content">
                    <p style="text-align: justify;">Vắc xin <strong>{{ $vaccine->name }}</strong> được bảo quản nghiêm ngặt trong hệ thống kho lạnh đạt chuẩn GSP quốc tế tại tất cả các chi nhánh Medicare Cờ Đỏ với nhiệt độ ổn định từ <strong>2°C đến 8°C</strong>, không được làm đông băng và luôn được bảo vệ khỏi ánh sáng trực tiếp.</p>
                </div>
            </section>

            <!-- Author Signature at Bottom Right -->
            <div class="article-author-signature">
                <span>Theo Bác sĩ Chuyên khoa Medicare Cờ Đỏ</span>
            </div>
        </main>
    </div>

    <!-- Related Vaccine Products Slider (Sản phẩm vắc xin liên quan có nút mũi tên điều hướng) -->
    @if(isset($relatedVaccines) && $relatedVaccines->isNotEmpty())
        <section class="suggested-news-section" data-aos="fade-up" style="margin-top: 40px; margin-bottom: 40px;">
            <div class="suggested-news-header" style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h2>
                        <i data-lucide="package" style="width: 22px; height: 22px; color: var(--primary-color, #c8102e);"></i>
                        Vắc Xin Liên Quan Cùng Phòng Ngừa Nhóm Bệnh
                    </h2>
                    <p style="text-align: justify; margin-top: 4px;">Tham khảo các sản phẩm vắc xin tương tự đang có sẵn tại hệ thống Medicare Cờ Đỏ.</p>
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
                                <button type="button" onclick="setDiseaseFilter(@js($relVac->disease_prevention), event)" class="catalog-product-disease" style="text-align: justify;">
                                    {{ $relVac->disease_prevention }}
                                </button>
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

    // Toggle chọn vắc xin ngay tại trang chi tiết
    function toggleCartDetail(vaccineId) {
        const btn = document.querySelector('.btn-select-detail');
        const isSelected = btn.classList.contains('btn-selected');
        const url = isSelected ? "{{ route('cart.remove') }}" : "{{ route('cart.add') }}";
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ vaccine_id: vaccineId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (isSelected) {
                    btn.classList.remove('btn-selected');
                    btn.style.backgroundColor = 'var(--primary-color, #c8102e)';
                    btn.innerHTML = '<i data-lucide="plus" style="width: 17px; height: 17px;"></i> <span>Đăng ký tiêm chủng</span>';
                } else {
                    btn.classList.add('btn-selected');
                    btn.style.backgroundColor = 'var(--secondary-color, #eaaa00)';
                    btn.innerHTML = '<i data-lucide="check" style="width: 17px; height: 17px;"></i> <span>Đã chọn vắc xin</span>';
                }
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        })
        .catch(err => console.error('Cart update error:', err));
    }
</script>
@endsection
