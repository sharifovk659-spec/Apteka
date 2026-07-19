<?php

namespace App\Http\Requests\Admin;

use App\Services\AdminImageService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
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
            'remove_logo' => $this->boolean('remove_logo'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('brands', 'slug')->ignore($this->route('brand'))],
            'is_active' => ['boolean'],
            'remove_logo' => ['boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:'.AdminImageService::MAX_SIZE_KB],
        ];
    }
}
