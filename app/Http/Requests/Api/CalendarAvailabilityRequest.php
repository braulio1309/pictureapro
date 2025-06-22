<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CalendarAvailabilityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filters.from_now' => ['sometimes', 'nullable', 'boolean'],
            'filters.start_date' => ['sometimes', 'nullable', 'date'],
            'filters.end_date' => ['sometimes', 'nullable', 'date'],
            'filters.pack' => ['sometimes', 'nullable', 'integer', 'exists:\App\Models\ServicePack,id'],
        ];
    }
}
