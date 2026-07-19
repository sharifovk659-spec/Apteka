@php
    $isEdit = $product !== null;
    $images = $isEdit ? $product->galleryImages() : collect();
    $maxImages = \App\Services\ProductGalleryService::MAX_IMAGES;
@endphp

<div class="admin-gallery" id="admin-product-gallery" data-max-images="{{ $maxImages }}">
    <p class="admin-gallery__hint">JPG, PNG, WebP до 5 МБ. Максимум {{ $maxImages }} изображений. Первое главное можно выбрать кнопкой «Сделать главным».</p>

    <div class="admin-gallery__list" id="admin-gallery-list">
        @foreach($images as $image)
            <div
                class="admin-gallery__item"
                data-image-id="{{ $image->id }}"
                draggable="true"
            >
                <img src="{{ $image->imageUrl() }}" alt="{{ $image->alt_text ?? $product->name }}" width="120" height="120" loading="lazy">
                <div class="admin-gallery__item-actions">
                    <label class="admin-gallery__primary">
                        <input
                            type="radio"
                            name="primary_image_id"
                            value="{{ $image->id }}"
                            @checked($image->is_primary)
                        >
                        <span>Главное</span>
                    </label>
                    <label class="admin-gallery__remove">
                        <input type="checkbox" name="delete_image_ids[]" value="{{ $image->id }}">
                        <span>Удалить</span>
                    </label>
                </div>
                <input type="hidden" name="image_order[]" value="{{ $image->id }}">
            </div>
        @endforeach
    </div>

    <div class="admin-gallery__new" id="admin-gallery-new"></div>

    <label class="admin-field admin-field--full">
        <span class="admin-field__label">Добавить изображения</span>
        <input
            type="file"
            id="admin-gallery-upload"
            name="gallery_images[]"
            accept="image/jpeg,image/png,image/webp"
            class="admin-field__input"
            multiple
        >
    </label>
</div>

@push('scripts')
    @vite('resources/js/admin-product-gallery.js')
@endpush
