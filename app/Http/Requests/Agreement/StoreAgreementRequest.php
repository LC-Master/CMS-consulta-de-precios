<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgreementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cambia esto a false si necesitas lógica de permisos (ej. solo admins)
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
            'name' => ['required', 'string', 'max:255', 'unique:agreements,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del acuerdo es obligatorio.',
            'name.unique' => 'Ya existe un acuerdo con este nombre.',
        ];
    }
}
