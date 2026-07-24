<!-- 10. Tin Tức / Kiến Thức Y Khoa (CANH GIỮA MAX-W-7XL) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <section class="space-y-6" id="news-section" data-aos="fade-up">
        <!-- Unified Centered Header Title -->
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <span class="section-badge">
                Tin Tức Y Khoa
            </span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900 uppercase tracking-tight">
                Tin Tức & Kiến Thức Y Khoa
            </h2>
            <p class="text-slate-600 text-sm md:text-base">
                Cập nhật các thông tin y khoa mới nhất, kiến thức tiêm chủng và khuyến cáo sức khỏe từ chuyên gia.
            </p>
        </div>

        <!-- Category Pills Filter Bar -->
        <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('news.index') }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-[#c8102e] text-white shadow-sm shrink-0">
                Tin nóng trong ngày
            </a>
            <a href="{{ route('news.index', ['category' => 'Bệnh Truyền Nhiễm']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#c8102e] hover:bg-red-50 shrink-0 transition-all">
                Bệnh Truyền Nhiễm
            </a>
            <a href="{{ route('news.index', ['category' => 'Vắc Xin Mới']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#c8102e] hover:bg-red-50 shrink-0 transition-all">
                Vắc Xin Mới
            </a>
            <a href="{{ route('news.index', ['category' => 'Khuyến cáo Y tế']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#c8102e] hover:bg-red-50 shrink-0 transition-all">
                Khuyến Cáo Y Tế
            </a>
            <a href="{{ route('news.index', ['category' => 'Chăm Sóc Bé']) }}" class="px-5 py-2 rounded-full font-bold text-xs uppercase tracking-wider bg-slate-100 text-[#c8102e] hover:bg-red-50 shrink-0 transition-all">
                Chăm Sóc Bé
            </a>
        </div>

        @if(($articles ?? collect())->isNotEmpty())
            @php
                $mainArticle = $articles->first();
                $sideArticles = $articles->slice(1, 3);
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-2">
                <!-- Left Column: 1 Main Featured Article -->
                <div class="lg:col-span-6 group cursor-pointer" onclick="window.location.href='{{ route('news.show', $mainArticle->slug ?? $mainArticle->id) }}'">
                    <div class="aspect-[16/10] w-full bg-slate-100 rounded-2xl overflow-hidden mb-4 shadow-sm border border-slate-100 relative">
                        <img src="{{ asset('images/vaccines/' . ($mainArticle->image ?: 'vaxigrip.jpg')) }}" alt="{{ $mainArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                        <span class="absolute top-3 left-3 bg-[#c8102e] text-white font-extrabold text-[11px] uppercase px-3.5 py-1 rounded-full shadow-md">
                            {{ $mainArticle->category ?: 'Mới Nhất' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-800 text-lg sm:text-xl group-hover:text-[#c8102e] transition-colors leading-snug mb-2.5 line-clamp-2">
                        {{ $mainArticle->title }}
                    </h3>
                    <p class="text-slate-600 text-sm line-clamp-3 leading-relaxed mb-4">
                        {{ $mainArticle->summary }}
                    </p>
                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                        <span class="flex items-center gap-1.5 text-slate-400 font-medium shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i> {{ $mainArticle->created_at ? $mainArticle->created_at->format('d/m/Y') : '23/07/2026' }}
                        </span>
                        <a href="{{ route('news.show', $mainArticle->slug ?? $mainArticle->id) }}" class="text-[#eaaa00] hover:text-[#d49800] font-bold text-xs hover:underline flex items-center gap-1 shrink-0">
                            Xem thêm <i data-lucide="arrow-right" class="w-3.5 h-3.5 inline"></i>
                        </a>
                    </div>
                </div>

                <!-- Right Column: 3 Compact Horizontal Articles -->
                <div class="lg:col-span-6 space-y-6">
                    @foreach($sideArticles as $sideArticle)
                        <div class="flex flex-col sm:flex-row gap-4 items-start pb-6 border-b border-slate-100 last:border-b-0 last:pb-0 group cursor-pointer" onclick="window.location.href='{{ route('news.show', $sideArticle->slug ?? $sideArticle->id) }}'">
                            <div class="w-full sm:w-44 lg:w-48 aspect-[16/10] rounded-2xl overflow-hidden bg-slate-50 shrink-0 border border-slate-100 relative">
                                <img src="{{ asset('images/vaccines/' . ($sideArticle->image ?: 'vaxigrip.jpg')) }}" alt="{{ $sideArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-between h-full space-y-2">
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm sm:text-base group-hover:text-[#c8102e] transition-colors leading-tight line-clamp-2 mb-1.5">
                                        {{ $sideArticle->title }}
                                    </h4>
                                    <p class="text-slate-600 text-xs line-clamp-2 leading-relaxed mb-3">
                                        {{ $sideArticle->summary }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100 mt-auto">
                                    <span class="flex items-center gap-1.5 text-slate-400 font-medium shrink-0">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> {{ $sideArticle->created_at ? $sideArticle->created_at->format('d/m/Y') : '23/07/2026' }}
                                    </span>
                                    <a href="{{ route('news.show', $sideArticle->slug ?? $sideArticle->id) }}" class="text-[#eaaa00] hover:text-[#d49800] font-bold text-xs hover:underline flex items-center gap-1 shrink-0 ml-2">
                                        Xem thêm <i data-lucide="arrow-right" class="w-3.5 h-3.5 inline"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="text-center pt-8">
            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-bold text-sm bg-[#c8102e] hover:bg-[#a00d24] text-white shadow-md hover:shadow-lg transition-all duration-300">
                Xem tất cả tin tức <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </section>
</div>
