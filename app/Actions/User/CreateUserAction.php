<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function execute(Request $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->input('name'),
                'email' => $data->input('email'),
                'password' => Hash::make($data->input('password')),
            ]);

            $selectedRole = $data->input('role');
            $roleToAssign = $selectedRole ?: 'consultor';
            $user->syncRoles($roleToAssign);

            $selectedPermissions = $data->input('selectedPermissions', []);
            if (!empty($selectedPermissions)) {
                $user->syncPermissions($selectedPermissions);
            }

            return $user;
        });
    }
}