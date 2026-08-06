@extends('vaccine::layouts.app')

@section('title', 'Danh Mục Sản Phẩm Tiêm Chủng')

@section('content')
<!-- CATALOG HERO BANNER (Tràn viền) -->
<section class="catalog-hero" style="background: linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%), url('https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=1600&q=80') no-repeat center center / cover; margin-top: -2rem;">
    <div class="catalog-hero-container">
        <div class="catalog-hero-content">
            <div class="catalog-breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <i data-lucide="chevron-right"></i>
                <span>Danh mục sản phẩm</span>
            </div>
            <h1>Danh mục sản phẩm tiêm chủng</h1>
            <p>Tra cứu vắc xin đang có tại Medicare, lọc theo bệnh phòng ngừa, độ tuổi, xuất xứ và số liều theo phác đồ.</p>
            <div class="search-bar-container catalog-search-box">
                <form action="{{ route('vaccine.index') }}" method="GET" class="search-form" onsubmit="filterVaccinesSpa(event)">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" name="search" id="spaSearchInput" placeholder="Tìm tên vắc xin..." value="{{ request('search') }}" oninput="debouncedFilterVaccinesSpa()">
                    <button type="submit" class="search-btn">Tìm kiếm</button>
                </form>
            </div>
        </div>
        <div class="catalog-hero-visual" aria-hidden="true">
            @foreach($productCategories->take(3) as $category)
                <div class="hero-vaccine-orbit orbit-{{ $loop->iteration }}">
                    <img src="{{ asset('images/vaccines/' . ($category['image'] ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="">
                </div>
            @endforeach
        </div>
    </div>
</section>

