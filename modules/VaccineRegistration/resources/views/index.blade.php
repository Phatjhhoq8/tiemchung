@extends('vaccine::layouts.app')

@section('title', 'Đăng Ký Tiêm Chủng VNVC - Chọn Vắc Xin')

@section('content')
<div class="vaccine-container">
    <!-- Banner Section -->
    <section class="hero-banner">
        <div class="banner-content">
            <h1>Đăng Ký Tiêm Chủng Trực Tuyến</h1>
            <p>Chọn vắc xin mong muốn, điền thông tin và đăng ký tiêm chủng dễ dàng tại hơn 100 trung tâm VNVC trên toàn quốc.</p>
            <div class="search-bar-container">
                <form action="{{ route('vaccine.index') }}" method="GET" class="search-form">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" name="search" placeholder="Tìm tên vắc xin hoặc bệnh phòng ngừa..." value="{{ request('search') }}">
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
                        <a href="{{ route('vaccine.index') }}" class="filter-chip {{ !request('age_group') ? 'active' : '' }}">Tất cả</a>
                        <a href="{{ route('vaccine.index', ['age_group' => 'Trẻ']) }}" class="filter-chip {{ request('age_group') == 'Trẻ' ? 'active' : '' }}">Trẻ em</a>
                        <a href="{{ route('vaccine.index', ['age_group' => 'người lớn']) }}" class="filter-chip {{ request('age_group') == 'người lớn' ? 'active' : '' }}">Người lớn</a>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Bệnh phòng ngừa phổ biến</h4>
                    <ul class="disease-list">
                        <li>
                            <a href="{{ route('vaccine.index') }}" class="{{ !request('search') ? 'active' : '' }}">Tất cả nhóm bệnh</a>
                        </li>
                        @foreach($diseases as $disease)
                        <li>
                            <a href="{{ route('vaccine.index', ['search' => $disease]) }}" class="{{ request('search') == $disease ? 'active' : '' }}">
                                {{ $disease }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Vaccine Grid -->
        <section class="vaccine-grid-section">
            <div class="section-header">
                <h2>Danh Mục Vắc Xin lẻ ({{ $vaccines->count() }})</h2>
                @if(request('search') || request('age_group'))
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
                        <div class="vaccine-card {{ isset($cart[$vaccine->id]) ? 'selected' : '' }}" data-id="{{ $vaccine->id }}">
                            <div class="vaccine-badge">{{ $vaccine->origin }}</div>
                            <div class="vaccine-card-body">
                                <h3 class="vaccine-name">{{ $vaccine->name }}</h3>
                                <div class="vaccine-prevent">
                                    <strong>Phòng bệnh:</strong> {{ $vaccine->disease_prevention }}
                                </div>
                                <div class="vaccine-age">
                                    <strong>Độ tuổi:</strong> {{ $vaccine->age_group }}
                                </div>
                                <p class="vaccine-desc">{{ Str::limit($vaccine->description, 100) }}</p>
                            </div>
                            <div class="vaccine-card-footer">
                                <div class="vaccine-price">
                                    <span>Giá tiêm:</span>
                                    <strong>{{ number_format($vaccine->price, 0, ',', '.') }} đ</strong>
                                </div>
                                <button class="btn-select-vaccine {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCart({{ $vaccine->id }})">
                                    @if(isset($cart[$vaccine->id]))
                                        <i data-lucide="check"></i> <span>Đã chọn</span>
                                    @else
                                        <i data-lucide="plus"></i> <span>Chọn tiêm</span>
                                    @endif
                                </button>
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
