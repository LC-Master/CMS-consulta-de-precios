<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportCampaignListRequest extends FormRequest
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
            'start_at' => ['required', 'date', 'before_or_equal:end_at'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'department_id' => ['nullable', 'uuid', 'exists:departments,id'],
            'agreement_id' => ['nullable', 'uuid', 'exists:agreements,id'],
            'status_id' => ['nullable', 'uuid', 'exists:statuses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
            'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
            'uuid' => 'El campo :attribute debe ser un UUID válido.',
            'exists' => 'El :attribute seleccionado es inválido.',

            'start_at.required' => 'La fecha de inicio es obligatoria.',
            'start_at.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_at.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha de fin.',

            'end_at.required' => 'La fecha de fin es obligatoria.',
            'end_at.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_at.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',

            'department_id.uuid' => 'El departamento debe ser un identificador válido.',
            'department_id.exists' => 'El departamento seleccionado no existe.',

            'agreement_id.uuid' => 'El convenio debe ser un identificador válido.',
            'agreement_id.exists' => 'El convenio seleccionado no existe.',

            'status_id.uuid' => 'El estado debe ser un identificador válido.',
            'status_id.exists' => 'El estado seleccionado no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'start_at' => 'fecha de inicio',
            'end_at' => 'fecha de fin',
            'department_id' => 'departamento',
            'agreement_id' => 'convenio',
            'status_id' => 'estado',
        ];
    }
}
