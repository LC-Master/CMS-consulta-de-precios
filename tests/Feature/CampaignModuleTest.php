<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Store;
use App\Models\Department;
use App\Models\Media;
use App\Models\Status;
use App\Enums\CampaignStatus;
use App\Enums\Schedules;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
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

    if (!Schema::hasTable('Supplier')) {
        Schema::create('Supplier', function (Blueprint $table) {
            $table->string('ID')->primary();
            $table->string('SupplierName')->nullable();
            $table->string('AccountNumber')->nullable();
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

    if (!Schema::hasTable('time_line_items')) {
        Schema::create('time_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id');
            $table->foreignUuid('media_id');
            $table->string('slot');
            $table->integer('position');
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

    $this->draftStatus = Status::firstOrCreate(['status' => CampaignStatus::DRAFT->value]);
    $this->activeStatus = Status::firstOrCreate(['status' => CampaignStatus::ACTIVE->value]);
    $this->finishedStatus = Status::firstOrCreate(['status' => CampaignStatus::FINISHED->value]);
    $this->cancelledStatus = Status::firstOrCreate(['status' => CampaignStatus::CANCELLED->value]);

    $this->admin = User::factory()->create(['email_verified_at' => now()]);
    $role = Role::firstOrCreate(['name' => 'admin']);

    $permissions = [
        'campaign.list',
        'campaign.show',
        'campaign.create',
        'campaign.update',
        'campaign.delete',
        'campaign.activate',
        'campaign.cancel',
        'campaign.report'
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }
    
    $role->syncPermissions($permissions);
    $this->admin->assignRole('admin');

    $this->department = Department::factory()->create();
    
    $this->stores = collect();
    for ($i = 1; $i <= 3; $i++) {
        $this->stores->push(Store::forceCreate([
            'ID' => 100 + $i,
            'Name' => "TIENDA $i",
            'StoreCode' => "T00$i",
            'Region' => 'GROUP-TEST',
            'Inactive' => false
        ]));
    }

    $this->mediaItems = Media::factory()->count(2)->create([
        'created_by' => $this->admin->id
    ]);

    $this->actingAs($this->admin);
});

describe('Vistas y Formularios', function () {
    
    test('renderiza el listado de campañas (Index)', function () {
        Campaign::factory()->create([
            'status_id' => $this->activeStatus->id,
            'department_id' => $this->department->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id,
            'title' => 'Campaña Verano'
        ]);

        $this->get(route('campaign.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Campaign/Index')
                ->has('campaigns.data', 1)
                ->where('campaigns.data.0.title', 'Campaña Verano')
                ->has('statuses')
            );
    });

    test('renderiza el formulario de creación con props', function () {
        $this->get(route('campaign.create'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Campaign/Create')
                ->has('media')
                ->has('stores')
                ->has('departments')
            );
    });

    test('renderiza el detalle de una campaña cargando todas sus relaciones (Show)', function () {

        DB::table('Supplier')->insert([
            'ID' => 'SUP-TEST',
            'SupplierName' => 'Proveedor Test',
            'AccountNumber' => '123456',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $agreement = \App\Models\Agreement::factory()->create(['supplier_id' => 'SUP-TEST']);
        
        $campaign = Campaign::factory()->create([
            'department_id' => $this->department->id,
            'status_id' => $this->activeStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $campaign->agreements()->attach($agreement->id);
        $campaign->stores()->attach($this->stores->pluck('ID'));

        $this->get(route('campaign.show', $campaign))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Campaign/Show')
                ->has('campaign', fn (Assert $prop) => $prop
                    ->where('id', $campaign->id)
                    ->where('title', $campaign->title)
                    ->has('department')       
                    ->has('status')          
                    ->has('stores', 3)       
                    ->where('agreements.0.id', $agreement->id) 
                    ->where('agreements.0.name', $agreement->name)
                    ->etc()
                )
            );
    });
});

describe('Lógica de Creación', function () {
    
    test('registra una campaña completa con centros', function () {
        $postData = [
            'title' => 'Nueva Campaña 2026',
            'start_at' => now()->addDay()->format('Y-m-d'),
            'end_at' => now()->addDays(10)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'agreements' => null,
            'stores' => [(string) $this->stores[0]->ID],
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[1]->id],
        ];

        $this->post(route('campaign.store'), $postData)
            ->assertRedirect(route('campaign.index'))
            ->assertSessionHas('success', 'Campaña creada correctamente.');

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Nueva Campaña 2026',
            'status_id' => $this->draftStatus->id,
        ]);

        $campaign = Campaign::where('title', 'Nueva Campaña 2026')->first();
        expect($campaign->stores)->toHaveCount(1);

        $this->assertDatabaseHas('time_line_items', [
            'campaign_id' => $campaign->id,
            'media_id' => $this->mediaItems[0]->id,
            'slot' => Schedules::AM->value,
        ]);

        $this->assertDatabaseHas('time_line_items', [
            'campaign_id' => $campaign->id,
            'media_id' => $this->mediaItems[1]->id,
            'slot' => Schedules::PM->value,
        ]);
    });

    test('falla validación si la fecha fin es anterior al inicio', function () {
        $postData = [
            'title' => 'Campaña Erronea',
            'department_id' => $this->department->id,
            'stores' => [(string) $this->stores[0]->id],
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[0]->id],
            'start_at' => now()->addDays(5)->format('Y-m-d'),
            'end_at' => now()->addDays(1)->format('Y-m-d'), 
        ];

        $response = $this->post(route('campaign.store'), $postData);

        $response->assertSessionHasErrors([
            'end_at' => 'La fecha de finalización debe ser posterior a la fecha de inicio.'
        ]);
    });
});

describe('Reglas de Negocio y Experiencia de Usuario', function () {

    test('respeta el orden exacto (posición) de los medios seleccionados', function () {
        $media1 = Media::factory()->create(['name' => 'Video 1.mp4', 'created_by' => $this->admin->id]);
        $media2 = Media::factory()->create(['name' => 'Video 2.mp4', 'created_by' => $this->admin->id]);
        $media3 = Media::factory()->create(['name' => 'Video 3.mp4', 'created_by' => $this->admin->id]);
    
        $postData = [
            'title' => 'Campaña Ordenada',
            'start_at' => now()->format('Y-m-d'),
            'end_at' => now()->addDays(5)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'stores' => [(string) $this->stores[0]->ID],
            
            'am_media' => [$media2->id, $media3->id, $media1->id],
            'pm_media' => [$media1->id], 
        ];
    
        $this->post(route('campaign.store'), $postData);
    
        $campaign = Campaign::where('title', 'Campaña Ordenada')->first();
    
        $this->assertDatabaseHas('time_line_items', [
            'campaign_id' => $campaign->id,
            'media_id' => $media2->id,
            'slot' => Schedules::AM->value,
            'position' => 1, 
        ]);
    
        $this->assertDatabaseHas('time_line_items', [
            'campaign_id' => $campaign->id,
            'media_id' => $media3->id,
            'slot' => Schedules::AM->value,
            'position' => 2, 
        ]);
        
        $this->assertDatabaseHas('time_line_items', [
            'campaign_id' => $campaign->id,
            'media_id' => $media1->id,
            'slot' => Schedules::AM->value,
            'position' => 3, 
        ]);
    });

    test('permite agregar el mismo video varias veces en el mismo bloque', function () {
        $promoVideo = Media::factory()->create(['name' => 'Promo.mp4', 'created_by' => $this->admin->id]);
        $contentVideo = Media::factory()->create(['name' => 'Content.mp4', 'created_by' => $this->admin->id]);
    
        $postData = [
            'title' => 'Campaña Repetitiva',
            'start_at' => now()->format('Y-m-d'),
            'end_at' => now()->addDays(5)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'stores' => [(string) $this->stores[0]->ID],
            
            'am_media' => [$promoVideo->id, $contentVideo->id, $promoVideo->id],
            'pm_media' => [$contentVideo->id],
        ];
    
        $this->post(route('campaign.store'), $postData)
            ->assertSessionHasNoErrors();
    
        $campaign = Campaign::where('title', 'Campaña Repetitiva')->first();
        
        $count = \Illuminate\Support\Facades\DB::table('time_line_items')
            ->where('campaign_id', $campaign->id)
            ->where('media_id', $promoVideo->id)
            ->where('slot', Schedules::AM->value)
            ->count();
    
        expect($count)->toBe(2);
    });

    test('puede crear una campaña sin seleccionar un convenio (Campo opcional)', function () {
        $postData = [
            'title' => 'Campaña Sin Convenio',
            'start_at' => now()->format('Y-m-d'),
            'end_at' => now()->addDays(5)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'stores' => [(string) $this->stores[0]->ID],
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[0]->id],
            
            'agreements' => null, 
        ];
    
        $this->post(route('campaign.store'), $postData)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('campaign.index'));
    
        $this->assertDatabaseHas('campaigns', [
            'title' => 'Campaña Sin Convenio',
        ]);
        
        $campaign = Campaign::where('title', 'Campaña Sin Convenio')->first();
        expect($campaign->agreements)->toHaveCount(0);
    });

    test('asigna correctamente múltiples centros a la campaña', function () {

        $selectedStoreIds = $this->stores->pluck('ID')->map(fn($id) => (string)$id)->toArray();
    
        $postData = [
            'title' => 'Campaña Multicentro',
            'start_at' => now()->format('Y-m-d'),
            'end_at' => now()->addDays(5)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[0]->id],
            
            'stores' => $selectedStoreIds,
        ];
    
        $this->post(route('campaign.store'), $postData);
    
        $campaign = Campaign::where('title', 'Campaña Multicentro')->first();
    
        expect($campaign->stores)->toHaveCount(3);
        
        $campaignStoreIds = $campaign->stores->pluck('ID')->map(fn($id) => (string)$id)->toArray();
        expect($campaignStoreIds)->toEqualCanonicalizing($selectedStoreIds);
    });

});

