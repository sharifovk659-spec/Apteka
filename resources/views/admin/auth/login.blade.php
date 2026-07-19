<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход — {{ config('store.name') }} Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="admin-login-page">
    <div class="admin-login">
        <div class="admin-login__card">
            <div class="admin-login__brand">
                <span class="admin-login__mark">+</span>
                <h1>{{ config('store.name') }}</h1>
                <p>Панель администратора</p>
            </div>

            @if(session('success'))
                <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="admin-alert admin-alert--error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="admin-alert admin-alert--error">
                    <ul class="admin-alert__list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.store') }}" method="POST" class="admin-form">
                @csrf
                <label class="admin-field admin-field--full">
                    <span class="admin-field__label">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" class="admin-field__input" required autofocus>
                </label>
                <label class="admin-field admin-field--full">
                    <span class="admin-field__label">Пароль</span>
                    <input type="password" name="password" class="admin-field__input" required>
                </label>
                <label class="admin-field admin-field--checkbox">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Запомнить меня</span>
                </label>
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--block">Войти</button>
            </form>
        </div>
    </div>
</body>
</html>
