@props(['filters', 'filterCategories', 'filterBrands', 'manufacturers', 'dosageForms', 'priceRange', 'id' => 'catalog-filters'])

<form
    id="{{ $id }}"
    action="{{ route('catalog.index') }}"
    method="GET"
    class="catalog-filters"
>
    @if($filters['search'])
        <input type="hidden" name="search" value="{{ $filters['search'] }}">
    @endif
    @if($filters['sort'] && $filters['sort'] !== 'popular')
        <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
    @endif
    @if($filters['view'] && $filters['view'] !== 'grid')
        <input type="hidden" name="view" value="{{ $filters['view'] }}">
    @endif

    <div class="catalog-filters__group">
        <h3 class="catalog-filters__title">Категории</h3>
        <ul class="catalog-filters__list">
            <li>
                <label class="catalog-filters__check">
                    <input type="radio" name="category" value="" @checked(! $filters['category'])>
                    <span>Все категории</span>
                </label>
            </li>
            @foreach($filterCategories as $category)
                <li>
                    <label class="catalog-filters__check" style="padding-left: {{ ($category->tree_depth ?? 0) * 14 }}px">
                        <input
                            type="radio"
                            name="category"
                            value="{{ $category->slug }}"
                            @checked($filters['category'] === $category->slug)
                        >
                        <span>{{ $category->tree_label ?? $category->name }}</span>
                        <span class="catalog-filters__count">{{ $category->products_count }}</span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="catalog-filters__group">
        <h3 class="catalog-filters__title">Цена, смн</h3>
        <div class="catalog-filters__range">
            <input
                type="number"
                name="min_price"
                class="catalog-filters__input"
                placeholder="От"
                min="0"
                value="{{ $filters['min_price'] }}"
            >
            <span class="catalog-filters__dash">—</span>
            <input
                type="number"
                name="max_price"
                class="catalog-filters__input"
                placeholder="До"
                min="0"
                value="{{ $filters['max_price'] }}"
            >
        </div>
        @if($priceRange)
            <p class="catalog-filters__hint">
                {{ number_format($priceRange->min_price, 0, '.', ' ') }}
                —
                {{ number_format($priceRange->max_price, 0, '.', ' ') }} смн
            </p>
        @endif
    </div>

    <div class="catalog-filters__group">
        <h3 class="catalog-filters__title">Бренды</h3>
        <ul class="catalog-filters__list catalog-filters__list--scroll">
            <li>
                <label class="catalog-filters__check">
                    <input type="radio" name="brand" value="" @checked(! $filters['brand'])>
                    <span>Все бренды</span>
                </label>
            </li>
            @foreach($filterBrands as $brand)
                <li>
                    <label class="catalog-filters__check">
                        <input
                            type="radio"
                            name="brand"
                            value="{{ $brand->slug }}"
                            @checked($filters['brand'] === $brand->slug)
                        >
                        <span>{{ $brand->name }}</span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="catalog-filters__group">
        <h3 class="catalog-filters__title">Производитель</h3>
        <select name="manufacturer" class="catalog-filters__select">
            <option value="">Все производители</option>
            @foreach($manufacturers as $manufacturer)
                <option value="{{ $manufacturer }}" @selected($filters['manufacturer'] === $manufacturer)>
                    {{ $manufacturer }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="catalog-filters__group">
        <h3 class="catalog-filters__title">Форма выпуска</h3>
        <select name="dosage_form" class="catalog-filters__select">
            <option value="">Все формы</option>
            @foreach($dosageForms as $form)
                <option value="{{ $form }}" @selected($filters['dosage_form'] === $form)>
                    {{ $form }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="catalog-filters__group">
        <h3 class="catalog-filters__title">Дополнительно</h3>
        <ul class="catalog-filters__list">
            <li>
                <label class="catalog-filters__check">
                    <input type="checkbox" name="in_stock" value="1" @checked($filters['in_stock'] === true)>
                    <span>В наличии</span>
                </label>
            </li>
            <li>
                <label class="catalog-filters__check">
                    <input type="checkbox" name="discount" value="1" @checked($filters['discount'] === true)>
                    <span>Товары со скидкой</span>
                </label>
            </li>
            <li>
                <label class="catalog-filters__check">
                    <input type="radio" name="prescription" value="" @checked($filters['prescription'] === null)>
                    <span>Любой рецепт</span>
                </label>
            </li>
            <li>
                <label class="catalog-filters__check">
                    <input type="radio" name="prescription" value="0" @checked($filters['prescription'] === false)>
                    <span>Без рецепта</span>
                </label>
            </li>
            <li>
                <label class="catalog-filters__check">
                    <input type="radio" name="prescription" value="1" @checked($filters['prescription'] === true)>
                    <span>Рецептурный</span>
                </label>
            </li>
        </ul>
    </div>

    <div class="catalog-filters__actions">
        <button type="submit" class="btn btn--primary catalog-filters__submit">Применить</button>
        <a href="{{ route('catalog.index') }}" class="catalog-filters__reset">Сбросить</a>
    </div>
</form>
