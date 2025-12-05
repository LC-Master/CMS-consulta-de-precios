<?php

namespace App\Http\Requests\CampaignStore;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'center_id' => [
                'required', 
                'exists:centers,id',
                // Opcional: Evitar duplicar la misma campaña en el mismo centro
                Rule::unique('campaign_stores')->where(function ($query) {
                    return $query->where('campaign_id', $this->campaign_id);
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required' => 'Debes seleccionar una campaña.',
            'campaign_id.exists' => 'La campaña seleccionada no es válida.',
            'center_id.required' => 'Debes seleccionar un centro.',
            'center_id.exists' => 'El centro seleccionado no es válido.',
            'center_id.unique' => 'Esta campaña ya está asignada a este centro.',
        ];
    }
}