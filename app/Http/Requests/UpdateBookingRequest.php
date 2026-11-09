<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motor_type' => ['sometimes', 'required', 'string', 'max:100'],
            'plate_number' => ['sometimes', 'required', 'string', 'max:15'],
            'booking_date' => ['sometimes', 'required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'booking_time' => ['sometimes', 'required', 'date_format:H:i'],
            'complaint' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}