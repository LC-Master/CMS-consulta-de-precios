<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Center;
use App\Models\Department;
use App\Models\Status;
use App\Enums\CampaignStatus;
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
    $this->center = Center::factory()->create();

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
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
            ->where('campaigns.data.0.title', 'Campaña Eliminada X') // La más reciente primero
            ->where('campaigns.data.1.title', 'Campaña Finalizada 2025') // La antigua después
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
        $deleted = Campaign::factory()->create(['title' => 'Papelera Item', 'user_id' => $this->admin->id, 'updated_by' => $this->admin->id]);
        $deleted->delete();

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
        
        $campaign->centers()->attach($this->center->id);

        $this->get(route('campaignsHistory.show', $campaign))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('CampaignHistory/Show')
                ->has('campaign', fn (Assert $prop) => $prop
                    ->where('id', $campaign->id)
                    ->has('department')
                    ->has('centers', 1)
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

        $urlHistorial = route('campaignsHistory.history');

        $this->from($urlHistorial) 
             ->post(route('campaignsHistory.restore', $campaign)) 
             ->assertRedirect($urlHistorial)
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
        
        $original->centers()->attach($this->center->id);

        $this->post(route('campaignsHistory.clone', $original))
            ->assertSessionHas('success', 'Campaña clonada. Revisa las fechas.');

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Copia de Campaña Original',
            'status_id' => $this->draftStatus->id,
        ]);

        $cloned = Campaign::where('title', 'Copia de Campaña Original')->first();
        expect($cloned->start_at->isFuture())->toBeTrue();
        expect($cloned->centers)->toHaveCount(1);
    });

});

describe('Vista de Calendario', function () {

    test('renderiza el calendario con campañas activas del año actual', function () {
        Campaign::factory()->create([
            'title' => 'Evento Calendario',
            'status_id' => $this->activeStatus->id,
            'start_at' => now()->startOfYear()->addMonth(),
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
            );
    });

});
