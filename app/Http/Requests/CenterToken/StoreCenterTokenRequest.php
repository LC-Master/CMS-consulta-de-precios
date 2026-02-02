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
            'store_id' => [
                'required',
                'string',
                'exists:Store,ID',
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

            'store_id.required' => 'El centro es obligatorio.',
            'store_id.string' => 'El identificador del centro debe ser una cadena.',
            'store_id.uuid' => 'El identificador del centro debe ser un UUID válido.',
            'store_id.exists' => 'El centro seleccionado no existe.',
            'store_id.unique' => 'Ya existe un token asociado con este centro.',
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
            'store_id' => 'centro',
        ];
    }
}
