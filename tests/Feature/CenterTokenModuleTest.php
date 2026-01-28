<?php

use App\Models\User;
use App\Models\Center;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->centerA = Center::factory()->create(['name' => 'Centro Norte', 'code' => 'CTR-001']);
    $this->centerB = Center::factory()->create(['name' => 'Centro Sur', 'code' => 'CTR-002']);

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
});

describe('Visualización y Búsqueda de Tokens', function () {

    test('renderiza el listado de tokens existentes', function () {
        $this->centerA->createToken('Token Norte APP');
        $this->centerB->createToken('Token Sur API');

        $this->get(route('centertokens.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('CenterTokens/Index')
                ->has('centerTokens.data', 2) 
                ->has('centers') 
            );
    });

    test('filtra tokens por Centro específico', function () {
        $this->centerA->createToken('Token A');
        $this->centerB->createToken('Token B');

        $this->get(route('centertokens.index', ['center' => $this->centerA->id]))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('centerTokens.data', 1)
                ->where('centerTokens.data.0.name', 'Token A')
                ->where('centerTokens.data.0.center.id', $this->centerA->id)
            );
    });

    test('busca tokens por Nombre del Token', function () {
        $this->centerA->createToken('Acceso Facturación');
        $this->centerA->createToken('Acceso Inventario');

        $this->get(route('centertokens.index', ['search' => 'Facturación']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('centerTokens.data', 1)
                ->where('centerTokens.data.0.name', 'Acceso Facturación')
            );
    });

    test('busca tokens por Nombre del Centro asociado', function () {
        $this->centerA->createToken('Token Generico 1'); 
        $this->centerB->createToken('Token Generico 2'); 

        $this->get(route('centertokens.index', ['search' => 'Norte']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('centerTokens.data', 1)
                ->where('centerTokens.data.0.center.name', 'Centro Norte')
            );
    });

});

describe('Generación de Nuevos Tokens', function () {

    test('genera un nuevo token y lo devuelve en la sesión (Flash)', function () {
        Event::fake([\App\Events\CenterToken\CenterTokenEvent::class]);

        $postData = [
            'name' => 'Nuevo Token API 2026',
            'center_id' => $this->centerA->id,
        ];

        $fromUrl = route('centertokens.index');

        $response = $this->from($fromUrl)
                         ->post(route('centertokens.store'), $postData);

        $response->assertRedirect($fromUrl);

        $response->assertSessionHas('success', function ($value) {
            return isset($value['token']) && !empty($value['token']);
        });

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'Nuevo Token API 2026',
            'tokenable_type' => Center::class,
            'tokenable_id' => $this->centerA->id
        ]);

        Event::assertDispatched(\App\Events\CenterToken\CenterTokenEvent::class);
    });

    test('valida que el nombre y el centro sean obligatorios', function () {
        $this->post(route('centertokens.store'), [])
            ->assertSessionHasErrors(['name', 'center_id']);
    });

});

describe('Revocación de Tokens', function () {

    test('revoca (elimina) un token existente correctamente', function () {
        Event::fake([\App\Events\CenterToken\CenterTokenEvent::class]);

        $token = $this->centerA->createToken('Token Para Borrar')->accessToken;

        $fromUrl = route('centertokens.index');

        $this->from($fromUrl)
             ->delete(route('centertokens.destroy', $token->id))
             ->assertRedirect($fromUrl)
             ->assertSessionHas('success', 'Token eliminado correctamente.');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->id
        ]);

        Event::assertDispatched(\App\Events\CenterToken\CenterTokenEvent::class);
    });

});

describe('Seguridad y Accesos', function () {

    test('bloquea acceso a usuarios sin confirmar contraseña', function () {

        $this->actingAs($this->admin)
             ->withSession(['auth.password_confirmed_at' => null]);

        $this->get(route('centertokens.index'))
            ->assertRedirect(); 
    });

    test('bloquea acceso a usuarios que NO son administradores', function () {

    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);
    
    $this->actingAs($user)
         ->withSession(['auth.password_confirmed_at' => time()])
         ->get(route('centertokens.index'))
         ->assertStatus(302); 
});

});