<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSensorLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint (usually authenticated via API key/token, but matches original logic)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sensor_id' => ['required', 'string'],
            'suhu' => ['required', 'numeric'],
            'kelembapan' => ['required', 'numeric'],
            'cahaya' => ['required', 'numeric'],
            'pompa_status' => ['nullable', 'string', 'in:ON,OFF'],
        ];
    }
}
