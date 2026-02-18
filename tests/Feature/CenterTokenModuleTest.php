<?php

use App\Models\User;
use App\Models\Store;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    if (!Schema::hasTable('Store')) {
        Schema::create('Store', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->string('Name');
            $table->string('StoreCode')->nullable();
            $table->boolean('Inactive')->default(false);
            $table->timestamps();
        });
    }

    $permissions = ['token.list', 'token.create', 'token.delete'];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role = Role::firstOrCreate(['name' => 'admin']);
    $role->syncPermissions($permissions);

    $this->admin = User::factory()->create(['email_verified_at' => now()]);
    $this->admin->assignRole('admin');

    $this->storeA = Store::forceCreate(['ID' => 1, 'Name' => 'Centro Norte', 'StoreCode' => 'CTR-001', 'Inactive' => false]);
    $this->storeB = Store::forceCreate(['ID' => 2, 'Name' => 'Centro Sur', 'StoreCode' => 'CTR-002', 'Inactive' => false]);

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
});

describe('Visualización y Búsqueda de Tokens', function () {

    test('renderiza el listado de tokens existentes', function () {
        $this->storeA->createToken('Token Norte APP');
        $this->storeB->createToken('Token Sur API');

        $this->get(route('centertokens.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('CenterTokens/Index')
                ->has('centerTokens.data', 2) 
                ->has('stores') 
            );
    });

    test('filtra tokens por Centro (Store) específico', function () {
        $this->storeA->createToken('Token A');
        $this->storeB->createToken('Token B');

        $this->get(route('centertokens.index', ['store' => $this->storeA->ID]))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('centerTokens.data', 1)
                ->where('centerTokens.data.0.name', 'Token A')
                ->where('centerTokens.data.0.store.id', $this->storeA->ID)
            );
    });

    test('busca tokens por Nombre del Token', function () {
        $this->storeA->createToken('Acceso Facturación');
        $this->storeA->createToken('Acceso Inventario');

        $this->get(route('centertokens.index', ['search' => 'Facturación']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('centerTokens.data', 1)
                ->where('centerTokens.data.0.name', 'Acceso Facturación')
            );
    });

    test('busca tokens por Nombre del Centro asociado', function () {
        $this->storeA->createToken('Token Generico 1'); 
        $this->storeB->createToken('Token Generico 2'); 

        $this->get(route('centertokens.index', ['search' => 'Norte']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('centerTokens.data', 1)
                ->where('centerTokens.data.0.store.name', 'Centro Norte')
            );
    });

});

describe('Generación de Nuevos Tokens', function () {

    test('genera un nuevo token y lo devuelve en la sesión (Flash)', function () {
        Event::fake([\App\Events\CenterToken\CenterTokenEvent::class]);

        $postData = [
            'name' => 'Nuevo Token API 2026',
            'store_id' => (string) $this->storeA->ID,
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
            'tokenable_type' => Store::class,
            'tokenable_id' => $this->storeA->ID
        ]);

        Event::assertDispatched(\App\Events\CenterToken\CenterTokenEvent::class);
    });

    test('valida que el nombre y el centro sean obligatorios', function () {
        $this->post(route('centertokens.store'), [])
            ->assertSessionHasErrors(['name', 'store_id']);
    });

});

describe('Revocación de Tokens', function () {

    test('revoca (elimina) un token existente correctamente', function () {
        Event::fake([\App\Events\CenterToken\CenterTokenEvent::class]);

        $token = $this->storeA->createToken('Token Para Borrar')->accessToken;

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

    test('bloquea acceso a usuarios que NO tienen permisos', function () {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);
        
        $this->actingAs($user)
             ->withSession(['auth.password_confirmed_at' => time()])
             ->get(route('centertokens.index'))
             ->assertStatus(302);
    });

});