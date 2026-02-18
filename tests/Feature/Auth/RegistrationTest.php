<?php

use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    Role::firstOrCreate(['name' => 'publicidad']);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});