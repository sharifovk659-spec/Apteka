@php
    $menuItems = [
        ['route' => 'admin.dashboard', 'label' => 'Главная', 'icon' => 'dashboard', 'pattern' => 'admin', 'exact' => true],
        ['route' => 'admin.products.index', 'label' => 'Товары', 'icon' => 'products', 'pattern' => 'admin/products*'],
        ['route' => 'admin.categories.index', 'label' => 'Категории', 'icon' => 'categories', 'pattern' => 'admin/categories*'],
        ['route' => 'admin.brands.index', 'label' => 'Бренды', 'icon' => 'products', 'pattern' => 'admin/brands*'],
        ['route' => 'admin.banners.index', 'label' => 'Баннеры', 'icon' => 'promotions', 'pattern' => 'admin/banners*'],
        ['route' => 'admin.orders.index', 'label' => 'Заказы', 'icon' => 'orders', 'pattern' => 'admin/orders*'],
        ['route' => 'admin.customers.index', 'label' => 'Покупатели', 'icon' => 'customers', 'pattern' => 'admin/customers*'],
        ['route' => 'admin.warehouse.index', 'label' => 'Склад', 'icon' => 'warehouse', 'pattern' => 'admin/warehouse*'],
        ['route' => 'admin.reviews.index', 'label' => 'Отзывы', 'icon' => 'reviews', 'pattern' => 'admin/reviews*'],
        ['route' => 'admin.reports.index', 'label' => 'Отчёты', 'icon' => 'reports', 'pattern' => 'admin/reports*'],
        ['route' => 'admin.settings.index', 'label' => 'Настройки', 'icon' => 'settings', 'pattern' => 'admin/settings*'],
        ['route' => 'admin.users.index', 'label' => 'Пользователи', 'icon' => 'users', 'pattern' => 'admin/users*'],
    ];
@endphp

<aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar__head">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__logo">
            <span class="admin-sidebar__logo-mark">+</span>
            <span class="admin-sidebar__logo-text">{{ $storeName }}</span>
        </a>
        <button type="button" class="admin-sidebar__collapse" id="admin-sidebar-collapse" aria-label="Свернуть меню">
            <x-admin-icon name="chevron-left" />
        </button>
    </div>

    <nav class="admin-sidebar__nav" aria-label="Админ-навигация">
        @foreach($menuItems as $item)
            @php
                $isActive = ! empty($item['exact'])
                    ? request()->routeIs($item['route'])
                    : request()->is($item['pattern']);
            @endphp
            <a
                href="{{ route($item['route']) }}"
                @class(['admin-sidebar__link', 'is-active' => $isActive])
                title="{{ $item['label'] }}"
            >
                <x-admin-icon :name="$item['icon']" />
                <span class="admin-sidebar__link-text">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="admin-sidebar__footer">
        <a href="{{ route('home') }}" class="admin-sidebar__link" title="На сайт">
            <x-admin-icon name="dashboard" />
            <span class="admin-sidebar__link-text">На сайт</span>
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" class="admin-sidebar__logout-form">
            @csrf
            <button type="submit" class="admin-sidebar__link admin-sidebar__link--logout" title="Выйти">
                <x-admin-icon name="logout" />
                <span class="admin-sidebar__link-text">Выйти</span>
            </button>
        </form>
    </div>
</aside>
