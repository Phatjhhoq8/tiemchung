@extends('vaccine::layouts.app')

@section('title', $vaccine->name . ' - Chi Tiết Vắc Xin')

@section('content')
<div class="vaccine-detail-container" style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    <!-- Breadcrumb -->
    <div class="breadcrumb" style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
        <a href="{{ route('home') }}" style="color: var(--text-muted); text-decoration: none;">Trang chủ</a> / 
        <a href="{{ route('vaccine.index') }}" style="color: var(--text-muted); text-decoration: none;">Danh mục vắc xin</a> / 
        <span style="color: var(--primary-color); font-weight: 500;">{{ $vaccine->name }}</span>
    </div>

    <!-- Main Detail Card -->
    <div class="detail-card" style="background-color: var(--bg-card); border-radius: var(--radius-md); box-shadow: var(--shadow-md); display: flex; overflow: hidden; border: 1px solid var(--border-color); flex-wrap: wrap;">
        <!-- Left Image Section -->
        <div class="detail-image" style="flex: 1 1 350px; background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; min-height: 350px;">
            <img src="{{ asset('images/vaccines/' . ($vaccine->image ?: 'default_vaccine.jpg')) }}" alt="{{ $vaccine->name }}" style="width: 100%; height: 100%; object-fit: cover;">
            <div class="badge-type" style="position: absolute; top: 20px; left: 20px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; background-color: {{ $vaccine->type === 'package' ? 'var(--secondary-color)' : 'var(--primary-color)' }}; color: #ffffff; z-index: 10;">
                {{ $vaccine->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ' }}
            </div>
        </div>

        <!-- Right Info Section -->
        <div class="detail-info" style="flex: 1 1 450px; padding: 40px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h1 style="font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">{{ $vaccine->name }}</h1>
                <p style="color: var(--text-muted); font-size: 15px; margin-bottom: 24px;">{{ $vaccine->description }}</p>

                <!-- Basic Specs Table -->
                <div class="specs-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                    <div>
                        <span style="display: block; font-size: 13px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Phòng bệnh</span>
                        <strong style="color: var(--text-primary); font-size: 15px;">{{ $vaccine->disease_prevention }}</strong>
                    </div>
                    <div>
                        <span style="display: block; font-size: 13px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Nguồn gốc</span>
                        <strong style="color: var(--text-primary); font-size: 15px;">{{ $vaccine->origin }}</strong>
                    </div>
                    <div style="margin-top: 10px;">
                        <span style="display: block; font-size: 13px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Độ tuổi chỉ định</span>
                        <strong style="color: var(--text-primary); font-size: 15px;">{{ $vaccine->age_group }}</strong>
                    </div>
                    <div style="margin-top: 10px;">
                        <span style="display: block; font-size: 13px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Phác đồ</span>
                        <strong style="color: var(--text-primary); font-size: 15px;">{{ $vaccine->doses }} mũi tiêm</strong>
                    </div>
                </div>
            </div>

            <!-- Price and Buy Action -->
            <div class="buy-section" style="border-top: 1px solid var(--border-color); padding-top: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                <div>
                    <span style="display: block; font-size: 14px; color: var(--text-muted);">Giá tiêm trọn gói:</span>
                    <strong style="font-size: 28px; color: var(--primary-color); font-weight: 800;">{{ number_format($vaccine->price, 0, ',', '.') }} đ</strong>
                </div>
                
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('vaccine.index') }}" class="btn-secondary" style="text-decoration: none; padding: 14px 24px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); color: var(--text-primary); font-weight: 600; display: inline-flex; align-items: center; gap: 8px; background: #ffffff; transition: all 0.2s ease;">
                        <i data-lucide="arrow-left" style="width: 20px; height: 20px;"></i>
                        <span>Quay lại</span>
                    </a>
                    <button class="btn-select-detail {{ isset($cart[$vaccine->id]) ? 'btn-selected' : '' }}" onclick="toggleCartDetail({{ $vaccine->id }})" style="padding: 14px 28px; border-radius: var(--radius-sm); border: none; color: #ffffff; font-weight: 700; font-size: 16px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; background-color: {{ isset($cart[$vaccine->id]) ? 'var(--secondary-color)' : 'var(--primary-color)' }};">
                        <i data-lucide="{{ isset($cart[$vaccine->id]) ? 'check' : 'plus' }}" style="width: 20px; height: 20px;"></i>
                        <span>{{ isset($cart[$vaccine->id]) ? 'Đã chọn' : 'Đăng ký tiêm' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hàm toggle giỏ hàng ngay tại trang chi tiết
    function toggleCartDetail(vaccineId) {
        const btn = document.querySelector('.btn-select-detail');
        const isSelected = btn.classList.contains('btn-selected');
        const url = isSelected ? "{{ route('cart.remove') }}" : "{{ route('cart.add') }}";
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ vaccine_id: vaccineId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (isSelected) {
                    btn.classList.remove('btn-selected');
                    btn.style.backgroundColor = 'var(--primary-color)';
                    btn.innerHTML = '<i data-lucide="plus"></i> <span>Đăng ký tiêm</span>';
                } else {
                    btn.classList.add('btn-selected');
                    btn.style.backgroundColor = 'var(--secondary-color)';
                    btn.innerHTML = '<i data-lucide="check"></i> <span>Đã chọn</span>';
                }
                lucide.createIcons();
                
                // Reload lại để đồng bộ giỏ hàng nổi nếu có
                if (window.opener || window.location) {
                    // Do page reload or let user redirect
                }
            }
        });
    }
</script>
@endsection
