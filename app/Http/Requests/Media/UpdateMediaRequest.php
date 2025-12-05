<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'nullable', 
                'file', 
                'mimes:jpg,jpeg,png,mp4', 
                'max:157286400'
            ],
        ];
    }
}