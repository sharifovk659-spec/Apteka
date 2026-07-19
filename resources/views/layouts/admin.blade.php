<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Панель управления') — {{ $storeName }} Admin</title>
    @vite(['resources/css/admin.css', 'resources/css/responsive.css', 'resources/js/admin.js'])
    @stack('scripts')
    <script>
        if (localStorage.getItem('sabth-admin-sidebar') === 'collapsed') {
            document.documentElement.classList.add('admin-sidebar-collapsed');
        }
    </script>
</head>
<body class="admin-body" id="admin-body">
    <div class="admin-overlay" id="admin-overlay" hidden></div>

    @include('partials.admin.sidebar')

    <div class="admin-wrapper">
        @include('partials.admin.topbar')

        <main class="admin-main">
            @yield('content')
        </main>
    </div>
</body>
</html>
