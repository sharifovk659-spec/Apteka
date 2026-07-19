<nav class="mobile-nav" aria-label="Мобильная навигация">
    <a href="{{ route('home') }}" class="mobile-nav__item {{ request()->routeIs('home') ? 'is-active' : '' }}">
        <x-icon name="home" />
        <span>Главная</span>
    </a>
    <a href="{{ route('catalog.index') }}" class="mobile-nav__item {{ request()->routeIs('catalog.*') ? 'is-active' : '' }}">
        <x-icon name="catalog" />
        <span>Каталог</span>
    </a>
    <a href="{{ route('cart.index') }}" class="mobile-nav__item {{ request()->routeIs('cart.*') ? 'is-active' : '' }}">
        <x-icon name="cart" />
        <span>Корзина</span>
    </a>
    <a href="{{ route('favorites.index') }}" class="mobile-nav__item {{ request()->routeIs('favorites.*') ? 'is-active' : '' }}">
        <x-icon name="heart" />
        <span>Избранное</span>
    </a>
    <a href="#" class="mobile-nav__item">
        <x-icon name="user" />
        <span>Кабинет</span>
    </a>
</nav>
