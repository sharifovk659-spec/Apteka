@props(['brand' => null])

<div class="admin-form-grid">
    <section class="admin-form-section">
        <div class="admin-form-grid__fields">
            <label class="admin-field admin-field--full"><span class="admin-field__label">Название *</span><input type="text" name="name" value="{{ old('name', $brand?->name) }}" class="admin-field__input" required></label>
            <label class="admin-field"><span class="admin-field__label">Slug *</span><input type="text" name="slug" value="{{ old('slug', $brand?->slug) }}" class="admin-field__input" required></label>
            <label class="admin-field admin-field--checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand?->is_active ?? true))><span>Активен</span></label>
            @if($brand?->logoUrl())
                <div class="admin-field admin-field--full"><img src="{{ $brand->logoUrl() }}" alt="" class="admin-form-preview" width="120" height="120"><label class="admin-field admin-field--checkbox"><input type="checkbox" name="remove_logo" value="1"><span>Удалить логотип</span></label></div>
            @endif
            <label class="admin-field admin-field--full"><span class="admin-field__label">Логотип (JPG, PNG, WEBP, до 5 МБ)</span><input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="admin-field__input"></label>
        </div>
    </section>
</div>