<div class="product-catalog-page" style="margin-top: 0;">

    @if($productCategories->isNotEmpty())
        <section class="catalog-category-section">
            <div class="catalog-section-heading">
                <div>
                    <span>Danh mục sản phẩm</span>
                    <h2>Chọn theo nhóm bệnh cần phòng ngừa</h2>
                </div>
                <button type="button" onclick="resetVaccineFilters(event)" class="catalog-link-btn">Xem tất cả danh mục</button>
            </div>
            <div class="catalog-category-grid collapsed" id="categoryGrid">
                @foreach($productCategories->take(24) as $category)
                    <a href="{{ route('vaccine.disease', ['disease' => urlencode($category['name'])]) }}" class="catalog-category-card" style="text-decoration: none; color: inherit; cursor: pointer;">
                        <div class="catalog-category-icon">
                            <img src="{{ asset('images/vaccines/' . ($category['image'] ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="{{ $category['name'] }}">
                        </div>
                        <span>{{ $category['name'] }}</span>
                        <small>{{ $category['count'] }} sản phẩm</small>
                    </a>
                @endforeach
            </div>
            @if($productCategories->count() > 8)
                <div style="display: flex; justify-content: center; margin-top: 24px;">
                    <button type="button" id="btnToggleCategories" onclick="toggleCategoriesGrid()" style="background: transparent; border: 1px solid var(--primary-color, #c8102e); color: var(--primary-color, #c8102e); padding: 8px 24px; border-radius: 20px; font-weight: 700; font-size: 13.5px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                        <span>Xem thêm danh mục</span>
                        <i data-lucide="chevron-down" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>
            @endif
        </section>
    @endif

    <div class="catalog-layout">
        <aside class="catalog-filter-panel">
            <div class="catalog-filter-header">
                <h3><i data-lucide="list-filter"></i> Bộ lọc vắc xin</h3>
            </div>

            <div class="lc-filter-group {{ request('age_group') ? 'open' : '' }}">
                <button type="button" class="lc-filter-toggle" onclick="toggleCatalogFilterGroup(this)">
                    <span>Độ tuổi</span>
                    <i data-lucide="{{ request('age_group') ? 'chevron-up' : 'chevron-down' }}"></i>
                </button>
                <div class="lc-filter-body collapsed" id="ageGroupFilterSelect">
                    <button type="button" onclick="setAgeGroupFilter('', event)" class="lc-check-row {{ !request('age_group') ? 'active' : '' }}" data-value="">
                        <span class="lc-check-box"><i data-lucide="check"></i></span>
                        <span>Tất cả</span>
                    </button>
                    @foreach($ageGroups as $ageGroup)
                        <button type="button" onclick="setAgeGroupFilter(@js($ageGroup), event)" class="lc-check-row {{ request('age_group') == $ageGroup ? 'active' : '' }}" data-value="{{ $ageGroup }}">
                            <span class="lc-check-box"><i data-lucide="check"></i></span>
                            <span>{{ $ageGroup }}</span>
                        </button>
                    @endforeach
                    @if(count($ageGroups) > 4)
                        <button type="button" class="filter-toggle-more-btn" onclick="toggleFilterItems(this)" style="background: none; border: none; color: var(--primary-color, #c8102e); font-size: 12.5px; font-weight: 700; cursor: pointer; padding: 8px 0; display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start; margin-top: 4px;">
                            <span>Xem thêm</span> <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="lc-filter-group {{ request('disease') ? 'open' : '' }}">
                <button type="button" class="lc-filter-toggle" onclick="toggleCatalogFilterGroup(this)">
                    <span>Phòng bệnh</span>
                    <i data-lucide="{{ request('disease') ? 'chevron-up' : 'chevron-down' }}"></i>
                </button>
                <div class="lc-filter-body collapsed" id="diseaseFilterSelect">
                    <label class="lc-filter-search">
                        <input type="text" placeholder="Tìm theo bệnh" oninput="filterDiseaseOptions(this.value)">
                        <i data-lucide="search"></i>
                    </label>
                    <button type="button" onclick="setDiseaseFilter('', event)" class="lc-check-row {{ !request('disease') ? 'active' : '' }}" data-value="">
                        <span class="lc-check-box"><i data-lucide="check"></i></span>
                        <span>Tất cả</span>
                    </button>
                    @foreach($diseases as $disease)
                        <button type="button" onclick="setDiseaseFilter(@js($disease), event)" class="lc-check-row disease-option {{ request('disease') == $disease ? 'active' : '' }}" data-value="{{ $disease }}">
                            <span class="lc-check-box"><i data-lucide="check"></i></span>
                            <span>{{ $disease }}</span>
                        </button>
                    @endforeach
                    @if(count($diseases) > 4)
                        <button type="button" class="filter-toggle-more-btn" onclick="toggleFilterItems(this)" style="background: none; border: none; color: var(--primary-color, #c8102e); font-size: 12.5px; font-weight: 700; cursor: pointer; padding: 8px 0; display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start; margin-top: 4px;">
                            <span>Xem thêm</span> <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="lc-filter-group open">
                <button type="button" class="lc-filter-toggle" onclick="toggleCatalogFilterGroup(this)">
                    <span>Nơi sản xuất</span>
                    <i data-lucide="chevron-up"></i>
                </button>
                <div class="lc-filter-body collapsed" id="originFilterSelect">
                    <label class="lc-filter-search">
                        <input type="text" placeholder="Tìm theo quốc gia" oninput="filterOriginOptions(this.value)">
                        <i data-lucide="search"></i>
                    </label>
                    <button type="button" onclick="setOriginFilter('', event)" class="lc-check-row {{ !request('origin') ? 'active' : '' }}" data-value="">
                        <span class="lc-check-box"><i data-lucide="check"></i></span>
                        <span>Tất cả</span>
                    </button>
                    @foreach($origins as $origin)
                        <button type="button" onclick="setOriginFilter(@js($origin), event)" class="lc-check-row origin-option {{ request('origin') == $origin ? 'active' : '' }}" data-value="{{ $origin }}">
                            <span class="lc-check-box"><i data-lucide="check"></i></span>
                            <span>{{ $origin }}</span>
                        </button>
                    @endforeach
                    @if(count($origins) > 4)
                        <button type="button" class="filter-toggle-more-btn" onclick="toggleFilterItems(this)" style="background: none; border: none; color: var(--primary-color, #c8102e); font-size: 12.5px; font-weight: 700; cursor: pointer; padding: 8px 0; display: flex; align-items: center; gap: 4px; width: 100%; justify-content: flex-start; margin-top: 4px;">
                            <span>Xem thêm</span> <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i>
                        </button>
                    @endif
                </div>
            </div>

            <div class="lc-filter-group open">
                <button type="button" class="lc-filter-toggle" onclick="toggleCatalogFilterGroup(this)">
                    <span>Số liều theo phác đồ</span>
                    <i data-lucide="chevron-up"></i>
                </button>
                <div class="lc-filter-body" id="dosesFilterSelect">
                    <button type="button" onclick="setDosesFilter('', event)" class="lc-check-row {{ !request('doses') ? 'active' : '' }}" data-value="">
                        <span class="lc-check-box"><i data-lucide="check"></i></span>
                        <span>Tất cả</span>
                    </button>
                    @foreach($doseOptions as $dose)
                        <button type="button" onclick="setDosesFilter(@js((string) $dose), event)" class="lc-check-row {{ (string) request('doses') === (string) $dose ? 'active' : '' }}" data-value="{{ $dose }}">
                            <span class="lc-check-box"><i data-lucide="check"></i></span>
                            <span>{{ $dose }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </aside>

        <section class="catalog-products-section">
            <!-- Mobile Clean Catalog Header Toolbar -->
            <div class="mobile-catalog-header-bar">
                <div class="mobile-catalog-title">
                    <span>{{ request('type') === 'package' ? 'Danh sách gói vắc xin' : 'Danh sách vắc xin' }}</span>
                    <small>({{ $vaccines->total() }} sản phẩm)</small>
                </div>
                <button type="button" class="btn-mobile-filter-pill" onclick="toggleMobileFilterBottomSheet(true)">
                    <i data-lucide="sliders-horizontal"></i>
                    <span>Bộ lọc</span>
                    @if(request('age_group') || request('disease') || request('origin') || request('doses') || (request('sort') && request('sort') !== 'popular'))
                        <span class="filter-active-dot"></span>
                    @endif
                </button>
            </div>

            <div class="catalog-toolbar">
                <div>
                    <h2 id="vaccineSectionTitle">
                        {{ request('type') === 'package' ? 'Danh sách gói vắc xin' : 'Danh sách vắc xin' }}
                        <span id="vaccineCountLabel" class="sr-only">{{ $vaccines->total() }}</span>
                    </h2>
                </div>
                <div class="catalog-sort-box">
                    <span>Sắp xếp theo:</span>
                    <div class="catalog-sort-pills" id="sortPillGroup">
                        <button type="button" data-sort="popular" onclick="setSortFilter('popular', event)" class="sort-pill {{ request('sort', 'popular') === 'popular' ? 'active' : '' }}">Được quan tâm</button>
                        <button type="button" data-sort="price_asc" onclick="setSortFilter('price_asc', event)" class="sort-pill {{ request('sort') === 'price_asc' ? 'active' : '' }}">Giá thấp</button>
                        <button type="button" data-sort="price_desc" onclick="setSortFilter('price_desc', event)" class="sort-pill {{ request('sort') === 'price_desc' ? 'active' : '' }}">Giá cao</button>
                    </div>
                    <button type="button" id="btnClearFilters" onclick="resetVaccineFilters(event)" class="clear-filters-btn" style="display: {{ (request('search') || request('disease') || request('age_group') || request('origin') || request('doses') || request('type') || request('sort')) ? 'inline-flex' : 'none' }};">Xóa bộ lọc <i data-lucide="x"></i></button>
                </div>
            </div>

            <div id="vaccineGridContainer">
                @include('vaccine::partials.grid', ['vaccines' => $vaccines, 'cart' => $cart])
            </div>
        </section>
    </div>

    <!-- TẦNG BANNER TƯ VẤN MEDICARE -->
    <section class="catalog-advice-banner">
        <div>
            <span>Tư vấn tiêm chủng</span>
            <h2>Bạn chưa biết nên tiêm gì?</h2>
            <p>Chọn sản phẩm trong danh mục hoặc gửi thông tin để đội ngũ Medicare tư vấn phác đồ phù hợp.</p>
        </div>
        <a href="{{ route('register.show') }}" onclick="openSpaConsultationModal(event)" class="catalog-advice-btn">
            Bắt đầu ngay <i data-lucide="arrow-right"></i>
        </a>
    </section>
</div>

<!-- Mobile Bottom Sheet Filter Drawer -->
<div class="mobile-filter-overlay" id="mobileFilterOverlay" onclick="toggleMobileFilterBottomSheet(false)"></div>
<div class="mobile-filter-bottom-sheet" id="mobileFilterBottomSheet">
    <div class="mobile-filter-sheet-header">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; margin: 0;">
            <i data-lucide="sliders-horizontal" style="width: 18px; height: 18px; color: var(--primary-color, #c8102e);"></i>
            Bộ lọc & Sắp xếp vắc xin
        </h3>
        <button type="button" onclick="toggleMobileFilterBottomSheet(false)" style="border: none; background: #f1f5f9; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <i data-lucide="x" style="width: 18px; height: 18px; color: #64748b;"></i>
        </button>
    </div>
    <div class="mobile-filter-sheet-body" style="padding: 16px 20px; max-height: 65vh; overflow-y: auto;">
        <!-- Sort Options -->
        <div style="margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px dashed #e2e8f0;">
            <label style="font-size: 13px; font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px;">Sắp xếp sản phẩm</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                <button type="button" onclick="setSortFilter('popular', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('sort', 'popular') === 'popular' ? 'active' : '' }}">Được quan tâm</button>
                <button type="button" onclick="setSortFilter('price_asc', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('sort') === 'price_asc' ? 'active' : '' }}">Giá: Thấp -> Cao</option>
                <button type="button" onclick="setSortFilter('price_desc', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('sort') === 'price_desc' ? 'active' : '' }}">Giá: Cao -> Thấp</option>
                <button type="button" onclick="setSortFilter('name_asc', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('sort') === 'name_asc' ? 'active' : '' }}">Tên A-Z</option>
            </div>
        </div>
        <!-- Age Groups -->
        <div style="margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px dashed #e2e8f0;">
            <label style="font-size: 13px; font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px;">Độ tuổi chỉ định</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                <button type="button" onclick="setAgeGroupFilter('', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ !request('age_group') ? 'active' : '' }}">Tất cả</button>
                @foreach($ageGroups as $ageGroup)
                    <button type="button" onclick="setAgeGroupFilter(@js($ageGroup), event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('age_group') == $ageGroup ? 'active' : '' }}">{{ $ageGroup }}</button>
                @endforeach
            </div>
        </div>
        <!-- Origins -->
        <div style="margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px dashed #e2e8f0;">
            <label style="font-size: 13px; font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px;">Xuất xứ / Nơi sản xuất</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                <button type="button" onclick="setOriginFilter('', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ !request('origin') ? 'active' : '' }}">Tất cả</button>
                @foreach($origins as $origin)
                    <button type="button" onclick="setOriginFilter(@js($origin), event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('origin') == $origin ? 'active' : '' }}">{{ $origin }}</button>
                @endforeach
            </div>
        </div>
        <!-- Doses -->
        <div style="margin-bottom: 10px;">
            <label style="font-size: 13px; font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px;">Số liều theo phác đồ</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                <button type="button" onclick="setDoseFilter('', event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ !request('doses') ? 'active' : '' }}">Tất cả</button>
                @foreach($doses as $dose)
                    <button type="button" onclick="setDoseFilter(@js((string)$dose), event); toggleMobileFilterBottomSheet(false);" class="mobile-filter-pill {{ request('doses') == (string)$dose ? 'active' : '' }}">{{ $dose }} liều</button>
                @endforeach
            </div>
        </div>
    </div>
    <div class="mobile-filter-sheet-footer" style="padding: 12px 20px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
        <button type="button" onclick="resetVaccineFilters(event); toggleMobileFilterBottomSheet(false);" style="flex: 1; padding: 10px; border-radius: 20px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 13px; font-weight: 700; cursor: pointer;">Đặt lại</button>
        <button type="button" onclick="toggleMobileFilterBottomSheet(false)" style="flex: 1; padding: 10px; border-radius: 20px; border: none; background: var(--primary-color, #c8102e); color: #ffffff; font-size: 13px; font-weight: 800; cursor: pointer;">Áp dụng</button>
    </div>
</div>

@endsection

@section('styles')
<style>
    .catalog-category-grid.collapsed .catalog-category-card:nth-child(n+9) {
        display: none !important;
    }
    .lc-filter-body.collapsed .lc-check-row:nth-child(n+7) {
        display: none !important;
    }
    
    /* Cấu hình lại phân trang catalog tránh bị đè nút */
    .catalog-pagination nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px dashed #cbd5e1;
    }
    .catalog-pagination nav > div:first-child {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    .catalog-pagination nav span.relative.z-0.inline-flex {
        display: inline-flex;
        gap: 6px;
        box-shadow: none !important;
        border: none !important;
        background: transparent !important;
    }
    .catalog-pagination nav span.relative.z-0.inline-flex a,
    .catalog-pagination nav span.relative.z-0.inline-flex > span {
        border-radius: 8px !important;
        margin: 0 !important;
        border: 1px solid #cbd5e1 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 10px;
        font-size: 13.5px;
        font-weight: 700;
        color: #334155 !important;
        background-color: #ffffff !important;
        text-decoration: none;
        transition: all 0.2s;
    }
    .catalog-pagination nav span.relative.z-0.inline-flex span[aria-current="page"] span {
        background-color: var(--primary-color, #c8102e) !important;
        color: #ffffff !important;
        border-color: var(--primary-color, #c8102e) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 8px !important;
    }
    .catalog-pagination nav span.relative.z-0.inline-flex a:hover {
        background-color: #f8fafc !important;
        border-color: var(--primary-color, #c8102e) !important;
        color: var(--primary-color, #c8102e) !important;
    }
    .catalog-pagination nav span.relative.z-0.inline-flex span[aria-disabled="true"] {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f8fafc !important;
    }
    .catalog-pagination nav span.relative.z-0.inline-flex span[aria-current="page"] {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }
</style>
@endsection

@section('scripts')
<script>
    function toggleCategoriesGrid() {
        const grid = document.getElementById('categoryGrid');
        const btn = document.getElementById('btnToggleCategories');
        if (!grid || !btn) return;
        
        const isCollapsed = grid.classList.contains('collapsed');
        if (isCollapsed) {
            grid.classList.remove('collapsed');
            btn.innerHTML = '<span>Thu gọn</span> <i data-lucide="chevron-up" style="width: 16px; height: 16px;"></i>';
        } else {
            grid.classList.add('collapsed');
            btn.innerHTML = '<span>Xem thêm danh mục</span> <i data-lucide="chevron-down" style="width: 16px; height: 16px;"></i>';
        }
        lucide.createIcons();
    }

    function toggleFilterItems(btn) {
        const body = btn.closest('.lc-filter-body');
        if (!body) return;
        
        const isCollapsed = body.classList.contains('collapsed');
        if (isCollapsed) {
            body.classList.remove('collapsed');
            btn.innerHTML = '<span>Thu gọn</span> <i data-lucide="chevron-up" style="width: 14px; height: 14px;"></i>';
        } else {
            body.classList.add('collapsed');
            btn.innerHTML = '<span>Xem thêm</span> <i data-lucide="chevron-down" style="width: 14px; height: 14px;"></i>';
        }
        lucide.createIcons();
    }
</script>
@endsection
