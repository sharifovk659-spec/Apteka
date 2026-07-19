@extends('layouts.app')

@section('title', 'Оформление заказа')
@section('main-class', 'main--checkout')

@section('content')
    <div class="container">

        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}" class="breadcrumbs__link">Главная</a>
            <span class="breadcrumbs__sep">/</span>
            <a href="{{ route('cart.index') }}" class="breadcrumbs__link">Корзина</a>
            <span class="breadcrumbs__sep">/</span>
            <span class="breadcrumbs__current">Оформление</span>
        </nav>

        <h1 class="page-title">Оформление заказа</h1>

        <div class="checkout-layout">
            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-form">
                @csrf

                <section class="checkout-section">
                    <h2 class="checkout-section__title">Контактные данные</h2>
                    <label class="checkout-field">
                        <span class="checkout-field__label">Имя *</span>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="checkout-field__input" required>
                        @error('customer_name')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </label>
                    <label class="checkout-field">
                        <span class="checkout-field__label">Телефон *</span>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" class="checkout-field__input" placeholder="+992 XX XXX XX XX" required>
                        @error('customer_phone')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </label>
                    <label class="checkout-field">
                        <span class="checkout-field__label">Email</span>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="checkout-field__input">
                        @error('customer_email')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </label>
                </section>

                <section class="checkout-section">
                    <h2 class="checkout-section__title">Доставка</h2>
                    <label class="checkout-field">
                        <span class="checkout-field__label">Адрес *</span>
                        <textarea name="address" rows="3" class="checkout-field__input" required>{{ old('address') }}</textarea>
                        @error('address')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </label>
                    <fieldset class="checkout-options">
                        <legend class="checkout-field__label">Способ получения *</legend>
                        <label class="checkout-option">
                            <input type="radio" name="delivery_type" value="courier" @checked(old('delivery_type', 'courier') === 'courier')>
                            <span>Доставка курьером (+{{ number_format($deliveryPrice, 0, '.', ' ') }} смн)</span>
                        </label>
                        <label class="checkout-option">
                            <input type="radio" name="delivery_type" value="pickup" @checked(old('delivery_type') === 'pickup')>
                            <span>Самовывоз (бесплатно)</span>
                        </label>
                        @error('delivery_type')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </fieldset>
                </section>

                <section class="checkout-section">
                    <h2 class="checkout-section__title">Оплата</h2>
                    <fieldset class="checkout-options">
                        <legend class="checkout-field__label">Способ оплаты *</legend>
                        @foreach([
                            'cash' => 'Наличные',
                            'card' => 'Банковская карта',
                            'alif' => 'Alif',
                            'dushanbe_city' => 'Dushanbe City',
                        ] as $value => $label)
                            <label class="checkout-option">
                                <input type="radio" name="payment_method" value="{{ $value }}" @checked(old('payment_method', 'cash') === $value)>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                        @error('payment_method')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </fieldset>
                </section>

                <section class="checkout-section">
                    <h2 class="checkout-section__title">Комментарий</h2>
                    <label class="checkout-field">
                        <span class="checkout-field__label">Комментарий к заказу</span>
                        <textarea name="comment" rows="3" class="checkout-field__input">{{ old('comment') }}</textarea>
                        @error('comment')<span class="checkout-field__error">{{ $message }}</span>@enderror
                    </label>
                </section>

                <button type="submit" class="btn btn--primary checkout-form__submit">Подтвердить заказ</button>
            </form>

            <aside class="checkout-summary">
                <h2 class="checkout-summary__title">Ваш заказ</h2>
                <ul class="checkout-summary__items">
                    @foreach($items as $item)
                        <li class="checkout-summary__item">
                            <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                            <span>{{ number_format($item['line_total'], 0, '.', ' ') }} смн</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="checkout-summary__totals">
                    <div class="checkout-summary__row">
                        <dt>Товары</dt>
                        <dd>{{ number_format($subtotal, 0, '.', ' ') }} смн</dd>
                    </div>
                    <div class="checkout-summary__row">
                        <dt>Доставка</dt>
                        <dd id="checkout-delivery-price">{{ number_format($deliveryPrice, 0, '.', ' ') }} смн</dd>
                    </div>
                    <div class="checkout-summary__row checkout-summary__row--total">
                        <dt>Итого</dt>
                        <dd id="checkout-total-price">{{ number_format($subtotal + $deliveryPrice, 0, '.', ' ') }} смн</dd>
                    </div>
                </dl>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.checkoutTotals = {
            subtotal: {{ (int) $subtotal }},
            delivery: {{ (int) $deliveryPrice }},
        };
    </script>
    @vite('resources/js/checkout.js')
@endpush
