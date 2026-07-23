@extends('vaccine::layouts.app')

@section('title', 'Hệ Thống Tiêm Chủng Medicare - Đăng Ký Tiêm Chủng')
@section('meta_description', 'Medicare - Hệ thống tiêm chủng vắc xin an toàn, chất lượng hàng đầu tại Cần Thơ. Đặt lịch tiêm chủng trực tuyến cho trẻ em và người lớn.')

@section('content')
    <div class="home-container space-y-16 py-4 w-full overflow-hidden">
        <!-- 1. Flowbite Hero Slider Banner (FULL WIDTH - TRÀN VIỀN 100%) -->
        <section class="banner-slider relative w-full overflow-hidden shadow-xl" id="hero-banner" data-aos="fade-up" data-aos-duration="600">
            @if($banners->isEmpty())
                <div class="bg-gradient-to-r from-slate-900 via-red-950 to-slate-900 text-white w-full p-6 sm:p-8 lg:p-10 border-y border-red-900/40 shadow-xl overflow-hidden relative">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center relative z-10">
                            <!-- Left Text & Actions -->
                            <div class="lg:col-span-7 space-y-5">
                                <span class="inline-block bg-[#c8102e] text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wider">
                                    Hệ Thống Tiêm Chủng An Toàn
                                </span>
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-[2.75rem] font-black text-white leading-tight tracking-tight">
                                    Hệ Thống Tiêm Chủng <span class="text-red-400">Medicare</span>
                                </h1>
                                <p class="text-slate-300 text-base lg:text-lg font-medium leading-relaxed max-w-xl">
                                    Hệ thống trung tâm tiêm chủng vắc xin an toàn, chất lượng hàng đầu cho trẻ em và người lớn với phác đồ y khoa chuẩn GSP.
                                </p>
                                <div class="flex flex-wrap gap-4 pt-1">
                                    <a href="{{ route('vaccine.index') }}" class="inline-flex items-center gap-2 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-red-500/25 transition-all duration-200">
                                        Xem bảng giá vắc xin <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                    </a>
                                    <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white hover:bg-white/20 font-bold py-3 px-6 rounded-xl transition-all duration-200">
                                        <i data-lucide="calendar-check-2" class="w-5 h-5"></i> Đặt lịch tiêm ngay
                                    </a>
                                </div>
                            </div>

                            <!-- Right Banner Image Frame -->
                            <div class="lg:col-span-5 flex items-center justify-center">
                                <div class="w-full h-56 sm:h-72 lg:h-80 rounded-2xl overflow-hidden border border-white/20 shadow-2xl bg-white/5 relative group flex items-center justify-center p-1.5">
                                    <img src="{{ asset('images/banners/banner_family.jpg') }}" alt="Hệ Thống Medicare" class="w-full h-full object-contain object-center group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div id="flowbite-hero-carousel" class="relative w-full" data-carousel="slide">
                    <!-- Carousel wrapper -->
                    <div class="relative min-h-[380px] sm:min-h-[420px] overflow-hidden">
                        @foreach($banners as $index => $banner)
                            <div class="{{ $index === 0 ? 'block' : 'hidden' }} duration-700 ease-in-out" data-carousel-item="{{ $index === 0 ? 'active' : '' }}">
                                <div class="bg-gradient-to-r from-slate-900 via-red-950 to-slate-900 text-white w-full p-6 sm:p-8 lg:p-10 border-y border-red-900/40 shadow-xl overflow-hidden relative">
                                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center relative z-10">
                                            <!-- Left Text & Actions -->
                                            <div class="lg:col-span-7 space-y-5">
                                                <span class="inline-block bg-[#c8102e] text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wider">
                                                    Medicare Standard
                                                </span>
                                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight">
                                                    {{ $banner->title }}
                                                </h2>
                                                <p class="text-slate-300 text-base lg:text-lg font-medium leading-relaxed max-w-xl">
                                                    {{ $banner->subtitle }}
                                                </p>
                                                <div class="pt-1">
                                                    <a href="{{ $banner->link_url ?: route('vaccine.index') }}" class="inline-flex items-center gap-2 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-red-500/25 transition-all duration-200">
                                                        Tìm hiểu thêm <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- Right Banner Image Frame -->
                                            <div class="lg:col-span-5 flex items-center justify-center">
                                                <div class="w-full h-56 sm:h-72 lg:h-80 rounded-2xl overflow-hidden border border-white/20 shadow-2xl bg-white/5 relative group flex items-center justify-center p-1.5">
                                                    <img src="{{ $banner->image_path ? asset($banner->image_path) : asset('images/banners/banner_family.jpg') }}" alt="{{ $banner->title }}" class="w-full h-full object-contain object-center group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                                </div>
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

        <!-- 2. Quick Action Toolbar (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="relative z-20 -mt-8" data-aos="fade-up" data-aos-delay="100">
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
        </div>

        <!-- 3. Trust Indicators / Counter Section (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="trust-counter-section" id="trust-counter" data-aos="fade-up" data-aos-duration="700">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <div class="trust-counter-card text-center p-6 md:p-8 bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all group">
                        <div class="w-14 h-14 mx-auto mb-4 bg-red-50 text-[#c8102e] rounded-2xl flex items-center justify-center group-hover:bg-[#c8102e] group-hover:text-white transition-colors duration-300">
                            <i data-lucide="syringe" class="w-7 h-7"></i>
                        </div>
                        <div class="counter-number text-3xl md:text-4xl font-black text-[#c8102e] mb-1" data-target="40">40+</div>
                        <div class="text-sm font-semibold text-slate-700">Loại Vắc Xin</div>
                        <div class="text-xs text-slate-500 mt-1">Chính hãng, nhập khẩu</div>
                    </div>
                    <div class="trust-counter-card text-center p-6 md:p-8 bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all group">
                        <div class="w-14 h-14 mx-auto mb-4 bg-red-50 text-[#c8102e] rounded-2xl flex items-center justify-center group-hover:bg-[#c8102e] group-hover:text-white transition-colors duration-300">
                            <i data-lucide="users" class="w-7 h-7"></i>
                        </div>
                        <div class="counter-number text-3xl md:text-4xl font-black text-[#c8102e] mb-1" data-target="10000">10.000+</div>
                        <div class="text-sm font-semibold text-slate-700">Khách Hàng</div>
                        <div class="text-xs text-slate-500 mt-1">Tin dùng hàng năm</div>
                    </div>
                    <div class="trust-counter-card text-center p-6 md:p-8 bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all group">
                        <div class="w-14 h-14 mx-auto mb-4 bg-red-50 text-[#c8102e] rounded-2xl flex items-center justify-center group-hover:bg-[#c8102e] group-hover:text-white transition-colors duration-300">
                            <i data-lucide="building-2" class="w-7 h-7"></i>
                        </div>
                        <div class="counter-number text-3xl md:text-4xl font-black text-[#c8102e] mb-1" data-target="2">2</div>
                        <div class="text-sm font-semibold text-slate-700">Chi Nhánh</div>
                        <div class="text-xs text-slate-500 mt-1">Tại TP. Cần Thơ</div>
                    </div>
                    <div class="trust-counter-card text-center p-6 md:p-8 bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-[#c8102e] transition-all group">
                        <div class="w-14 h-14 mx-auto mb-4 bg-red-50 text-[#c8102e] rounded-2xl flex items-center justify-center group-hover:bg-[#c8102e] group-hover:text-white transition-colors duration-300">
                            <i data-lucide="shield-check" class="w-7 h-7"></i>
                        </div>
                        <div class="counter-number text-3xl md:text-4xl font-black text-[#c8102e] mb-1" data-target="100" data-suffix="%">100%</div>
                        <div class="text-sm font-semibold text-slate-700">Vắc Xin Chính Hãng</div>
                        <div class="text-xs text-slate-500 mt-1">Nhập khẩu chính ngạch</div>
                    </div>
                </div>
            </section>
        </div>

        <!-- 4. Khuyến nghị quan trọng (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="recommendations-section space-y-8" id="recommendations-section" data-aos="fade-up">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                        Lời Khuyên Y Khoa
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                        {{ $settings['rec_section_title'] ?? 'Vắc Xin Khuyến Nghị Cho Người Cao Tuổi & Bệnh Nền' }}
                    </h2>
                    <p class="text-slate-600 text-sm md:text-base max-w-lg mx-auto">
                        {{ $settings['rec_section_desc'] ?? 'Chủ động bảo vệ sức khỏe trước các tác nhân gây suy giảm hệ miễn dịch nguy hiểm.' }}
                    </p>
                </div>

                <div class="space-y-5">
                    <!-- Card 1: Cúm Mùa -->
                    <div class="bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-lg hover:border-[#c8102e]/40 transition-all overflow-hidden group" data-aos="fade-up" data-aos-delay="100">
                        <div class="grid grid-cols-1 md:grid-cols-12 items-center">
                            <div class="md:col-span-4 lg:col-span-3 aspect-[4/3] md:aspect-auto md:h-full w-full overflow-hidden bg-slate-50 flex items-center justify-center p-4 border-b md:border-b-0 md:border-r border-slate-100">
                                <img src="{{ asset('images/vaccines/vaxigrip.jpg') }}" alt="Vắc xin Cúm Tứ Giá" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            </div>
                            <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 space-y-3 flex flex-col justify-between h-full">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="bg-[#c8102e] text-white font-bold text-[11px] uppercase px-2.5 py-0.5 rounded-md">Vắc Xin Cúm Mùa</span>
                                        <span class="text-slate-500 text-xs font-semibold">Vaxigrip Tetra (Pháp) / Influvac Tetra (Hà Lan)</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-[#004b8f] group-hover:text-[#c8102e] transition-colors leading-tight mb-2">
                                        Bảo Vệ Người Cao Tuổi & Người Có Bệnh Nền Tránh Biến Chứng Cúm
                                    </h3>
                                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">
                                        Bệnh cúm có thể dẫn đến các biến chứng nguy hiểm như viêm phổi suy hô hấp, đặc biệt ở người có bệnh tim mạch, tiểu đường, hen suyễn. Tiêm vắc xin cúm hàng năm giúp giảm 80% tỷ lệ nhập viện.
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-100 mt-2">
                                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                                        <span class="flex items-center gap-1 text-[#004b8f]"><i data-lucide="user-check" class="w-4 h-4 text-[#c8102e]"></i> Đối tượng: Người từ 6 tháng tuổi</span>
                                        <span class="flex items-center gap-1 text-[#004b8f]"><i data-lucide="clock" class="w-4 h-4 text-[#eaaa00]"></i> Lịch tiêm: 1 mũi / năm</span>
                                    </div>
                                    <a href="{{ route('vaccine.index', ['search' => 'Cúm']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm hover:underline">
                                        Xem chi tiết & giá <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Phế cầu -->
                    <div class="bg-white rounded-2xl border border-red-100 shadow-sm hover:shadow-lg hover:border-[#c8102e]/40 transition-all overflow-hidden group" data-aos="fade-up" data-aos-delay="200">
                        <div class="grid grid-cols-1 md:grid-cols-12 items-center">
                            <div class="md:col-span-4 lg:col-span-3 aspect-[4/3] md:aspect-auto md:h-full w-full overflow-hidden bg-slate-50 flex items-center justify-center p-4 border-b md:border-b-0 md:border-r border-slate-100">
                                <img src="{{ asset('images/vaccines/prevenar13.jpg') }}" alt="Vắc xin Phế Cầu Prevenar 13" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            </div>
                            <div class="md:col-span-8 lg:col-span-9 p-6 md:p-8 space-y-3 flex flex-col justify-between h-full">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="bg-[#004b8f] text-white font-bold text-[11px] uppercase px-2.5 py-0.5 rounded-md">Vắc Xin Phế Cầu</span>
                                        <span class="text-slate-500 text-xs font-semibold">Prevenar 13 (Bỉ - Mỹ) / Pneumovax 23</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-[#004b8f] group-hover:text-[#c8102e] transition-colors leading-tight mb-2">
                                        Phòng Ngừa Viêm Phổi, Viêm Màng Não & Nhiễm Trùng Huyết Do Phế Cầu
                                    </h3>
                                    <p class="text-slate-600 text-sm leading-relaxed line-clamp-2">
                                        Phế cầu khuẩn là thủ phạm hàng đầu gây viêm phổi nặng và tử vong ở người lớn tuổi. Chủ động tiêm vắc xin phế cầu giúp tạo lá chắn vững chắc bảo vệ phổi.
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-slate-100 mt-2">
                                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-700">
                                        <span class="flex items-center gap-1 text-[#004b8f]"><i data-lucide="user-check" class="w-4 h-4 text-[#c8102e]"></i> Đối tượng: Trẻ nhỏ & Người lớn</span>
                                        <span class="flex items-center gap-1 text-[#004b8f]"><i data-lucide="clock" class="w-4 h-4 text-[#eaaa00]"></i> Lịch tiêm: Theo phác đồ tuổi</span>
                                    </div>
                                    <a href="{{ route('vaccine.index', ['search' => 'Prevenar']) }}" class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm hover:underline">
                                        Xem chi tiết & giá <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- 5. Banner Qdenga Sốt Xuất Huyết (FULL WIDTH - TRÀN VIỀN 100%) -->
        <section class="w-full bg-gradient-to-r from-red-900 via-[#c8102e] to-red-800 p-6 md:p-10 text-white shadow-xl overflow-hidden relative border-y border-red-700/50" id="qdenga-promo" data-aos="fade-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                    <div class="md:col-span-4 rounded-xl overflow-hidden shadow-2xl border-2 border-white/20 bg-white/10 flex items-center justify-center p-2">
                        <img src="{{ asset('images/vaccines/qdenga.jpg') }}" alt="Vắc Xin Sốt Xuất Huyết Qdenga" class="w-full aspect-[4/3] object-contain rounded-lg" loading="lazy">
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
            </div>
        </section>

        <!-- 6. Danh Mục Vắc Xin (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="bg-white p-8 md:p-12 rounded-2xl border border-red-100 shadow-sm space-y-8" id="vaccine-catalog" data-aos="fade-up">
                <!-- Centered Header Title in Medicare Red (#c8102e) -->
                <div class="text-center max-w-3xl mx-auto space-y-3">
                    <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                        Bảng Giá Niêm Yết
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-[#c8102e] uppercase tracking-tight">
                        Danh Mục Vắc Xin Tại Medicare
                    </h2>
                    <p class="text-slate-600 text-sm md:text-base">
                        Đầy đủ các loại vắc xin chính hãng nhập khẩu thế hệ mới cho trẻ em và người lớn với phác đồ y khoa chuẩn GSP.
                    </p>
                </div>

                <!-- Action Bar: Tab Buttons & Xem Tất Cả link -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    @if($vaccinePackages->isNotEmpty())
                        <div class="flex items-center gap-3">
                            <button type="button" id="tab-btn-single" onclick="switchVaccineTab('single')" class="px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-[#c8102e] text-white shadow-md transition-all">
                                Vắc Xin Lẻ Hot
                            </button>
                            <button type="button" id="tab-btn-package" onclick="switchVaccineTab('package')" class="px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                                Gói Vắc Xin Ưu Đãi
                            </button>
                        </div>
                    @endif
                    <a href="{{ route('vaccine.index') }}" class="inline-flex items-center gap-1.5 text-[#004b8f] hover:text-[#c8102e] font-bold text-sm transition-colors ml-auto">
                        Xem tất cả bảng giá vắc xin <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <!-- Tabs Content -->
                <div id="vaccine-tab-content">
                    <!-- Tab 1: Single Featured Vaccines Grid (4 columns x 2 rows) -->
                    <div id="single-vaccines" role="tabpanel">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($featuredVaccines as $vaccine)
                                <div class="group bg-white rounded-3xl p-4 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between cursor-pointer" onclick="openVaccineDetailModal({{ $vaccine->id }}, event)">
                                    <div>
                                        <!-- Light grey image container frame -->
                                        <div class="relative aspect-[16/10] w-full bg-[#f4f7fa] rounded-2xl p-4 flex items-center justify-center overflow-hidden mb-3 border border-slate-100">
                                            <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'default_vaccine.jpg')) }}" alt="{{ $vaccine->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                            <span class="absolute top-2.5 right-2.5 bg-[#c8102e] text-white font-black text-[10px] uppercase px-2.5 py-0.5 rounded-full shadow-sm">
                                                MỚI
                                            </span>
                                            <span class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm text-white font-medium text-[11px] px-2 py-0.5 rounded-md flex items-center gap-1">
                                                <i data-lucide="eye" class="w-3 h-3 text-white"></i>
                                                <span id="vaccine-views-count-{{ $vaccine->id }}">{{ number_format($vaccine->views ?? 0, 0, ',', '.') }}</span> lượt xem
                                            </span>
                                        </div>
                                        <!-- Centered Navy Title -->
                                        <h3 class="text-center font-bold text-[#004b8f] text-sm md:text-[15px] uppercase leading-tight line-clamp-2 mb-1 group-hover:text-[#c8102e] transition-colors">
                                            {{ $vaccine->name }}
                                        </h3>
                                        <!-- Subtitle in parentheses (Brand / Origin) -->
                                        <p class="text-center text-slate-500 text-xs font-medium line-clamp-1 mb-2">
                                            ({{ $vaccine->disease_prevention ?? $vaccine->name }}, {{ $vaccine->origin }})
                                        </p>
                                    </div>
                                    <div class="text-center pt-2 border-t border-slate-100">
                                        <span class="font-black text-[#c8102e] text-base md:text-lg">
                                            {{ number_format($vaccine->price, 0, ',', '.') }} đ
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tab 2: Vaccine Packages -->
                    @if($vaccinePackages->isNotEmpty())
                        <div class="hidden" id="package-vaccines" role="tabpanel">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($vaccinePackages as $package)
                                    <div class="group bg-white rounded-3xl p-4 border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between cursor-pointer" onclick="openVaccineDetailModal({{ $package->id }}, event)">
                                        <div>
                                            <div class="relative aspect-[16/10] w-full bg-[#f4f7fa] rounded-2xl p-4 flex items-center justify-center overflow-hidden mb-3 border border-slate-100">
                                                <img src="{{ asset('images/vaccines/' . ($package->image ?: 'default_vaccine.jpg')) }}" alt="{{ $package->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                                <span class="absolute top-2.5 right-2.5 bg-[#eaaa00] text-slate-900 font-black text-[10px] uppercase px-2.5 py-0.5 rounded-full shadow-sm">
                                                    GÓI ƯU ĐÃI
                                                </span>
                                                <span class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm text-white font-medium text-[11px] px-2 py-0.5 rounded-md flex items-center gap-1">
                                                    <i data-lucide="eye" class="w-3 h-3 text-white"></i>
                                                    <span id="vaccine-views-count-{{ $package->id }}">{{ number_format($package->views ?? 0, 0, ',', '.') }}</span> lượt xem
                                                </span>
                                            </div>
                                            <h3 class="text-center font-bold text-[#004b8f] text-base uppercase leading-snug line-clamp-1 mb-1 group-hover:text-[#c8102e] transition-colors">
                                                {{ $package->name }}
                                            </h3>
                                            <p class="text-center text-slate-500 text-xs font-medium line-clamp-1 mb-2">
                                                ({{ Str::limit($package->disease_prevention, 35) }})
                                            </p>
                                        </div>
                                        <div class="text-center pt-2 border-t border-slate-100">
                                            <span class="font-black text-[#c8102e] text-lg">
                                                {{ number_format($package->price, 0, ',', '.') }} đ
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- 7. Quy trình 5 bước (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="bg-white p-8 md:p-12 rounded-2xl border border-red-100 shadow-sm space-y-10" id="safe-process-section" data-aos="fade-up">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                        Chuẩn Y Tế An Toàn
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                        {{ $settings['process_section_title'] ?? 'Quy Trình Tiêm Chủng 5 Bước An Toàn' }}
                    </h2>
                    <p class="text-slate-600 text-sm md:text-base">
                        {{ $settings['process_section_desc'] ?? 'Medicare áp dụng nghiêm ngặt quy trình tiêm chủng an toàn bảo vệ sức khỏe tối đa cho khách hàng.' }}
                    </p>
                </div>

                <!-- Timeline Layout -->
                <div class="process-timeline relative max-w-3xl mx-auto">
                    <!-- Connector line -->
                    <div class="hidden md:block absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#c8102e] via-[#c8102e]/60 to-[#c8102e]/20"></div>

                    <!-- Step 1 -->
                    <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="100">
                        <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-lg border-4 border-white z-10 relative">1</div>
                        <div class="flex-1 bg-red-50/50 border border-red-100 rounded-2xl p-5 hover:border-[#c8102e]/40 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="clipboard-signature" class="w-5 h-5 text-[#c8102e]"></i>
                                <h4 class="font-bold text-slate-900 text-base">Đăng Ký & Khám Sàng Lọc</h4>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">Bác sĩ khám sàng lọc cẩn thận miễn phí trước khi tiêm, kiểm tra thân nhiệt, huyết áp và tiền sử bệnh.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="200">
                        <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-lg border-4 border-white z-10 relative">2</div>
                        <div class="flex-1 bg-red-50/50 border border-red-100 rounded-2xl p-5 hover:border-[#c8102e]/40 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="stethoscope" class="w-5 h-5 text-[#c8102e]"></i>
                                <h4 class="font-bold text-slate-900 text-base">Tư Vấn Phác Đồ</h4>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">Tư vấn loại vắc xin phù hợp, liều lượng, lịch tiêm nhắc và chi phí niêm yết minh bạch.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="300">
                        <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-lg border-4 border-white z-10 relative">3</div>
                        <div class="flex-1 bg-red-50/50 border border-red-100 rounded-2xl p-5 hover:border-[#c8102e]/40 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="syringe" class="w-5 h-5 text-[#c8102e]"></i>
                                <h4 class="font-bold text-slate-900 text-base">Tiêm Chuẩn GSP</h4>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">Điều dưỡng thực hiện tiêm đúng kỹ thuật nhẹ nhàng, không đau. Vắc xin bảo quản đúng dây chuyền lạnh.</p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="timeline-step flex gap-6 mb-8 relative" data-aos="fade-right" data-aos-delay="400">
                        <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-lg border-4 border-white z-10 relative">4</div>
                        <div class="flex-1 bg-red-50/50 border border-red-100 rounded-2xl p-5 hover:border-[#c8102e]/40 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="timer" class="w-5 h-5 text-[#c8102e]"></i>
                                <h4 class="font-bold text-slate-900 text-base">Theo Dõi 30 Phút</h4>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">Theo dõi phản ứng sau tiêm 30 phút tại phòng chờ hiện đại, có y tá trực sẵn sàng xử lý.</p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="timeline-step flex gap-6 relative" data-aos="fade-right" data-aos-delay="500">
                        <div class="shrink-0 w-16 h-16 bg-[#c8102e] text-white rounded-2xl font-black text-xl flex items-center justify-center shadow-lg border-4 border-white z-10 relative">5</div>
                        <div class="flex-1 bg-red-50/50 border border-red-100 rounded-2xl p-5 hover:border-[#c8102e]/40 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3 mb-2">
                                <i data-lucide="shield-check" class="w-5 h-5 text-[#c8102e]"></i>
                                <h4 class="font-bold text-slate-900 text-base">Kiểm Tra & Ra Về An Toàn</h4>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">Kiểm tra vết tiêm, đo lại thân nhiệt, dặn dò chăm sóc tại nhà và hẹn lịch tiêm nhắc tiếp theo.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- 8. Dịch vụ tiêm chủng (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="space-y-8" id="services-section" data-aos="fade-up">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                        Dịch Vụ Chính
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                        {{ $settings['service_section_title'] ?? 'Dịch Vụ Tiêm Chủng Tại Phòng Khám' }}
                    </h2>
                    <p class="text-slate-600 text-sm md:text-base">
                        {{ $settings['service_section_desc'] ?? 'Giải pháp phòng ngừa bệnh tật toàn diện dành cho mọi lứa tuổi và gia đình.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('vaccine.index', ['age_group' => 'Trẻ']) }}" class="service-card-large bg-white border border-red-100 rounded-2xl p-8 hover:shadow-lg hover:border-[#c8102e]/40 transition-all flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#c8102e] to-red-700 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="baby" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-2 flex-1">
                            <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Tiêm Chủng Trẻ Em</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Cung cấp đầy đủ các loại vắc xin quan trọng cho bé từ sơ sinh đến 6 tuổi với quy trình tiêm nhẹ nhàng, giảm đau.</p>
                            <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                                Xem vắc xin trẻ em <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                        </div>
                    </a>

                    <a href="{{ route('vaccine.index', ['age_group' => 'người lớn']) }}" class="service-card-large bg-white border border-red-100 rounded-2xl p-8 hover:shadow-lg hover:border-[#c8102e]/40 transition-all flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="200">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#c8102e] to-red-700 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="user-check" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-2 flex-1">
                            <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Tiêm Chủng Người Lớn</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Bảo vệ người trưởng thành và người cao tuổi trước Cúm mùa, Phế cầu, Zona thần kinh, Sốt xuất huyết Qdenga.</p>
                            <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                                Xem vắc xin người lớn <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                        </div>
                    </a>

                    <a href="{{ route('vaccine.index', ['type' => 'package']) }}" class="service-card-large bg-white border border-red-100 rounded-2xl p-8 hover:shadow-lg hover:border-[#c8102e]/40 transition-all flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="300">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#eaaa00] to-amber-600 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="package-check" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-2 flex-1">
                            <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Gói Tiêm Trọn Gói</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Tiết kiệm chi phí, cam kết giữ vắc xin không lo thiếu hàng hay tăng giá trong suốt phác đồ tiêm chủng.</p>
                            <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                                Xem các gói tiêm <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                        </div>
                    </a>

                    <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="service-card-large bg-white border border-red-100 rounded-2xl p-8 hover:shadow-lg hover:border-[#c8102e]/40 transition-all flex items-start gap-6 group" data-aos="fade-up" data-aos-delay="400">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#004b8f] to-blue-800 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-md group-hover:scale-110 transition-transform">
                            <i data-lucide="stethoscope" class="w-8 h-8"></i>
                        </div>
                        <div class="space-y-2 flex-1">
                            <h3 class="font-bold text-slate-900 text-xl group-hover:text-[#c8102e] transition-colors">Tư Vấn & Khám Sàng Lọc</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">Miễn phí khám sàng lọc bởi bác sĩ chuyên khoa trước khi tiêm và theo dõi sức khỏe cẩn thận sau tiêm.</p>
                            <span class="inline-flex items-center gap-1.5 text-[#c8102e] font-bold text-sm group-hover:gap-2.5 transition-all">
                                Đặt lịch hẹn ngay <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </section>
        </div>

        <!-- 9. ⭐ Testimonials / Đánh giá khách hàng (FULL WIDTH - TRÀN VIỀN 100%) -->
        <section class="w-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8 md:p-14 shadow-2xl relative overflow-hidden border-y border-slate-800" id="testimonials-section" data-aos="fade-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-2xl mx-auto space-y-3 mb-10">
                    <span class="inline-block bg-[#c8102e] text-white font-bold text-xs px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                        Khách Hàng Nói Gì
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-white">
                        Phụ Huynh & Khách Hàng Tin Tưởng Medicare
                    </h2>
                    <p class="text-slate-400 text-sm md:text-base">
                        Hàng nghìn gia đình đã lựa chọn Medicare cho hành trình tiêm chủng an toàn.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Testimonial 1 -->
                    <div class="testimonial-card bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all" data-aos="fade-up" data-aos-delay="100">
                        <div class="flex gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <i data-lucide="star" class="w-4 h-4 fill-[#eaaa00] text-[#eaaa00]"></i>
                            @endfor
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed mb-6 italic">
                            "Mình đưa bé đến Medicare tiêm rất yên tâm. Nhân viên tư vấn nhiệt tình, bác sĩ khám kỹ trước khi tiêm. Bé tiêm xong không quấy, theo dõi 30 phút rất chu đáo."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#c8102e] text-white flex items-center justify-center font-bold text-sm">NT</div>
                            <div>
                                <div class="text-white font-bold text-sm">Chị Ngọc Thanh</div>
                                <div class="text-slate-500 text-xs">Phụ huynh, Cờ Đỏ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="testimonial-card bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all" data-aos="fade-up" data-aos-delay="200">
                        <div class="flex gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <i data-lucide="star" class="w-4 h-4 fill-[#eaaa00] text-[#eaaa00]"></i>
                            @endfor
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed mb-6 italic">
                            "Giá vắc xin ở Medicare niêm yết rõ ràng, không phát sinh chi phí. Đặt lịch online rất tiện, đến nơi không phải chờ lâu. Rất hài lòng về dịch vụ!"
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#004b8f] text-white flex items-center justify-center font-bold text-sm">MH</div>
                            <div>
                                <div class="text-white font-bold text-sm">Anh Minh Hoàng</div>
                                <div class="text-slate-500 text-xs">Khách hàng, Thới Lai</div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="testimonial-card bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all" data-aos="fade-up" data-aos-delay="300">
                        <div class="flex gap-1 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <i data-lucide="star" class="w-4 h-4 fill-[#eaaa00] text-[#eaaa00]"></i>
                            @endfor
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed mb-6 italic">
                            "Cơ sở vật chất sạch sẽ, khang trang. Mình tiêm vắc xin Zona và Cúm cho ba mẹ ở đây. Bác sĩ giải thích rất dễ hiểu, cảm thấy an tâm khi tiêm cho người lớn tuổi."
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#eaaa00] text-white flex items-center justify-center font-bold text-sm">TL</div>
                            <div>
                                <div class="text-white font-bold text-sm">Chị Thùy Linh</div>
                                <div class="text-slate-500 text-xs">Khách hàng, Cờ Đỏ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. Tin Tức / Kiến Thức Y Khoa (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="space-y-6" id="news-section" data-aos="fade-up">
                <!-- Header Line: Title left, Xem tất cả right, underline divider -->
                <div class="border-b-2 border-slate-200 pb-3 flex items-center justify-between">
                    <h2 class="text-xl sm:text-2xl font-black text-[#004b8f] uppercase tracking-wide">
                        TIN TỨC & KIẾN THỨC Y KHOA
                    </h2>
                    <a href="{{ route('news.index') }}" class="text-[#eaaa00] hover:text-[#d49800] font-bold text-sm sm:text-base flex items-center gap-1 transition-colors">
                        Xem tất cả <i data-lucide="chevron-right" class="w-4 h-4 inline"></i>
                    </a>
                </div>

                <!-- Category Pills Filter Bar -->
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
                    <a href="{{ route('news.index') }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-[#eaaa00] text-slate-900 shadow-sm shrink-0">
                        Tin nóng trong ngày
                    </a>
                    <a href="{{ route('news.index', ['category' => 'Bệnh Truyền Nhiễm']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#004b8f] hover:bg-slate-200 shrink-0 transition-all">
                        Bệnh Truyền Nhiễm
                    </a>
                    <a href="{{ route('news.index', ['category' => 'Vắc Xin Mới']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#004b8f] hover:bg-slate-200 shrink-0 transition-all">
                        Vắc Xin Mới
                    </a>
                    <a href="{{ route('news.index', ['category' => 'Khuyến cáo Y tế']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#004b8f] hover:bg-slate-200 shrink-0 transition-all">
                        Khuyến Cáo Y Tế
                    </a>
                    <a href="{{ route('news.index', ['category' => 'Chăm Sóc Bé']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#004b8f] hover:bg-slate-200 shrink-0 transition-all">
                        Chăm Sóc Bé
                    </a>
                </div>

                @if(($articles ?? collect())->isNotEmpty())
                    @php
                        $mainArticle = $articles->first();
                        $sideArticles = $articles->slice(1, 3);
                    @endphp

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-2">
                        <!-- Left Column: 1 Main Featured Article -->
                        <div class="lg:col-span-6 group cursor-pointer" onclick="window.location.href='{{ route('news.show', $mainArticle->slug ?? $mainArticle->id) }}'">
                            <div class="aspect-[16/10] w-full bg-slate-100 rounded-2xl overflow-hidden mb-4 shadow-sm border border-slate-100 relative">
                                <img src="{{ asset('images/vaccines/' . ($mainArticle->image ?: 'vaxigrip.jpg')) }}" alt="{{ $mainArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                                <span class="absolute top-3 left-3 bg-[#c8102e] text-white font-extrabold text-[11px] uppercase px-3.5 py-1 rounded-full shadow-md">
                                    {{ $mainArticle->category ?: 'Mới Nhất' }}
                                </span>
                            </div>
                            <h3 class="font-bold text-[#004b8f] text-lg sm:text-xl group-hover:text-[#c8102e] transition-colors leading-snug mb-2.5 line-clamp-2">
                                {{ $mainArticle->title }}
                            </h3>
                            <p class="text-slate-600 text-sm line-clamp-3 leading-relaxed mb-4">
                                {{ $mainArticle->summary }}
                            </p>
                            <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                                <span class="flex items-center gap-1.5 text-slate-400 font-medium shrink-0">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i> {{ $mainArticle->created_at ? $mainArticle->created_at->format('d/m/Y') : '23/07/2026' }}
                                </span>
                                <a href="{{ route('news.show', $mainArticle->slug ?? $mainArticle->id) }}" class="text-[#eaaa00] hover:text-[#d49800] font-bold text-xs hover:underline flex items-center gap-1 shrink-0">
                                    Xem thêm <i data-lucide="arrow-right" class="w-3.5 h-3.5 inline"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Right Column: 3 Compact Horizontal Articles -->
                        <div class="lg:col-span-6 space-y-6">
                            @foreach($sideArticles as $sideArticle)
                                <div class="flex flex-col sm:flex-row gap-4 items-start pb-6 border-b border-slate-100 last:border-b-0 last:pb-0 group cursor-pointer" onclick="window.location.href='{{ route('news.show', $sideArticle->slug ?? $sideArticle->id) }}'">
                                    <div class="w-full sm:w-44 lg:w-48 aspect-[16/10] rounded-2xl overflow-hidden bg-slate-50 shrink-0 border border-slate-100 relative">
                                        <img src="{{ asset('images/vaccines/' . ($sideArticle->image ?: 'vaxigrip.jpg')) }}" alt="{{ $sideArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                                    </div>
                                    <div class="flex-1 min-w-0 flex flex-col justify-between h-full space-y-2">
                                        <div>
                                            <h4 class="font-bold text-[#004b8f] text-sm sm:text-base group-hover:text-[#c8102e] transition-colors leading-tight line-clamp-2 mb-1.5">
                                                {{ $sideArticle->title }}
                                            </h4>
                                            <p class="text-slate-600 text-xs line-clamp-2 leading-relaxed mb-3">
                                                {{ $sideArticle->summary }}
                                            </p>
                                        </div>
                                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 mt-auto">
                                            <span class="flex items-center gap-1.5 text-slate-400 font-medium shrink-0">
                                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> {{ $sideArticle->created_at ? $sideArticle->created_at->format('d/m/Y') : '23/07/2026' }}
                                            </span>
                                            <a href="{{ route('news.show', $sideArticle->slug ?? $sideArticle->id) }}" class="text-[#eaaa00] hover:text-[#d49800] font-bold text-xs hover:underline flex items-center gap-1 shrink-0 ml-2">
                                                Xem thêm <i data-lucide="arrow-right" class="w-3.5 h-3.5 inline"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        </div>

        <!-- 11. FAQ Accordion (CANH GIỮA MAX-W-7XL) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="bg-white p-8 md:p-12 rounded-2xl border border-red-100 shadow-sm space-y-8" id="faq-section" data-aos="fade-up">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <span class="inline-block bg-red-100 text-[#c8102e] font-bold text-xs px-3.5 py-1 rounded-full uppercase tracking-wider">
                        Hỏi Đáp
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900">
                        Câu Hỏi Thường Gặp
                    </h2>
                    <p class="text-slate-600 text-sm md:text-base">
                        Giải đáp những thắc mắc phổ biến nhất về dịch vụ tiêm chủng tại Medicare.
                    </p>
                </div>

                <div class="max-w-3xl mx-auto space-y-3" id="faq-accordion">
                    <!-- FAQ 1 -->
                    <div class="faq-item border border-red-100 rounded-xl overflow-hidden">
                        <button class="faq-toggle w-full flex items-center justify-between p-5 text-left hover:bg-red-50/50 transition-colors" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-900 text-sm md:text-base pr-4">Trẻ bao nhiêu tháng tuổi bắt đầu tiêm chủng?</span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-[#c8102e] shrink-0 faq-icon transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-5 pb-5">
                            <p class="text-sm text-slate-600 leading-relaxed">Trẻ sơ sinh nên được tiêm mũi đầu tiên (Viêm Gan B) ngay trong 24 giờ sau sinh. Từ 2 tháng tuổi, bé sẽ bắt đầu các mũi tiêm cơ bản như 6in1, Rotavirus, Phế cầu. Hãy liên hệ Medicare để được tư vấn lịch tiêm chi tiết theo từng mốc tuổi.</p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq-item border border-red-100 rounded-xl overflow-hidden">
                        <button class="faq-toggle w-full flex items-center justify-between p-5 text-left hover:bg-red-50/50 transition-colors" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-900 text-sm md:text-base pr-4">Tiêm vắc xin có tác dụng phụ không?</span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-[#c8102e] shrink-0 faq-icon transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-5 pb-5">
                            <p class="text-sm text-slate-600 leading-relaxed">Phản ứng nhẹ như sưng đỏ tại chỗ tiêm, sốt nhẹ trong 1-2 ngày là bình thường và tự hết. Các phản ứng nặng rất hiếm gặp. Tại Medicare, khách hàng được theo dõi 30 phút sau tiêm và được hướng dẫn chi tiết cách chăm sóc tại nhà.</p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq-item border border-red-100 rounded-xl overflow-hidden">
                        <button class="faq-toggle w-full flex items-center justify-between p-5 text-left hover:bg-red-50/50 transition-colors" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-900 text-sm md:text-base pr-4">Medicare có nhận đặt giữ vắc xin không?</span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-[#c8102e] shrink-0 faq-icon transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-5 pb-5">
                            <p class="text-sm text-slate-600 leading-relaxed">Có. Medicare hỗ trợ đặt giữ vắc xin 100% khi khách hàng đăng ký gói tiêm chủng trọn gói. Vắc xin được cam kết giữ hàng trong suốt phác đồ tiêm, không lo thiếu mũi hay tăng giá.</p>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="faq-item border border-red-100 rounded-xl overflow-hidden">
                        <button class="faq-toggle w-full flex items-center justify-between p-5 text-left hover:bg-red-50/50 transition-colors" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-900 text-sm md:text-base pr-4">Giá vắc xin có bao gồm phí khám sàng lọc không?</span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-[#c8102e] shrink-0 faq-icon transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-5 pb-5">
                            <p class="text-sm text-slate-600 leading-relaxed">Phí khám sàng lọc trước tiêm tại Medicare là MIỄN PHÍ hoàn toàn. Giá niêm yết trên website đã bao gồm vắc xin, vật tư tiêm và theo dõi sau tiêm. Không phát sinh thêm chi phí nào.</p>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq-item border border-red-100 rounded-xl overflow-hidden">
                        <button class="faq-toggle w-full flex items-center justify-between p-5 text-left hover:bg-red-50/50 transition-colors" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-900 text-sm md:text-base pr-4">Cần chuẩn bị gì trước khi đến tiêm chủng?</span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-[#c8102e] shrink-0 faq-icon transition-transform duration-300"></i>
                        </button>
                        <div class="faq-content hidden px-5 pb-5">
                            <p class="text-sm text-slate-600 leading-relaxed">Quý khách cần mang theo sổ tiêm chủng (nếu có), CMND/CCCD, và thông báo cho bác sĩ về tiền sử dị ứng hoặc bệnh nền. Nên cho bé ăn no trước khi tiêm, mặc quần áo thoáng mát. Đặt lịch trước qua website hoặc hotline để giảm thời gian chờ.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>    </div>
@endsection

@section('scripts')
<script>
    // ========== FAQ Accordion ==========
    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        const content = item.querySelector('.faq-content');
        const icon = item.querySelector('.faq-icon');
        const isOpen = !content.classList.contains('hidden');

        // Close all other FAQs
        document.querySelectorAll('.faq-item').forEach(otherItem => {
            if (otherItem !== item) {
                otherItem.querySelector('.faq-content').classList.add('hidden');
                otherItem.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
            }
        });

        // Toggle current
        content.classList.toggle('hidden');
        icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    // ========== Counter Animation ==========
    function animateCounters() {
        const counters = document.querySelectorAll('.counter-number');
        counters.forEach(counter => {
            if (counter.dataset.animated === 'true') return;
            counter.dataset.animated = 'true';

            const target = parseInt(counter.getAttribute('data-target')) || 0;
            const suffix = counter.getAttribute('data-suffix') || (target === 40 || target === 10000 ? '+' : '');
            const duration = 1800;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('vi-VN') + suffix;
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('vi-VN') + suffix;
                }
            };
            updateCounter();
        });
    }

    // Trigger counter animation on load & scroll
    document.addEventListener('DOMContentLoaded', () => {
        const counterSection = document.getElementById('trust-counter');
        if (counterSection) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting || entry.intersectionRatio > 0) {
                        animateCounters();
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.05 });
            observer.observe(counterSection);

            // Immediate fallback check
            const rect = counterSection.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom >= 0) {
                animateCounters();
            }
        }
    });

    // ========== Vaccine Catalog Tab Switcher ==========
    function switchVaccineTab(tabType) {
        const singleTab = document.getElementById('single-vaccines');
        const packageTab = document.getElementById('package-vaccines');
        const singleBtn = document.getElementById('tab-btn-single');
        const packageBtn = document.getElementById('tab-btn-package');

        if (tabType === 'single') {
            if (singleTab) singleTab.classList.remove('hidden');
            if (packageTab) packageTab.classList.add('hidden');
            if (singleBtn) {
                singleBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-[#c8102e] text-white shadow-md transition-all';
            }
            if (packageBtn) {
                packageBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all';
            }
        } else {
            if (singleTab) singleTab.classList.add('hidden');
            if (packageTab) packageTab.classList.remove('hidden');
            if (packageBtn) {
                packageBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-[#c8102e] text-white shadow-md transition-all';
            }
            if (singleBtn) {
                singleBtn.className = 'px-5 py-2.5 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all';
            }
        }
    }
</script>
@endsection
