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

            foreach ($files as $file) {
                if ($file->getMimeType() === 'video/mp4') {
                    if (!isset($thumbnails[$thumbIndex])) {
                        $validator->errors()->add(
                            'thumbnails',
                            "El archivo '{$file->getClientOriginalName()}' es un video (MP4) y requiere un thumbnail en formato JPG/JPEG (máx. 5 MB)."
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
            'files.array' => 'La entrada "files" debe ser un arreglo de archivos.',
            'files.*.file' => 'Cada elemento de "files" debe ser un archivo válido.',
            'files.*.mimetypes' => 'Formato no permitido. Se aceptan: video MP4 (.mp4), imágenes JPEG/JPG, PNG, WEBP (.jpg, .jpeg, .png, .webp).',
            'files.*.max' => 'El archivo :attribute excede el tamaño máximo permitido de 150 MB.',

            'thumbnails.*.file' => 'Cada thumbnail debe ser un archivo válido.',
            'thumbnails.*.image' => 'Los thumbnails deben ser imágenes en formato JPG/JPEG.',
            'thumbnails.*.mimes' => 'Formato de thumbnail no permitido. Se aceptan: JPEG / JPG (.jpg, .jpeg).',
            'thumbnails.*.max' => 'El thumbnail :attribute excede el tamaño máximo permitido de 5 MB.',
        ];
    }
}
