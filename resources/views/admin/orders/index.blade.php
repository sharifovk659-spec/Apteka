@extends('layouts.admin')

@section('title', 'Заказы')
@section('page-title', 'Заказы')

@section('content')
    @include('admin.partials.alerts')

    <div class="admin-page-head"><h1 class="admin-page-head__title">Заказы</h1></div>

    <form action="{{ route('admin.orders.index') }}" method="GET" class="admin-filters">
        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Номер, имя, телефон" class="admin-field__input admin-filters__search">
        <select name="status" class="admin-field__input">
            <option value="">Все статусы</option>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-btn admin-btn--primary">Применить</button>
        <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn--outline">Сбросить</a>
    </form>

    <div class="admin-panel admin-panel--table">
        <div class="admin-panel__body admin-panel__body--flush">
            <x-admin-table :headers="['Номер', 'Клиент', 'Телефон', 'Сумма', 'Статус', 'Дата', 'Действия']">
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="admin-link">{{ $order->order_number }}</a></td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>{{ number_format($order->total, 0, '.', ' ') }} смн</td>
                        <td><x-status-badge :status="$order->status" /></td>
                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="admin-link">Открыть</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-admin-empty-state title="Заказы не найдены" /></td></tr>
                @endforelse
            </x-admin-table>
        </div>
    </div>
    {{ $orders->links('components.admin-pagination') }}
@endsection
