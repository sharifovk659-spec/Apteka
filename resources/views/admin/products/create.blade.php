@extends('layouts.admin')

@section('title', 'Добавить товар')
@section('page-title', 'Добавить товар')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <a href="{{ route('admin.products.index') }}" class="admin-breadcrumb__link">Товары</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">Добавить</span>
@endsection

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">Добавить товар</h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">Назад к списку</a>
    </div>

    @if($errors->any())
        <div class="admin-alert admin-alert--error">
            <ul class="admin-alert__list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @include('admin.products.partials.form', ['categories' => $categories, 'brands' => $brands])
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--primary">Сохранить товар</button>
            <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">Отмена</a>
        </div>
    </form>
@endsection
