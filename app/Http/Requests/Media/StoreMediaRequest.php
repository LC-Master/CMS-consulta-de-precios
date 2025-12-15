<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required', 
                'file', 
                'mimes:jpg,jpeg,png,mp4', 
                'max:157286400' 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Debes subir un archivo.',
            'file.mimes' => 'El formato del archivo no es compatible.',
            'file.max' => 'El archivo no puede pesar más de 150MB.',
        ];
    }
}