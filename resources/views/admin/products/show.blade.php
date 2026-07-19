@extends('layouts.admin')

@section('title', $product->name)
@section('page-title', 'Просмотр товара')

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">{{ $product->name }}</h1>
            <p class="admin-page-head__meta">SKU: {{ $product->sku }}</p>
        </div>
        <div class="admin-form-actions">
            <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn--primary">Редактировать</a>
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">К списку</a>
        </div>
    </div>

    <div class="admin-form-grid">
        <section class="admin-form-section">
            <h2 class="admin-form-section__title">Основное</h2>
            <p>Категория: {{ $product->category?->full_path ?? '—' }}</p>
            <p>Бренд: {{ $product->brand?->name ?? '—' }}</p>
            <p>Цена: {{ number_format($product->price, 0, '.', ' ') }} смн @if($product->old_price) / {{ number_format($product->old_price, 0, '.', ' ') }} смн @endif</p>
            <p>Остаток: {{ $product->stock }} шт.</p>
            <p>Статус: <x-status-badge :status="$product->is_active ? 'active' : 'inactive'" /> {{ $product->status }}</p>
        </section>
        <section class="admin-form-section">
            <h2 class="admin-form-section__title">Изображения</h2>
            <div class="admin-gallery__list">
                @forelse($product->galleryImages() as $image)
                    <div class="admin-gallery__item">
                        <img src="{{ $image->imageUrl() }}" alt="" width="120" height="120" loading="lazy">
                        @if($image->is_primary)<span class="admin-gallery__badge">Главное</span>@endif
                    </div>
                @empty
                    <img src="{{ $product->mainImageUrl() }}" alt="" class="admin-form-preview" width="120" height="120">
                @endforelse
            </div>
        </section>
        @if($product->short_description)
            <section class="admin-form-section admin-field--full"><h2 class="admin-form-section__title">Описание</h2><p>{{ $product->short_description }}</p></section>
        @endif
    </div>
@endsection
