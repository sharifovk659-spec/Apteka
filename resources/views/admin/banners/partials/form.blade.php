@props(['banner' => null, 'positions'])

<div class="admin-form-grid">
    <section class="admin-form-section">
        <div class="admin-form-grid__fields">
            <label class="admin-field admin-field--full"><span class="admin-field__label">Заголовок *</span><input type="text" name="title" value="{{ old('title', $banner?->title) }}" class="admin-field__input" required></label>
            <label class="admin-field admin-field--full"><span class="admin-field__label">Подзаголовок</span><input type="text" name="subtitle" value="{{ old('subtitle', $banner?->subtitle) }}" class="admin-field__input"></label>
            <label class="admin-field"><span class="admin-field__label">Текст кнопки</span><input type="text" name="button_text" value="{{ old('button_text', $banner?->button_text) }}" class="admin-field__input"></label>
            <label class="admin-field"><span class="admin-field__label">Ссылка кнопки</span><input type="text" name="button_url" value="{{ old('button_url', $banner?->button_url) }}" class="admin-field__input"></label>
            <label class="admin-field"><span class="admin-field__label">Позиция *</span><select name="position" class="admin-field__input" required>@foreach($positions as $value => $label)<option value="{{ $value }}" @selected(old('position', $banner?->position) === $value)>{{ $label }}</option>@endforeach</select></label>
            <label class="admin-field"><span class="admin-field__label">Порядок</span><input type="number" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" class="admin-field__input" min="0"></label>
            <label class="admin-field admin-field--checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner?->is_active ?? true))><span>Активен</span></label>
            @if($banner?->image)
                <div class="admin-field admin-field--full"><img src="{{ $banner->imageUrl() }}" alt="" class="admin-form-preview" width="240" height="120" style="width:240px;height:120px"></div>
            @endif
            <label class="admin-field admin-field--full"><span class="admin-field__label">Изображение {{ $banner ? '' : '*' }} (JPG, PNG, WEBP)</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="admin-field__input" @if(! $banner) required @endif></label>
        </div>
    </section>
</div>
