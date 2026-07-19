@extends('layouts.admin')

@section('title', 'Редактировать баннер')
@section('page-title', 'Редактировать баннер')

@section('content')
    @include('admin.partials.alerts')
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf @method('PUT')
        @include('admin.banners.partials.form', ['banner' => $banner, 'positions' => $positions])
        <div class="admin-form-actions"><button type="submit" class="admin-btn admin-btn--primary">Сохранить</button></div>
    </form>
@endsection
