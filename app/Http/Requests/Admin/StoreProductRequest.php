<?php

namespace App\Http\Requests\Admin;

use App\Services\ProductGalleryService;
use App\Services\ProductImageService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }

        $this->merge([
            'requires_prescription' => $this->boolean('requires_prescription'),
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
            'is_daily_product' => $this->boolean('is_daily_product'),
            'is_bestseller' => $this->boolean('is_bestseller'),
            'status' => $this->input('status', 'published'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'requires_prescription' => ['boolean'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'composition' => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'string'],
            'contraindications' => ['nullable', 'string'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'dosage_form' => ['nullable', 'string', 'max:255'],
            'dosage' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_daily_product' => ['boolean'],
            'is_bestseller' => ['boolean'],
            'gallery_images' => ['nullable', 'array', 'max:'.ProductGalleryService::MAX_IMAGES],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,webp', 'max:'.ProductImageService::MAX_SIZE_KB],
        ];
    }

    public function messages(): array
    {
        return [
            'gallery_images.max' => 'Максимум '.ProductGalleryService::MAX_IMAGES.' изображений на товар.',
            'gallery_images.*.max' => 'Каждое изображение не должно быть больше 5 МБ.',
            'gallery_images.*.image' => 'Загрузите файлы в формате JPG, PNG или WEBP.',
        ];
    }
}
