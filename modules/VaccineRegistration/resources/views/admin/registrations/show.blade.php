@extends('vaccine::layouts.admin')

@section('title', 'Đơn ' . $registration->registration_code)
@section('page_title', 'Chi Tiết Đơn Đặt Lịch')

@section('admin_content')
<div style="max-width:1000px; margin:0 auto; display:grid; gap:24px;">
    <div class="card-modern" style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:center;">
        <a href="{{ route('admin.registrations.index') }}" class="btn-action-sm">Quay lại danh sách</a>
        <div style="font-weight:800; color:var(--primary-color);">{{ $registration->registration_code }}</div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
        <div class="card-modern">
            <h3 style="margin-top:0;">Người tiêm</h3>
            <p style="margin:0 0 8px;">
                @if($registration->patient_id)
                    <a href="{{ route('admin.patients.show', $registration->patient_id) }}" style="color:var(--primary-color); font-weight:700; text-decoration:none;">
                        {{ $registration->patient_name }}
                    </a>
                @else
                    <strong>{{ $registration->patient_name }}</strong>
                @endif
            </p>
            <p style="margin:0 0 12px;">{{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($registration->patient_phone) }}</p>
            
            @if($registration->patient_id)
                <div style="margin-bottom:12px;">
                    <a class="btn-action-sm" href="{{ route('admin.patients.show', $registration->patient_id) }}">Xem lịch sử tiêm y tế</a>
                </div>
            @endif

            @if($registration->customer)
                <p style="margin:0 0 12px; color:var(--text-muted);"><strong>Tài khoản tích điểm:</strong> {{ $registration->customer->name }} · {{ \Modules\VaccineRegistration\Support\PhoneNormalizer::display($registration->customer->phone) }}</p>
                <a class="btn-action-sm" href="{{ route('admin.customers.show', $registration->customer) }}">Xem lịch sử mua và điểm</a>
            @endif
        </div>

        <div class="card-modern">
            <h3 style="margin-top:0;">Lịch hẹn</h3>
            <p><strong>Chi nhánh:</strong> {{ $registration->center_name }}</p>
            <p><strong>Ngày:</strong> {{ $registration->injection_date?->format('d/m/Y') }}</p>
            <p><strong>Khung giờ:</strong> {{ $registration->slot ? $registration->slot->start_at . ' - ' . $registration->slot->end_at : 'Chưa chọn' }}</p>
            <form action="{{ route('admin.registrations.status', $registration) }}" method="POST" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
                @csrf
                @method('PATCH')
                <div style="flex:1; min-width:180px;">
                    <label class="form-label-modern" for="booking_status">Trạng thái lịch hẹn</label>
                    <select id="booking_status" name="booking_status" class="form-control-modern">
                        @foreach(['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất', 'no_show' => 'Không đến', 'cancelled' => 'Đã hủy'] as $value => $label)
                            <option value="{{ $value }}" {{ $registration->booking_status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-modern" style="background: var(--accent-color, #004b8f); color: #fff; font-weight: 700; border: none; padding: 0 18px; border-radius: 8px; cursor: pointer; height: 42px; transition: all 0.2s;">Cập nhật</button>
            </form>
        </div>
    </div>

    <div class="card-modern">
        <h3 style="margin-top:0;">Thanh toán tại quầy và điểm</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:20px;">
            <div><span style="color:var(--text-muted); display:block;">Trạng thái thanh toán</span><strong>{{ $registration->paymentStatusLabel() }}</strong></div>
            <div><span style="color:var(--text-muted); display:block;">Tổng đơn</span><strong>{{ number_format($registration->total_price) }} đ</strong></div>
            <div><span style="color:var(--text-muted); display:block;">Giảm bằng điểm</span><strong>{{ number_format($registration->points_discount_amount) }} đ</strong></div>
            <div><span style="color:var(--text-muted); display:block;">Thực thu</span><strong style="color:var(--primary-color);">{{ number_format($registration->netPaidAmount()) }} đ</strong></div>
        </div>

        @if($registration->payment_status === \Modules\VaccineRegistration\Models\Registration::PAYMENT_UNPAID && $registration->booking_status !== \Modules\VaccineRegistration\Models\Registration::BOOKING_CANCELLED)
            <form action="{{ route('admin.registrations.settle', $registration) }}" method="POST" data-confirm="Xác nhận đã thu tiền tại quầy?" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
                @csrf
                <div style="flex:1 1 230px;">
                    <label class="form-label-modern" for="redeem_points">Điểm khách muốn dùng</label>
                    <input class="form-control-modern" id="redeem_points" type="number" name="redeem_points" min="0" max="{{ $pointQuote['available_points'] ?? 0 }}" value="{{ old('redeem_points', 0) }}">
                    <small style="display:block; margin-top:5px; color:var(--text-muted);">
                        Số dư toàn hệ thống: {{ number_format($pointQuote['balance'] ?? 0) }} điểm. 
                        Có thể dùng tối đa {{ number_format($pointQuote['available_points'] ?? 0) }} điểm 
                        ({{ $loyaltySettings['max_redeem_percent'] }}% đơn 
                        @if(isset($loyaltySettings['max_redeem_amount']) && (int)$loyaltySettings['max_redeem_amount'] > 0)
                            , tối đa {{ number_format($loyaltySettings['max_redeem_amount']) }} đ
                        @endif
                        ).
                    </small>
                </div>
                <button type="submit" class="btn-modern btn-modern-primary">Xác nhận thanh toán</button>
            </form>
        @elseif($registration->payment_status === \Modules\VaccineRegistration\Models\Registration::PAYMENT_PAID)
            <form action="{{ route('admin.registrations.refund', $registration) }}" method="POST" data-confirm="Hoàn tiền toàn bộ đơn này? Điểm sẽ được hoàn lại.">
                @csrf
                <button type="submit" class="btn-modern btn-modern-secondary">Hoàn tiền toàn bộ</button>
            </form>
        @endif
    @if($registration->booking_status !== \Modules\VaccineRegistration\Models\Registration::BOOKING_CANCELLED)
        @if($registration->payment_status !== \Modules\VaccineRegistration\Models\Registration::PAYMENT_PAID)
            <div class="card-modern" style="border-left: 4px solid var(--primary-color);">
                <h3 style="margin-top:0; color:var(--primary-color);">Quy trình y tế lâm sàng (Tiếp đón & Tiêm chủng)</h3>
                <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:16px; border-radius:8px; display:flex; align-items:center; gap:12px; font-weight:700; font-size:13.5px; text-align:justify;">
                    <i data-lucide="alert-triangle" style="width:20px; height:20px; flex-shrink:0; color:#dc2626;"></i>
                    <span>Cảnh báo: Bệnh nhân chưa hoàn tất thanh toán hóa đơn. Vui lòng xác nhận thanh toán ở phía trên trước khi tiến hành thủ tục tiếp đón (check-in) và tiêm chủng.</span>
                </div>
            </div>
        @else
            <div class="card-modern" style="border-left: 4px solid var(--accent-color);">
                <h3 style="margin-top:0; color:var(--accent-color);">Quy trình y tế lâm sàng (Tiếp đón & Tiêm chủng)</h3>
                
                <!-- BƯỚC 1: TIẾP ĐÓN (CHECK-IN) -->
                <div style="padding:16px; background:#f8fafc; border-radius:10px; margin-bottom:20px; border:1px solid #e2e8f0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                        <div>
                            <h4 style="margin:0 0 5px; color:#1e293b;">Bước 1: Tiếp đón (Check-in)</h4>
                            <p style="margin:0; font-size:13px; color:var(--text-muted);">
                                @if($registration->status === 'checked_in' || $registration->status === 'completed')
                                    <span style="color:#15803d; font-weight:700;">✓ Bệnh nhân đã làm thủ tục tiếp đón.</span>
                                @else
                                    Bệnh nhân cần được check-in tại quầy trước khi tiến hành khám sàng lọc.
                                @endif
                            </p>
                        </div>
                        <div>
                            @if($registration->status !== 'checked_in' && $registration->status !== 'completed')
                                <form action="{{ route('admin.registrations.check-in', $registration->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-modern btn-modern-primary" style="background:#004b8f;">Tiếp nhận bệnh nhân</button>
                                </form>
                            @else
                                <span class="badge-modern badge-modern-success" style="background:#d1fae5; color:#065f46; font-size:13px; padding:6px 12px;">Đã tiếp nhận</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($registration->status === 'checked_in' || $registration->status === 'completed')
                    <!-- BƯỚC 2: KHÁM SÀNG LỌC -->
                    <div style="padding:16px; background:#f8fafc; border-radius:10px; margin-bottom:20px; border:1px solid #e2e8f0;">
                        <h4 style="margin:0 0 12px; color:#1e293b;">Bước 2: Khám sàng lọc lâm sàng</h4>
                        
                        @if($registration->screening_status)
                            <div style="margin-bottom:15px; padding:10px 12px; border-radius:8px; font-weight:600; font-size:13px;
                                background: {{ $registration->screening_status === 'eligible' ? '#d1fae5' : '#fee2e2' }};
                                color: {{ $registration->screening_status === 'eligible' ? '#065f46' : '#991b1b' }};">
                                Kết quả: {{ match($registration->screening_status) {
                                    'eligible' => 'Đủ điều kiện tiêm',
                                    'deferred' => 'Tạm hoãn tiêm',
                                    'contraindicated' => 'Chống chỉ định tiêm',
                                    default => 'Chưa rõ'
                                } }}
                                @if($registration->screening_notes)
                                    <div style="font-weight:400; margin-top:5px; color:#334155;">Ghi chú: {{ $registration->screening_notes }}</div>
                                @endif
                            </div>
                        @endif

                        @if($registration->status !== 'completed')
                            <form action="{{ route('admin.registrations.screening', $registration->id) }}" method="POST" style="display:grid; gap:12px;">
                                @csrf
                                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                    <div style="flex:1; min-width:200px;">
                                        <label class="form-label-modern" for="screening_status">Đánh giá sức khỏe lâm sàng</label>
                                        <select id="screening_status" name="screening_status" class="form-control-modern" required>
                                            <option value="">-- Chọn kết quả khám --</option>
                                            <option value="eligible" {{ $registration->screening_status === 'eligible' ? 'selected' : '' }}>Đủ điều kiện tiêm chủng</option>
                                            <option value="deferred" {{ $registration->screening_status === 'deferred' ? 'selected' : '' }}>Tạm hoãn tiêm chủng</option>
                                            <option value="contraindicated" {{ $registration->screening_status === 'contraindicated' ? 'selected' : '' }}>Chống chỉ định tiêm chủng</option>
                                        </select>
                                    </div>
                                    <div style="flex:2; min-width:300px;">
                                        <label class="form-label-modern" for="screening_notes">Ghi chú lâm sàng / Lý do</label>
                                        <input class="form-control-modern" id="screening_notes" type="text" name="screening_notes" value="{{ $registration->screening_notes }}" placeholder="Nhập nhiệt độ, huyết áp, cân nặng, dị ứng...">
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <button type="submit" class="btn-modern" style="background:#eaaa00; color:#0f172a; font-weight:700; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Lưu kết quả khám</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    <!-- BƯỚC 3: THỰC HIỆN TIÊM -->
                    @if($registration->screening_status === 'eligible' || $registration->status === 'completed')
                        <div style="padding:16px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
                            <h4 style="margin:0 0 12px; color:#1e293b;">Bước 3: Thực hiện tiêm vắc xin</h4>
                            
                            @php
                                $administeredDoseIds = $registration->administeredDoses->pluck('vaccine_id')->toArray();
                            @endphp

                            <div style="display:grid; gap:16px;">
                                @foreach($registration->vaccines as $vaccine)
                                    @php
                                        $isVaccinated = in_array($vaccine->id, $administeredDoseIds);
                                        $doseRecord = $registration->administeredDoses->where('vaccine_id', $vaccine->id)->first();
                                        $availableLots = \Modules\VaccineRegistration\Models\InventoryLot::where('vaccine_id', $vaccine->id)
                                            ->where('center_id', $registration->center_id)
                                    @endphp
                                    
                                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; background:#fff; border-radius:8px; border:1px solid #e2e8f0; flex-wrap:wrap; gap:16px;">
                                        <div style="flex:1; min-width:200px;">
                                            <strong style="color:var(--accent-color);">{{ $vaccine->name }}</strong>
                                            <div style="font-size:12px; color:var(--text-muted);">
                                                Liều lượng: 1 mũi tiêm
                                            </div>
                                            @if($isVaccinated)
                                                <div style="margin-top:5px; color:#15803d; font-size:12px; font-weight:700;">
                                                    ✓ Đã tiêm lúc {{ $doseRecord->administered_at?->format('d/m/Y H:i') }}@if($doseRecord->inventoryLot?->lot_number) - Lô: {{ $doseRecord->inventoryLot->lot_number }}@endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if(!$isVaccinated && $registration->status !== 'completed')
                                            <form action="{{ route('admin.registrations.administer', $registration->id) }}" method="POST" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin:0;">
                                                @csrf
                                                <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
                                                
                                                <button type="submit" class="btn-modern btn-modern-primary" style="height:36px; line-height:36px; padding:0 12px; font-size:12px; background:var(--primary-color);">
                                                    Xác nhận tiêm
                                                </button>
                                            </form>
                                        @else
                                            @if($isVaccinated)
                                                <span class="badge-modern badge-modern-success" style="background:#d1fae5; color:#065f46; padding:4px 10px; font-size:12px;">Đã hoàn tất</span>
                                            @else
                                                <span style="color:#b91c1c; font-size:12px; font-weight:700;">Chưa tiêm</span>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif
    @endif

    <div class="card-modern">
        <h3 style="margin-top:0;">Sản phẩm đã chọn</h3>
        <div class="table-responsive-modern">
            <table class="table-modern">
                <thead><tr><th>Tên vắc xin</th><th style="text-align:center;">Số lượng</th><th style="text-align:right;">Giá chốt</th><th style="text-align:right;">Thành tiền</th></tr></thead>
                <tbody>
                    @foreach($registration->vaccines as $vaccine)
                        <tr>
                            <td><strong>{{ $vaccine->name }}</strong><small style="display:block; color:var(--text-muted);">{{ $vaccine->origin }}</small></td>
                            <td style="text-align:center;">{{ $vaccine->pivot->quantity }}</td>
                            <td style="text-align:right;">{{ number_format($vaccine->pivot->price) }} đ</td>
                            <td style="text-align:right; font-weight:700; color:var(--primary-color);">{{ number_format($vaccine->pivot->price * $vaccine->pivot->quantity) }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
