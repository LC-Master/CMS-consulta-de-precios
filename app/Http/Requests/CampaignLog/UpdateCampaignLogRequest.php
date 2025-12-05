<?php

namespace App\Http\Requests\CampaignLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignLogRequest extends FormRequest
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
            'message' => ['required', 'string', 'max:500'],
            'level' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'campaign_id.required' => 'La campaña es obligatoria.',
            'campaign_id.exists' => 'La campaña seleccionada no es válida.',
            'message.required' => 'El mensaje del log es obligatorio.',
            'level.required' => 'El nivel del log es obligatorio.',
        ];
    }
}