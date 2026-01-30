<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UpdateUserAction
{
    public function execute(User $user, Request $data): bool
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data->input('name'),
            ];

            if (!empty($data->input('password'))) {
                $updateData['password'] = Hash::make($data->input('password'));
            }

            $updated = $user->update($updateData);

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

            if ($shouldKeepRole && $selectedRole) {
                $user->syncRoles($selectedRole);
                $user->syncPermissions($selectedPermissions);
            } else {
                $user->syncRoles([]);
                $user->syncPermissions($selectedPermissions);
            }

            return $updated;
        });
    }
}