<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Store;
use App\Models\Department;
use App\Models\Status;
use App\Enums\CampaignStatus;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // 1. Simular Tablas SQL Server y Pivotes
    if (!Schema::hasTable('Store')) {
        Schema::create('Store', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->string('Name');
            $table->string('StoreCode')->nullable();
            $table->string('Region')->nullable(); 
            $table->string('Address1')->nullable();
            $table->string('City')->nullable();
            $table->string('State')->nullable();
            $table->string('Zip')->nullable();
            $table->string('Country')->nullable();
            $table->string('PhoneNumber')->nullable();
            $table->string('FaxNumber')->nullable();
            $table->boolean('Inactive')->default(false);
            $table->dateTime('LastUpdated')->nullable();
            $table->timestamps();
        });
    }

    if (!Schema::hasTable('campaign_store')) {
        Schema::create('campaign_store', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('campaign_id');
            $table->integer('store_id');
            $table->timestamps();
        });
    }

    if (!Schema::hasTable('campaign_agreements')) {
        Schema::create('campaign_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('campaign_id');
            $table->foreignUuid('agreement_id');
            $table->timestamps();
        });
    }

    // 2. Estados
    $this->draftStatus = Status::firstOrCreate(['status' => CampaignStatus::DRAFT->value]);
    $this->activeStatus = Status::firstOrCreate(['status' => CampaignStatus::ACTIVE->value]);
    $this->finishedStatus = Status::firstOrCreate(['status' => CampaignStatus::FINISHED->value]);
    $this->cancelledStatus = Status::firstOrCreate(['status' => CampaignStatus::CANCELLED->value]);

    // 3. Usuario y Roles
    $this->admin = User::factory()->create(['email_verified_at' => now()]);
    $role = Role::firstOrCreate(['name' => 'admin']);
    
    // --> PERMISOS EXACTOS SEGÚN TU ARCHIVO DE RUTAS <--
    $permissions = [
        'campaign.history.view',     // Para ver el historial y detalles
        'campaign.history.restore',  // Para restaurar
        'campaign.history.clone',    // Para clonar
        'campaign.history.calendar', // Para el calendario
        'campaign.edit',             // Necesario porque al clonar redirige a edit
        'campaign.index'             // Por si acaso redirige al index general
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }
    
    $role->syncPermissions($permissions);
    $this->admin->assignRole('admin');

    // 4. Datos Base
    $this->department = Department::factory()->create();
    
    // Store con forceCreate para evitar problemas de MassAssignment
    $this->store = Store::forceCreate([
        'ID' => 101,
        'Name' => 'TIENDA PRINCIPAL',
        'StoreCode' => 'T001',
        'Region' => 'GROUP-CAPITAL',
        'Inactive' => false
    ]);

    $this->actingAs($this->admin);
});

