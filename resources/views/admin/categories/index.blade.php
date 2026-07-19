@extends('layouts.admin')

@section('title', 'Категории')
@section('page-title', 'Категории')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">Категории</span>
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
            <h1 class="admin-page-head__title">Категории</h1>
            <p class="admin-page-head__meta">{{ $categories->count() }} категорий в дереве</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="admin-btn admin-btn--primary">Добавить категорию</a>
    </div>

    <div class="admin-panel admin-panel--table">
        @php($categoryDeleteService = app(\App\Services\CategoryDeleteService::class))
        <div class="admin-panel__body admin-panel__body--flush">
            <x-admin-table
                class="admin-table--categories"
                :headers="['Название', 'Родитель', 'Уровень', 'Товаров', 'Статус', 'Порядок', 'Действия']"
            >
                @forelse($categories as $category)
                    <tr>
                        <td data-label="Название">
                            <div class="admin-category-name" style="padding-left: {{ ($category->tree_depth ?? 0) * 16 }}px">
                                @if($category->image)
                                    <img src="{{ $category->imageUrl() }}" alt="" class="admin-category-name__thumb" width="32" height="32" loading="lazy">
                                @endif
                                <div>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="admin-product-cell__title">{{ $category->name }}</a>
                                    <span class="admin-category-name__slug">{{ $category->slug }}</span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Родитель">{{ $category->parent?->name ?? '—' }}</td>
                        <td data-label="Уровень">{{ ($category->tree_depth ?? 0) + 1 }}</td>
                        <td data-label="Товаров">{{ $category->products_count }}</td>
                        <td data-label="Статус">
                            <x-status-badge :status="$category->is_active ? 'active' : 'inactive'" :label="$category->is_active ? 'Активна' : 'Неактивна'" />
                        </td>
                        <td data-label="Порядок">{{ $category->sort_order }}</td>
                        <td data-label="Действия">
                            <div class="admin-actions">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="admin-link">Редактировать</a>
                                <form action="{{ route('admin.categories.toggle', $category) }}" method="POST" class="admin-inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="admin-link">{{ $category->is_active ? 'Деактивировать' : 'Активировать' }}</button>
                                </form>
                                <form
                                    id="delete-category-form-{{ $category->id }}"
                                    action="{{ route('admin.categories.destroy', $category) }}"
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
                                    data-delete-form="delete-category-form-{{ $category->id }}"
                                    data-delete-name="{{ $category->name }}"
                                    @disabled(! $categoryDeleteService->canDelete($category))
                                    title="{{ $categoryDeleteService->deleteReason($category) ?? 'Удалить категорию' }}"
                                >
                                    Удалить
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-admin-empty-state
                                title="Категории не найдены"
                                text="Добавьте первую категорию для каталога."
                                action-label="Добавить категорию"
                                :action-url="route('admin.categories.create')"
                            />
                        </td>
                    </tr>
                @endforelse
            </x-admin-table>
        </div>
    </div>

    @include('admin.categories.partials.delete-modal')
@endsection
