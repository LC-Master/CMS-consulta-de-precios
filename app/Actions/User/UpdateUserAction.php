<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function execute(User $user, array $data): bool
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            // Solo actualizamos la contraseña si se envió una nueva
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            return $user->update($updateData);
        });
    }
}