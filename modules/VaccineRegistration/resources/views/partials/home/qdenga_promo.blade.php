<!-- 5. Banner Vắc-xin Nổi Bật (CANH GIỮA MAX-W-7XL - LƯỚI 2X2 THẺ NGANG ĐỘNG) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="campaign-featured-vaccines-container" data-aos="fade-up">
    <section class="space-y-8" id="campaign-featured-vaccines">
        <!-- Unified Centered Header Title -->
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <span class="section-badge">
                Vắc Xin Tiêu Điểm
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900 uppercase tracking-tight">
                Vắc Xin Nổi Bật
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                Medicare chọn lọc những loại vắc xin quan trọng nhất để bảo vệ sức khỏe gia đình bạn.
            </p>
        </div>

        <!-- Lưới 2x2 Thẻ Ngang Động -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($campaignVaccines as $vaccine)
                <div class="bg-white p-5 rounded-2xl border border-slate-100/60 shadow-sm hover:shadow-md transition-all duration-300 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                    <!-- Left: Vaccine Image (md:col-span-5) -->
                    <div class="md:col-span-5 rounded-xl overflow-hidden aspect-[4/3] flex items-center justify-center bg-slate-50 border border-slate-100/40 relative">
                        @if($vaccine->image)
                            <img src="{{ asset('images/vaccines/' . $vaccine->image) }}" alt="{{ $vaccine->name }}" class="w-full h-full object-contain p-2 rounded-xl" loading="lazy">
                        @else
                            <div class="text-slate-300 flex flex-col items-center gap-1">
                                <i data-lucide="image" class="w-8 h-8"></i>
                                <span class="text-xs">Không có ảnh</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Right: Vaccine Details (md:col-span-7) -->
                    <div class="md:col-span-7 space-y-2.5 flex flex-col justify-between h-full">
                        <div>
                            <!-- Mini Gold Badge -->
                            <span class="inline-block bg-[#eaaa00] text-white text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-1">
                                {{ $vaccine->category ?? 'Medicare Chọn Lọc' }}
                            </span>
                            <!-- Vaccine Name -->
                            <h3 class="font-black text-slate-900 text-base md:text-lg leading-snug hover:text-[#c8102e] transition-colors">
                                <a href="{{ route('register.show', ['add_vaccine_id' => $vaccine->id]) }}">{{ $vaccine->name }}</a>
                            </h3>
                            <!-- Prevention Info -->
                            <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed mt-1">
                                <strong>Phòng bệnh:</strong> {{ $vaccine->disease_prevention ?? 'Đang cập nhật' }}
                            </p>
                            <!-- Indicators -->
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-slate-500 text-[11px] font-medium pt-1">
                                <span class="flex items-center gap-1"><i data-lucide="user" class="w-3 h-3 text-[#c8102e]"></i> {{ $vaccine->age_group ?? 'Mọi đối tượng' }}</span>
                                <span class="flex items-center gap-1"><i data-lucide="globe" class="w-3 h-3 text-[#b91c1c]"></i> {{ $vaccine->origin ?? 'Nhập khẩu' }}</span>
                            </div>
                        </div>
                        
                        <!-- Pricing & CTA -->
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <div class="flex flex-col">
                                @if($vaccine->hasSalePrice())
                                    <span class="text-slate-400 line-through text-[11px] font-semibold">{{ number_format($vaccine->price, 0, ',', '.') }} đ</span>
                                    <span class="text-[#c8102e] font-black text-sm md:text-base leading-none">{{ number_format($vaccine->sale_price, 0, ',', '.') }} đ</span>
                                @else
                                    <span class="text-[#c8102e] font-black text-sm md:text-base leading-none">{{ number_format($vaccine->price, 0, ',', '.') }} đ</span>
                                @endif
                            </div>
                            <a href="{{ route('register.show', ['add_vaccine_id' => $vaccine->id]) }}" class="inline-flex items-center gap-1.5 bg-[#c8102e] hover:bg-[#a00d24] text-white font-bold py-1.5 px-3.5 rounded-lg text-xs transition-all shadow-sm hover:shadow">
                                <i data-lucide="syringe" class="w-3.5 h-3.5"></i> Đặt lịch
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
