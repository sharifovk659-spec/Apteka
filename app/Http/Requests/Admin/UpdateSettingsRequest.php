<?php

namespace App\Http\Requests\Admin;

use App\Services\AdminImageService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store.name' => ['required', 'string', 'max:255'],
            'store.phone' => ['required', 'string', 'max:50'],
            'store.email' => ['nullable', 'email', 'max:255'],
            'store.address' => ['nullable', 'string', 'max:500'],
            'store.tagline' => ['nullable', 'string', 'max:255'],
            'delivery.default_price' => ['required', 'numeric', 'min:0'],
            'order.min_amount' => ['required', 'numeric', 'min:0'],
            'social.telegram' => ['nullable', 'string', 'max:255'],
            'social.instagram' => ['nullable', 'string', 'max:255'],
            'social.facebook' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:'.AdminImageService::MAX_SIZE_KB],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }
}