describe('Visualización del Historial (Index)', function () {

    test('muestra campañas finalizadas y eliminadas en el listado ordenadas por fecha', function () {
        Campaign::factory()->create([
            'title' => 'Campaña Finalizada 2025',
            'status_id' => $this->finishedStatus->id,
            'created_at' => now()->subDay(),
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        $deletedCampaign = Campaign::factory()->create([
            'title' => 'Campaña Eliminada X',
            'status_id' => $this->draftStatus->id,
            'created_at' => now(), 
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);
        $deletedCampaign->delete(); 

        Campaign::factory()->create([
            'title' => 'Campaña Activa Invisible',
            'status_id' => $this->activeStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        $this->get(route('campaignsHistory.history'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('CampaignHistory/Index')
                ->has('campaigns.data', 2) 
                ->where('campaigns.data.0.title', 'Campaña Eliminada X')
                ->where('campaigns.data.1.title', 'Campaña Finalizada 2025')
                ->has('statuses')
            );
    });

});

describe('Filtros de Búsqueda', function () {

    test('filtra por rango de fechas (Inicio y Fin)', function () {
        Campaign::factory()->create([
            'title' => 'Campaña Target',
            'status_id' => $this->finishedStatus->id,
            'start_at' => '2026-05-10',
            'end_at' => '2026-05-20',
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        Campaign::factory()->create([
            'title' => 'Campaña Vieja',
            'status_id' => $this->finishedStatus->id,
            'start_at' => '2025-01-01',
            'end_at' => '2025-01-10',
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        $filters = [
            'started_at' => '2026-05-01',
            'ended_at' => '2026-06-01'
        ];

        $this->get(route('campaignsHistory.history', $filters))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('campaigns.data', 1)
                ->where('campaigns.data.0.title', 'Campaña Target')
            );
    });

    test('filtra solo campañas eliminadas (Papelera)', function () {
        $deleted = Campaign::factory()->create([
            'title' => 'Papelera Item', 
            'user_id' => $this->admin->id, 
            'updated_by' => $this->admin->id,
            'status_id' => $this->draftStatus->id 
        ]);
        $deleted->delete();

        Campaign::factory()->create([
            'title' => 'Campaña Normal', 
            'status_id' => $this->finishedStatus->id,
            'user_id' => $this->admin->id
        ]);

        $this->get(route('campaignsHistory.history', ['status' => 'deleted']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('campaigns.data', 1)
                ->where('campaigns.data.0.title', 'Papelera Item')
            );
    });

});

describe('Visualización de Detalles', function () {

    test('muestra detalle de campaña finalizada con relaciones', function () {
        $campaign = Campaign::factory()->create([
            'status_id' => $this->finishedStatus->id,
            'department_id' => $this->department->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);
        
        $campaign->stores()->attach($this->store->ID);

        $this->get(route('campaignsHistory.show', $campaign))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('CampaignHistory/Show')
                ->has('campaign', fn (Assert $prop) => $prop
                    ->where('id', $campaign->id)
                    ->has('department')
                    ->has('stores', 1) 
                    ->etc()
                )
            );
    });

});

describe('Acciones de Historial', function () {

    test('puede restaurar una campaña eliminada', function () {
        $campaign = Campaign::factory()->create([
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id,
            'status_id' => $this->draftStatus->id, 
        ]);
        $campaign->delete();

        $this->assertSoftDeleted('campaigns', ['id' => $campaign->id]);

        $this->from(route('campaignsHistory.history')) 
             ->post(route('campaignsHistory.restore', $campaign)) 
             ->assertRedirect(route('campaignsHistory.history'))
             ->assertSessionHas('success', 'Campaña restaurada al panel de campañas.');

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'deleted_at' => null
        ]);
    });

    test('puede clonar una campaña finalizada como borrador nuevo', function () {
        $original = Campaign::factory()->create([
            'title' => 'Campaña Original',
            'status_id' => $this->finishedStatus->id,
            'start_at' => '2020-01-01',
            'end_at' => '2020-01-10',
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);
        
        $original->stores()->attach($this->store->ID);

        $response = $this->post(route('campaignsHistory.clone', $original));
        
        $cloned = Campaign::where('title', 'Copia de Campaña Original')->first();
        
        $response->assertRedirect(route('campaign.edit', $cloned))
                 ->assertSessionHas('success', 'Campaña clonada. Revisa las fechas y tiendas.');

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Copia de Campaña Original',
            'status_id' => $this->draftStatus->id,
        ]);

        expect($cloned->start_at->isFuture())->toBeTrue();
        expect($cloned->stores)->toHaveCount(1);
    });

});

describe('Vista de Calendario', function () {

    test('renderiza el calendario con campañas activas del año actual', function () {
        Campaign::factory()->create([
            'title' => 'Evento Calendario',
            'status_id' => $this->activeStatus->id,
            'start_at' => now()->addWeek(),
            'end_at' => now()->addWeek()->addDay(),
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        Campaign::factory()->create([
            'title' => 'Evento Viejo',
            'status_id' => $this->finishedStatus->id,
            'start_at' => now(),
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        $this->get(route('calendar.show'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('CampaignHistory/Calendar')
                ->has('campaigns', 1)
                ->where('campaigns.0.title', 'Evento Calendario')
                ->has('stores')
            );
    });

});