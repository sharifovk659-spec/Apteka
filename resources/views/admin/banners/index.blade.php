@extends('layouts.admin')

@section('title', 'Баннеры')
@section('page-title', 'Баннеры')

@section('content')
    @include('admin.partials.alerts')
    <div class="admin-page-head">
        <div><h1 class="admin-page-head__title">Баннеры</h1></div>
        <a href="{{ route('admin.banners.create') }}" class="admin-btn admin-btn--primary">Добавить баннер</a>
    </div>
    <div class="admin-panel admin-panel--table">
        <div class="admin-panel__body admin-panel__body--flush">
            <x-admin-table :headers="['', 'Заголовок', 'Позиция', 'Порядок', 'Статус', 'Действия']">
                @forelse($banners as $banner)
                    <tr>
                        <td><img src="{{ $banner->imageUrl() }}" alt="" width="80" height="48" style="object-fit:cover;border-radius:8px" loading="lazy"></td>
                        <td><strong>{{ $banner->title }}</strong><br><span class="admin-category-name__slug">{{ $banner->subtitle }}</span></td>
                        <td>{{ $banner->position }}</td>
                        <td>{{ $banner->sort_order }}</td>
                        <td><x-status-badge :status="$banner->is_active ? 'active' : 'inactive'" /></td>
                        <td>
                            <div class="admin-actions">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="admin-link">Редактировать</a>
                                <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST" class="admin-inline-form">@csrf @method('PATCH')<button type="submit" class="admin-link">{{ $banner->is_active ? 'Деактивировать' : 'Активировать' }}</button></form>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="admin-inline-form" onsubmit="return confirm('Удалить баннер?')">@csrf @method('DELETE')<button type="submit" class="admin-link admin-link--danger">Удалить</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-admin-empty-state title="Баннеры не найдены" action-label="Добавить баннер" :action-url="route('admin.banners.create')" /></td></tr>
                @endforelse
            </x-admin-table>
        </div>
    </div>
    {{ $banners->links('components.admin-pagination') }}
@endsection
