@php
    $user = auth()->user();
    $initial = $user ? mb_strtoupper(mb_substr($user->name, 0, 1)) : 'A';
@endphp

<header class="admin-topbar">
    <div class="admin-topbar__left">
        <button type="button" class="admin-topbar__burger" id="admin-mobile-toggle" aria-label="Открыть меню">
            <x-admin-icon name="menu" />
        </button>
        <nav class="admin-breadcrumb" aria-label="Breadcrumb">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @else
                <span class="admin-breadcrumb__current">@yield('page-title', 'Панель управления')</span>
            @endif
        </nav>
    </div>

    <div class="admin-topbar__right">
        <div class="admin-profile" id="admin-profile">
            <button type="button" class="admin-profile__toggle" id="admin-profile-toggle" aria-expanded="false" aria-controls="admin-profile-menu">
                <span class="admin-profile__avatar">{{ $initial }}</span>
                <span class="admin-profile__info">
                    <span class="admin-profile__name">{{ $user?->name ?? 'Администратор' }}</span>
                    <span class="admin-profile__role">{{ $user?->role?->name ?? 'Admin' }}</span>
                </span>
            </button>
            <div class="admin-profile__menu" id="admin-profile-menu" hidden>
                <a href="{{ route('admin.settings.index') }}" class="admin-profile__menu-item">Настройки</a>
                <a href="{{ route('home') }}" class="admin-profile__menu-item">На сайт</a>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-profile__menu-item admin-profile__menu-item--danger admin-profile__menu-item--button">Выйти</button>
                </form>
            </div>
        </div>
    </div>
</header>
