@extends('layouts.admin')

@section('title', 'Главная')
@section('page-title', 'Главная')

@section('breadcrumb')
    <span class="admin-breadcrumb__current">Главная</span>
@endsection

@section('content')
    <div class="dashboard">
        <section class="dashboard__stats">
            @foreach($stats as $stat)
                <x-admin-stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :trend="$stat['trend']"
                    :icon="$stat['icon']"
                />
            @endforeach
        </section>

        <section class="dashboard__charts">
            <div class="admin-panel">
                <div class="admin-panel__head">
                    <h2 class="admin-panel__title">Заказы за 7 дней</h2>
                </div>
                <div class="admin-panel__body">
                    <canvas id="orders-line-chart" height="120" aria-label="График заказов за 7 дней"></canvas>
                </div>
            </div>

            <div class="admin-panel">
                <div class="admin-panel__head">
                    <h2 class="admin-panel__title">Статусы заказов</h2>
                </div>
                <div class="admin-panel__body admin-panel__body--chart">
                    <canvas id="orders-donut-chart" height="200" aria-label="Диаграмма статусов заказов"></canvas>
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2 class="admin-panel__title">Последние заказы</h2>
                <a href="{{ route('admin.orders.index') }}" class="admin-panel__link">Все заказы</a>
            </div>
            <div class="admin-panel__body admin-panel__body--flush">
                <x-admin-table :headers="['Номер', 'Клиент', 'Телефон', 'Сумма', 'Статус', 'Дата', '']">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td data-label="Номер">{{ $order->order_number }}</td>
                            <td data-label="Клиент">{{ $order->customer_name }}</td>
                            <td data-label="Телефон">{{ $order->customer_phone }}</td>
                            <td data-label="Сумма">{{ number_format($order->total, 0, '.', ' ') }} смн</td>
                            <td data-label="Статус">
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td data-label="Дата">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td data-label="Действие">
                                <a href="{{ route('admin.orders.show', $order) }}" class="admin-link">Открыть</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-admin-empty-state title="Заказов пока нет" text="Новые заказы появятся здесь." />
                            </td>
                        </tr>
                    @endforelse
                </x-admin-table>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2 class="admin-panel__title">Низкий остаток</h2>
                <span class="admin-panel__meta">Меньше 10 шт.</span>
            </div>
            <div class="admin-panel__body admin-panel__body--flush">
                <x-admin-table :headers="['', 'Название', 'SKU', 'Остаток', '']">
                    @forelse($lowStockProducts as $product)
                        <tr>
                            <td data-label="">
                                <div class="admin-product-thumb">
                                    @if($product->mainImageUrl())
                                        <img src="{{ $product->mainImageUrl() }}" alt="" width="48" height="48" loading="lazy">
                                    @else
                                        <span class="admin-product-thumb__placeholder"><x-admin-icon name="products" /></span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Название">{{ $product->name }}</td>
                            <td data-label="SKU">{{ $product->sku }}</td>
                            <td data-label="Остаток">
                                <span class="stock-badge stock-badge--low">{{ $product->stock }} шт.</span>
                            </td>
                            <td data-label="">
                                <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn--sm admin-btn--outline">Редактировать</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin-empty-state title="Все товары в норме" text="Нет товаров с остатком меньше 10 шт." />
                            </td>
                        </tr>
                    @endforelse
                </x-admin-table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        window.sabthDashboard = {
            line: {
                labels: @json($chartLabels),
                data: @json($chartData),
            },
            donut: {
                labels: @json($donutLabels),
                data: @json($donutData),
                colors: @json($donutColors),
            },
        };
    </script>
    @vite('resources/js/admin-dashboard.js')
@endpush
