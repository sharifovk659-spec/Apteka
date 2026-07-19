<header class="site-header" id="site-header">
    {{-- Top bar --}}
    <div class="header-top">
        <div class="container header-top__inner">
            <div class="header-top__city">
                <x-icon name="location" class="header-top__city-icon" />
                <span>Душанбе</span>
            </div>
            <nav class="header-top__nav" aria-label="Сервисные ссылки">
                <a href="#" class="header-top__link">Мобильное приложение</a>
                <a href="#" class="header-top__link">E-рецепты</a>
                <a href="#" class="header-top__link">Как сделать заказ</a>
                <a href="#" class="header-top__link">Доставка и оплата</a>
            </nav>
            <div class="header-lang" role="group" aria-label="Выбор языка">
                <button type="button" class="header-lang__btn is-active" data-lang="ru">RU</button>
                <span class="header-lang__sep">/</span>
                <button type="button" class="header-lang__btn" data-lang="tj">TJ</button>
            </div>
        </div>
    </div>

    {{-- Main row --}}
    <div class="header-main">
        <div class="container header-main__inner">
            <a href="{{ route('home') }}" class="header-logo" aria-label="{{ $storeName }} — на главную">
                <span class="header-logo__mark">+</span>
                <span class="header-logo__text">{{ $storeName }}</span>
            </a>

            <div class="header-catalog" id="header-catalog">
                <button type="button" class="header-catalog__btn" id="catalog-toggle" aria-expanded="false" aria-controls="catalog-dropdown">
                    <x-icon name="catalog" />
                    <span>Каталог товаров</span>
                    <x-icon name="chevron-down" class="header-catalog__chevron" />
                </button>
                <div class="header-catalog__dropdown" id="catalog-dropdown" hidden>
                    <div class="header-catalog__dropdown-inner">
                        <a href="{{ route('catalog.index') }}" class="header-catalog__item">Все товары</a>
                        @foreach($headerCategories as $category)
                            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="header-catalog__item">
                                {{ $category->name }}
                            </a>
                            @include('partials.public.category-nav-children', ['categories' => $category->children, 'depth' => 1])
                        @endforeach
                    </div>
                </div>
            </div>

            <form action="{{ route('catalog.index') }}" method="GET" class="header-search" role="search">
                <x-icon name="search" class="header-search__icon" />
                <input
                    type="search"
                    name="q"
                    class="header-search__input"
                    placeholder="Поиск лекарств, витаминов, товаров..."
                    aria-label="Поиск по каталогу"
                >
                <button type="submit" class="header-search__submit">Найти</button>
            </form>

            <div class="header-actions">
                <a href="{{ route('cart.index') }}" class="header-action" aria-label="Корзина{{ $cartCount ? ', '.$cartCount.' товаров' : '' }}">
                    <span class="header-action__icon-wrap">
                        <x-icon name="cart" />
                        @if($cartCount > 0)
                            <span class="header-action__badge">{{ $cartCount }}</span>
                        @endif
                    </span>
                    <span class="header-action__label">Корзина</span>
                </a>
                <a href="#" class="header-action header-action--hide-mobile" aria-label="Мои заказы">
                    <x-icon name="orders" />
                    <span class="header-action__label">Мои заказы</span>
                </a>
                <a href="{{ route('favorites.index') }}" class="header-action header-action--hide-mobile" aria-label="Избранное{{ $favoritesCount ? ', '.$favoritesCount.' товаров' : '' }}">
                    <span class="header-action__icon-wrap">
                        <x-icon name="heart" />
                        @if($favoritesCount > 0)
                            <span class="header-action__badge">{{ $favoritesCount }}</span>
                        @endif
                    </span>
                    <span class="header-action__label">Избранное</span>
                </a>
                <a href="#" class="header-action header-action--hide-mobile" aria-label="Войти">
                    <x-icon name="user" />
                    <span class="header-action__label">Войти</span>
                </a>
            </div>

            <button type="button" class="header-burger" id="header-burger" aria-label="Открыть меню" aria-expanded="false">
                <x-icon name="burger" class="header-burger__open" />
                <x-icon name="close" class="header-burger__close" />
            </button>
        </div>
    </div>

    {{-- Category chips --}}
    <div class="header-categories">
        <div class="container header-categories__inner">
            <nav class="header-chips" id="header-chips" aria-label="Категории">
                <a href="{{ route('catalog.index', ['discount' => 1]) }}" class="header-chip header-chip--accent">% Акции</a>
                @foreach($headerCategories as $category)
                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="header-chip">{{ $category->name }}</a>
                @endforeach
                <button type="button" class="header-chip header-chip--more" id="header-categories-more">Ещё</button>
            </nav>
            <button type="button" class="header-chips__scroll" id="header-chips-scroll" aria-label="Прокрутить категории">
                <x-icon name="chevron-right" />
            </button>
        </div>
    </div>

    {{-- Mobile menu overlay --}}
    <div class="header-mobile-menu" id="header-mobile-menu" hidden>
        <nav class="header-mobile-menu__nav">
            <a href="{{ route('catalog.index') }}">Каталог товаров</a>
            @foreach($headerCategories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                @include('partials.public.category-nav-children', ['categories' => $category->children, 'depth' => 1, 'mobile' => true])
            @endforeach
            <a href="#">Мои заказы</a>
            <a href="{{ route('favorites.index') }}">Избранное</a>
            <a href="#">Войти</a>
            <a href="#">E-рецепты</a>
            <a href="#">Доставка и оплата</a>
        </nav>
    </div>
</header>
