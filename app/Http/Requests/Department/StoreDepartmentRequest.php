<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Respetamos la mayúscula de 'Name' según tu modelo
            'Name' => ['required', 'string', 'max:255', 'unique:departments,Name'],
        ];
    }

    public function messages(): array
    {
        return [
            'Name.required' => 'El nombre del departamento es obligatorio.',
            'Name.unique' => 'Ya existe un departamento con este nombre.',
        ];
    }
}