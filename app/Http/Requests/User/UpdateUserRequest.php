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
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'selectedPermissions' => ['nullable', 'array'],
            'selectedPermissions.*' => ['string'],
        ];
    }

   public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $targetUser = $this->route('user'); 

            if ($targetUser->id === Auth::id()) {
                if ($this->input('role') !== $targetUser->getRoleNames()->first()) {
                     $validator->errors()->add('role', 'Por seguridad, no puedes cambiar tu propio rol.');
                }
            }
        });
    }
}