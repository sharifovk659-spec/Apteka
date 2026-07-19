<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('q') && ! $this->filled('search')) {
            $this->merge(['search' => $this->input('q')]);
        }

        foreach (['in_stock', 'discount'] as $booleanField) {
            if ($this->has($booleanField)) {
                $this->merge([
                    $booleanField => filter_var($this->input($booleanField), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'dosage_form' => ['nullable', 'string', 'max:255'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'in_stock' => ['nullable', 'boolean'],
            'discount' => ['nullable', 'boolean'],
            'prescription' => ['nullable', Rule::in(['0', '1', 'yes', 'no', 'true', 'false'])],
            'sort' => ['nullable', Rule::in(['popular', 'price_asc', 'price_desc', 'newest'])],
            'search' => ['nullable', 'string', 'max:255'],
            'view' => ['nullable', Rule::in(['grid', 'list'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'category' => $validated['category'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'manufacturer' => $validated['manufacturer'] ?? null,
            'dosage_form' => $validated['dosage_form'] ?? null,
            'min_price' => isset($validated['min_price']) ? (float) $validated['min_price'] : null,
            'max_price' => isset($validated['max_price']) ? (float) $validated['max_price'] : null,
            'in_stock' => $validated['in_stock'] ?? null,
            'discount' => $validated['discount'] ?? null,
            'prescription' => $this->prescriptionFilter(),
            'sort' => $validated['sort'] ?? 'popular',
            'search' => $validated['search'] ?? null,
            'view' => $validated['view'] ?? 'grid',
        ];
    }

    public function prescriptionFilter(): ?bool
    {
        if (! $this->filled('prescription')) {
            return null;
        }

        return in_array($this->input('prescription'), ['1', 'yes', 'true'], true);
    }

    public function queryParams(): array
    {
        return array_filter(
            $this->only([
                'category', 'brand', 'manufacturer', 'dosage_form',
                'min_price', 'max_price', 'in_stock', 'discount',
                'prescription', 'sort', 'search', 'view',
            ]),
            fn ($value) => $value !== null && $value !== '',
        );
    }
}
