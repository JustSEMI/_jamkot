<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPermissionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'can_panel' => ['nullable', 'boolean'],
            'can_analisis' => ['nullable', 'boolean'],
            'can_schedule' => ['nullable', 'boolean'],
            'can_view3d' => ['nullable', 'boolean'],
            'can_settings' => ['nullable', 'boolean'],
            'can_admin' => ['nullable', 'boolean'],
        ];
    }
}
