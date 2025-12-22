<?php

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Users/Index', [
            'users' => Inertia::scroll(fn() => $query->latest()->paginate()),
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

            return Redirect::route('user.index')
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
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return Redirect::route('user.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}