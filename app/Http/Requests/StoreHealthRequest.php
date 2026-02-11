<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreHealthRequest extends FormRequest
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
            'syncState' => ['required', 'string', 'in:pending,syncing,success,failed,stale'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date'],
            'communicationKey' => ['nullable', 'string'],
            'disk.size' => ['required', 'numeric'],
            'disk.free' => ['required', 'numeric'],
            'disk.used' => ['required', 'numeric'],
            'errorMessage' => ['nullable'],
            'dtoChanged' => ['required', 'boolean'],
            'uptime' => ['required', 'decimal:1,1000'],
            'mediaCount' => ['required', 'integer'],
            'reported_at' => ['nullable', 'date'],
            'mediaError' => ['nullable', 'array'],
            'mediaError.*.id' => ['required_with:mediaError', 'string'],
            'mediaError.*.name' => ['required_with:mediaError', 'string'],
            'mediaError.*.checksum' => ['required_with:mediaError', 'string'],
            'mediaError.*.errorType' => ['required_with:mediaError', 'string'],
            'mediaError.*.errorCount' => ['required_with:mediaError', 'integer'],
            'mediaError.*.lastErrorAt' => ['required_with:mediaError', 'date'],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation Failed',
            ], 422)
        );
    }
}
