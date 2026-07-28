<!-- 1. Flowbite Hero Slider Banner (FULL WIDTH - TRÀN VIỀN 100%) -->
<section class="banner-slider relative w-full overflow-hidden shadow-xl snap-section" id="hero-banner" data-aos="fade-up" data-aos-duration="600">
    @if($banners->isEmpty())
        <div class="hero-carousel-wrapper text-white w-full h-[460px] sm:h-[500px] lg:h-[520px] border-y border-red-800/40 shadow-xl overflow-hidden relative flex items-center" style="background: linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%);">
            <!-- Background ambient glow effect -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-red-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center relative z-10">
                    <!-- Left Text & Actions -->
                    <div class="lg:col-span-7 space-y-4">
                        <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-md text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wider border border-white/25">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-yellow-300"></i> Hệ Thống Tiêm Chủng An Toàn
                        </span>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-[2.75rem] font-black text-white leading-tight tracking-tight">
                            Hệ Thống Tiêm Chủng <span class="text-yellow-300 drop-shadow-sm">Medicare</span>
                        </h1>
                        <p class="text-red-100/90 text-base lg:text-lg font-medium leading-relaxed max-w-xl">
                            Hệ thống trung tâm tiêm chủng vắc xin an toàn, chất lượng hàng đầu cho trẻ em và người lớn với phác đồ y khoa chuẩn GSP.
                        </p>
                        <div class="flex flex-wrap gap-4 pt-1">
                            <a href="{{ route('vaccine.index') }}" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-[#6d0515] font-extrabold py-3 px-6 rounded-xl shadow-lg hover:shadow-yellow-400/30 transition-all duration-200">
                                Xem danh mục sản phẩm <i data-lucide="arrow-right" class="w-5 h-5"></i>
                            </a>
                            <a href="{{ route('register.show') }}" onclick="openSpaRegisterModal(event)" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 text-white hover:bg-white/20 font-bold py-3 px-6 rounded-xl backdrop-blur-sm transition-all duration-200">
                                <i data-lucide="calendar-check-2" class="w-5 h-5"></i> Đặt lịch tiêm ngay
                            </a>
                        </div>
                    </div>

                    <!-- Right Banner Image Frame -->
                    <div class="lg:col-span-5 flex items-center justify-center">
                        <div class="w-full h-56 sm:h-72 lg:h-[320px] rounded-2xl overflow-hidden border-2 border-white/30 shadow-2xl bg-black/10 relative group">
                            <img src="{{ asset('images/banners/banner_family.jpg') }}" alt="Hệ Thống Medicare" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 block" loading="lazy">
                            <div class="absolute inset-0 ring-1 ring-inset ring-white/20 rounded-2xl pointer-events-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div id="flowbite-hero-carousel" class="relative w-full" data-carousel="slide">
            <!-- Carousel wrapper with EXPLICIT height -->
            <div class="hero-carousel-wrapper relative h-[460px] sm:h-[500px] lg:h-[520px] overflow-hidden">
                @foreach($banners as $index => $banner)
                    <div class="hidden duration-700 ease-in-out h-full w-full" data-carousel-item="{{ $index === 0 ? 'active' : '' }}">
                        <div class="text-white w-full h-full border-y border-red-800/40 shadow-xl overflow-hidden relative flex items-center" style="background: linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%);">
                            <!-- Background ambient glow effect -->
                            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-red-400/10 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-6">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center relative z-10">
                                    <!-- Left Text & Actions -->
                                    <div class="lg:col-span-7 space-y-4">
                                        <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-md text-white font-bold text-xs uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wider border border-white/25">
                                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-yellow-300"></i> Medicare Standard
                                        </span>
                                        <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-[2.75rem] font-black text-white leading-tight tracking-tight">
                                            {{ $banner->title }}
                                        </h2>
                                        <p class="text-red-100/90 text-base lg:text-lg font-medium leading-relaxed max-w-xl">
                                            {{ $banner->subtitle }}
                                        </p>
                                        <div class="pt-1">
                                            <a href="{{ $banner->link_url ?: route('vaccine.index') }}" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-[#6d0515] font-extrabold py-3 px-6 rounded-xl shadow-lg hover:shadow-yellow-400/30 transition-all duration-200">
                                                Tìm hiểu thêm <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Right Banner Image Frame -->
                                    <div class="lg:col-span-5 flex items-center justify-center">
                                        <div class="w-full h-56 sm:h-72 lg:h-[320px] rounded-2xl overflow-hidden border-2 border-white/30 shadow-2xl bg-black/10 relative group">
                                            <img src="{{ $banner->image_url ? asset($banner->image_url) : asset('images/banners/banner_family.jpg') }}" alt="{{ $banner->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 block" loading="lazy">
                                            <div class="absolute inset-0 ring-1 ring-inset ring-white/20 rounded-2xl pointer-events-none"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                @foreach($banners as $index => $banner)
                    <button type="button" class="w-3 h-3 rounded-full bg-white/50 hover:bg-yellow-400 transition-colors" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}" data-carousel-slide-to="{{ $index }}"></button>
                @endforeach
            </div>
            <!-- Slider controls -->
            <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 group-hover:bg-[#c8102e] group-hover:text-white transition-all backdrop-blur-xs">
                    <i data-lucide="chevron-left" class="w-6 h-6"></i>
                </span>
            </button>
            <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-black/30 group-hover:bg-[#c8102e] group-hover:text-white transition-all backdrop-blur-xs">
                    <i data-lucide="chevron-right" class="w-6 h-6"></i>
                </span>
            </button>
        </div>
    @endif
</section>
