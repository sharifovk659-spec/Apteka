@extends('layouts.admin')

@section('title', 'Редактировать бренд')
@section('page-title', 'Редактировать бренд')

@section('content')
    <div class="admin-page-head"><h1 class="admin-page-head__title">{{ $brand->name }}</h1></div>
    @include('admin.partials.alerts')
    <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf @method('PUT')
        @include('admin.brands.partials.form', ['brand' => $brand])
        <div class="admin-form-actions"><button type="submit" class="admin-btn admin-btn--primary">Сохранить</button></div>
    </form>
@endsection
