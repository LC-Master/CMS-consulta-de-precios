<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgreementRequest extends FormRequest
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
        // Obtenemos el objeto Agreement de la ruta
        $agreement = $this->route('agreement');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Ignoramos el ID del registro actual para permitir guardar el mismo nombre si no cambia
                Rule::unique('agreements', 'name')->ignore($agreement->id),
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del acuerdo es obligatorio.',
            'name.unique' => 'Ya existe otro acuerdo con este nombre.',
        ];
    }
}
