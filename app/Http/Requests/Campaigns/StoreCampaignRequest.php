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
            'title' => 'required|string|max:255',
            'start_at'      => 'required|date|before:end_at',
            'end_at'        => 'required|date|after:start_at',
            'centers'     => 'nullable|array|min:1',
            'department_id' => 'required|exists:departments,id',
            'agreement_id'  => 'nullable|exists:agreements,id',
            'centers.*'   => 'string|exists:centers,id',
        ];
    }
}
