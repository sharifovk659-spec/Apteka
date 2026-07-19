@extends('layouts.admin')

@section('title', 'Настройки')
@section('page-title', 'Настройки')

@section('content')
    @include('admin.partials.alerts')

    <div class="admin-page-head"><h1 class="admin-page-head__title">Настройки сайта</h1></div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf @method('PUT')

        <div class="admin-form-grid">
            <section class="admin-form-section">
                <h2 class="admin-form-section__title">Аптека</h2>
                <div class="admin-form-grid__fields">
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Название *</span><input type="text" name="store[name]" value="{{ old('store.name', $values->get('store.name')) }}" class="admin-field__input" required></label>
                    <label class="admin-field"><span class="admin-field__label">Телефон *</span><input type="text" name="store[phone]" value="{{ old('store.phone', $values->get('store.phone')) }}" class="admin-field__input" required></label>
                    <label class="admin-field"><span class="admin-field__label">Email</span><input type="email" name="store[email]" value="{{ old('store.email', $values->get('store.email')) }}" class="admin-field__input"></label>
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Адрес</span><input type="text" name="store[address]" value="{{ old('store.address', $values->get('store.address')) }}" class="admin-field__input"></label>
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Слоган</span><input type="text" name="store[tagline]" value="{{ old('store.tagline', $values->get('store.tagline')) }}" class="admin-field__input"></label>
                    @if($values->get('store.logo'))
                        <div class="admin-field admin-field--full"><img src="{{ asset('storage/'.$values->get('store.logo')) }}" alt="" class="admin-form-preview" width="120" height="120"><label class="admin-field admin-field--checkbox"><input type="checkbox" name="remove_logo" value="1"><span>Удалить логотип</span></label></div>
                    @endif
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Логотип</span><input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="admin-field__input"></label>
                </div>
            </section>

            <section class="admin-form-section">
                <h2 class="admin-form-section__title">Доставка и заказы</h2>
                <div class="admin-form-grid__fields">
                    <label class="admin-field"><span class="admin-field__label">Стоимость доставки, смн *</span><input type="number" name="delivery[default_price]" value="{{ old('delivery.default_price', $values->get('delivery.default_price', 15000)) }}" class="admin-field__input" min="0" step="1" required></label>
                    <label class="admin-field"><span class="admin-field__label">Мин. сумма заказа, смн *</span><input type="number" name="order[min_amount]" value="{{ old('order.min_amount', $values->get('order.min_amount', 0)) }}" class="admin-field__input" min="0" step="1" required></label>
                </div>
            </section>

            <section class="admin-form-section">
                <h2 class="admin-form-section__title">Социальные сети</h2>
                <div class="admin-form-grid__fields">
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Telegram</span><input type="text" name="social[telegram]" value="{{ old('social.telegram', $values->get('social.telegram')) }}" class="admin-field__input"></label>
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Instagram</span><input type="text" name="social[instagram]" value="{{ old('social.instagram', $values->get('social.instagram')) }}" class="admin-field__input"></label>
                    <label class="admin-field admin-field--full"><span class="admin-field__label">Facebook</span><input type="text" name="social[facebook]" value="{{ old('social.facebook', $values->get('social.facebook')) }}" class="admin-field__input"></label>
                </div>
            </section>
        </div>

        <div class="admin-form-actions"><button type="submit" class="admin-btn admin-btn--primary">Сохранить настройки</button></div>
    </form>
@endsection
