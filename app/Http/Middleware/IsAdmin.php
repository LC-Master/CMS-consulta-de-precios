<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $param = $request->route('user');

        $userIdToAccess = is_object($param) ? $param->id : $param;
        /** @var \App\Models\User $targetUser */
        $targetUser = \App\Models\User::find($userIdToAccess);

        if (!$targetUser) {
            abort(404);
        }

        if ($targetUser->hasRole('admin')) {
            if (!Auth::check() || !Auth::user()->hasRole('admin')) {
                abort(403, 'No puedes manipular a un administrador.');
            }
        }

        if (Auth::check() && (Auth::user()->hasRole('admin') || Auth::id() == $targetUser->id)) {
            return $next($request);
        }

        abort(403);
    }
}
