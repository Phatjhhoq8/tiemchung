<!-- 6. Danh Mục Sản Phẩm (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-8" id="vaccine-catalog" data-aos="fade-up">
        <!-- Centered Header Title in Medicare Red (#c8102e) -->
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <span class="section-badge">
                Danh Mục Sản Phẩm
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-[#c8102e] uppercase tracking-tight">
                Danh Mục Sản Phẩm Tại Medicare
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
            <a href="{{ route('vaccine.index') }}" class="inline-flex items-center gap-1.5 text-[#c8102e] hover:text-[#a00d24] font-bold text-sm transition-colors ml-auto">
                Xem tất cả danh mục sản phẩm <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <!-- Tabs Content -->
        <div id="vaccine-tab-content">
            <!-- Tab 1: Single Featured Vaccines Grid (4 columns x 2 rows) -->
            <div id="single-vaccines" role="tabpanel">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredVaccines as $vaccine)
                        <div class="group bg-white rounded-3xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between cursor-pointer" onclick="openVaccineDetailModal({{ $vaccine->id }}, event)">
                            <div>
                                <!-- Light grey image container frame -->
                                <div class="relative aspect-[16/10] w-full bg-white rounded-2xl p-4 flex items-center justify-center overflow-hidden mb-3">
                                    <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'default_vaccine.jpg')) }}" alt="{{ $vaccine->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                    <span class="absolute top-2.5 right-2.5 bg-[#c8102e] text-white font-black text-[10px] uppercase px-2.5 py-0.5 rounded-full shadow-sm">
                                        MỚI
                                    </span>
                                    <span class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm text-white font-medium text-[11px] px-2 py-0.5 rounded-md flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3 h-3 text-white"></i>
                                        <span id="vaccine-views-count-{{ $vaccine->id }}">{{ number_format($vaccine->views ?? 0, 0, ',', '.') }}</span> lượt xem
                                    </span>
                                </div>
                                <!-- Centered Red Title -->
                                <h3 class="text-center font-bold text-slate-800 text-sm md:text-[15px] uppercase leading-tight line-clamp-2 mb-1 group-hover:text-[#c8102e] transition-colors">
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
                            <div class="group bg-white rounded-3xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between cursor-pointer" onclick="openVaccineDetailModal({{ $package->id }}, event)">
                                <div>
                                    <div class="relative aspect-[16/10] w-full bg-white rounded-2xl p-4 flex items-center justify-center overflow-hidden mb-3">
                                        <img src="{{ asset('images/vaccines/' . ($package->image ?: 'default_vaccine.jpg')) }}" alt="{{ $package->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                        <span class="absolute top-2.5 right-2.5 bg-[#eaaa00] text-slate-900 font-black text-[10px] uppercase px-2.5 py-0.5 rounded-full shadow-sm">
                                            GÓI ƯU ĐÃI
                                        </span>
                                        <span class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-sm text-white font-medium text-[11px] px-2 py-0.5 rounded-md flex items-center gap-1">
                                            <i data-lucide="eye" class="w-3 h-3 text-white"></i>
                                            <span id="vaccine-views-count-{{ $package->id }}">{{ number_format($package->views ?? 0, 0, ',', '.') }}</span> lượt xem
                                        </span>
                                    </div>
                                    <h3 class="text-center font-bold text-slate-800 text-base uppercase leading-snug line-clamp-1 mb-1 group-hover:text-[#c8102e] transition-colors">
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
