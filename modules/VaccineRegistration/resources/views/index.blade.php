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
                <form action="{{ route('vaccine.index') }}" method="GET" class="search-form">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" name="search" placeholder="Tìm tên vắc xin hoặc bệnh phòng ngừa..." value="{{ request('search') }}">
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
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
                    <div class="filter-options">
                        <a href="{{ route('vaccine.index', request()->except('age_group')) }}" class="filter-chip {{ !request('age_group') ? 'active' : '' }}">Tất cả</a>
                        <a href="{{ route('vaccine.index', array_merge(request()->query(), ['age_group' => 'Trẻ'])) }}" class="filter-chip {{ request('age_group') == 'Trẻ' ? 'active' : '' }}">Trẻ em</a>
                        <a href="{{ route('vaccine.index', array_merge(request()->query(), ['age_group' => 'người lớn'])) }}" class="filter-chip {{ request('age_group') == 'người lớn' ? 'active' : '' }}">Người lớn</a>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Bệnh phòng ngừa (Động từ DB)</h4>
                    <ul class="disease-list">
                        <li>
                            <a href="{{ route('vaccine.index', request()->except('search')) }}" class="{{ !request('search') ? 'active' : '' }}">Tất cả nhóm bệnh</a>
                        </li>
                        @foreach($diseases as $disease)
                        <li>
                            <a href="{{ route('vaccine.index', array_merge(request()->query(), ['search' => $disease])) }}" class="{{ request('search') == $disease ? 'active' : '' }}">
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
                <a href="{{ route('vaccine.index', array_merge(request()->query(), ['type' => 'single'])) }}" class="tab-btn" style="text-decoration: none; font-size: 16px; font-weight: 700; color: {{ request('type', 'single') === 'single' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 3px solid {{ request('type', 'single') === 'single' ? 'var(--primary-color)' : 'transparent' }}; padding-bottom: 12px; transition: all 0.2s ease;">
                    VẮC XIN LẺ
                </a>
                <a href="{{ route('vaccine.index', array_merge(request()->query(), ['type' => 'package'])) }}" class="tab-btn" style="text-decoration: none; font-size: 16px; font-weight: 700; color: {{ request('type') === 'package' ? 'var(--primary-color)' : 'var(--text-muted)' }}; border-bottom: 3px solid {{ request('type') === 'package' ? 'var(--primary-color)' : 'transparent' }}; padding-bottom: 12px; transition: all 0.2s ease;">
                    GÓI VẮC XIN GIA ĐÌNH
                </a>
            </div>

            <div class="section-header">
                <h2>
                    @if(request('type') === 'package')
                        Danh Sách Gói Vắc Xin ({{ $vaccines->count() }})
                    @else
                        Danh Mục Vắc Xin Lẻ ({{ $vaccines->count() }})
                    @endif
                </h2>
                @if(request('search') || request('age_group') || request('type'))
                    <a href="{{ route('vaccine.index') }}" class="clear-filters-btn">Xóa bộ lọc <i data-lucide="x"></i></a>
                @endif
            </div>

            @if($vaccines->isEmpty())
                <div class="empty-vaccines">
                    <i data-lucide="alert-circle" class="empty-icon"></i>
                    <p>Không tìm thấy vắc xin nào phù hợp với bộ lọc hiện tại.</p>
                    <a href="{{ route('vaccine.index') }}" class="btn-primary">Xem tất cả vắc xin</a>
                </div>
            @else
                <div class="vaccines-grid">
                    @foreach($vaccines as $vaccine)
                        <div class="vaccine-card {{ isset($cart[$vaccine->id]) ? 'selected' : '' }}" data-id="{{ $vaccine->id }}" style="display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; padding: 0;">
                            <a href="{{ route('vaccine.show', $vaccine->id) }}" style="text-decoration: none; color: inherit; display: block;">
                                <div>
                                    <div class="vaccine-card-img" style="height: 160px; width: 100%; background: var(--bg-main); display: flex; align-items: center; justify-content: center; overflow: hidden; border-bottom: 1px solid var(--border-color);">
                                        <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: '1-hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/1-hexaxim.jpg') }}';" alt="{{ $vaccine->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="vaccine-card-body" style="padding: 20px;">
                                    <div style="display: inline-block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary-color); background-color: rgba(239, 68, 68, 0.08); padding: 4px 10px; border-radius: 4px; margin-bottom: 10px; letter-spacing: 0.05em;">
                                        {{ $vaccine->origin }}
                                    </div>
                                    <h3 class="vaccine-name" style="margin-top: 0; padding-right: 0;">
                                        {{ $vaccine->name }}
                                    </h3>
                                    <div class="vaccine-prevent">
                                        <strong>Phòng bệnh:</strong> {{ Str::limit($vaccine->disease_prevention, 60) }}
                                    </div>
                                    <div class="vaccine-age">
                                        <strong>Độ tuổi:</strong> {{ $vaccine->age_group }}
                                    </div>
                                </div>
                            </a>
                            <div class="vaccine-card-footer" style="padding: 15px 12px; border-top: 1px solid var(--border-color); background: var(--bg-card); display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div class="vaccine-price" style="flex-shrink: 0;">
                                    <span style="font-size: 11px; color: var(--text-light); text-transform: uppercase; display: block; line-height: 1.2;">Giá tiêm:</span>
                                    <strong style="font-size: 16px; color: var(--primary-color); font-family: 'Roboto', sans-serif; white-space: nowrap;">{{ number_format($vaccine->price, 0, ',', '.') }} đ</strong>
                                </div>
                                <div style="display: flex; gap: 6px; align-items: center; flex-shrink: 0;">
                                    <a href="{{ route('vaccine.show', $vaccine->id) }}" class="btn-view-detail-card" style="width: 34px; height: 34px; border-radius: 6px; border: 1px solid var(--border-color); color: var(--text-primary); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; background: var(--bg-card); flex-shrink: 0; transition: all 0.2s;"><i data-lucide="info" style="width: 16px; height: 16px;"></i></a>
                                    <button class="btn-select-vaccine {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCart({{ $vaccine->id }})" style="padding: 8px 12px; font-size: 12.5px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0;">
                                        @if(isset($cart[$vaccine->id]))
                                            <i data-lucide="check" style="width: 14px; height: 14px;"></i> <span>Đã chọn</span>
                                        @else
                                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i> <span>Chọn tiêm</span>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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
            <a href="{{ route('register.show') }}" class="btn-checkout">
                <span>Đăng ký tiêm chủng</span>
                <i data-lucide="arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection
