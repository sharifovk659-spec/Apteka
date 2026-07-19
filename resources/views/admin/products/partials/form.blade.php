@props(['product' => null, 'categories', 'brands'])

@php
    $isEdit = $product !== null;
@endphp

<div class="admin-form-grid">
    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Основная информация</h2>
        <div class="admin-form-grid__fields">
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Название *</span>
                <input type="text" name="name" value="{{ old('name', $product?->name) }}" class="admin-field__input" required>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Slug *</span>
                <input type="text" name="slug" value="{{ old('slug', $product?->slug) }}" class="admin-field__input" required>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">SKU *</span>
                <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" class="admin-field__input" required>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Штрихкод</span>
                <input type="text" name="barcode" value="{{ old('barcode', $product?->barcode) }}" class="admin-field__input">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Категория *</span>
                <select name="category_id" class="admin-field__input" required>
                    <option value="">Выберите категорию</option>
                    @foreach($categories as $category)
                        <option value="{{ $category['id'] }}" @selected(old('category_id', $product?->category_id) == $category['id'])>
                            {{ $category['label'] }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Бренд</span>
                <select name="brand_id" class="admin-field__input">
                    <option value="">Без бренда</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(old('brand_id', $product?->brand_id) == $brand->id)>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </label>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Цена и склад</h2>
        <div class="admin-form-grid__fields">
            <label class="admin-field">
                <span class="admin-field__label">Цена, смн *</span>
                <input type="number" name="price" value="{{ old('price', $product?->price) }}" class="admin-field__input" min="0" step="0.01" required>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Старая цена, смн</span>
                <input type="number" name="old_price" value="{{ old('old_price', $product?->old_price) }}" class="admin-field__input" min="0" step="0.01">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Статус публикации *</span>
                <select name="status" class="admin-field__input" required>
                    @foreach(['published' => 'Опубликован', 'draft' => 'Черновик', 'archived' => 'Архив'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $product?->status ?? 'published') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Количество *</span>
                <input type="number" name="stock" value="{{ old('stock', $product?->stock ?? 0) }}" class="admin-field__input" min="0" required>
            </label>
            <label class="admin-field admin-field--checkbox">
                <input type="checkbox" name="requires_prescription" value="1" @checked(old('requires_prescription', $product?->requires_prescription))>
                <span>Рецептурный товар</span>
            </label>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Описание</h2>
        <div class="admin-form-grid__fields">
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Краткое описание</span>
                <textarea name="short_description" rows="3" class="admin-field__input">{{ old('short_description', $product?->short_description) }}</textarea>
            </label>
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Полное описание</span>
                <textarea name="description" rows="4" class="admin-field__input">{{ old('description', $product?->description) }}</textarea>
            </label>
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Состав</span>
                <textarea name="composition" rows="3" class="admin-field__input">{{ old('composition', $product?->composition) }}</textarea>
            </label>
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Инструкция</span>
                <textarea name="usage_instructions" rows="3" class="admin-field__input">{{ old('usage_instructions', $product?->usage_instructions) }}</textarea>
            </label>
            <label class="admin-field admin-field--full">
                <span class="admin-field__label">Противопоказания</span>
                <textarea name="contraindications" rows="3" class="admin-field__input">{{ old('contraindications', $product?->contraindications) }}</textarea>
            </label>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Дополнительно</h2>
        <div class="admin-form-grid__fields">
            <label class="admin-field">
                <span class="admin-field__label">Производитель</span>
                <input type="text" name="manufacturer" value="{{ old('manufacturer', $product?->manufacturer) }}" class="admin-field__input">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Страна</span>
                <input type="text" name="country" value="{{ old('country', $product?->country) }}" class="admin-field__input">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Форма выпуска</span>
                <input type="text" name="dosage_form" value="{{ old('dosage_form', $product?->dosage_form) }}" class="admin-field__input">
            </label>
            <label class="admin-field">
                <span class="admin-field__label">Дозировка</span>
                <input type="text" name="dosage" value="{{ old('dosage', $product?->dosage) }}" class="admin-field__input">
            </label>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Отображение</h2>
        <div class="admin-form-grid__checks">
            <label class="admin-field admin-field--checkbox">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))>
                <span>Активен</span>
            </label>
            <label class="admin-field admin-field--checkbox">
                <input type="checkbox" name="is_daily_product" value="1" @checked(old('is_daily_product', $product?->is_daily_product))>
                <span>Товар дня</span>
            </label>
            <label class="admin-field admin-field--checkbox">
                <input type="checkbox" name="is_bestseller" value="1" @checked(old('is_bestseller', $product?->is_bestseller))>
                <span>Хит продаж</span>
            </label>
            <label class="admin-field admin-field--checkbox">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured))>
                <span>Рекомендуемый</span>
            </label>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="admin-form-section__title">Изображения товара</h2>
        @include('admin.products.partials.gallery', ['product' => $product])
    </section>
</div>
