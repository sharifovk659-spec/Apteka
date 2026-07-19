@extends('layouts.app')

@section('title', $selectedCategory?->name ?? 'Каталог')
@section('main-class', 'main--catalog')

@section('content')
    <div class="catalog-page" id="catalog-page">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <a href="{{ route('home') }}" class="breadcrumbs__link">Главная</a>
                <span class="breadcrumbs__sep">/</span>
                @if($selectedCategory)
                    <a href="{{ route('catalog.index') }}" class="breadcrumbs__link">Каталог</a>
                    <span class="breadcrumbs__sep">/</span>
                    <span class="breadcrumbs__current">{{ $selectedCategory->name }}</span>
                @else
                    <span class="breadcrumbs__current">Каталог</span>
                @endif
            </nav>

            <header class="catalog-header">
                <h1 class="catalog-header__title">{{ $selectedCategory?->name ?? 'Каталог' }}</h1>
                <p class="catalog-header__count">Найдено: {{ number_format($products->total(), 0, '.', ' ') }} товаров</p>
            </header>

            <div class="catalog-layout">
                <aside class="catalog-sidebar" aria-label="Фильтры">
                    @include('catalog.partials.filters', [
                        'id' => 'catalog-filters-desktop',
                    ])
                </aside>

                <div class="catalog-content">
                    @include('catalog.partials.toolbar', [
                        'filters' => $filters,
                        'products' => $products,
                    ])

                    @include('catalog.partials.skeleton')

                    @if($products->isEmpty())
                        <div class="catalog-empty">
                            <div class="catalog-empty__icon" aria-hidden="true">
                                <x-icon name="search" />
                            </div>
                            <h2 class="catalog-empty__title">По вашему запросу товары не найдены</h2>
                            <p class="catalog-empty__text">Попробуйте изменить фильтры или сбросить параметры поиска</p>
                            <a href="{{ route('catalog.index') }}" class="btn btn--primary">Сбросить фильтры</a>
                        </div>
                    @else
                        <div class="catalog-grid catalog-grid--{{ $filters['view'] }}" id="catalog-grid">
                            @foreach($products as $product)
                                <x-product-card :product="$product" :lazy="true" :variant="$filters['view']" />
                            @endforeach
                        </div>

                        <div class="catalog-pagination">
                            {{ $products->links('components.catalog-pagination') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="catalog-drawer" id="catalog-drawer" hidden>
            <div class="catalog-drawer__overlay" id="catalog-drawer-overlay"></div>
            <div class="catalog-drawer__panel" role="dialog" aria-modal="true" aria-label="Фильтры">
                <div class="catalog-drawer__head">
                    <h2 class="catalog-drawer__title">Фильтры</h2>
                    <button type="button" class="catalog-drawer__close" id="catalog-drawer-close" aria-label="Закрыть">
                        <x-icon name="close" />
                    </button>
                </div>
                <div class="catalog-drawer__body">
                    @include('catalog.partials.filters', [
                        'id' => 'catalog-filters-mobile',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/catalog-page.js')
@endpush
