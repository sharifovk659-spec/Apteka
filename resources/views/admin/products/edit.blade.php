@extends('layouts.admin')

@section('title', 'Редактировать товар')
@section('page-title', 'Редактировать товар')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <a href="{{ route('admin.products.index') }}" class="admin-breadcrumb__link">Товары</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">{{ $product->name }}</span>
@endsection

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">Редактировать товар</h1>
            <p class="admin-page-head__meta">SKU: {{ $product->sku }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">Назад к списку</a>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="admin-alert admin-alert--error">
            <ul class="admin-alert__list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @method('PUT')
        @include('admin.products.partials.form', ['product' => $product, 'categories' => $categories, 'brands' => $brands])
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--primary">Сохранить изменения</button>
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">Отмена</a>
        </div>
    </form>
@endsection
