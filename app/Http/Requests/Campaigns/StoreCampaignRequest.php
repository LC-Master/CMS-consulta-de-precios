<?php

namespace App\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCampaignRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:155'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:today', 'after:start_at'],
            'department_id' => ['required', 'exists:departments,id'],
            'agreements' => ['nullable', 'array', 'min:1'],
            'agreements.*' => ['string', 'exists:agreements,id'],
            'stores' => ['required', 'array', 'min:1'],
            'stores.*' => ['string', 'exists:Store,ID'],
            'am_media' => ['nullable', 'array'],
            'am_media.*' => ['string', 'exists:media,id'],
            'pm_media' => ['nullable', 'array'],
            'pm_media.*' => ['string', 'exists:media,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = \Carbon\Carbon::parse($this->start_at);
            $end = \Carbon\Carbon::parse($this->end_at);

            $isAmRequired = $start->format('A') === 'AM' || $end->diffInHours($start) >= 12;

            if ($isAmRequired && empty($this->am_media)) {
                $validator->errors()->add('am_media', 'El bloque AM no puede estar vacío porque la campaña inicia en la mañana o cubre ese horario.');
            }

            $isPmRequired = $end->format('A') === 'PM' || $start->format('A') === 'AM' && $end->format('A') === 'PM';

            if ($isPmRequired && empty($this->pm_media)) {
                $validator->errors()->add('pm_media', 'El bloque PM es obligatorio ya que la campaña finaliza o pasa por la tarde.');
            }
        });
    }

    /**
     * Mensajes de validación en español.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser texto.',
            'title.max' => 'El título no puede tener más de :max caracteres.',

            'start_at.required' => 'La fecha de inicio es obligatoria.',
            'start_at.date' => 'La fecha de inicio no es una fecha válida.',

            'end_at.required' => 'La fecha de finalización es obligatoria.',
            'end_at.date' => 'La fecha de finalización no es una fecha válida.',
            'end_at.after_or_equal' => 'La fecha de finalización debe ser hoy o una fecha futura.',
            'end_at.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',

            'stores.required' => 'Debe seleccionar al menos un centro.',
            'stores.array' => 'Los centros deben enviarse como un arreglo.',
            'stores.min' => 'Debe seleccionar al menos :min centro(s).',
            'stores.*.string' => 'Cada centro debe ser texto.',
            'stores.*.exists' => 'El centro seleccionado no existe.',

            'department_id.required' => 'El departamento es obligatorio.',
            'department_id.exists' => 'El departamento seleccionado no existe.',

            'agreements.array' => 'Los convenios deben enviarse como un arreglo.',
            'agreements.min' => 'Debe seleccionar al menos :min convenio(s).',
            'agreements.*.string' => 'Cada convenio debe ser texto.',
            'agreements.*.exists' => 'El convenio seleccionado no existe.',

            'am_media.required' => 'Debe seleccionar al menos un medio AM.',
            'am_media.array' => 'Los medios AM deben enviarse como un arreglo.',
            'am_media.min' => 'Debe seleccionar al menos :min medio(s) AM.',
            'am_media.*.string' => 'Cada medio AM debe ser texto.',
            'am_media.*.exists' => 'El medio AM seleccionado no existe.',

            'pm_media.required' => 'Debe seleccionar al menos un medio PM.',
            'pm_media.array' => 'Los medios PM deben enviarse como un arreglo.',
            'pm_media.min' => 'Debe seleccionar al menos :min medio(s) PM.',
            'pm_media.*.string' => 'Cada medio PM debe ser texto.',
            'pm_media.*.exists' => 'El medio PM seleccionado no existe.',
        ];
    }

    /**
     * Nombres legibles para los atributos (opcional).
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'start_at' => 'fecha de inicio',
            'end_at' => 'fecha de finalización',
            'stores' => 'centros',
            'stores.*' => 'centro',
            'department_id' => 'departamento',
            'agreements' => 'convenios',
            'agreements.*' => 'convenio',
            'am_media' => 'medios AM',
            'am_media.*' => 'medio AM',
            'pm_media' => 'medios PM',
            'pm_media.*' => 'medio PM',
        ];
    }
}
