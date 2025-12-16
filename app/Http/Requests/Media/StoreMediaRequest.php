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
            'files' => ['required', 'array'],
            'files.*.img' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,mp4',
                'max:153600',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Debes subir al menos un archivo.',
            'files.array' => 'El campo files debe ser un arreglo.',
            'files.*.required' => 'Cada archivo es obligatorio.',
            'files.*.file' => 'Cada elemento debe ser un archivo válido.',
            'files.*.mimes' => 'Los archivos deben ser de tipo: jpg, jpeg, png o mp4.',
            'files.*.max' => 'Cada archivo no debe exceder los 150 MB.',
        ];
    }
}
