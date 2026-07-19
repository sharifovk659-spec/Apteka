@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Пагинация">
        @if ($paginator->onFirstPage())
            <span class="admin-pagination__btn is-disabled">Назад</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="admin-pagination__btn" rel="prev">Назад</a>
        @endif

        <div class="admin-pagination__pages">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="admin-pagination__dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="admin-pagination__page is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="admin-pagination__page">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="admin-pagination__btn" rel="next">Далее</a>
        @else
            <span class="admin-pagination__btn is-disabled">Далее</span>
        @endif
    </nav>
@endif
