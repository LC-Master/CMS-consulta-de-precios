<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <--- Importante

class UpdateAgreementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'nullable|integer',

            'name' => [
                'required',
                'string',
                'max:155',
                Rule::unique('agreements', 'name')
                    ->ignore($this->route('agreement')->id ?? $this->route('agreement'))
                    ->withoutTrashed(),
            ],
            
            'legal_name' => ['required', 'string', 'max:155'],
            
            'tax_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('agreements', 'tax_id')
                    ->ignore($this->route('agreement')->id ?? $this->route('agreement'))
                    ->withoutTrashed(),
            ],

            'contact_person' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'contact_phone' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
            'observations' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.integer' => 'El identificador del proveedor debe ser un número entero.',
            
            'name.required' => 'El nombre del acuerdo es obligatorio.',
            'name.string' => 'El nombre del acuerdo debe ser texto.',
            'name.max' => 'El nombre del acuerdo no debe exceder los 155 caracteres.',
            'name.unique' => 'Ya existe un acuerdo ACTIVO con este nombre.',

            'is_active.required' => 'El estado es obligatorio.',
            'is_active.boolean' => 'El estado debe ser verdadero o falso.',

            'legal_name.required' => 'La razón social es obligatoria.',
            'legal_name.string' => 'La razón social debe ser texto.',
            'legal_name.max' => 'La razón social no debe exceder los 155 caracteres.',

            'tax_id.required' => 'El RIF es obligatorio.',
            'tax_id.string' => 'El RIF debe ser texto.',
            'tax_id.max' => 'El RIF no debe exceder los 20 caracteres.',
            'tax_id.unique' => 'Este RIF ya se encuentra registrado y ACTIVO en el sistema.',

            'contact_person.required' => 'La persona de contacto es obligatoria.',
            'contact_person.string' => 'La persona de contacto debe ser texto.',
            'contact_person.max' => 'La persona de contacto no debe exceder los 255 caracteres.',

            'contact_email.required' => 'El correo de contacto es obligatorio.',
            'contact_email.string' => 'El correo de contacto debe ser texto.',
            'contact_email.email' => 'Debes ingresar una dirección de correo válida.',
            'contact_email.max' => 'El correo de contacto no debe exceder los 255 caracteres.',

            'contact_phone.required' => 'El teléfono de contacto es obligatorio.',
            'contact_phone.integer' => 'El teléfono solo debe contener números, sin espacios ni guiones.',
            'contact_phone.min' => 'El teléfono debe tener al menos 10 dígitos.',
            'contact_phone.max' => 'El teléfono no debe exceder los 20 dígitos.',

            'observations.required' => 'Los detalles del acuerdo son obligatorios',
            'observations.string' => 'Los detalles del acuerdo deben ser texto.',
            'observations.max'    => 'Los detalles del acuerdo no deben exceder los 1000 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre del acuerdo',
            'legal_name' => 'razón social',
            'tax_id' => 'RIF',
            'contact_person' => 'persona de contacto',
            'contact_email' => 'correo de contacto',
            'contact_phone' => 'teléfono de contacto',
            'is_active' => 'estado',
            'observations' => 'Detalles del acuerdo',
        ];
    }
}