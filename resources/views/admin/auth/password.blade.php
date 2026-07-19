<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Смена пароля — {{ config('store.name') }} Admin</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="admin-login-page">
    <div class="admin-login">
        <div class="admin-login__card">
            <div class="admin-login__brand">
                <span class="admin-login__mark">!</span>
                <h1>Смена пароля</h1>
                <p>В production необходимо задать новый пароль администратора.</p>
            </div>

            @if(session('warning'))
                <div class="admin-alert admin-alert--error">{{ session('warning') }}</div>
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

            <form action="{{ route('admin.password.update') }}" method="POST" class="admin-form">
                @csrf
                @method('PUT')
                <label class="admin-field admin-field--full">
                    <span class="admin-field__label">Текущий пароль</span>
                    <input type="password" name="current_password" class="admin-field__input" required autofocus>
                </label>
                <label class="admin-field admin-field--full">
                    <span class="admin-field__label">Новый пароль</span>
                    <input type="password" name="password" class="admin-field__input" required>
                </label>
                <label class="admin-field admin-field--full">
                    <span class="admin-field__label">Подтверждение пароля</span>
                    <input type="password" name="password_confirmation" class="admin-field__input" required>
                </label>
                <button type="submit" class="admin-btn admin-btn--primary admin-btn--block">Сохранить пароль</button>
            </form>
        </div>
    </div>
</body>
</html>
