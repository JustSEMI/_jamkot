<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->canAccess('schedule');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jadwal_pagi_mulai' => ['required', 'string'],
            'jadwal_pagi_selesai' => ['required', 'string'],
            'jadwal_siang_mulai' => ['required', 'string'],
            'jadwal_siang_selesai' => ['required', 'string'],
            'jadwal_sore_mulai' => ['required', 'string'],
            'jadwal_sore_selesai' => ['required', 'string'],
            'batas_kelembapan' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
