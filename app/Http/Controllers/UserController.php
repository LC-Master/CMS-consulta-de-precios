<?php

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.list', only: ['index']),
            new Middleware('permission:users.show', only: ['show']),
            new Middleware('permission:users.create', only: ['create', 'store']),
            new Middleware('permission:users.update', only: ['edit', 'update']),
            new Middleware('permission:users.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = User::withTrashed();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Users/Index', [
            'users' => Inertia::scroll($query->latest()->paginate()),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => \Spatie\Permission\Models\Role::with('permissions:id,name')
                ->select('id', 'name')
                ->get()
        ]);
    }

    public function store(StoreUserRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        try {
            $request->validated();

            $createUserAction->execute($request);

            return to_route('user.index')
                ->with('success', 'Usuario creado correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error creating user: ' . $e->getMessage(), ['admin_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error inesperado al crear el usuario.');
        }
    }


    public function edit(User $user)
    {
        $user->load('roles');

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => \Spatie\Permission\Models\Role::with('permissions:id,name')
                ->select('id', 'name')
                ->get()
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $updateUserAction): RedirectResponse
    {
        try {

            $request->validated();

            $updateUserAction->execute($user, $request);

            return to_route('user.index')
                ->with('success', 'Usuario actualizado correctamente.');


        } catch (\Throwable $e) {
            Log::error('Error updating user: ' . $e->getMessage(), ['admin_id' => Auth::id()]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar el usuario.');
        }
    }

    public function destroy(User $user)
    {
        if ($user->getKey() === Auth::id()) {
            return back()->withErrors('name', 'No puedes desactivar tu propia cuenta.');
        }

        $user->delete();

        return to_route('user.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }

    public function restore(User $user)
    {
        try {
            $user->restore();

            return to_route('user.index')
                ->with('success', 'Usuario restaurado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error restoring user: ' . $e->getMessage(), ['admin_id' => Auth::id()]);

            return back()
                ->with('error', 'Ocurrió un error al restaurar el usuario.');
        }
    }
}