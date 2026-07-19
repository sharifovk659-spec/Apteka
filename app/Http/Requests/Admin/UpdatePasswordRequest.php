<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! Hash::check($this->input('current_password'), $this->user()->password)) {
                $validator->errors()->add('current_password', 'Текущий пароль указан неверно.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'Подтверждение пароля не совпадает.',
        ];
    }
}
