@extends('layouts.admin')

@section('title', 'Новая категория')
@section('page-title', 'Новая категория')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <a href="{{ route('admin.categories.index') }}" class="admin-breadcrumb__link">Категории</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">Создание</span>
@endsection

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">Новая категория</h1>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--outline">Назад к списку</a>
    </div>

    @include('admin.categories.partials.form-errors')

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @include('admin.categories.partials.form', ['category' => null, 'parentOptions' => $parentOptions, 'iconOptions' => $iconOptions])
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--primary">Создать категорию</button>
            <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--outline">Отмена</a>
        </div>
    </form>
@endsection
