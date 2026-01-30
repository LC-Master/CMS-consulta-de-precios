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
            $selectedPermissions = $data->input('selectedPermissions', []);

            $roleConfig = config("permissions.roles.{$selectedRole}");
            $shouldKeepRole = true;

            if ($data->has('selectedPermissions')) {
                if ($roleConfig === '*') {
                    $allPermissionsCount = \count(config('permissions.permissions'));
                    if (\count($selectedPermissions) < $allPermissionsCount) {
                        $shouldKeepRole = false;
                    }
                } elseif (\is_array($roleConfig)) {
                    foreach ($roleConfig as $rolePerm) {
                        if (!\in_array($rolePerm, $selectedPermissions)) {
                            $shouldKeepRole = false;
                            break;
                        }
                    }
                }
            }

            if ($shouldKeepRole) {
                $user->assignRole($selectedRole);
                if (!empty($selectedPermissions)) {
                    $user->syncPermissions($selectedPermissions);
                }
            } else {
                $user->syncPermissions($selectedPermissions);
            }

            return $user;
        });
    }
}