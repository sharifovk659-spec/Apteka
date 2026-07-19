@props(['filters', 'products'])

@php
    $sortOptions = [
        'popular' => 'По популярности',
        'price_asc' => 'Сначала дешевле',
        'price_desc' => 'Сначала дороже',
        'newest' => 'Новинки',
    ];
@endphp

<div class="catalog-toolbar">
    <button type="button" class="catalog-toolbar__filters-btn" id="catalog-filters-open" aria-controls="catalog-drawer">
        <x-icon name="burger" />
        <span>Фильтры</span>
    </button>

    <form action="{{ route('catalog.index') }}" method="GET" class="catalog-toolbar__sort-form" id="catalog-sort-form">
        @foreach(request()->except(['sort', 'view', 'page']) as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <input type="hidden" name="view" value="{{ $filters['view'] }}">

        <label class="catalog-toolbar__sort-label" for="catalog-sort">Сортировка</label>
        <select name="sort" id="catalog-sort" class="catalog-toolbar__sort">
            @foreach($sortOptions as $value => $label)
                <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="catalog-toolbar__view" role="group" aria-label="Вид отображения">
        @php
            $gridParams = array_merge(request()->except('view'), ['view' => 'grid']);
            $listParams = array_merge(request()->except('view'), ['view' => 'list']);
        @endphp
        <a
            href="{{ route('catalog.index', $gridParams) }}"
            class="catalog-toolbar__view-btn {{ $filters['view'] === 'grid' ? 'is-active' : '' }}"
            aria-label="Сетка"
        >
            <x-icon name="catalog" />
        </a>
        <a
            href="{{ route('catalog.index', $listParams) }}"
            class="catalog-toolbar__view-btn {{ $filters['view'] === 'list' ? 'is-active' : '' }}"
            aria-label="Список"
        >
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M5 7h14M5 12h14M5 17h14"/>
            </svg>
        </a>
    </div>

    <p class="catalog-toolbar__count">{{ number_format($products->total(), 0, '.', ' ') }} товаров</p>
</div>
