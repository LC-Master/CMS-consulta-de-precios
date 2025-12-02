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
            'campaign_name' => 'required|string|max:255',
            'start_at'      => 'required|date',
            'end_at'        => 'required|date|after_or_equal:start_at',
            'status_id'     => 'nullable|exists:status,id',
            'department_id' => 'nullable|exists:departments,id',
            'agreement_id'  => 'nullable|exists:agreements,id',
        ];
    }
}
