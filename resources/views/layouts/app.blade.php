<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $storeName) — {{ $storeTagline }}</title>
    @vite(['resources/css/app.css', 'resources/css/responsive.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="@yield('body-class')">
    @include('partials.public.header')

    <main class="main @yield('main-class')">
        @include('partials.public.flash')
        @yield('content')
    </main>

    @include('partials.public.footer')
    @include('partials.public.mobile-nav')
    @stack('scripts')
</body>
</html>
