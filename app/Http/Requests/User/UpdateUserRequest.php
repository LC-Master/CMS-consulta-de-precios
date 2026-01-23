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
            // 'email' => [
            //     'required', 
            //     'string', 
            //     'email:rfc,dns', 
            //     'max:255', 
            //     Rule::unique('users')->ignore($this->user->id)
            // ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', 'in:0,1'],
            
            'role' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            
            if ($this->user->id === Auth::id() && $this->input('status') == 0) {
                $validator->errors()->add('status', 'Por seguridad, no puedes desactivar tu propia cuenta mientras estás logueado.');
            }

            if ($this->user->id === Auth::id() && $this->input('role') !== $this->user->getRoleNames()->first()) {
                 $validator->errors()->add('role', 'Por seguridad, no puedes cambiar tu propio rol.');
            }

        });
    }
}