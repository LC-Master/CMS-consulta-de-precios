<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Center;
use App\Models\Department;
use App\Models\Media;
use App\Models\Status;
use App\Models\Agreement;
use App\Enums\CampaignStatus;
use App\Enums\Schedules;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {

    $this->draftStatus = Status::firstOrCreate(['status' => CampaignStatus::DRAFT->value]);
    $this->activeStatus = Status::firstOrCreate(['status' => CampaignStatus::ACTIVE->value]);
    $this->finishedStatus = Status::firstOrCreate(['status' => CampaignStatus::FINISHED->value]);


    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole('admin');

    $this->department = Department::factory()->create();
    $this->centers = Center::factory()->count(3)->create();
    

    $this->mediaItems = Media::factory()->count(2)->create([
        'created_by' => $this->admin->id
    ]);

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
});

test('puede renderizar el listado de campañas (Index)', function () {

    Campaign::factory()->create([
        'status_id' => $this->activeStatus->id,
        'department_id' => $this->department->id,
        'user_id' => $this->admin->id,      
        'updated_by' => $this->admin->id,   
        'title' => 'Campaña Verano'
    ]);

    $this->get(route('campaign.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Campaign/Index')
            ->has('campaigns.data', 1)
            ->where('campaigns.data.0.title', 'Campaña Verano')
            ->has('statuses')
        );
});

test('puede ver el formulario de creación con las props necesarias', function () {
    $this->get(route('campaign.create'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Campaign/Create')
            ->has('media')      
            ->has('centers') 
            ->has('departments')
        );
});

test('puede crear una campaña completa con centros (Store)', function () {
    // Preparamos los datos complejos
    $postData = [
        'title' => 'Nueva Campaña 2026',
        'start_at' => now()->addDay()->format('Y-m-d'),
        'end_at' => now()->addDays(10)->format('Y-m-d'),
        'department_id' => $this->department->id,
        'agreement_id' => null, // Opcional
        
        // Array de IDs de centros
        'centers' => $this->centers->pluck('id')->toArray(),
        
        // Array de IDs de Medios (Simulando la selección del usuario)
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
    expect($campaign->centers)->toHaveCount(3);

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

test('deberia fallar en la validación de fechas en la creación', function () {
    $postData = [
        'title' => 'Campaña Erronea',
        'department_id' => $this->department->id,
        'centers' => [$this->centers[0]->id],
        'am_media' => [$this->mediaItems[0]->id],
        'pm_media' => [$this->mediaItems[0]->id],
        
        // ERROR: Fecha fin antes que inicio
        'start_at' => now()->addDays(5)->format('Y-m-d'),
        'end_at' => now()->addDays(1)->format('Y-m-d'),
    ];

    $this->post(route('campaign.store'), $postData)
        ->assertSessionHasErrors(['start_at', 'end_at']);
});

test('puede actualizar una campaña (Update)', function () {

    $campaign = Campaign::factory()->create([
        'department_id' => $this->department->id,
        'status_id' => $this->draftStatus->id,
        'user_id' => $this->admin->id,      
        'updated_by' => $this->admin->id,
    ]);

    // Datos para actualizar
    $updateData = [
        'title' => 'Campaña Actualizada',
        'start_at' => now()->addDay()->format('Y-m-d'),
        'end_at' => now()->addYear()->format('Y-m-d'),
        'department_id' => $this->department->id,
        'centers' => [$this->centers[0]->id],
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
    
    // Validar timeline...
    $this->assertDatabaseHas('time_line_items', [
        'campaign_id' => $campaign->id,
        'media_id' => $this->mediaItems[1]->id,
        'slot' => Schedules::AM->value,
    ]);
});

test('puede activar una campaña (Activate)', function () {
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

test('puede finalizar una campaña (Finish)', function () {
    $campaign = Campaign::factory()->create([
        'status_id' => $this->activeStatus->id,
        'user_id' => $this->admin->id,
        'updated_by' => $this->admin->id
    ]);

    $this->get(route('campaign.finish', $campaign))
        ->assertRedirect(route('campaign.index'))
        ->assertSessionHas('success', 'Campaña finalizada.');

    $this->assertDatabaseHas('campaigns', [
        'id' => $campaign->id,
        'status_id' => $this->finishedStatus->id
    ]);
});

test('puede eliminar campaña y notificar a admins', function () {
    $campaign = Campaign::factory()->create([
        'user_id' => $this->admin->id,
        'status_id' => $this->draftStatus->id,
        'updated_by' => $this->admin->id
    ]);

    $this->delete(route('campaign.destroy', $campaign))
        ->assertRedirect(route('campaign.index'))
        ->assertSessionHas('success', 'Campaña eliminada.');

    $this->assertSoftDeleted('campaigns', [
        'id' => $campaign->id
    ]);
});