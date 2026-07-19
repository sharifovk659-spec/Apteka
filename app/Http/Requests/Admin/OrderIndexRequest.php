<?php

namespace App\Http\Requests\Admin;

use App\Support\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(OrderStatus::all())],
        ];
    }

    public function filters(): array
    {
        return [
            'search' => trim((string) $this->input('search', '')),
            'status' => $this->input('status'),
        ];
    }
}
