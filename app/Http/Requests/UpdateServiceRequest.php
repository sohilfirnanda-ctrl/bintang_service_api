<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'price' => ['sometimes', 'required', 'integer', 'min:1000'],
            'duration_minutes' => ['sometimes', 'required', 'integer', 'min:5', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}