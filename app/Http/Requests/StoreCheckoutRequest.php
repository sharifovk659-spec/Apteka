<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'delivery_type' => ['required', Rule::in(['courier', 'pickup'])],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'alif', 'dushanbe_city'])],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Укажите ваше имя.',
            'customer_phone.required' => 'Укажите номер телефона.',
            'customer_email.email' => 'Введите корректный email.',
            'address.required' => 'Укажите адрес доставки или пункт самовывоза.',
            'delivery_type.required' => 'Выберите способ получения.',
            'payment_method.required' => 'Выберите способ оплаты.',
        ];
    }
}
