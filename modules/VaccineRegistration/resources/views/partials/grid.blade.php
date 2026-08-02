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
                <a href="{{ route('vaccine.show', $vaccine->id) }}" class="catalog-product-media" style="display: block; text-decoration: none;">
                    <span class="origin-badge"><i data-lucide="map-pin"></i>{{ $vaccine->origin ?: 'Đang cập nhật' }}</span>
                    <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')) }}" onerror="this.onerror=null; this.src='{{ asset('images/vaccines/hexaxim.jpg') }}';" alt="{{ $vaccine->name }}" loading="lazy">
                </a>

                <div class="catalog-product-body">
                    <a href="{{ route('vaccine.show', $vaccine->id) }}" class="catalog-product-title" style="display: block; text-decoration: none; text-align: justify;">
                        {{ $vaccine->name }}
                    </a>
                    <button type="button" onclick="setDiseaseFilter(@js($vaccine->disease_prevention), event)" class="catalog-product-disease" style="text-align: justify;">
                        {{ $vaccine->disease_prevention }}
                    </button>
                    <div class="catalog-product-meta">
                        <span><i data-lucide="syringe"></i>{{ $vaccine->doses ?: 1 }} liều</span>
                        @if($vaccine->manufacturer)
                            <span><i data-lucide="factory"></i>{{ $vaccine->manufacturer }}</span>
                        @endif
                    </div>
                </div>

                <div class="catalog-product-footer" style="display: flex; flex-direction: column; gap: 12px; align-items: stretch; width: 100%;">
                    <div class="catalog-price-block" style="display: flex; align-items: baseline; gap: 4px;">
                        <strong style="font-size: 19px; color: var(--primary-color, #c8102e); font-weight: 800;">{{ number_format($displayPrice, 0, ',', '.') }}đ</strong>
                        <span style="font-size: 12px; color: #64748b;">/ liều</span>
                        @if($hasSalePrice)
                            <del style="font-size: 12px; color: #94a3b8; margin-left: 6px;">{{ number_format($vaccine->price, 0, ',', '.') }}đ</del>
                        @endif
                    </div>
                    <div class="catalog-action-group" style="display: flex; gap: 8px; align-items: center; width: 100%;">
                        <a href="{{ route('vaccine.show', $vaccine->id) }}" class="btn-detail-link" style="flex: 1; text-align: center; padding: 8px 10px; border-radius: 20px; border: 1px solid var(--primary-color, #c8102e); color: var(--primary-color, #c8102e); font-size: 12.5px; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap;">
                            Xem chi tiết
                        </a>
                        <button class="btn-select-vaccine {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCart({{ $vaccine->id }})" style="flex: 1; text-align: center; padding: 8px 10px; justify-content: center; display: inline-flex; align-items: center; gap: 4px; border-radius: 20px; white-space: nowrap;">
                            <i data-lucide="{{ isset($cart[$vaccine->id]) ? 'x' : 'plus' }}"></i>
                            <span>{{ isset($cart[$vaccine->id]) ? 'Hủy chọn' : 'Chọn tiêm' }}</span>
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    @if($vaccines->hasPages())
        <div class="catalog-pagination">
            {{ $vaccines->links('partials.pagination') }}
        </div>
    @endif
@endif
