<?php

namespace App\Http\Requests\Center;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCenterRequest extends FormRequest
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
        // Obtenemos la instancia del modelo 'center' de la ruta
        $center = $this->route('center');

        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                // Ignora el ID actual para permitir guardar sin cambiar el nombre
                Rule::unique('centers', 'name')->ignore($center->id)
            ],
            'code' => [
                'required', 
                'string', 
                'max:50', 
                // Ignora el ID actual para permitir guardar sin cambiar el código
                Rule::unique('centers', 'code')->ignore($center->id)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del centro es obligatorio.',
            'name.unique' => 'Ya existe otro centro con este nombre.',
            'code.required' => 'El código del centro es obligatorio.',
            'code.unique' => 'Ya existe otro centro con este código.',
        ];
    }
}