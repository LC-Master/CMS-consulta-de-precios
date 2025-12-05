<?php

namespace App\Http\Requests\CampaignStore;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el registro actual de la ruta para ignorarlo en la validación unique
        $campaignStore = $this->route('campaign_store'); 

        return [
            'campaign_id' => ['required', 'exists:campaigns,id'],
            'center_id' => [
                'required', 
                'exists:centers,id',
                // Validamos que la combinación campaña+centro sea única, ignorando el registro actual
                Rule::unique('campaign_stores')->where(function ($query) {
                    return $query->where('campaign_id', $this->campaign_id);
                })->ignore($campaignStore->id)
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