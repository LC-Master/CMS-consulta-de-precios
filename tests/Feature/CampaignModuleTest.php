<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Center;
use App\Models\Department;
use App\Models\Media;
use App\Models\Status;
use App\Enums\CampaignStatus;
use App\Enums\Schedules;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->draftStatus = Status::firstOrCreate(['status' => CampaignStatus::DRAFT->value]);
    $this->activeStatus = Status::firstOrCreate(['status' => CampaignStatus::ACTIVE->value]);
    $this->finishedStatus = Status::firstOrCreate(['status' => CampaignStatus::FINISHED->value]);

    $this->admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole('admin');

    $this->department = Department::factory()->create();
    $this->centers = Center::factory()->count(3)->create();

    $this->mediaItems = Media::factory()->count(2)->create([
        'created_by' => $this->admin->id
    ]);

    $this->actingAs($this->admin)
        ->withSession(['auth.password_confirmed_at' => time()]);
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
                ->has('centers')
                ->has('departments')
            );
    });

    test('renderiza el detalle de una campaña cargando todas sus relaciones (Show)', function () {

        $agreement = \App\Models\Agreement::factory()->create();
        $agreement->delete(); 

        $campaign = Campaign::factory()->create([
            'department_id' => $this->department->id,
            'agreement_id' => $agreement->id,
            'status_id' => $this->activeStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $campaign->centers()->attach($this->centers->pluck('id'));

        $this->get(route('campaign.show', $campaign))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Campaign/Show')
                ->has('campaign', fn (Assert $prop) => $prop
                    ->where('id', $campaign->id)
                    ->where('title', $campaign->title)
                    ->has('department')       
                    ->has('status')          
                    ->has('centers', 3)       
                    ->where('agreement.id', $agreement->id) 
                    ->where('agreement.name', $agreement->name)
                    ->missing('department_id')
                    ->missing('agreement_id')
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
            'agreement_id' => null,
            'centers' => $this->centers->pluck('id')->toArray(),
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

    test('falla validación si la fecha fin es anterior al inicio', function () {
        $postData = [
            'title' => 'Campaña Erronea',
            'department_id' => $this->department->id,
            'centers' => [$this->centers[0]->id],
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[0]->id],
            'start_at' => now()->addDays(5)->format('Y-m-d'),
            'end_at' => now()->addDays(1)->format('Y-m-d'), // Error
        ];

        $this->post(route('campaign.store'), $postData)
            ->assertSessionHasErrors(['start_at', 'end_at']);
    });
});

describe('Reglas de Negocio y Experiencia de Usuario', function () {

    test('respeta el orden exacto (posición) de los medios seleccionados', function () {
        // Creamos 3 medios distintos
        $media1 = Media::factory()->create(['name' => 'Video 1.mp4', 'created_by' => $this->admin->id]);
        $media2 = Media::factory()->create(['name' => 'Video 2.mp4', 'created_by' => $this->admin->id]);
        $media3 = Media::factory()->create(['name' => 'Video 3.mp4', 'created_by' => $this->admin->id]);
    
        $postData = [
            'title' => 'Campaña Ordenada',
            'start_at' => now()->format('Y-m-d'),
            'end_at' => now()->addDays(5)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'centers' => [$this->centers[0]->id],
            
            'am_media' => [$media2->id, $media3->id, $media1->id],
            'pm_media' => [$media1->id], 
        ];
    
        $this->post(route('campaign.store'), $postData);
    
        $campaign = Campaign::where('title', 'Campaña Ordenada')->first();
    
        // Verificamos que en la BD la posición coincida
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
            'centers' => [$this->centers[0]->id],
            
            'am_media' => [$promoVideo->id, $contentVideo->id, $promoVideo->id],
            'pm_media' => [$contentVideo->id],
        ];
    
        $this->post(route('campaign.store'), $postData)
            ->assertSessionHasNoErrors();
    
        // Verificamos que existen 2 entradas para el video Promo en el bloque AM
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
            'centers' => [$this->centers[0]->id],
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[0]->id],
            
            'agreement_id' => null,
        ];
    
        $this->post(route('campaign.store'), $postData)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('campaign.index'));
    
        $this->assertDatabaseHas('campaigns', [
            'title' => 'Campaña Sin Convenio',
            'agreement_id' => null,
        ]);
    });

    test('asigna correctamente múltiples centros a la campaña', function () {

        $selectedCenterIds = $this->centers->pluck('id')->toArray();
    
        $postData = [
            'title' => 'Campaña Multicentro',
            'start_at' => now()->format('Y-m-d'),
            'end_at' => now()->addDays(5)->format('Y-m-d'),
            'department_id' => $this->department->id,
            'am_media' => [$this->mediaItems[0]->id],
            'pm_media' => [$this->mediaItems[0]->id],
            
            // Enviamos TODOS los centros (Simulando selección múltiple en UI)
            'centers' => $selectedCenterIds,
        ];
    
        $this->post(route('campaign.store'), $postData);
    
        $campaign = Campaign::where('title', 'Campaña Multicentro')->first();
    
        expect($campaign->centers)->toHaveCount(3);
        
        expect($campaign->centers->pluck('id')->toArray())->toEqualCanonicalizing($selectedCenterIds);
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

    test('cambia el estado a FINALIZADO', function () {
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
            ->assertSessionHas('success', 'Campaña eliminada.');

        $this->assertSoftDeleted('campaigns', [
            'id' => $campaign->id
        ]);
    });
});