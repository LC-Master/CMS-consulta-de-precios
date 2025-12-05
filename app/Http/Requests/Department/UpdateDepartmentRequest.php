<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
        // Obtenemos el departamento de la ruta
        $department = $this->route('department');

        return [
            'Name' => [
                'required',
                'string',
                'max:255',
                // Ignoramos el ID del departamento actual
                Rule::unique('departments', 'Name')->ignore($department->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'Name.required' => 'El nombre del departamento es obligatorio.',
            'Name.unique' => 'Ya existe otro departamento con este nombre.',
        ];
    }
}