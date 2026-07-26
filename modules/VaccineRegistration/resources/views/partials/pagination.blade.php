@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
    @endphp

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

        {{-- Always show Page 1 and Page 2 --}}
        @if ($currentPage == 1)
            <span class="pagination-btn active">1</span>
            @if ($lastPage >= 2)
                <a href="{{ $paginator->url(2) }}" class="pagination-btn">2</a>
            @endif
        @elseif ($currentPage == 2)
            <a href="{{ $paginator->url(1) }}" class="pagination-btn">1</a>
            <span class="pagination-btn active">2</span>
        @else
            <a href="{{ $paginator->url(1) }}" class="pagination-btn">1</a>
            @if ($lastPage >= 2)
                <a href="{{ $paginator->url(2) }}" class="pagination-btn">2</a>
            @endif
        @endif

        {{-- Dots / Middle active page --}}
        @if ($currentPage > 2 && $currentPage < $lastPage - 1)
            @if ($currentPage > 3)
                <span class="pagination-dots">...</span>
            @endif
            <span class="pagination-btn active">{{ $currentPage }}</span>
            @if ($currentPage < $lastPage - 2)
                <span class="pagination-dots">...</span>
            @endif
        @else
            @if ($lastPage > 4)
                <span class="pagination-dots">...</span>
            @endif
        @endif

        {{-- Always show Penultimate Page (End-1) and Last Page (End) --}}
        @if ($lastPage >= 3)
            @php $penultimate = $lastPage - 1; @endphp
            @if ($penultimate > 2)
                @if ($currentPage == $penultimate)
                    <span class="pagination-btn active">{{ $penultimate }}</span>
                @else
                    <a href="{{ $paginator->url($penultimate) }}" class="pagination-btn">{{ $penultimate }}</a>
                @endif
            @endif

            @if ($currentPage == $lastPage)
                <span class="pagination-btn active">{{ $lastPage }}</span>
            @else
                <a href="{{ $paginator->url($lastPage) }}" class="pagination-btn">{{ $lastPage }}</a>
            @endif
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
