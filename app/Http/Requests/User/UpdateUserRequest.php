<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 
                'string', 
                'email:rfc,dns', 
                'max:255', 
                Rule::unique('users')->ignore($this->user->id)
            ],
            // La contraseña es opcional al editar (nullable)
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }
}