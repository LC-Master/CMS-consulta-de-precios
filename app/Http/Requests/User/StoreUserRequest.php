<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email|confirmed',
            'role' => 'nullable|string|exists:roles,name',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'selectedPermissions' => 'nullable|array',
            'selectedPermissions.*' => 'string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasRole = $this->filled('role');
            $hasPermissions = $this->filled('selectedPermissions') && count($this->selectedPermissions) > 0;

            if (!$hasRole && !$hasPermissions) {
                $validator->errors()->add(
                    'role',
                    'Debes asignar al menos un rol o seleccionar permisos manualmente.'
                );
            }


            if (Auth::id() == $this->route('id')) {
                $validator->errors()->add(
                    'name',
                    'No puedes modificar tu propio perfil.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.confirmed' => 'Los correos electrónicos no coinciden.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ];
    }
}