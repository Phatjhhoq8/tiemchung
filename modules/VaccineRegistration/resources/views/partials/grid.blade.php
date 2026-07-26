@if($vaccines->isEmpty())
    <div class="empty-vaccines">
        <i data-lucide="package-search" class="empty-icon"></i>
        <p>Không tìm thấy sản phẩm phù hợp với bộ lọc hiện tại.</p>
        <button onclick="resetVaccineFilters()" class="btn-primary">Xem tất cả sản phẩm</button>
    </div>
@else
    <div class="catalog-result-summary">
        Hiển thị {{ $vaccines->firstItem() }}-{{ $vaccines->lastItem() }} trên {{ $vaccines->total() }} sản phẩm
    </div>

    <div class="catalog-product-grid">
        @foreach($vaccines as $vaccine)
            @php
                $hasSalePrice = $vaccine->hasSalePrice();
                $displayPrice = $hasSalePrice ? $vaccine->sale_price : $vaccine->price;
            @endphp
            <article class="catalog-product-card {{ isset($cart[$vaccine->id]) ? 'selected' : '' }}" data-id="{{ $vaccine->id }}">
                <button type="button" class="catalog-product-media" onclick="openVaccineDetailModal({{ $vaccine->id }})">
                    <span class="origin-badge"><i data-lucide="map-pin"></i>{{ $vaccine->origin ?: 'Đang cập nhật' }}</span>
                    <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="{{ $vaccine->name }}" loading="lazy">
                </button>

                <div class="catalog-product-body">
                    <button type="button" onclick="openVaccineDetailModal({{ $vaccine->id }})" class="catalog-product-title">
                        {{ $vaccine->name }}
                    </button>
                    <button type="button" onclick="setDiseaseFilter(@js($vaccine->disease_prevention), event)" class="catalog-product-disease">
                        {{ $vaccine->disease_prevention }}
                    </button>
                    <div class="catalog-product-meta">
                        <span><i data-lucide="syringe"></i>{{ $vaccine->doses ?: 1 }} liều</span>
                        @if($vaccine->manufacturer)
                            <span><i data-lucide="factory"></i>{{ $vaccine->manufacturer }}</span>
                        @endif
                    </div>
                </div>

                <div class="catalog-product-footer">
                    <div class="catalog-price-block">
                        <strong>{{ number_format($displayPrice, 0, ',', '.') }}đ</strong>
                        <span>/ liều</span>
                        @if($hasSalePrice)
                            <del>{{ number_format($vaccine->price, 0, ',', '.') }}đ</del>
                        @endif
                    </div>
                    <button class="btn-select-vaccine {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCart({{ $vaccine->id }})">
                        <i data-lucide="{{ isset($cart[$vaccine->id]) ? 'x' : 'plus' }}"></i>
                        <span>{{ isset($cart[$vaccine->id]) ? 'Hủy chọn' : 'Chọn vắc xin' }}</span>
                    </button>
                </div>
            </article>
        @endforeach
    </div>

    @if($vaccines->hasPages())
        <div class="catalog-pagination">
            {{ $vaccines->links() }}
        </div>
    @endif
@endif
