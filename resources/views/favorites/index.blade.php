@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
    <div class="container">
        <section class="page-section">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <a href="{{ route('home') }}" class="breadcrumbs__link">Главная</a>
                <span class="breadcrumbs__sep">/</span>
                <span class="breadcrumbs__current">Избранное</span>
            </nav>

            <h1 class="page-title">Избранное</h1>

            @if($products->isEmpty())
                <div class="catalog-empty">
                    <div class="catalog-empty__icon" aria-hidden="true">
                        <x-icon name="heart" />
                    </div>
                    <h2 class="catalog-empty__title">В избранном пока нет товаров</h2>
                    <p class="catalog-empty__text">Нажмите на сердечко на карточке товара, чтобы сохранить его здесь</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn--primary">Перейти в каталог</a>
                </div>
            @else
                <p class="page-text">Сохранено товаров: {{ $products->count() }}</p>
                <div class="catalog-grid catalog-grid--grid">
                    @foreach($products as $product)
                        <x-product-card :product="$product" :lazy="true" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
