<?php

namespace App\Http\Requests\Center;

use Illuminate\Foundation\Http\FormRequest;

class StoreCenterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:centers,name'],
            'code' => ['required', 'string', 'max:50', 'unique:centers,code'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del centro es obligatorio.',
            'name.unique' => 'Ya existe un centro con este nombre.',
            'code.required' => 'El código del centro es obligatorio.',
            'code.unique' => 'Ya existe un centro con este código.',
        ];
    }
}