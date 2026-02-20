<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $permissionsList = [
        'user.list',
        'user.show',
        'user.create',
        'user.update',
        'user.delete',
        'user.restore'
    ];

    Config::set('permissions.roles', [
        'admin' => '*', 
        'agente' => ['user.list'] 
    ]);
    
    Config::set('permissions.permissions', $permissionsList);

    foreach ($permissionsList as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role = Role::firstOrCreate(['name' => 'admin']);
    $role->syncPermissions($permissionsList); 

    Role::firstOrCreate(['name' => 'agente']);

    $this->admin = User::factory()->create([
        'name' => 'Super Admin',
        'email' => 'admin@master.com',
        'email_verified_at' => now(), 
    ]);
    
    $this->admin->assignRole('admin');

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]); 
});

describe('Visualización de Usuarios', function () {

    test('renderiza la lista de usuarios (Index)', function () {
        User::factory()->count(2)->create();

        $this->get(route('user.index'))
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

        $this->get(route('user.index', ['search' => 'Juan']))
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
        $this->get(route('user.create'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Create')
                ->has('roles')
                ->has('permissions') 
            );
    });

    test('crea un usuario nuevo asignando rol correctamente', function () {
        $userData = [
            'name' => 'Agente Nuevo',
            'email' => 'agente@admin.com',
            'email_confirmation' => 'agente@admin.com', 
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!', 
            'role' => 'admin',
        ];

        $this->post(route('user.store'), $userData)
            ->assertRedirect(route('user.index'))
            ->assertSessionHas('success', 'Usuario creado correctamente.');

        $this->assertDatabaseHas('users', [
            'email' => 'agente@admin.com',
            'name' => 'Agente Nuevo'
        ]);

        $user = User::where('email', 'agente@admin.com')->first();
        expect($user->refresh()->hasRole('admin'))->toBeTrue();
    });

    test('valida que el rol o permisos sean obligatorios', function () {
        $userData = [
            'name' => 'User Sin Rol',
            'email' => 'fail@admin.com',
            'email_confirmation' => 'fail@admin.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role' => null, 
            'selectedPermissions' => [] 
        ];

        $this->post(route('user.store'), $userData)
            ->assertSessionHasErrors(['role']); 
    });

});

describe('Edición y Actualización', function () {

    test('renderiza el formulario de edición', function () {
        $userToEdit = User::factory()->create();
        $userToEdit->assignRole('agente');

        $this->get(route('user.edit', $userToEdit))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Edit')
                ->where('user.id', $userToEdit->id)
                ->has('roles')
                ->has('permissions')
            );
    });

    test('actualiza un usuario cambiando rol', function () {
        $userToEdit = User::factory()->create();
        $userToEdit->assignRole('agente');

        $updateData = [
            'name' => 'Usuario Actualizado',
            'role' => 'admin', 
        ];

        $this->put(route('user.update', $userToEdit), $updateData)
            ->assertRedirect(route('user.index'))
            ->assertSessionHas('success', 'Usuario actualizado correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $userToEdit->id,
            'name' => 'Usuario Actualizado',
        ]);

        $userToEdit->refresh();
        expect($userToEdit->hasRole('admin'))->toBeTrue();
    });

});

describe('Seguridad y Acciones Propias', function () {

    test('impide que un usuario elimine su propia cuenta', function () {
        $this->from(route('user.index'))
             ->delete(route('user.destroy', $this->admin))
             ->assertSessionHasErrors(); 
        
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    });

    test('permite eliminar otro usuario', function () {
        $otherUser = User::factory()->create();
        
        $this->from(route('user.index'))
             ->delete(route('user.destroy', $otherUser))
             ->assertRedirect(route('user.index'))
             ->assertSessionHas('success', 'Usuario desactivado correctamente.');

        $this->assertSoftDeleted('users', ['id' => $otherUser->id]);
    });

});