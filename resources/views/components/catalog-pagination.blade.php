@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Пагинация">
        @if ($paginator->onFirstPage())
            <span class="pagination__btn pagination__btn--disabled">Назад</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination__btn" rel="prev">Назад</a>
        @endif

        <ul class="pagination__list">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination__dots">{{ $element }}</li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="pagination__page is-active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="pagination__page">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination__btn" rel="next">Далее</a>
        @else
            <span class="pagination__btn pagination__btn--disabled">Далее</span>
        @endif
    </nav>
@endif
