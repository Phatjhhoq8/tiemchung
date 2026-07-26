@extends('vaccine::layouts.app')

@section('title', $vaccine->name . ' - Chi Tiết Vắc Xin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vaccines.css') }}">
@endsection

@section('content')
@php
    $activeTab = request('tab', 'phac-do');
@endphp

<div class="vaccine-detail-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a> / 
        <a href="{{ route('vaccine.index') }}">Danh mục vắc xin</a> / 
        <span style="color: var(--primary-color); font-weight: 500;">{{ $vaccine->name }}</span>
    </div>

    <!-- Main Detail Card -->
    <div class="detail-card">
        <!-- Left Image Section -->
        <div class="detail-image">
            <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'default_vaccine.jpg')) }}" alt="{{ $vaccine->name }}">
            <div class="badge-type {{ $vaccine->type === 'package' ? 'package' : 'single' }}">
                {{ $vaccine->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ' }}
            </div>
        </div>

        <!-- Right Info Section -->
        <div class="detail-info">
            <div>
                <h1 style="font-family: 'Roboto', sans-serif; font-size: 28px; font-weight: 700; color: #0f172a; margin-bottom: 12px; margin-top: 0;">{{ $vaccine->name }}</h1>
                <p style="color: #475569; font-size: 15px; line-height: 1.6; margin-bottom: 24px; margin-top: 0;">{{ $vaccine->description }}</p>

                <!-- Basic Specs Table -->
                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Phòng bệnh</span>
                        <strong class="spec-value">{{ $vaccine->disease_prevention }}</strong>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Nguồn gốc</span>
                        <strong class="spec-value">{{ $vaccine->origin }}</strong>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Độ tuổi chỉ định</span>
                        <strong class="spec-value">{{ $vaccine->age_group }}</strong>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Phác đồ</span>
                        <strong class="spec-value">{{ $vaccine->doses }} mũi tiêm</strong>
                    </div>
                </div>
            </div>

            <!-- Price and Buy Action -->
            <div class="buy-section">
                <div class="price-info-wrapper">
                    <span class="price-label">Giá tiêm trọn gói:</span>
                    <strong class="price-value">{{ number_format($vaccine->price, 0, ',', '.') }} đ / mũi</strong>
                    
                    <!-- GSP Cold Chain Storage Commitment Badge -->
                    <div class="gsp-cold-chain-box">
                        <i data-lucide="shield-check"></i>
                        <span>Đạt tiêu chuẩn bảo quản Dây chuyền lạnh GSP 2 - 8 độ C</span>
                    </div>
                </div>
                
                @if(!isset($cart[$vaccine->id]))
                    <form action="{{ route('cart.add') }}" method="POST" style="margin: 0; width: 100%;">
                        @csrf
                        <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
                        
                        <!-- Dose / Quantity Selector Bar -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 4px; background-color: #f8fafc;">
                            <label for="quantity" style="font-size: 13.5px; color: #475569; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-lucide="layers" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                                Chọn số mũi đăng ký:
                            </label>
                            <select name="quantity" id="quantity" style="padding: 6px 12px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 14px; color: #0f172a; font-weight: 700; outline: none; cursor: pointer; background: #ffffff;">
                                @for($i = 1; $i <= max(3, $vaccine->doses); $i++)
                                    <option value="{{ $i }}">
                                        {{ $i }} mũi {{ $i == $vaccine->doses ? '(Trọn phác đồ)' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        
                        <div class="actions-wrapper">
                            <a href="{{ route('vaccine.index') }}" class="btn-back">
                                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
                                <span>Quay lại</span>
                            </a>
                            <button type="submit" class="btn-select-detail">
                                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                                <span>Đăng ký tiêm</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div style="width: 100%;">
                        <!-- Current Selected Dose Status Bar -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding: 10px 14px; border: 1px solid rgba(200,16,46,0.12); border-radius: 4px; background-color: #fff5f5;">
                            <span style="font-size: 13.5px; color: #475569; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i data-lucide="check-circle-2" style="width: 16px; height: 16px; color: var(--primary-color);"></i>
                                Đã chọn trong danh sách:
                            </span>
                            <strong style="font-size: 14.5px; color: var(--primary-color); font-weight: 700;">
                                {{ $cart[$vaccine->id]['quantity'] ?? 1 }} mũi {{ ($cart[$vaccine->id]['quantity'] ?? 1) == $vaccine->doses ? '(Trọn phác đồ)' : '' }}
                            </strong>
                        </div>
                        
                        <div class="actions-wrapper">
                            <a href="{{ route('vaccine.index') }}" class="btn-back">
                                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
                                <span>Quay lại</span>
                            </a>
                            <form action="{{ route('cart.remove') }}" method="POST" style="margin: 0;">
                                @csrf
                                <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
                                <button type="submit" class="btn-select-detail btn-selected">
                                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                                    <span>Đã chọn</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Vaccine Medical Tabs UI (Pure PHP Query Operated) -->
    <div class="medical-tabs-nav">
        <a href="{{ route('vaccine.show', ['id' => $vaccine->id, 'tab' => 'phac-do']) }}" class="tab-nav-btn {{ $activeTab === 'phac-do' ? 'active' : '' }}">
            <i data-lucide="clipboard-list" style="width: 16px; height: 16px;"></i>
            <span>Chỉ định & Phác đồ</span>
        </a>
        <a href="{{ route('vaccine.show', ['id' => $vaccine->id, 'tab' => 'luu-y']) }}" class="tab-nav-btn {{ $activeTab === 'luu-y' ? 'active' : '' }}">
            <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
            <span>Chống chỉ định & Lưu ý</span>
        </a>
        <a href="{{ route('vaccine.show', ['id' => $vaccine->id, 'tab' => 'theo-doi']) }}" class="tab-nav-btn {{ $activeTab === 'theo-doi' ? 'active' : '' }}">
            <i data-lucide="activity" style="width: 16px; height: 16px;"></i>
            <span>Phản ứng sau tiêm & Theo dõi</span>
        </a>
    </div>

    <!-- Tab Panels Content -->
    @if($activeTab === 'phac-do')
        <div class="tab-panel-content">
            <h3 class="tab-panel-title">
                <i data-lucide="clipboard-list" style="width: 18px; height: 18px; color: var(--primary-color);"></i>
                Chỉ định & Phác đồ tiêm chủng
            </h3>
            <p class="tab-panel-body">
                Vắc-xin được chỉ định phòng ngừa các bệnh truyền nhiễm nguy hiểm theo đúng khuyến cáo của Bộ Y tế. Độ tuổi chỉ định phù hợp từ <strong>{{ $vaccine->age_group }}</strong>. Phác đồ tiêm chủng đầy đủ gồm <strong>{{ $vaccine->doses }} mũi tiêm</strong> để đạt hiệu quả bảo vệ tối ưu. Khách hàng sẽ được bác sĩ khám sàng lọc miễn phí trước khi tiêm.
            </p>
        </div>
    @elseif($activeTab === 'luu-y')
        <div class="tab-panel-content">
            <h3 class="tab-panel-title">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px; color: var(--primary-color);"></i>
                Chống chỉ định & Lưu ý y khoa
            </h3>
            <p class="tab-panel-body">
                Không tiêm vắc-xin cho người có tiền sử dị ứng nghiêm trọng với bất kỳ thành phần nào của thuốc. Hoãn tiêm đối với trường hợp đang sốt cao hoặc nhiễm trùng cấp tính. Vui lòng thông báo đầy đủ tình trạng sức khỏe hiện tại và lịch sử tiêm chủng của bạn cho bác sĩ trong quá trình khám sàng lọc.
            </p>
        </div>
    @elseif($activeTab === 'theo-doi')
        <div class="tab-panel-content">
            <h3 class="tab-panel-title">
                <i data-lucide="activity" style="width: 18px; height: 18px; color: var(--primary-color);"></i>
                Phản ứng sau tiêm & Hướng dẫn theo dõi
            </h3>
            <p class="tab-panel-body">
                Các phản ứng thông thường sau tiêm có thể xảy ra bao gồm: sưng đỏ, đau nhẹ tại vị trí tiêm, sốt nhẹ hoặc mệt mỏi. Đây là những phản ứng sinh lý tự nhiên của cơ thể và thường tự biến mất sau 1-2 ngày. Người tiêm chủng cần được theo dõi tại trung tâm ít nhất 30 phút sau tiêm và tiếp tục tự theo dõi tại nhà trong vòng 24 - 48 giờ tiếp theo.
            </p>
        </div>
    @endif
</div>
@endsection
