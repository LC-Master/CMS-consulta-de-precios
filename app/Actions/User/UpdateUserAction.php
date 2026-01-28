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
                'status' => $data->input('status'),
            ];

            if (!empty($data->input('password'))) {
                $updateData['password'] = Hash::make($data->input('password'));
            }

            $updated = $user->update($updateData);
                
            $user->syncRoles($data->input('role'));

            return $updated;
        });
    }
}