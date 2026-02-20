<?php

namespace App\Traits;

use App\Models\User;

trait IsAdmin
{
    public function authorizeUserAccess(User $user)
    {
        if ($user->getAttribute('email') === config('mail.admin_email') || $user->hasRole('supervisor')) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }
}