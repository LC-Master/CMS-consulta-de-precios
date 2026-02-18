<?php

use App\Models\User;
use App\Models\ActivityLog; 
use App\Models\Campaign;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {

    $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
    $rolePublicidad = Role::firstOrCreate(['name' => 'publicidad']);
    
    $permission = Permission::firstOrCreate(['name' => 'log.list']);
    
    $roleAdmin->givePermissionTo($permission);
    $rolePublicidad->givePermissionTo($permission);


    $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@test.com']);
    $this->admin->assignRole('admin');

    $this->publicista = User::factory()->create(['name' => 'Publicista User', 'email' => 'publicidad@test.com']);
    $this->publicista->assignRole('publicidad');


    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
         
    ActivityLog::query()->delete();
});

describe('Acceso y Visualización', function () {

    test('usuarios no autenticados son redirigidos al login', function () {
        auth()->logout();

        $this->get(route('logs.index'))
            ->assertRedirect(route('login'));
    });

    test('renderiza la lista de logs correctamente con los props necesarios', function () {
        ActivityLog::create([
            'action' => 'CREATE',
            'level' => 'INFO',
            'message' => 'Se ha creado una campaña de prueba',
            'subject_type' => Campaign::class,
            'subject_id' => 'uuid-falso-123',
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'properties' => ['old' => [], 'attributes' => []],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->get(route('logs.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Logs/Index')
                ->has('logs.data', 1)
                ->has('filters')
                ->has('elements') 
            );
    });

});

describe('Filtros y Búsqueda Avanzada', function () {

    test('puede buscar logs por IP', function () {
        ActivityLog::query()->delete(); 

        ActivityLog::create([
            'action' => 'LOGIN',
            'level' => 'INFO',
            'message' => 'Inicio de sesión A',
            'ip_address' => '192.168.1.50',
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'LOGIN',
            'level' => 'INFO',
            'message' => 'Inicio de sesión B',
            'ip_address' => '10.0.0.1',
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['search' => '192.168.1.50']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.ip_address', '192.168.1.50')
            );
    });

    test('puede buscar logs por Nombre de Usuario responsable', function () {
        $userTarget = User::factory()->create(['name' => 'Roberto Auditor', 'email' => 'roberto@test.com']);
        
        ActivityLog::query()->delete();

        ActivityLog::create([
            'action' => 'DELETE',
            'level' => 'DANGER',
            'message' => 'Eliminó un archivo',
            'causer_id' => $userTarget->id,
            'user_name' => 'Roberto Auditor',
            'user_email' => 'roberto@test.com',
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'DELETE',
            'level' => 'DANGER',
            'message' => 'Eliminó otro archivo',
            'causer_id' => $this->admin->id,
            'user_name' => 'Admin User',
            'user_email' => 'admin@test.com',
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['search' => 'Roberto']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.user_name', 'Roberto Auditor')
            );
    });

    test('puede buscar dentro de las propiedades JSON', function () {
        ActivityLog::query()->delete();

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Actualización de campaña',
            'properties' => ['title' => 'Super Promo Verano'],
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Actualización de campaña',
            'properties' => ['title' => 'Campaña Invierno'],
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['search' => 'Verano']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.properties.title', 'Super Promo Verano')
            );
    });

    test('filtra por tipo de elemento (subject_type)', function () {
        ActivityLog::query()->delete();

        ActivityLog::create([
            'subject_type' => Campaign::class, 
            'action' => 'VIEW',
            'level' => 'INFO',
            'message' => 'Vio campaña', 
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'subject_type' => User::class, 
            'action' => 'VIEW',
            'level' => 'INFO',
            'message' => 'Vio usuario', 
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email, 
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['element' => 'user']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.subject_type', 'User')
            );
    });

});

describe('Integridad de Datos (Modal)', function () {

    test('envía las propiedades correctamente parseadas para el modal', function () {
        ActivityLog::query()->delete();

        $properties = [
            'old' => ['title' => 'Titulo Viejo'],
            'attributes' => ['title' => 'Titulo Nuevo']
        ];

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Actualización importante',
            'properties' => $properties,
            'causer_id' => $this->admin->id,
            'user_name' => $this->admin->name,   
            'user_email' => $this->admin->email,
            'subject_type' => Campaign::class,
            'created_at' => now(),
        ]);

        $this->get(route('logs.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data.0.properties', fn (Assert $json) => $json
                    ->has('old')
                    ->has('attributes')
                    ->where('old.title', 'Titulo Viejo')
                    ->where('attributes.title', 'Titulo Nuevo')
                )
            );
    });

});