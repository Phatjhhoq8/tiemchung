@if ($paginator->hasPages())
    <div class="news-pagination-custom">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-btn disabled" aria-disabled="true">
                <i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev">
                <i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i>
            </a>
        @endif

        {{-- Page Numbers (Maximum 4 page numbers + ...) --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $maxPagesToShow = 4;
            
            $start = max(1, min($currentPage - 1, $lastPage - $maxPagesToShow + 1));
            $end = min($lastPage, $start + $maxPagesToShow - 1);
            if ($end - $start + 1 < $maxPagesToShow) {
                $start = max(1, $end - $maxPagesToShow + 1);
            }
        @endphp

        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $currentPage)
                <span class="pagination-btn active">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="pagination-btn">{{ $page }}</a>
            @endif
        @endfor

        @if ($end < $lastPage)
            <span class="pagination-dots">...</span>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next">
                <i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i>
            </a>
        @else
            <span class="pagination-btn disabled" aria-disabled="true">
                <i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i>
            </span>
        @endif
    </div>
@endif
