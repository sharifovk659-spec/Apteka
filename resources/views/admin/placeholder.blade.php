@extends('layouts.admin')

@section('title', $title ?? 'Раздел')
@section('page-title', $title ?? 'Раздел')

@section('content')
    <x-admin-empty-state
        :title="$title ?? 'Раздел в разработке'"
        :text="$text ?? 'Содержимое этого раздела будет добавлено на следующем этапе.'"
    />
@endsection
