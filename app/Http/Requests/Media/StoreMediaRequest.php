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
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                'max:153600', // 150MB
                'mimetypes:video/mp4,image/jpeg,image/png,image/webp',
            ],

            'thumbnails' => ['nullable', 'array'],
            'thumbnails.*' => [
                'file',
                'image',
                'mimes:jpeg,jpg',
                'max:5120', // 5MB
            ],
        ];
    }

    /**
     * Validaciones cruzadas (video => thumbnail obligatorio)
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $files = $this->file('files', []);
            $thumbnails = $this->file('thumbnails', []);

            $thumbIndex = 0;

            foreach ($files as $index => $file) {
                if ($file->getMimeType() === 'video/mp4') {
                    if (! isset($thumbnails[$thumbIndex])) {
                        $validator->errors()->add(
                            'thumbnails',
                            "El archivo '{$file->getClientOriginalName()}' es un video y requiere thumbnail."
                        );
                    }
                    $thumbIndex++;
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Debes subir al menos un archivo.',
            'files.*.mimetypes' => 'Formato no permitido.',
            'thumbnails.*.image' => 'Los thumbnails deben ser imágenes JPG.',
        ];
    }
}
