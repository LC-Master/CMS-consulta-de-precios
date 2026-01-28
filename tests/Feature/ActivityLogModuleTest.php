<?php

use App\Models\User;
use App\Models\ActivityLog; 
use App\Models\Campaign;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'publicidad']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->publicista = User::factory()->create();
    $this->publicista->assignRole('publicidad');

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
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
            'user_id' => $this->admin->id,
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

describe('Restricciones por Rol (Admin vs Publicidad)', function () {

    test('ADMIN puede ver logs de cualquier tipo (User, Campaign, Center)', function () {
        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'WARNING',
            'message' => 'Usuario actualizado',
            'subject_type' => User::class,
            'subject_id' => 'user-uuid-1',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Campaña actualizada',
            'subject_type' => Campaign::class,
            'subject_id' => 'campaign-uuid-1',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('logs.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 2)
                ->where('elements', function ($elements) {
                    return count($elements) >= 3;
                })
            );
    });

    test('PUBLICIDAD solo puede ver logs de Campañas (Filtro forzado)', function () {
        ActivityLog::create([
            'action' => 'CREATE',
            'level' => 'INFO',
            'message' => 'Usuario creado',
            'subject_type' => User::class,
            'subject_id' => 'user-uuid-99',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Campaña modificada',
            'subject_type' => Campaign::class,
            'subject_id' => 'campaign-uuid-50',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $this->actingAs($this->publicista)
            ->get(route('logs.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1) 
                ->where('logs.data.0.subject_type', 'Campaign') 
                ->where('elements', function ($elements) {
                    return count($elements) === 1 && $elements[0]['value'] === 'campaign';
                })
            );
    });

});

describe('Filtros y Búsqueda Avanzada', function () {

    test('puede buscar logs por IP', function () {
        ActivityLog::create([
            'action' => 'LOGIN',
            'level' => 'INFO',
            'message' => 'Inicio de sesión',
            'ip_address' => '192.168.1.50',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'LOGIN',
            'level' => 'INFO',
            'message' => 'Inicio de sesión',
            'ip_address' => '10.0.0.1',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['search' => '192.168.1.50']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.ip_address', '192.168.1.50')
            );
    });

    test('puede buscar logs por Nombre de Usuario responsable', function () {
        $userTarget = User::factory()->create(['name' => 'Roberto Auditor']);
        
        ActivityLog::create([
            'action' => 'DELETE',
            'level' => 'DANGER',
            'message' => 'Eliminó un archivo',
            'user_id' => $userTarget->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'DELETE',
            'level' => 'DANGER',
            'message' => 'Eliminó un archivo',
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['search' => 'Roberto']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.user.name', 'Roberto Auditor')
            );
    });

    test('puede buscar dentro de las propiedades JSON', function () {
        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Actualización de campaña',
            'properties' => ['title' => 'Super Promo Verano'],
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Actualización de campaña',
            'properties' => ['title' => 'Campaña Invierno'],
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['search' => 'Verano']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.properties.title', 'Super Promo Verano')
            );
    });

    test('filtra por tipo de elemento', function () {
        ActivityLog::create([
            'subject_type' => Campaign::class, 
            'action' => 'VIEW',
            'level' => 'INFO',
            'message' => 'Vio campaña', 
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        ActivityLog::create([
            'subject_type' => User::class, 
            'action' => 'VIEW',
            'level' => 'INFO',
            'message' => 'Vio usuario', 
            'user_id' => $this->admin->id,
            'created_at' => now(),
        ]);

        $this->get(route('logs.index', ['element' => 'user']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('logs.data', 1)
                ->where('logs.data.0.subject_type', 'User')
            );
    });

});

describe('Integridad de Datos (Modal)', function () {

    test('envía las propiedades OLD y NEW para mostrar en el modal de auditoría', function () {
        $properties = [
            'old' => ['title' => 'Titulo Viejo'],
            'attributes' => ['title' => 'Titulo Nuevo']
        ];

        ActivityLog::create([
            'action' => 'UPDATE',
            'level' => 'INFO',
            'message' => 'Actualización importante',
            'properties' => $properties,
            'user_id' => $this->admin->id,
            'subject_type' => Campaign::class,
            'created_at' => now(),
        ]);

        $this->get(route('logs.index'))
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