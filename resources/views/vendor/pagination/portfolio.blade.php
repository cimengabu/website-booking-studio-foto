@if ($paginator->hasPages())
<nav aria-label="Pagination" class="flex items-center justify-center gap-2">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-cream border border-cream-dark text-ink-muted cursor-not-allowed opacity-50">
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="w-10 h-10 rounded-xl flex items-center justify-center bg-white border border-cream-dark text-ink hover:border-gold hover:text-gold transition-all shadow-card"
           rel="prev" aria-label="Previous">
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="w-10 h-10 rounded-xl flex items-center justify-center text-ink-muted text-sm">···</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center btn-gold text-white text-sm font-bold shadow-warm" aria-current="page">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="w-10 h-10 rounded-xl flex items-center justify-center bg-white border border-cream-dark text-ink text-sm hover:border-gold hover:text-gold transition-all shadow-card">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="w-10 h-10 rounded-xl flex items-center justify-center bg-white border border-cream-dark text-ink hover:border-gold hover:text-gold transition-all shadow-card"
           rel="next" aria-label="Next">
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </a>
    @else
        <span class="w-10 h-10 rounded-xl flex items-center justify-center bg-cream border border-cream-dark text-ink-muted cursor-not-allowed opacity-50">
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </span>
    @endif
</nav>
@endif
