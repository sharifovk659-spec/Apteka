@extends('layouts.admin')

@section('title', 'Бренды')
@section('page-title', 'Бренды')

@section('content')
    @include('admin.partials.alerts')

    <div class="admin-page-head">
        <div>
            <h1 class="admin-page-head__title">Бренды</h1>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="admin-btn admin-btn--primary">Добавить бренд</a>
    </div>

    <div class="admin-panel admin-panel--table">
        @php($deleteService = app(\App\Services\BrandDeleteService::class))
        <div class="admin-panel__body admin-panel__body--flush">
            <x-admin-table :headers="['', 'Название', 'Slug', 'Товаров', 'Статус', 'Действия']">
                @forelse($brands as $brand)
                    <tr>
                        <td>
                            @if($brand->logoUrl())
                                <img src="{{ $brand->logoUrl() }}" alt="" width="40" height="40" class="admin-form-preview" loading="lazy">
                            @endif
                        </td>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->slug }}</td>
                        <td>{{ $brand->products_count }}</td>
                        <td><x-status-badge :status="$brand->is_active ? 'active' : 'inactive'" /></td>
                        <td>
                            <div class="admin-actions">
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="admin-link">Редактировать</a>
                                <form action="{{ route('admin.brands.toggle', $brand) }}" method="POST" class="admin-inline-form">@csrf @method('PATCH')<button type="submit" class="admin-link">{{ $brand->is_active ? 'Деактивировать' : 'Активировать' }}</button></form>
                                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="admin-inline-form" onsubmit="return confirm('Удалить бренд?')">@csrf @method('DELETE')<button type="submit" class="admin-link admin-link--danger" @disabled(! $deleteService->canDelete($brand)) title="{{ $deleteService->deleteReason($brand) ?? 'Удалить' }}">Удалить</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-admin-empty-state title="Бренды не найдены" text="Добавьте первый бренд." action-label="Добавить бренд" :action-url="route('admin.brands.create')" /></td></tr>
                @endforelse
            </x-admin-table>
        </div>
    </div>
    {{ $brands->links('components.admin-pagination') }}
@endsection