describe('Lógica de Actualización', function () {
    
    test('actualiza correctamente una campaña', function () {
        $campaign = Campaign::factory()->create([
            'department_id' => $this->department->id,
            'status_id' => $this->draftStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $updateData = [
            'title' => 'Campaña Actualizada',
            'start_at' => now()->addDay()->format('Y-m-d'),
            'end_at' => now()->addYear()->format('Y-m-d'),
            'department_id' => $this->department->id,
            'stores' => [(string) $this->stores[0]->ID],
            'am_media' => [$this->mediaItems[1]->id],
            'pm_media' => [$this->mediaItems[0]->id],
        ];

        $this->put(route('campaign.update', $campaign), $updateData)
            ->assertRedirect(route('campaign.index'))
            ->assertSessionHas('success', 'Campaña actualizada correctamente.');

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'title' => 'Campaña Actualizada',
        ]);

        $this->assertDatabaseHas('time_line_items', [
            'campaign_id' => $campaign->id,
            'media_id' => $this->mediaItems[1]->id,
            'slot' => Schedules::AM->value,
        ]);
    });
});

describe('Gestión de Estados', function () {
    
    test('cambia el estado a ACTIVO', function () {
        $campaign = Campaign::factory()->create([
            'status_id' => $this->draftStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        $url = route('campaign.index');

        $this->from($url)
            ->get(route('campaign.activate', $campaign))
            ->assertRedirect($url)
            ->assertSessionHas('success', 'Campaña activada correctamente.');

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'status_id' => $this->activeStatus->id
        ]);
    });

    test('cambia el estado a CANCELADO', function () {
        $campaign = Campaign::factory()->create([
            'status_id' => $this->activeStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        $this->get(route('campaign.cancel', $campaign))
            ->assertRedirect(route('campaign.index'))
            ->assertSessionHas('success', 'Campaña cancelada.');

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'status_id' => $this->cancelledStatus->id
        ]);
    });
});

describe('Eliminación', function () {
    
    test('borra la campaña y notifica a los administradores', function () {
        $campaign = Campaign::factory()->create([
            'user_id' => $this->admin->id,
            'status_id' => $this->draftStatus->id,
            'updated_by' => $this->admin->id
        ]);

        $this->delete(route('campaign.destroy', $campaign))
            ->assertRedirect(route('campaign.index'))
            ->assertSessionHas('success', 'Campaña inhabilitada.');

        $this->assertSoftDeleted('campaigns', [
            'id' => $campaign->id
        ]);
    });
});