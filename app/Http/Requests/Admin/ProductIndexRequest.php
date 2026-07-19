<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'in_stock' => ['nullable', Rule::in(['yes', 'no'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'search' => $validated['search'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'status' => $validated['status'] ?? null,
            'in_stock' => $validated['in_stock'] ?? null,
        ];
    }
}
