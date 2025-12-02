<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cambiamos false a true para permitir que cualquiera (o usuarios logueados) usen esto.
        // Aquí podrías poner lógica de roles más adelante.
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'campaign_name' => 'required|string|max:255',
            'start_at'      => 'required|date',
            // after_or_equal valida que la fecha final no sea menor a la de inicio
            'end_at'        => 'required|date|after_or_equal:start_at',
            'status_id'     => 'nullable|exists:status,id',
            'department_id' => 'nullable|exists:departments,id',
            'agreement_id'  => 'nullable|exists:agreements,id',
        ];
    }

    /**
     * (Opcional) Mensajes personalizados
     */
    public function messages(): array
    {
        return [
           
        ];
    }
}