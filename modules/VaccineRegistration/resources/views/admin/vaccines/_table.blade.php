@if($vaccines->isEmpty())
    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
        <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 12px; color: var(--text-light);"></i>
        <p>Không tìm thấy vắc xin nào
            @if(request()->hasAny(['search', 'stock_status', 'category', 'min_quantity', 'max_quantity', 'day', 'month', 'year', 'filter_day', 'filter_month', 'filter_year']))
                phù hợp với bộ lọc.
            @else
                trong hệ thống.
            @endif
        </p>
    </div>
@else
    <div class="table-responsive-modern">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 45px; text-align: center;">#</th>
                    @if(!empty($selectedCenterId))
                        <th style="width: 170px;">Chi nhánh</th>
                    @endif
                    <th>Tên Vắc Xin & Chi Tiết</th>
                    <th>Nhóm bệnh</th>
                    <th style="width: 120px;">Nguồn gốc</th>
                    <th style="width: 90px; text-align: center;">Mũi tiêm</th>
                    @if(!empty($selectedCenterId))
                        <th style="width: 120px;">Giá</th>
                        <th style="width: 120px;">Giá ưu đãi</th>
                    @else
                        <th style="width: 140px; text-align: center;">Đơn giá</th>
                    @endif
                    <th style="width: 150px; text-align: center;">Tồn kho / Trạng thái</th>
                    <th style="width: 140px; text-align: center;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vaccines as $index => $vac)
                    @php($rowCenterId = !empty($selectedCenterId) ? (int) $vac->center_id : null)
                    <tr id="vaccine-row-{{ $vac->id }}">
                        <td style="text-align: center; color: var(--text-light); font-weight: 600;">
                            {{ $vaccines->firstItem() + $index }}
                        </td>

                        @if(!empty($selectedCenterId))
                            <td>
                                <span class="category-tag-modern">{{ $vac->center_name }}</span>
                            </td>
                        @endif

                        <td>
                            <div style="font-weight: 700; color: var(--text-primary); font-size: 14.5px; font-family: var(--font-display);">{{ $vac->name }}</div>
                            @if($vac->is_featured)
                                <span title="Nổi bật" style="font-size: 11px; font-weight: 700; color: #d97706; background-color: #fffbeb; border: 1px solid #fde68a; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px; width: fit-content; margin-top: 3px;">
                                    <i data-lucide="star" style="width: 9px; height: 9px; fill: #d97706;"></i> Nổi bật
                                </span>
                            @endif
                            @if($vac->manufacturer)
                                <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 2px;">{{ $vac->manufacturer }}</div>
                            @endif
                            @if($vac->dosage)
                                <div style="font-size: 12px; color: var(--text-light);">{{ $vac->dosage }}</div>
                            @endif
                        </td>

                        <td>
                            @if($vac->category)
                                <span class="category-tag-modern">{{ $vac->category }}</span>
                            @else
                                <span style="color: var(--text-light);">—</span>
                            @endif
                        </td>

                        <td style="font-weight: 500;">{{ $vac->origin ?: '—' }}</td>
                        <td style="text-align: center; font-weight: 700; color: var(--text-primary);">{{ $vac->doses }}</td>

                        @if(!empty($selectedCenterId))
                            <td style="font-weight: 600; white-space: nowrap;
                                @if($vac->hasSalePrice()) text-decoration: line-through; color: var(--text-light); font-size: 12.5px;
                                @else color: var(--primary-color); font-size: 14.5px; @endif">
                                {{ number_format($vac->price, 0, ',', '.') }} đ
                            </td>

                            <td style="font-weight: 700; white-space: nowrap;">
                                @if($vac->hasSalePrice())
                                    <span style="color: #dc2626; font-size: 14.5px;">{{ number_format($vac->sale_price, 0, ',', '.') }} đ</span>
                                @else
                                    <span style="color: var(--text-light);">—</span>
                                @endif
                            </td>
                        @else
                            <td style="text-align: center; color: var(--text-muted); font-size: 12.5px;">
                                <span title="Giá được thiết lập riêng theo từng chi nhánh" style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-weight: 600; border: 1px solid #e2e8f0; font-size: 11.5px; white-space: nowrap; color: #475569;">
                                    Theo từng chi nhánh
                                </span>
                            </td>
                        @endif

                        <td style="text-align: center;">
                            <?php
                                $reqDoses = (int) ($scheduledDemands[$vac->id] ?? 0);
                                $stockQty = (int) ($vac->stock_quantity ?? 0);
                                $isSufficient = $reqDoses > 0 ? ($stockQty >= $reqDoses) : ($stockQty > 0);
                            ?>

                            @if(!empty($selectedCenterId) && !$vac->center_is_active)
                                <span class="badge-modern badge-modern-secondary">Tạm ngưng</span>
                            @else
                                <div style="font-size: 16px; font-weight: 800; color: {{ ($reqDoses > 0 ? $stockQty >= $reqDoses : $stockQty > 0) ? '#15803d' : '#b91c1c' }};">
                                    {{ number_format($stockQty) }}
                                </div>
                                
                                @if($reqDoses > 0)
                                    @if($stockQty >= $reqDoses)
                                        <span class="badge-modern badge-modern-success" style="font-size: 11px; padding: 2px 8px; margin-top: 4px; display: inline-block;" title="Lịch tiêm: {{ $reqDoses }} liều - Tồn kho: {{ $stockQty }} liều (Đầy đủ)">
                                            Đầy đủ
                                        </span>
                                    @else
                                        <span class="badge-modern badge-modern-danger" style="font-size: 11px; padding: 2px 8px; margin-top: 4px; display: inline-block; background: #fee2e2; color: #c8102e; border: 1px solid #fecaca; font-weight: 700;" title="Lịch tiêm cần {{ $reqDoses }} liều nhưng tồn kho chỉ có {{ $stockQty }} liều (Thiếu {{ $reqDoses - $stockQty }} liều)">
                                            Thiếu (-{{ $reqDoses - $stockQty }})
                                        </span>
                                    @endif
                                @else
                                    @if($stockQty > 0)
                                        <span class="badge-modern badge-modern-success" style="font-size: 11px; padding: 2px 8px; margin-top: 4px; display: inline-block;">
                                            Đầy đủ
                                        </span>
                                    @else
                                        <span class="badge-modern badge-modern-danger" style="font-size: 11px; padding: 2px 8px; margin-top: 4px; display: inline-block; background: #fee2e2; color: #c8102e; border: 1px solid #fecaca; font-weight: 700;">
                                            Hết hàng
                                        </span>
                                    @endif
                                @endif

                                @if(empty($selectedCenterId))
                                    <small style="display: block; color: #64748b; font-size: 11px; margin-top: 2px; font-weight: 600;">
                                        Tổng hệ thống
                                    </small>
                                @endif
                            @endif
                        </td>

                        <td style="text-align: center;">
                            <div class="action-dropdown-wrapper">
                                <button type="button" class="btn-action-trigger" onclick="toggleActionMenu(this, event)" title="Thao tác">
                                    <i data-lucide="more-horizontal" style="width: 16px; height: 16px;"></i>
                                </button>
                                
                                <div class="action-dropdown-menu">
                                    <form action="{{ route('admin.vaccines.toggle-featured', $vac->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @if($rowCenterId)
                                            <input type="hidden" name="center_id" value="{{ $rowCenterId }}">
                                        @endif
                                        <button type="submit" class="dropdown-item-action">
                                            @if($vac->is_featured)
                                                <i data-lucide="star-off" style="width: 14px; height: 14px; color: #d97706;"></i> Bỏ nổi bật
                                            @else
                                                <i data-lucide="star" style="width: 14px; height: 14px; color: #64748b;"></i> Đặt nổi bật
                                            @endif
                                        </button>
                                    </form>

                                    @if($isSuperAdmin ?? false)
                                    <button type="button" class="dropdown-item-action btn-view-branches-stock" data-stock-url="{{ route('admin.vaccines.branches-stock', $vac->id) }}">
                                        <i data-lucide="layers" style="width: 14px; height: 14px; color: #64748b;"></i> Quản lý kho
                                    </button>
                                    @endif

                                    <a href="{{ route('admin.vaccines.edit', $rowCenterId ? ['vaccine' => $vac->id, 'center_id' => $rowCenterId] : ['vaccine' => $vac->id]) }}" class="dropdown-item-action">
                                        <i data-lucide="edit-2" style="width: 14px; height: 14px; color: #64748b;"></i> Sửa
                                    </a>

                                    <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 4px 0;">

                                    @if($isSuperAdmin ?? false)
                                    <form action="{{ route('admin.vaccines.destroy', $vac->id) }}" method="POST" class="delete-vaccine-ajax-form" data-id="{{ $vac->id }}" data-name="{{ $vac->name }}" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item-action danger">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Xóa vắc xin
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: center; margin-top: 24px;">
        {{ $vaccines->links() }}
    </div>
@endif
