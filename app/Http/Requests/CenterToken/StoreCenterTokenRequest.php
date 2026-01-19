<?php

namespace App\Http\Requests\CenterToken;

use Illuminate\Foundation\Http\FormRequest;

class StoreCenterTokenRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'center_id' => [
                'required',
                'string',
                'uuid',
                'exists:centers,id',
            ],
        ];
    }

    /**
     * Custom validation messages (Spanish).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',

            'center_id.required' => 'El centro es obligatorio.',
            'center_id.string' => 'El identificador del centro debe ser una cadena.',
            'center_id.uuid' => 'El identificador del centro debe ser un UUID válido.',
            'center_id.exists' => 'El centro seleccionado no existe.',
            'center_id.unique' => 'Ya existe un token asociado con este centro.',
        ];
    }

    /**
     * Attribute names for error messages (Spanish).
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'center_id' => 'centro',
        ];
    }
}
