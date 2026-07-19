@extends('layouts.admin')

@section('title', 'Новый бренд')
@section('page-title', 'Новый бренд')

@section('content')
    <div class="admin-page-head"><h1 class="admin-page-head__title">Новый бренд</h1></div>
    @include('admin.partials.alerts')
    <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="admin-form">
        @csrf
        @include('admin.brands.partials.form')
        <div class="admin-form-actions"><button type="submit" class="admin-btn admin-btn--primary">Создать</button></div>
    </form>
@endsection
