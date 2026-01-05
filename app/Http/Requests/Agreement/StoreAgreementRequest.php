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
            'contact_phone' => ['required', 'string', 'digits_between:10,20'],
            'start_date' => 'required|date|before:end_date',
            'end_date'   => 'required|date|after:start_date',
            'observations' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            // name
            'name.required' => 'El nombre del acuerdo es obligatorio.',
            'name.string'   => 'El nombre del acuerdo debe ser texto.',
            'name.max'      => 'El nombre del acuerdo no debe exceder los 155 caracteres.',
            'name.unique'   => 'Ya existe un acuerdo con este nombre.',

            // legal_name
            'legal_name.required' => 'La razón social es obligatoria.',
            'legal_name.string'   => 'La razón social debe ser texto.',
            'legal_name.max'      => 'La razón social no debe exceder los 155 caracteres.',

            // tax_id
            'tax_id.required' => 'El RIF es obligatorio.',
            'tax_id.string'   => 'El RIF debe ser texto.',
            'tax_id.max'      => 'El RIF no debe exceder los 20 caracteres.',
            'tax_id.unique'   => 'Este RIF ya se encuentra registrado en el sistema.',

            // contact_person
            'contact_person.required' => 'La persona de contacto es obligatoria.',
            'contact_person.string'   => 'La persona de contacto debe ser texto.',
            'contact_person.max'      => 'La persona de contacto no debe exceder los 255 caracteres.',

            // contact_email
            'contact_email.required' => 'El correo de contacto es obligatorio.',
            'contact_email.string'   => 'El correo de contacto debe ser texto.',
            'contact_email.email'    => 'Debes ingresar una dirección de correo válida.',
            'contact_email.max'      => 'El correo de contacto no debe exceder los 255 caracteres.',

            // contact_phone
            'contact_phone.required' => 'El teléfono de contacto es obligatorio.',
            'contact_phone.integer'  => 'El teléfono solo debe contener números, sin espacios ni guiones.',
            'contact_phone.min'      => 'El teléfono debe tener al menos 10 dígitos.',
            'contact_phone.max'      => 'El teléfono no debe exceder los 20 dígitos.',

            // start_date
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date'     => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.before'   => 'La fecha de inicio debe ser anterior a la fecha de finalización.',

            // end_date
            'end_date.required' => 'La fecha de finalización es obligatoria.',
            'end_date.date'     => 'La fecha de finalización debe ser una fecha válida.',
            'end_date.after'    => 'La fecha de finalización debe ser posterior a la fecha de inicio.',

            // observations
            'observations.string' => 'Las observaciones deben ser texto.',
            'observations.max'    => 'Las observaciones no deben exceder los 1000 caracteres.',
        ];
    }
}
