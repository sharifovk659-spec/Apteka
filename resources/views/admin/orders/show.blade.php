@extends('layouts.admin')

@section('title', 'Заказ '.$order->order_number)
@section('page-title', 'Заказ '.$order->order_number)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <a href="{{ route('admin.orders.index') }}" class="admin-breadcrumb__link">Заказы</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">{{ $order->order_number }}</span>
@endsection

@section('content')
    @include('admin.partials.alerts')

    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">{{ $order->order_number }}</h1>
            <p class="admin-page-head__meta">{{ $order->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <x-status-badge :status="$order->status" />
    </div>

    <div class="admin-form-grid">
        <section class="admin-form-section">
            <h2 class="admin-form-section__title">Клиент</h2>
            <p><strong>{{ $order->customer_name }}</strong></p>
            <p>{{ $order->customer_phone }}</p>
            @if($order->customer_email)<p>{{ $order->customer_email }}</p>@endif
            <p>{{ $order->address }}</p>
            @if($order->comment)<p class="admin-page-head__meta">{{ $order->comment }}</p>@endif
        </section>

        <section class="admin-form-section">
            <h2 class="admin-form-section__title">Доставка и оплата</h2>
            <p>Доставка: {{ $order->delivery_type === 'courier' ? 'Курьер' : 'Самовывоз' }}</p>
            <p>Оплата: {{ $order->payment_method }}</p>
            <p>Товары: {{ number_format($order->subtotal, 0, '.', ' ') }} смн</p>
            <p>Доставка: {{ number_format($order->delivery_price, 0, '.', ' ') }} смн</p>
            <p><strong>Итого: {{ number_format($order->total, 0, '.', ' ') }} смн</strong></p>
        </section>

        <section class="admin-form-section admin-field--full">
            <h2 class="admin-form-section__title">Товары заказа</h2>
            <x-admin-table :headers="['Товар', 'Цена', 'Кол-во', 'Сумма']">
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ number_format($item->price, 0, '.', ' ') }} смн</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->total, 0, '.', ' ') }} смн</td>
                    </tr>
                @endforeach
            </x-admin-table>
        </section>

        <section class="admin-form-section">
            <h2 class="admin-form-section__title">Изменить статус</h2>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="admin-form-actions">
                @csrf @method('PATCH')
                <select name="status" class="admin-field__input">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="admin-btn admin-btn--primary">Сохранить статус</button>
            </form>
            @if($order->stock_returned_at)
                <p class="admin-page-head__meta">Товары возвращены на склад: {{ $order->stock_returned_at->format('d.m.Y H:i') }}</p>
            @endif
        </section>
    </div>
@endsection
