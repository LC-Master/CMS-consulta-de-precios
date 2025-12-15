<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAgreementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(Auth::id()){
            return true; 
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:155|unique:agreements,name',
            'legal_name' => 'required|string|max:155',
            'tax_id' => 'required|string|max:20|unique:agreements,tax_id',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|string|email:rfc,dns|max:255',
            'contact_phone' => ['required', 'integer', 'min:10', 'max:20'],
            'start_date' => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:start_date',
            'observations' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del acuerdo es obligatorio.',
            'name.unique' => 'Ya existe un acuerdo con este nombre.',
            'contact_phone.integer' => 'El teléfono solo debe contener números, sin espacios ni guiones.',
            'contact_email.email' => 'Debes ingresar una dirección de correo válida.',
            'tax_id.unique' => 'Este RIF ya se encuentra registrado en el sistema.',
        ];
    }
}
