<?php

use App\Models\User;

beforeAll(function () {
    // Set up roles and permissions
    \Spatie\Permission\PermissionRegistrar::class::forgetCachedPermissions();

    $permissions = include base_path('config/permissions.php');

    foreach ($permissions['permissions'] as $permission) {
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
    }

    foreach ($permissions['roles'] as $roleName => $rolePermissions) {
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
        if ($rolePermissions === '*') {
            $role->givePermissionTo(\Spatie\Permission\Models\Permission::all());
        } else {
            $role->givePermissionTo($rolePermissions);
        }
    }
});


test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());
    $user->assignRole('publicidad');
    $this->get(route('dashboard'))->assertOk();
});