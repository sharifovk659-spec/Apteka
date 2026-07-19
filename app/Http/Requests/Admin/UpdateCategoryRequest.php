<?php

namespace App\Http\Requests\Admin;

use App\Services\CategoryImageService;
use App\Services\CategoryTreeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'is_active' => $this->boolean('is_active'),
            'parent_id' => $this->input('parent_id') ?: null,
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $categoryId = $category?->id;
        $treeService = app(CategoryTreeService::class);

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($treeService, $categoryId) {
                    if (! $treeService->isValidParent($value ? (int) $value : null, $categoryId)) {
                        $fail('Нельзя назначить категорию родителем самой себя или своей дочерней категории.');
                    }
                },
            ],
            'icon' => ['nullable', 'string', 'max:50', Rule::in(['pill', 'vitamin', 'hygiene', 'medical', 'baby', 'cosmetic', 'first-aid', 'eye'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'remove_image' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:'.CategoryImageService::MAX_SIZE_KB],
        ];
    }
}
