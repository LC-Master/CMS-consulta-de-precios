<?php

namespace App\Http\Requests\Timeline;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeLineItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'media_id' => ['required', 'exists:media,id'],
            'scheduled_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required' => 'Debes seleccionar una campaña.',
            'campaign_id.exists' => 'La campaña seleccionada no es válida.',
            'media_id.required' => 'Debes seleccionar un archivo multimedia.',
            'media_id.exists' => 'El archivo multimedia no es válido.',
            'scheduled_at.required' => 'La fecha de programación es obligatoria.',
            'scheduled_at.date' => 'El formato de la fecha no es válido.',
        ];
    }
}