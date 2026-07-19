@extends('layouts.admin')

@section('title', 'Редактировать категорию')
@section('page-title', 'Редактировать категорию')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="admin-breadcrumb__link">Главная</a>
    <span class="admin-breadcrumb__sep">/</span>
    <a href="{{ route('admin.categories.index') }}" class="admin-breadcrumb__link">Категории</a>
    <span class="admin-breadcrumb__sep">/</span>
    <span class="admin-breadcrumb__current">{{ $category->name }}</span>
@endsection

@section('content')
    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">Редактировать категорию</h1>
            <p class="admin-page-head__meta">{{ $category->full_path }}</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--outline">Назад к списку</a>
    </div>

    @if(session('success'))
        <div class="admin-alert admin-alert--success">{{ session('success') }}</div>
    @endif

    @include('admin.categories.partials.form-errors')

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @method('PUT')
        @include('admin.categories.partials.form', ['category' => $category, 'parentOptions' => $parentOptions, 'iconOptions' => $iconOptions])
        <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn--primary">Сохранить изменения</button>
            <a href="{{ route('admin.categories.index') }}" class="admin-btn admin-btn--outline">Отмена</a>
        </div>
    </form>
@endsection
