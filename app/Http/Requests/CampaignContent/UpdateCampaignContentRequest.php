<?php

namespace App\Http\Requests\CampaignContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignContentRequest extends FormRequest
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
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'campaign_type' => ['required', 'string', 'max:50'],
            'url' => ['required', 'url', 'max:2048'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required' => 'Debes seleccionar una campaña.',
            'campaign_id.exists' => 'La campaña seleccionada no es válida.',
            'campaign_type.required' => 'El tipo de contenido es obligatorio.',
            'url.required' => 'La URL es obligatoria.',
            'url.url' => 'El formato de la URL no es válido.',
        ];
    }
}