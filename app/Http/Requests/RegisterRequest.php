<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:customers,email'],
            'phone' => ['required', 'string', 'regex:/^08[0-9]{8,12}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}