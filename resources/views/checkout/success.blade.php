@extends('layouts.app')

@section('title', 'Заказ оформлен')
@section('main-class', 'main--checkout-success')

@section('content')
    <div class="container">
        <div class="checkout-success">
            <div class="checkout-success__icon" aria-hidden="true">✓</div>
            <h1 class="checkout-success__title">Спасибо за заказ!</h1>
            <p class="checkout-success__text">
                Ваш заказ <strong>{{ $order->order_number }}</strong> принят и передан в обработку.
            </p>

            <dl class="checkout-success__details">
                <div>
                    <dt>Получатель</dt>
                    <dd>{{ $order->customer_name }}</dd>
                </div>
                <div>
                    <dt>Телефон</dt>
                    <dd>{{ $order->customer_phone }}</dd>
                </div>
                <div>
                    <dt>Сумма</dt>
                    <dd>{{ number_format($order->total, 0, '.', ' ') }} смн</dd>
                </div>
                <div>
                    <dt>Оплата</dt>
                    <dd>
                        @switch($order->payment_method)
                            @case('cash') Наличные @break
                            @case('card') Банковская карта @break
                            @case('alif') Alif @break
                            @case('dushanbe_city') Dushanbe City @break
                        @endswitch
                    </dd>
                </div>
            </dl>

            <div class="checkout-success__actions">
                <a href="{{ route('catalog.index') }}" class="btn btn--primary">Продолжить покупки</a>
                <a href="{{ route('home') }}" class="btn btn--outline">На главную</a>
            </div>
        </div>
    </div>
@endsection
