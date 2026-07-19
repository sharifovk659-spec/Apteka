@props(['category' => null, 'parentOptions', 'iconOptions'])

@php
    $isEdit = $category !== null;
@endphp

<div class="admin-form-grid">
    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Основная информация</h2>
        <div class="admin-form-grid__fields">
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Название *</span>
                <input type="text" name="name" value="{{ old('name', $category?->name) }}" class="admin-field__input" required>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Slug *</span>
                <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" class="admin-field__input" required>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Родительская категория</span>
                <select name="parent_id" class="admin-field__input">
                    <option value="">Корневая категория</option>
                    @foreach($parentOptions as $option)
                        <option value="{{ $option['id'] }}" @selected(old('parent_id', $category?->parent_id) == $option['id'])>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Порядок сортировки</span>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="admin-field__input" min="0">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Иконка</span>
                <select name="icon" class="admin-field__input">
                    <option value="">Без иконки</option>
                    @foreach($iconOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('icon', $category?->icon) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="admin-field admin-field--checkbox">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))>
                <span>Активна</span>
            </label>
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Описание</span>
                <textarea name="description" rows="4" class="admin-field__input">{{ old('description', $category?->description) }}</textarea>
            </label>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Изображение категории</h2>
        <div class="admin-form-grid__fields">
            @if($isEdit && $category->image)
                <div class="admin-field admin-field--full">
                    <img src="{{ $category->imageUrl() }}" alt="" class="admin-form-preview" width="120" height="120">
                    <label class="admin-field admin-field--checkbox">
                        <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                        <span>Удалить текущее изображение</span>
                    </label>
                </div>
            @endif
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Загрузить изображение (JPG, PNG, WEBP, до 5 МБ)</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="admin-field__input">
            </label>
        </div>
    </section>
</div>
