<?php

namespace App\Http\Requests\Admin;

use App\Services\CategoryImageService;
use App\Services\CategoryTreeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
        ]);
    }

    public function rules(): array
    {
        $treeService = app(CategoryTreeService::class);

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($treeService) {
                    if (! $treeService->isValidParent($value ? (int) $value : null)) {
                        $fail('Нельзя выбрать эту родительскую категорию.');
                    }
                },
            ],
            'icon' => ['nullable', 'string', 'max:50', Rule::in(['pill', 'vitamin', 'hygiene', 'medical', 'baby', 'cosmetic', 'first-aid', 'eye'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:'.CategoryImageService::MAX_SIZE_KB],
        ];
    }
}
