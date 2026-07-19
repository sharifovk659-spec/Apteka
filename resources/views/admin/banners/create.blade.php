@extends('layouts.admin')

@section('title', 'Новый баннер')
@section('page-title', 'Новый баннер')

@section('content')
    @include('admin.partials.alerts')
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @include('admin.banners.partials.form', ['positions' => $positions])
        <div class="admin-form-actions"><button type="submit" class="admin-btn admin-btn--primary">Создать</button></div>
    </form>
@endsection
