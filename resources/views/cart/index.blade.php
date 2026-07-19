@extends('layouts.app')

@section('title', 'Корзина')
@section('main-class', 'main--cart')

@section('content')
    <div class="container">

        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}" class="breadcrumbs__link">Главная</a>
            <span class="breadcrumbs__sep">/</span>
            <span class="breadcrumbs__current">Корзина</span>
        </nav>

        <header class="cart-header">
            <h1 class="cart-header__title">Корзина</h1>
            @if($items->isNotEmpty())
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="cart-header__clear">Очистить корзину</button>
                </form>
            @endif
        </header>

        @if($items->isEmpty())
            <div class="cart-empty">
                <p class="cart-empty__text">Ваша корзина пуста</p>
                <a href="{{ route('catalog.index') }}" class="btn btn--primary">Перейти в каталог</a>
            </div>
        @else
            <div class="cart-layout">
                <div class="cart-items">
                    @foreach($items as $item)
                        @php($product = $item['product'])
                        <article class="cart-item">
                            <a href="{{ route('product.show', $product->slug) }}" class="cart-item__image">
                                <x-product-image :product="$product" :width="96" :height="96" :lazy="true" />
                            </a>
                            <div class="cart-item__info">
                                <h2 class="cart-item__title">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                </h2>
                                <p class="cart-item__price">{{ number_format($product->price, 0, '.', ' ') }} смн</p>
                                <p class="cart-item__stock">В наличии: {{ $product->stock }} шт.</p>
                            </div>
                            <div class="cart-item__quantity">
                                <form action="{{ route('cart.update', $product->id) }}" method="POST" class="cart-qty">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}">
                                    <button type="submit" class="cart-qty__btn" aria-label="Уменьшить количество">−</button>
                                </form>
                                <span class="cart-qty__value">{{ $item['quantity'] }}</span>
                                <form action="{{ route('cart.update', $product->id) }}" method="POST" class="cart-qty">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ min($product->stock, $item['quantity'] + 1) }}">
                                    <button type="submit" class="cart-qty__btn" aria-label="Увеличить количество" @disabled($item['quantity'] >= $product->stock)>+</button>
                                </form>
                            </div>
                            <div class="cart-item__total">
                                {{ number_format($item['line_total'], 0, '.', ' ') }} смн
                            </div>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="cart-item__remove">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cart-item__remove-btn" aria-label="Удалить {{ $product->name }}">
                                    <x-icon name="close" />
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>

                <aside class="cart-summary">
                    <h2 class="cart-summary__title">Итого</h2>
                    <dl class="cart-summary__list">
                        <div class="cart-summary__row">
                            <dt>Товары</dt>
                            <dd>{{ number_format($subtotal, 0, '.', ' ') }} смн</dd>
                        </div>
                        <div class="cart-summary__row cart-summary__row--total">
                            <dt>К оплате</dt>
                            <dd>{{ number_format($subtotal, 0, '.', ' ') }} смн</dd>
                        </div>
                    </dl>
                    <p class="cart-summary__note">Стоимость доставки рассчитывается при оформлении</p>
                    <a href="{{ route('checkout.index') }}" class="btn btn--primary cart-summary__btn">Оформить заказ</a>
                </aside>
            </div>
        @endif
    </div>
@endsection
