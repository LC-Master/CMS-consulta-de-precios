<?php

use App\Models\User;
use App\Models\Media;
use App\Models\Campaign;
use App\Models\Status;
use App\Enums\CampaignStatus;
use App\Enums\Schedules;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('public');

    $this->admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole('admin');

    $this->draftStatus = Status::firstOrCreate(['status' => CampaignStatus::DRAFT->value]);
    $this->activeStatus = Status::firstOrCreate(['status' => CampaignStatus::ACTIVE->value]);
    $this->finishedStatus = Status::firstOrCreate(['status' => CampaignStatus::FINISHED->value]);

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
});

describe('Visualización y Búsqueda', function () {

    test('renderiza la lista de archivos con sus campañas asociadas', function () {
        Media::factory()->count(3)->create(['created_by' => $this->admin->id]);

        $this->get(route('media.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Media/Index')
                ->has('medias.data', 3)
                ->has('mimeTypes')
            );
    });

    test('puede buscar media por su NOMBRE de archivo', function () {
        Media::factory()->create(['name' => 'Video_Objetivo.mp4', 'created_by' => $this->admin->id]);
        Media::factory()->create(['name' => 'Otro_Archivo.jpg', 'created_by' => $this->admin->id]);

        $this->get(route('media.index', ['search' => 'Objetivo']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('medias.data', 1)
                ->where('medias.data.0.name', 'Video_Objetivo.mp4')
            );
    });

    test('puede buscar media por el nombre de la CAMPAÑA asociada', function () {
        $media = Media::factory()->create(['name' => 'Video_Generico.mp4', 'created_by' => $this->admin->id]);
        
        $campaign = Campaign::factory()->create([
            'title' => 'Campaña Navidad 2026',
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id,
            'status_id' => $this->activeStatus->id, 
        ]);

        DB::table('time_line_items')->insert([
            'id' => Str::uuid(),
            'campaign_id' => $campaign->id,
            'media_id' => $media->id,
            'slot' => Schedules::AM->value,
            'position' => 1
        ]);

        Media::factory()->create(['name' => 'Video_Verano.mp4', 'created_by' => $this->admin->id]);

        $this->get(route('media.index', ['search' => 'Navidad']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('medias.data', 1)
                ->where('medias.data.0.id', $media->id)
            );
    });

    test('filtra por tipo de archivo (Mime Type)', function () {
        Media::factory()->create(['mime_type' => 'video/mp4', 'created_by' => $this->admin->id]);
        Media::factory()->create(['mime_type' => 'image/png', 'created_by' => $this->admin->id]);

        $this->get(route('media.index', ['type' => 'image/png']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('medias.data', 1)
                ->where('medias.data.0.mime_type', 'image/png')
            );
    });

});

describe('Detalles del Archivo', function () {

    test('muestra el detalle del media y carga campañas asociadas', function () {
        $media = Media::factory()->create(['created_by' => $this->admin->id]);
        
        $campaign = Campaign::factory()->create([
            'status_id' => $this->activeStatus->id,
            'title' => 'Campaña Activa',
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);
        
        DB::table('time_line_items')->insert([
            'id' => Str::uuid(),
            'campaign_id' => $campaign->id,
            'media_id' => $media->id,
            'slot' => Schedules::AM->value,
            'position' => 1
        ]);

        $this->get(route('media.show', $media))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Media/Show')
                ->where('media.id', $media->id)
                ->has('media.campaigns')
            );
    });
    
    test('permite previsualizar el archivo físico', function () {

        $fileName = 'archivo_prueba.txt';
        $path = 'uploads/' . $fileName;
        
        Storage::disk('public')->put($path, 'Contenido de prueba para validar descarga');

        $media = Media::factory()->create([
            'path' => $path,
            'disk' => 'public',
            'mime_type' => 'text/plain',
            'created_by' => $this->admin->id
        ]);

        $this->get('/media/cdn/' . $media->id)
            ->assertStatus(200)
            ->assertHeader('content-type', 'text/plain; charset=utf-8'); 
    });

});

describe('Eliminación y Restricciones', function () {

    test('BLOQUEA eliminación si el media está en una campaña ACTIVA', function () {
        $media = Media::factory()->create(['created_by' => $this->admin->id]);
        
        $campaign = Campaign::factory()->create([
            'status_id' => $this->activeStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        DB::table('time_line_items')->insert([
            'id' => Str::uuid(),
            'campaign_id' => $campaign->id,
            'media_id' => $media->id,
            'slot' => Schedules::AM->value,
            'position' => 1
        ]);

        $this->delete(route('media.destroy', $media))
            ->assertSessionHas('error'); 
            
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    });

    test('BLOQUEA eliminación si el media está en un BORRADOR', function () {
        $media = Media::factory()->create(['created_by' => $this->admin->id]);
        
        $campaign = Campaign::factory()->create([
            'status_id' => $this->draftStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        DB::table('time_line_items')->insert([
            'id' => Str::uuid(),
            'campaign_id' => $campaign->id,
            'media_id' => $media->id,
            'slot' => Schedules::AM->value,
            'position' => 1
        ]);

        $this->delete(route('media.destroy', $media))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    });

    test('PERMITE eliminación si solo está en campañas FINALIZADAS o ELIMINADAS', function () {
        $media = Media::factory()->create(['created_by' => $this->admin->id]);
        
        $campaign = Campaign::factory()->create([
            'status_id' => $this->finishedStatus->id,
            'user_id' => $this->admin->id,
            'updated_by' => $this->admin->id
        ]);

        DB::table('time_line_items')->insert([
            'id' => Str::uuid(),
            'campaign_id' => $campaign->id,
            'media_id' => $media->id,
            'slot' => Schedules::AM->value,
            'position' => 1
        ]);

        $this->delete(route('media.destroy', $media))
            ->assertSessionHas('success', 'Archivo eliminado correctamente.');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    });

    test('PERMITE eliminación si el media no está asignado a nada', function () {
        $media = Media::factory()->create(['created_by' => $this->admin->id]);

        $this->delete(route('media.destroy', $media))
            ->assertSessionHas('success', 'Archivo eliminado correctamente.');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    });

});