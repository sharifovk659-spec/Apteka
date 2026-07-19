@extends('layouts.admin')

@section('title', 'Товары')
@section('page-title', 'Товары')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">Товары</span>
@endsection

@section('content')
    @if(session('success'))
        <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert--error">{{ session('error') }}</div>
    @endif

    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">Товары</h1>
            <p class="admin-page-head__meta">{{ number_format($totalProducts, 0, '.', ' ') }} товаров в каталоге</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn--primary">Добавить товар</a>
    </div>

    <form action="{{ route('admin.products.index') }}" method="GET" class="admin-filters">
        <input
            type="search"
            name="search"
            value="{{ $filters['search'] }}"
            placeholder="Поиск по названию, SKU, штрихкоду"
            class="admin-field__input admin-filters__search"
        >
        <select name="category_id" class="admin-field__input">
            <option value="">Все категории</option>
            @foreach($categories as $category)
                <option value="{{ $category['id'] }}" @selected($filters['category_id'] == $category['id'])>{{ $category['label'] }}</option>
            @endforeach
        </select>
        <select name="brand_id" class="admin-field__input">
            <option value="">Все бренды</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" @selected($filters['brand_id'] == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        <select name="status" class="admin-field__input">
            <option value="">Любой статус</option>
            <option value="active" @selected($filters['status'] === 'active')>Активные</option>
            <option value="inactive" @selected($filters['status'] === 'inactive')>Неактивные</option>
        </select>
        <select name="in_stock" class="admin-field__input">
            <option value="">Любое наличие</option>
            <option value="yes" @selected($filters['in_stock'] === 'yes')>В наличии</option>
            <option value="no" @selected($filters['in_stock'] === 'no')>Нет в наличии</option>
        </select>
        <button type="submit" class="admin-btn admin-btn--primary">Применить</button>
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">Сбросить</a>
    </form>

    <div class="admin-panel admin-panel--table">
        @php($productDeleteService = app(\App\Services\ProductDeleteService::class))
        <div class="admin-panel__body admin-panel__body--flush">
            <x-admin-table
                class="admin-table--products"
                :headers="['', 'Название', 'SKU', 'Категория', 'Бренд', 'Цена', 'Остаток', 'Статус', 'Действия']"
            >
                @forelse($products as $product)
                    <tr>
                        <td data-label="">
                            <input type="checkbox" aria-label="Выбрать {{ $product->name }}">
                        </td>
                        <td data-label="Товар">
                            <div class="admin-product-cell">
                                <div class="admin-product-thumb">
                                    @if($product->mainImageUrl())
                                        <img src="{{ $product->mainImageUrl() }}" alt="" width="48" height="48" loading="lazy">
                                    @else
                                        <span class="admin-product-thumb__placeholder"><x-admin-icon name="products" /></span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('admin.products.show', $product) }}" class="admin-product-cell__title">{{ $product->name }}</a>
                                </div>
                            </div>
                        </td>
                        <td data-label="SKU">{{ $product->sku }}</td>
                        <td data-label="Категория">{{ $product->category?->name ?? '—' }}</td>
                        <td data-label="Бренд">{{ $product->brand?->name ?? '—' }}</td>
                        <td data-label="Цена">{{ number_format($product->price, 0, '.', ' ') }} смн</td>
                        <td data-label="Остаток">{{ $product->stock }} шт.</td>
                        <td data-label="Статус">
                            <x-status-badge :status="$product->is_active ? 'active' : 'inactive'" :label="$product->is_active ? 'Активен' : 'Неактивен'" />
                        </td>
                        <td data-label="Действия">
                            <div class="admin-actions">
                                <a href="{{ route('admin.products.show', $product) }}" class="admin-link">Просмотр</a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="admin-link">Редактировать</a>
                                <form action="{{ route('admin.products.toggle', $product) }}" method="POST" class="admin-inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="admin-link">{{ $product->is_active ? 'Деактивировать' : 'Активировать' }}</button>
                                </form>
                                <form
                                    id="delete-form-{{ $product->id }}"
                                    action="{{ route('admin.products.destroy', $product) }}"
                                    method="POST"
                                    class="admin-hidden-form"
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button
                                    type="button"
                                    class="admin-link admin-link--danger"
                                    data-delete-open
                                    data-delete-form="delete-form-{{ $product->id }}"
                                    data-delete-name="{{ $product->name }}"
                                    @disabled(! $productDeleteService->canDelete($product))
                                    title="{{ $productDeleteService->deleteReason($product) ?? 'Удалить товар' }}"
                                >
                                    Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <x-admin-empty-state
                                title="Товары не найдены"
                                text="Измените фильтры или добавьте новый товар."
                                action-label="Добавить товар"
                                :action-url="route('admin.products.create')"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-admin-table>
        </div>
    </div>

    @if($products->hasPages())
        {{ $products->links('components.admin-pagination') }}
    @endif

    @include('admin.products.partials.delete-modal')
@endsection
