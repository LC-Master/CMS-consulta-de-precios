<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {

    if (!Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    // Simulamos que la contraseña ya fue confirmada en la sesión (Middleware password.confirm)
    session(['auth.password_confirmed_at' => time()]);
});

describe('Visualización de Usuarios', function () {

    test('renderiza la lista de usuarios (Index)', function () {
        $this->actingAs($this->admin)
            ->get(route('user.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Index')
                ->has('users.data')
                ->has('filters')
            );
    });

    test('filtra usuarios por búsqueda', function () {

        User::factory()->create(['name' => 'Juan Perez', 'email' => 'juan@test.com']);
        User::factory()->create(['name' => 'Otro Usuario', 'email' => 'otro@test.com']);

        $this->actingAs($this->admin)
            ->get(route('user.index', ['search' => 'Juan']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Index')
                ->has('users.data', 1) 
                ->where('users.data.0.email', 'juan@test.com')
            );
    });

});

describe('Creación de Usuarios', function () {

    test('renderiza el formulario de creación', function () {
        $this->actingAs($this->admin)
            ->get(route('user.create'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Create')
                ->has('roles')
            );
    });

    test('crea un usuario nuevo asignando rol correctamente', function () {

        Role::firstOrCreate(['name' => 'admin']);

        $userData = [
            'name' => 'Agente Administrativo',
            'email' => 'agente@admin.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => 'admin',
        ];

        $this->actingAs($this->admin)
            ->post(route('user.store'), $userData)
            ->assertRedirect(route('user.index'))
            ->assertSessionHas('success', 'Usuario creado correctamente.');

        $this->assertDatabaseHas('users', [
            'email' => 'agente@admin.com',
            'name' => 'Agente Administrativo',
        ]);

        $newUser = User::where('email', 'agente@admin.com')->first();
        expect($newUser->hasRole('admin'))->toBeTrue();
    });

});

describe('Edición y Actualización', function () {

    test('renderiza el formulario de edición', function () {
        $userToEdit = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('user.edit', $userToEdit))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Edit')
                ->where('user.id', $userToEdit->id)
                ->has('statuses')
                ->has('roles')
            );
    });

    test('actualiza un usuario cambiando rol y estado', function () {

        Role::firstOrCreate(['name' => 'publicidad']);
        
        $userToEdit = User::factory()->create();
        $userToEdit->assignRole('admin');

        // Datos nuevos para el formulario
        $updateData = [
            'name' => 'Nombre Editado',
            'email' => 'nuevo@correo.com',
            'status' => '1', 
            'role' => 'publicidad', 
            'password' => '', 
            'password_confirmation' => '',
        ];

        $this->actingAs($this->admin)
            ->put(route('user.update', $userToEdit), $updateData)
            ->assertRedirect(route('user.index'))
            ->assertSessionHas('success', 'Usuario actualizado correctamente.');
            
        // Verificar cambios en la tabla users
        $this->assertDatabaseHas('users', [
            'id' => $userToEdit->id,
            'email' => 'nuevo@correo.com',
            'name' => 'Nombre Editado',
            'status' => '1',
        ]);

        // Recargamos el modelo para verificar roles
        $userToEdit->refresh();
        
        expect($userToEdit->hasRole('publicidad'))->toBeTrue();
        expect($userToEdit->hasRole('admin'))->toBeFalse(); 
    });

});