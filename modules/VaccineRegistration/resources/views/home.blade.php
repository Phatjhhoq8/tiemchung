@extends('vaccine::layouts.app')

@section('title', 'Hệ Thống Tiêm Chủng Medicare - Đăng Ký Tiêm Chủng')

@section('content')
    <div class="home-container max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-4 space-y-12">
        <!-- 1. Flowbite Hero Slider Banner (Cố định hiển thị slide đầu tiên, không bị ẩn) -->
        <section class="banner-slider relative rounded-2xl overflow-hidden shadow-xl" id="hero-banner">
            @if($banners->isEmpty())
                <div class="bg-gradient-to-r from-slate-900 via-red-950 to-slate-900 text-white rounded-2xl p-6 sm:p-8 lg:p-12 border border-red-900/40 shadow-xl overflow-hidden relative">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
                        <!-- Left Text & Actions -->
                        <div class="lg:col-span-7 space-y-6">
                            <span class="inline-block bg-[#c8102e] text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wider">
                                Hệ Thống Tiêm Chủng An Toàn
                            </span>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                                Hệ Thống Tiêm Chủng <span class="text-red-400">Medicare</span>
                            </h1>
                            <p class="text-slate-300 text-base lg:text-lg font-medium leading-relaxed max-w-xl">
                                Hệ thống trung tâm tiêm chủng vắc xin an toàn, chất lượng hàng đầu cho trẻ em và người lớn với phác đồ y khoa chuẩn GSP.
                            </p>
                            <div class="flex flex-wrap gap-4 pt-2">
                                <a href="{{ route('vaccine.index') }}" class="inline-flex items-center gap-2 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-3.5 px-7 rounded-xl shadow-md hover:shadow-red-500/25 transition-all duration-200">
                                    Xem bảng giá vắc xin <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                </a>
                                <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white hover:bg-white/20 font-bold py-3.5 px-7 rounded-xl transition-all duration-200">
                                    <i data-lucide="calendar-check-2" class="w-5 h-5"></i> Đặt lịch tiêm ngay
                                </a>
                            </div>
                        </div>

                        <!-- Right Banner Image Frame (Khớp 100% khuôn hình, hiển thị trọn vẹn) -->
                        <div class="lg:col-span-5 flex items-center justify-center">
                            <div class="w-full h-64 sm:h-80 lg:h-96 rounded-2xl overflow-hidden border-2 border-white/20 shadow-2xl bg-white/5 relative group flex items-center justify-center">
                                <img src="{{ asset('images/banners/banner_family.jpg') }}" alt="Hệ Thống Medicare" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div id="flowbite-hero-carousel" class="relative w-full" data-carousel="slide">
                    <!-- Carousel wrapper -->
                    <div class="relative min-h-[380px] sm:min-h-[420px] overflow-hidden rounded-2xl">
                        @foreach($banners as $index => $banner)
                            <div class="{{ $index === 0 ? 'block' : 'hidden' }} duration-700 ease-in-out" data-carousel-item="{{ $index === 0 ? 'active' : '' }}">
                                <div class="bg-gradient-to-r from-slate-900 via-red-950 to-slate-900 text-white rounded-2xl p-6 sm:p-8 lg:p-12 border border-red-900/40 shadow-xl overflow-hidden relative">
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
                                        <!-- Left Text & Actions -->
                                        <div class="lg:col-span-7 space-y-6">
                                            <span class="inline-block bg-[#c8102e] text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wider">
                                                Medicare Standard
                                            </span>
                                            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                                                {{ $banner->title }}
                                            </h2>
                                            <p class="text-slate-300 text-base lg:text-lg font-medium leading-relaxed max-w-xl">
                                                {{ $banner->subtitle }}
                                            </p>
                                            <div class="pt-2">
                                                <a href="{{ $banner->link_url ?: route('vaccine.index') }}" class="inline-flex items-center gap-2 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-3.5 px-7 rounded-xl shadow-md hover:shadow-red-500/25 transition-all duration-200">
                                                    Tìm hiểu thêm <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Right Banner Image Frame (Khớp 100% khuôn hình) -->
                                        <div class="lg:col-span-5 flex items-center justify-center">
                                            <div class="w-full h-64 sm:h-80 lg:h-96 rounded-2xl overflow-hidden border-2 border-white/20 shadow-2xl bg-white/5 relative group flex items-center justify-center">
                                                <img src="{{ $banner->image_path ? asset($banner->image_path) : asset('images/banners/banner_family.jpg') }}" alt="{{ $banner->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Slider indicators -->
                    <div class="absolute z-30 flex -translate-x-1/2 bottom-4 left-1/2 space-x-3 rtl:space-x-reverse">
                        @foreach($banners as $index => $banner)
                            <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-[#c8102e] transition-colors" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}" data-carousel-slide-to="{{ $index }}"></button>
                        @endforeach
                    </div>
                    <!-- Slider controls -->
                    <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/40 group-hover:bg-[#c8102e] group-hover:text-white transition-all">
                            <i data-lucide="chevron-left" class="w-6 h-6"></i>
                        </span>
                    </button>
                    <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/40 group-hover:bg-[#c8102e] group-hover:text-white transition-all">
                            <i data-lucide="chevron-right" class="w-6 h-6"></i>
                        </span>
                    </button>
                </div>
            @endif
        </section>

        <!-- 2. Flowbite Quick Action Toolbar -->
        <section class="relative z-20 -mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 bg-white p-4 rounded-2xl border border-red-100 shadow-xl">
                <a href="{{ route('vaccine.index') }}" class="flex items-center gap-4 p-4 rounded-xl bg-red-50/50 border border-red-100 hover:border-[#c8102e] hover:bg-red-50 hover:-translate-y-0.5 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-[#c8102e] text-white flex items-center justify-center shrink-0 shadow-md group-hover:scale-105 transition-transform">
                        <i data-lucide="syringe" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Đặt Mua Vắc Xin Online</div>
                        <div class="text-xs text-slate-500 font-medium">Giữ vắc xin 100%, không lo thiếu mũi</div>
                    </div>
                </a>

                <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="flex items-center gap-4 p-4 rounded-xl bg-red-50/50 border border-red-100 hover:border-[#c8102e] hover:bg-red-50 hover:-translate-y-0.5 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-[#c8102e] text-white flex items-center justify-center shrink-0 shadow-md group-hover:scale-105 transition-transform">
                        <i data-lucide="calendar-check-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Đăng Ký Tiêm Chủng</div>
                        <div class="text-xs text-slate-500 font-medium">Chọn chi nhánh & ngày giờ hẹn trước</div>
                    </div>
                </a>

                <a href="{{ route('vaccine.index') }}" class="flex items-center gap-4 p-4 rounded-xl bg-red-50/50 border border-red-100 hover:border-[#c8102e] hover:bg-red-50 hover:-translate-y-0.5 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-[#c8102e] text-white flex items-center justify-center shrink-0 shadow-md group-hover:scale-105 transition-transform">
                        <i data-lucide="badge-percent" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Bảng Giá Vắc Xin</div>
                        <div class="text-xs text-slate-500 font-medium">Giá niêm yết công khai, bình ổn</div>
                    </div>
                </a>

                <a href="{{ route('contact') }}" class="flex items-center gap-4 p-4 rounded-xl bg-red-50/50 border border-red-100 hover:border-[#c8102e] hover:bg-red-50 hover:-translate-y-0.5 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-[#c8102e] text-white flex items-center justify-center shrink-0 shadow-md group-hover:scale-105 transition-transform">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm group-hover:text-[#c8102e] transition-colors">Tìm Chi Nhánh Gần Bạn</div>
                        <div class="text-xs text-slate-500 font-medium">Chi nhánh Cờ Đỏ & Thới Lai</div>
                    </div>
                </a>
            </div>
        </section>

        <!-- 3. Khuyến nghị quan trọng (Cúm, Phế cầu, Zona) -->
        <section class="recommendations-section space-y-6" id="recommendations-section">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                    Lời Khuyên Y Khoa
                </span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900">
                    {{ $settings['rec_section_title'] ?? 'Vắc Xin Khuyến Nghị Cho Người Cao Tuổi & Bệnh Nền' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base">
                    {{ $settings['rec_section_desc'] ?? 'Chủ động bảo vệ sức khỏe trước các tác nhân gây suy giảm hệ miễn dịch nguy hiểm.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Cúm Mùa -->
                <div class="bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all flex flex-col justify-between overflow-hidden group">
                    <div>
                        <div class="aspect-[16/10] w-full overflow-hidden bg-slate-50 relative border-b border-slate-100 flex items-center justify-center p-2">
                            <img src="{{ asset('images/vaccines/vaxigrip.jpg') }}" alt="Vắc Xin Cúm Mùa" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-red-100 text-[#c8102e] flex items-center justify-center font-bold shrink-0">
                                    <i data-lucide="thermometer-snowflake" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-bold text-lg text-slate-900 group-hover:text-[#c8102e] transition-colors">1. Vắc Xin Cúm Mùa</h3>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Phòng bệnh cúm mùa - bệnh hô hấp dễ gây biến chứng viêm phổi nặng ở người lớn tuổi. Cần tiêm nhắc lại hàng năm.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-2">
                        <a href="{{ route('vaccine.index', ['search' => 'Cúm']) }}" class="inline-flex items-center gap-2 text-[#c8102e] font-bold text-sm hover:gap-3 transition-all">
                            Xem chi tiết & Đăng ký <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Phế Cầu -->
                <div class="bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all flex flex-col justify-between overflow-hidden group">
                    <div>
                        <div class="aspect-[16/10] w-full overflow-hidden bg-slate-50 relative border-b border-slate-100 flex items-center justify-center p-2">
                            <img src="{{ asset('images/vaccines/prevenar13.jpg') }}" alt="Vắc Xin Phế Cầu" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-red-100 text-[#c8102e] flex items-center justify-center font-bold shrink-0">
                                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-bold text-lg text-slate-900 group-hover:text-[#c8102e] transition-colors">2. Vắc Xin Phế Cầu</h3>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Ngăn ngừa hiệu quả các thể bệnh xâm lấn nguy kịch: Viêm phổi nặng, viêm màng não và nhiễm trùng huyết do phế cầu.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-2">
                        <a href="{{ route('vaccine.index', ['search' => 'Phế cầu']) }}" class="inline-flex items-center gap-2 text-[#c8102e] font-bold text-sm hover:gap-3 transition-all">
                            Xem chi tiết & Đăng ký <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 3: Zona Thần Kinh -->
                <div class="bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all flex flex-col justify-between overflow-hidden group">
                    <div>
                        <div class="aspect-[16/10] w-full overflow-hidden bg-slate-50 relative border-b border-slate-100 flex items-center justify-center p-2">
                            <img src="{{ asset('images/vaccines/shingrix.jpg') }}" alt="Vắc Xin Zona Thần Kinh" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-red-100 text-[#c8102e] flex items-center justify-center font-bold shrink-0">
                                    <i data-lucide="zap" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-bold text-lg text-slate-900 group-hover:text-[#c8102e] transition-colors">3. Vắc Xin Zona Thần Kinh</h3>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Phòng bệnh Zona thần kinh (giời leo) và biến chứng đau dây thần kinh sau Zona dai dẳng đau đớn ở người cao tuổi.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-2">
                        <a href="{{ route('vaccine.index', ['search' => 'Zona']) }}" class="inline-flex items-center gap-2 text-[#c8102e] font-bold text-sm hover:gap-3 transition-all">
                            Xem chi tiết & Đăng ký <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Banner Qdenga Sốt Xuất Huyết (Tối ưu hình ảnh hiển thị trọn vẹn) -->
        <section class="bg-gradient-to-r from-red-900 via-[#c8102e] to-red-800 rounded-2xl p-6 md:p-10 text-white shadow-xl overflow-hidden relative" id="qdenga-promo">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                <div class="md:col-span-4 rounded-xl overflow-hidden shadow-2xl border-2 border-white/20 bg-white/10 flex items-center justify-center p-2">
                    <img src="{{ asset('images/vaccines/qdenga.jpg') }}" alt="Vắc Xin Sốt Xuất Huyết Qdenga" class="w-full aspect-[4/3] object-contain rounded-lg">
                </div>
                <div class="md:col-span-8 space-y-4">
                    <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block backdrop-blur-sm">
                        ĐÃ CÓ SẴN TẠI TRUNG TÂM
                    </span>
                    <h2 class="text-2xl md:text-4xl font-black leading-tight">
                        Vắc Xin Sốt Xuất Huyết Qdenga (Nhật Bản)
                    </h2>
                    <p class="text-white/90 text-sm md:text-base leading-relaxed">
                        Medicare tự hào cung cấp vắc xin sốt xuất huyết Qdenga thế hệ mới.
                        Giảm nguy cơ nhiễm bệnh sốt xuất huyết lên đến <strong class="text-yellow-300 font-bold">80%</strong> và giảm tỷ lệ nhập viện do bệnh lên đến <strong class="text-yellow-300 font-bold">90%</strong>.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-white/90 pt-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-yellow-300"></i> <strong>Đối tượng:</strong> Trẻ từ 4 tuổi & người lớn
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-yellow-300"></i> <strong>Phác đồ:</strong> Tiêm 2 mũi cách nhau 3 tháng
                        </div>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('vaccine.index', ['search' => 'Qdenga']) }}" class="inline-flex items-center gap-2 bg-white text-[#c8102e] hover:bg-red-50 font-bold py-3 px-6 rounded-xl shadow-md transition-all">
                            <i data-lucide="syringe" class="w-5 h-5"></i> Đăng ký tiêm ngay
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Flowbite Tabbed Vaccine Catalog -->
        <section class="space-y-6" id="vaccine-catalog">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                    Danh Mục Vắc Xin
                </span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900">
                    Vắc Xin Thiết Yếu Tại Medicare
                </h2>
                <p class="text-slate-600 text-sm md:text-base">
                    Tra cứu bảng giá vắc xin niêm yết công khai và chọn các gói vắc xin ưu đãi cho gia đình.
                </p>
            </div>

            <!-- Flowbite Tabs Nav -->
            <div class="border-b border-red-100">
                <ul class="flex flex-wrap -mb-px text-sm font-bold text-center justify-center" id="vaccine-tabs" data-tabs-toggle="#vaccine-tab-content" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg text-[#c8102e] border-[#c8102e] font-bold text-base" id="single-vaccine-tab" data-tabs-target="#single-vaccines" type="button" role="tab" aria-controls="single-vaccines" aria-selected="true">
                            <i data-lucide="syringe" class="inline w-5 h-5 mr-2"></i> Vắc Xin Lẻ Nổi Bật
                        </button>
                    </li>
                    @if($vaccinePackages->isNotEmpty())
                        <li class="me-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-[#c8102e] border-transparent font-bold text-base" id="package-vaccine-tab" data-tabs-target="#package-vaccines" type="button" role="tab" aria-controls="package-vaccines" aria-selected="false">
                                <i data-lucide="package-check" class="inline w-5 h-5 mr-2"></i> Gói Vắc Xin Ưu Đãi
                            </button>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Flowbite Tabs Content -->
            <div id="vaccine-tab-content">
                <!-- Tab 1: Single Featured Vaccines -->
                <div class="p-4 rounded-2xl bg-white border border-red-100" id="single-vaccines" role="tabpanel" aria-labelledby="single-vaccine-tab">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($featuredVaccines as $vaccine)
                            <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all flex flex-col justify-between overflow-hidden group">
                                <a href="{{ route('vaccine.show', $vaccine->id) }}" onclick="openVaccineDetailModal({{ $vaccine->id }}, event)" class="block">
                                    <div class="aspect-[4/3] w-full bg-slate-50 flex items-center justify-center p-3 overflow-hidden border-b border-slate-100">
                                        <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'default_vaccine.jpg')) }}" alt="{{ $vaccine->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <div class="p-4 space-y-2">
                                        <span class="inline-block text-[11px] font-extrabold uppercase text-[#c8102e] bg-red-50 px-2.5 py-1 rounded tracking-wider">
                                            {{ $vaccine->origin }}
                                        </span>
                                        <h3 class="font-bold text-slate-900 text-base line-clamp-1 group-hover:text-[#c8102e] transition-colors">{{ $vaccine->name }}</h3>
                                        <p class="text-xs text-slate-500 line-clamp-2"><strong>Phòng bệnh:</strong> {{ $vaccine->disease_prevention }}</p>
                                        <p class="text-xs text-slate-500"><strong>Độ tuổi:</strong> {{ $vaccine->age_group }}</p>
                                    </div>
                                </a>
                                <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                                    <span class="font-black text-[#c8102e] text-base">{{ number_format($vaccine->price, 0, ',', '.') }} đ</span>
                                    <a href="{{ route('vaccine.show', $vaccine->id) }}" onclick="openVaccineDetailModal({{ $vaccine->id }}, event)" class="bg-[#c8102e] text-white hover:bg-[#a00d24] text-xs font-bold px-3 py-2 rounded-lg transition-colors">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-6">
                        <a href="{{ route('vaccine.index', ['type' => 'single']) }}" class="inline-flex items-center gap-2 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-3 px-6 rounded-xl transition-all">
                            Xem tất cả vắc xin lẻ <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Tab 2: Vaccine Packages -->
                @if($vaccinePackages->isNotEmpty())
                    <div class="hidden p-4 rounded-2xl bg-white border border-red-100" id="package-vaccines" role="tabpanel" aria-labelledby="package-vaccine-tab">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($vaccinePackages as $package)
                                <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all flex flex-col justify-between overflow-hidden group">
                                    <a href="{{ route('vaccine.show', $package->id) }}" onclick="openVaccineDetailModal({{ $package->id }}, event)" class="block">
                                        <div class="aspect-[4/3] w-full bg-slate-50 flex items-center justify-center p-3 overflow-hidden border-b border-slate-100">
                                            <img src="{{ asset('images/vaccines/' . ($package->image ?: 'default_vaccine.jpg')) }}" alt="{{ $package->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                        <div class="p-5 space-y-3">
                                            <span class="inline-block text-[11px] font-extrabold uppercase text-[#c8102e] bg-red-50 px-2.5 py-1 rounded tracking-wider">
                                                Gói Trọn Gói
                                            </span>
                                            <h3 class="font-bold text-slate-900 text-lg group-hover:text-[#c8102e] transition-colors">{{ $package->name }}</h3>
                                            <p class="text-xs text-slate-600 line-clamp-2">{{ $package->description }}</p>
                                            <div class="text-xs text-slate-500 space-y-1 pt-1">
                                                <div class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-[#c8102e]"></i> {{ Str::limit($package->disease_prevention, 40) }}</div>
                                                <div class="flex items-center gap-1.5"><i data-lucide="milestone" class="w-4 h-4 text-[#c8102e]"></i> Phác đồ: {{ $package->doses }} mũi tiêm</div>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="p-5 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                                        <div>
                                            <div class="text-[11px] text-slate-500 font-medium">Giá trọn gói:</div>
                                            <div class="font-black text-[#c8102e] text-lg">{{ number_format($package->price, 0, ',', '.') }} đ</div>
                                        </div>
                                        <a href="{{ route('vaccine.show', $package->id) }}" onclick="openVaccineDetailModal({{ $package->id }}, event)" class="bg-[#c8102e] text-white hover:bg-[#a00d24] text-xs font-bold px-3.5 py-2.5 rounded-lg transition-colors">
                                            Xem gói
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- 5. Quy trình 5 bước tiêm chủng an toàn chuẩn Y Tế -->
        <section class="bg-white p-8 md:p-12 rounded-2xl border border-red-100 shadow-sm space-y-8" id="safe-process-section">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                    Chuẩn Y Tế An Toàn
                </span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900">
                    {{ $settings['process_section_title'] ?? 'Quy Trình Tiêm Chủng 5 Bước An Toàn Mẫu Mực' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base">
                    {{ $settings['process_section_desc'] ?? 'Medicare áp dụng nghiêm ngặt quy trình tiêm chủng an toàn bảo vệ sức khỏe tối đa cho khách hàng.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 pt-4">
                <!-- Step 1 -->
                <div class="bg-red-50/40 border border-red-100 rounded-2xl p-6 text-center relative hover:border-[#c8102e] hover:-translate-y-1 transition-all duration-200">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-9 h-9 bg-[#c8102e] text-white rounded-full font-black text-sm flex items-center justify-center shadow-md border-2 border-white">1</div>
                    <div class="w-12 h-12 mx-auto my-3 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                        <i data-lucide="clipboard-signature" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">1. Đăng Ký & Khám</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Bác sĩ khám sàng lọc cẩn thận miễn phí trước khi tiêm.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-red-50/40 border border-red-100 rounded-2xl p-6 text-center relative hover:border-[#c8102e] hover:-translate-y-1 transition-all duration-200">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-9 h-9 bg-[#c8102e] text-white rounded-full font-black text-sm flex items-center justify-center shadow-md border-2 border-white">2</div>
                    <div class="w-12 h-12 mx-auto my-3 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                        <i data-lucide="stethoscope" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">2. Tư Vấn Phác Đồ</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Tư vấn loại vắc xin, liều lượng và chi phí niêm yết.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-red-50/40 border border-red-100 rounded-2xl p-6 text-center relative hover:border-[#c8102e] hover:-translate-y-1 transition-all duration-200">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-9 h-9 bg-[#c8102e] text-white rounded-full font-black text-sm flex items-center justify-center shadow-md border-2 border-white">3</div>
                    <div class="w-12 h-12 mx-auto my-3 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                        <i data-lucide="syringe" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">3. Tiêm Chuẩn GSP</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Điều dưỡng thực hiện tiêm đúng kỹ thuật nhẹ nhàng, không đau.</p>
                </div>

                <!-- Step 4 -->
                <div class="bg-red-50/40 border border-red-100 rounded-2xl p-6 text-center relative hover:border-[#c8102e] hover:-translate-y-1 transition-all duration-200">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-9 h-9 bg-[#c8102e] text-white rounded-full font-black text-sm flex items-center justify-center shadow-md border-2 border-white">4</div>
                    <div class="w-12 h-12 mx-auto my-3 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                        <i data-lucide="timer" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">4. Theo Dõi 30 Phút</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Theo dõi phản ứng sau tiêm 30 phút tại phòng chờ hiện đại.</p>
                </div>

                <!-- Step 5 -->
                <div class="bg-red-50/40 border border-red-100 rounded-2xl p-6 text-center relative hover:border-[#c8102e] hover:-translate-y-1 transition-all duration-200">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-9 h-9 bg-[#c8102e] text-white rounded-full font-black text-sm flex items-center justify-center shadow-md border-2 border-white">5</div>
                    <div class="w-12 h-12 mx-auto my-3 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">5. Kiểm Tra & Ra Về</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Kiểm tra vết tiêm, đo lại thân nhiệt, dặn dò và ra về an toàn.</p>
                </div>
            </div>
        </section>

        <!-- 6. Dịch vụ tiêm chủng -->
        <section class="space-y-6" id="services-section">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                    Dịch Vụ Chính
                </span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900">
                    {{ $settings['service_section_title'] ?? 'Dịch Vụ Tiêm Chủng Tại Phòng Khám' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base">
                    {{ $settings['service_section_desc'] ?? 'Giải pháp phòng ngừa bệnh tật toàn diện dành cho mọi lứa tuổi và gia đình.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-red-100 rounded-2xl p-6 hover:shadow-md hover:border-[#c8102e] transition-all space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                            <i data-lucide="baby" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg">Tiêm Chủng Trẻ Em</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Cung cấp đầy đủ các loại vắc xin quan trọng cho bé từ sơ sinh đến 6 tuổi với quy trình tiêm nhẹ nhàng.</p>
                    </div>
                    <a href="{{ route('vaccine.index', ['age_group' => 'Trẻ']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-xs hover:gap-2 transition-all">
                        Xem vắc xin trẻ em <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="bg-white border border-red-100 rounded-2xl p-6 hover:shadow-md hover:border-[#c8102e] transition-all space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg">Tiêm Chủng Người Lớn</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Bảo vệ người trưởng thành và người cao tuổi trước Cúm mùa, Phế cầu, Zona thần kinh, Sốt xuất huyết Qdenga...</p>
                    </div>
                    <a href="{{ route('vaccine.index', ['age_group' => 'người lớn']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-xs hover:gap-2 transition-all">
                        Xem vắc xin người lớn <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="bg-white border border-red-100 rounded-2xl p-6 hover:shadow-md hover:border-[#c8102e] transition-all space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                            <i data-lucide="package-check" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg">Gói Tiêm Trọn Gói</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Tiết kiệm chi phí, cam kết giữ vắc xin không lo thiếu hàng hay tăng giá trong suốt phác đồ tiêm chủng.</p>
                    </div>
                    <a href="{{ route('vaccine.index', ['type' => 'package']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-xs hover:gap-2 transition-all">
                        Xem các gói tiêm <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="bg-white border border-red-100 rounded-2xl p-6 hover:shadow-md hover:border-[#c8102e] transition-all space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="w-12 h-12 bg-red-100 text-[#c8102e] rounded-xl flex items-center justify-center">
                            <i data-lucide="stethoscope" class="w-6 h-6"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg">Tư Vấn & Khám Sàng Lọc</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">Miễn phí khám sàng lọc bởi bác sĩ chuyên khoa trước khi tiêm và theo dõi sức khỏe cẩn thận sau tiêm.</p>
                    </div>
                    <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-xs hover:gap-2 transition-all">
                        Đặt lịch hẹn ngay <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- 7. Tin Tức / Kiến Thức Tiêm Chủng -->
        <section class="space-y-6" id="news-section">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                    Góc Y Khoa
                </span>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900">
                    {{ $settings['news_section_title'] ?? 'Kiến Thức Tiêm Chủng & Tin Tức' }}
                </h2>
                <p class="text-slate-600 text-sm md:text-base">
                    {{ $settings['news_section_desc'] ?? 'Cập nhật những thông tin y tế chính thống và lời khuyên hữu ích từ chuyên gia.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($articles ?? [] as $article)
                    <article class="bg-white border border-red-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all flex flex-col justify-between group">
                        <div>
                            <div class="aspect-[16/10] w-full bg-slate-100 overflow-hidden relative">
                                <img src="{{ asset('images/vaccines/' . ($article->image ?: 'default_vaccine.jpg')) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-6 space-y-3">
                                <span class="inline-block text-[11px] font-bold text-[#c8102e] bg-red-50 px-2.5 py-1 rounded uppercase tracking-wider">
                                    {{ $article->category }}
                                </span>
                                <h3 class="font-bold text-slate-900 text-base line-clamp-2 group-hover:text-[#c8102e] transition-colors">
                                    {{ $article->title }}
                                </h3>
                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $article->summary }}
                                </p>
                            </div>
                        </div>
                        <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $article->created_at ? $article->created_at->format('d/m/Y') : '21/07/2026' }}</span>
                            <a href="{{ route('vaccine.index') }}" class="font-bold text-[#c8102e] hover:underline">Đọc chi tiết →</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- 8. Mạng Lưới Chi Nhánh -->
        <section class="bg-slate-900 rounded-3xl p-8 md:p-12 text-white shadow-2xl relative overflow-hidden border border-slate-800" id="contact-section">
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="max-w-2xl space-y-4">
                    <span class="inline-block bg-[#c8102e] text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full tracking-wider shadow-sm">
                        Mạng Lưới Chi Nhánh
                    </span>
                    <h2 class="text-2xl md:text-4xl font-black leading-tight text-white">
                        Phục Vụ Quý Khách Tại 2 Chi Nhánh Trung Tâm
                    </h2>
                    <p class="text-slate-400 text-sm md:text-base leading-relaxed">
                        Hệ thống tiêm chủng Medicare sẵn sàng phục vụ tại <strong>Chi nhánh 1 (Medicare Cờ Đỏ)</strong> và <strong>Chi nhánh 2 (Medicare Thới Lai)</strong> với đầy đủ 40 loại vắc xin chất lượng cao.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto shrink-0">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-4 px-7 rounded-xl shadow-lg hover:shadow-red-900/30 transition-all text-sm">
                        <i data-lucide="map-pin" class="w-5 h-5"></i> Xem địa chỉ & bản đồ
                    </a>
                    <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-7 rounded-xl border border-white/20 transition-all text-sm">
                        Đăng ký tiêm ngay →
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
