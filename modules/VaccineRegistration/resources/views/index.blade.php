@extends('vaccine::layouts.app')

@section('title', 'Đăng Ký Tiêm Chủng - Chọn Vắc Xin')

@section('content')
<div class="vaccine-container">
    <!-- Banner Section -->
    <section class="hero-banner">
        <div class="banner-content">
            <h1>Bảng Giá Vắc Xin & Đăng Ký</h1>
            <p>Tra cứu giá vắc xin lẻ hoặc gói vắc xin gia đình ưu đãi, đặt lịch tiêm chủng trực tuyến nhanh chóng.</p>
            <div class="search-bar-container">
                <form action="{{ route('vaccine.index') }}" method="GET" class="search-form" onsubmit="filterVaccinesSpa(event)">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" name="search" id="spaSearchInput" placeholder="Tìm tên vắc xin hoặc bệnh phòng ngừa..." value="{{ request('search') }}" oninput="debouncedFilterVaccinesSpa()">
                    @if(request('type'))
                        <input type="hidden" name="type" id="spaTypeInput" value="{{ request('type') }}">
                    @endif
                    <button type="submit" class="search-btn">Tìm kiếm</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="main-layout">
        <!-- Sidebar Filters -->
        <aside class="sidebar-filters">
            <div class="filter-card">
                <h3><i data-lucide="filter"></i> Bộ lọc nhanh</h3>
                
                <div class="filter-group">
                    <h4>Độ tuổi chỉ định</h4>
                    <div class="filter-options" id="ageGroupFilterContainer">
                        <button type="button" onclick="setAgeGroupFilter('', event)" class="filter-chip {{ !request('age_group') ? 'active' : '' }}">Tất cả</button>
                        <button type="button" onclick="setAgeGroupFilter('Trẻ', event)" class="filter-chip {{ request('age_group') == 'Trẻ' ? 'active' : '' }}">Trẻ em</button>
                        <button type="button" onclick="setAgeGroupFilter('người lớn', event)" class="filter-chip {{ request('age_group') == 'người lớn' ? 'active' : '' }}">Người lớn</button>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Bệnh phòng ngừa</h4>
                    <ul class="disease-list" id="diseaseFilterList">
                        <li>
                            <a href="#" onclick="setDiseaseFilter('', event)" class="{{ !request('search') ? 'active' : '' }}">Tất cả nhóm bệnh</a>
                        </li>
                        @foreach($diseases as $disease)
                        <li>
                            <a href="#" onclick="setDiseaseFilter('{{ $disease }}', event)" class="{{ request('search') == $disease ? 'active' : '' }}">
                                {{ $disease }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Vaccine Grid & Tabs -->
        <section class="vaccine-grid-section">
            <!-- Tabs phân loại vắc xin lẻ / gói vắc xin -->
            <div class="tabs-container" style="display: flex; gap: 24px; margin-bottom: 24px; border-bottom: 2px solid var(--border-color); padding-bottom: 0;">
                <button type="button" id="tabBtnSingle" onclick="setVaccineTypeFilter('single', event)" class="tab-btn" style="background: none; border: none; font-size: 16px; font-weight: 700; cursor: pointer; color: {{ request('type', 'single') === 'single' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 3px solid {{ request('type', 'single') === 'single' ? 'var(--primary-color)' : 'transparent' }}; padding-bottom: 12px; transition: all 0.2s ease;">
                    VẮC XIN LẺ
                </button>
                <button type="button" id="tabBtnPackage" onclick="setVaccineTypeFilter('package', event)" class="tab-btn" style="background: none; border: none; font-size: 16px; font-weight: 700; cursor: pointer; color: {{ request('type') === 'package' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 3px solid {{ request('type') === 'package' ? 'var(--primary-color)' : 'transparent' }}; padding-bottom: 12px; transition: all 0.2s ease;">
                    GÓI VẮC XIN GIA ĐÌNH
                </button>
            </div>

            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 id="vaccineSectionTitle">
                    @if(request('type') === 'package')
                        Danh Sách Gói Vắc Xin (<span id="vaccineCountLabel">{{ $vaccines->count() }}</span>)
                    @else
                        Danh Mục Vắc Xin Lẻ (<span id="vaccineCountLabel">{{ $vaccines->count() }}</span>)
                    @endif
                </h2>
                <button type="button" id="btnClearFilters" onclick="resetVaccineFilters(event)" class="clear-filters-btn" style="background: none; border: 1px solid var(--border-color); padding: 6px 14px; border-radius: 6px; font-size: 13px; color: var(--text-muted); cursor: pointer; display: {{ (request('search') || request('age_group') || request('type')) ? 'inline-flex' : 'none' }}; align-items: center; gap: 6px;">
                    Xóa bộ lọc <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                </button>
            </div>

            <!-- Dynamic Vaccine Grid Partial Container -->
            <div id="vaccineGridContainer">
                @include('vaccine::partials.grid', ['vaccines' => $vaccines, 'cart' => $cart])
            </div>
        </section>
    </div>
</div>

<!-- Floating Cart Container -->
<div class="floating-cart {{ empty($cart) ? 'hidden' : '' }}" id="floatingCart">
    <div class="cart-toggle-btn" onclick="toggleCartDrawer()">
        <div class="cart-icon-wrapper">
            <i data-lucide="shopping-cart"></i>
            <span class="cart-badge" id="cartCount">{{ count($cart) }}</span>
        </div>
        <div class="cart-toggle-text">
            <span>Danh sách đăng ký</span>
            <strong id="cartTotalPrice">{{ number_format(collect($cart)->sum('price'), 0, ',', '.') }} đ</strong>
        </div>
        <i data-lucide="chevron-up" class="chevron-icon" id="cartChevron"></i>
    </div>
    
    <div class="cart-drawer" id="cartDrawer">
        <div class="drawer-header">
            <h4>Vắc xin đã chọn</h4>
            <button class="clear-cart-text-btn" onclick="clearCartUI()">Xóa tất cả</button>
        </div>
        <div class="drawer-content" id="cartItemsList">
            @foreach($cart as $id => $item)
                <div class="cart-item" data-id="{{ $id }}">
                    <div class="cart-item-info">
                        <h5>{{ $item['name'] }}</h5>
                        <span>{{ number_format($item['price'], 0, ',', '.') }} đ</span>
                    </div>
                    <button class="remove-item-btn" onclick="toggleCart({{ $id }})">
                        <i data-lucide="trash-2"></i>
                    </button>
                </div>
            @endforeach
        </div>
        <div class="drawer-footer">
            <div class="drawer-total">
                <span>Tổng chi phí dự kiến:</span>
                <strong id="drawerTotalPrice">{{ number_format(collect($cart)->sum('price'), 0, ',', '.') }} đ</strong>
            </div>
            <button onclick="openSpaRegisterModal(event)" class="btn-checkout" style="width: 100%; border: none; cursor: pointer; text-decoration: none;">
                <span>Đăng ký tiêm chủng</span>
                <i data-lucide="arrow-right"></i>
            </button>
        </div>
    </div>
</div>
@endsection
