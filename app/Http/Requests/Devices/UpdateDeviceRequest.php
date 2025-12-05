<?php

namespace App\Http\Requests\Devices;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'center_id' => ['required', 'exists:centers,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'center_id.required' => 'Debes seleccionar un centro.',
            'center_id.exists' => 'El centro seleccionado no es válido.',
            'name.required' => 'El nombre del dispositivo es obligatorio.',
            'type.required' => 'El tipo de dispositivo es obligatorio.',
        ];
    }
}