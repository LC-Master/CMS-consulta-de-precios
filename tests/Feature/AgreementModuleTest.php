<?php

use App\Models\User;
use App\Models\Agreement;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole('admin');

    // Simulamos autenticación y contraseña confirmada
    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
});

describe('Vistas y Navegación', function () {

    test('renderiza la lista de convenios (Index)', function () {
        Agreement::factory()->count(3)->create();

        $this->get(route('agreement.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Index')
                ->has('agreements.data', 3) 
                ->has('statuses') 
            );
    });

    test('filtra convenios por nombre o RIF', function () {
        Agreement::factory()->create(['name' => 'Empresa Alpha', 'tax_id' => 'J-11111111']);
        Agreement::factory()->create(['name' => 'Empresa Beta', 'tax_id' => 'J-22222222']);

        // Búsqueda por Nombre
        $this->get(route('agreement.index', ['search' => 'Alpha']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('agreements.data', 1)
                ->where('agreements.data.0.name', 'Empresa Alpha')
            );

        // Búsqueda por RIF
        $this->get(route('agreement.index', ['search' => '22222222']))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->has('agreements.data', 1)
                ->where('agreements.data.0.tax_id', 'J-22222222')
            );
    });

    test('renderiza el formulario de creación', function () {
        $this->get(route('agreement.create'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Create')
            );
    });

    test('muestra los detalles de un convenio (Show)', function () {
        $agreement = Agreement::factory()->create();
    
        $this->get(route('agreement.show', $agreement))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Show')
                ->where('agreement.name', $agreement->name)
            );
    });
    
    test('renderiza el formulario de edición', function () {
        $agreement = Agreement::factory()->create();
    
        $this->get(route('agreement.edit', $agreement))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Edit')
                ->where('agreement.id', $agreement->id)
            );
    });
});

describe('Lógica de Creación', function () {

    test('registra un nuevo convenio correctamente', function () {
        $agreementData = [
            'name' => 'Convenio 2026',
            'legal_name' => 'Soluciones C.A.',
            'tax_id' => 'J-123456789',
            'contact_person' => 'Carlos Gerente',
            'contact_email' => 'gerencia@gmail.com',
            'contact_phone' => '04141234567',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'observations' => 'Sin observaciones',
        ];
    
        $this->post(route('agreement.store'), $agreementData)
            ->assertRedirect(route('agreement.index'))
            ->assertSessionHas('success', 'Convenio creado correctamente.');
    
        // Verificar BD
        $this->assertDatabaseHas('agreements', [
            'name' => 'Convenio 2026',
            'tax_id' => 'J-123456789',
            'is_active' => 1,
        ]);
    });

    test('valida incoherencia de fechas (Inicio > Fin)', function () {
        $agreementData = Agreement::factory()->raw([
            'start_date' => '2026-12-31', 
            'end_date' => '2026-01-01',   
        ]);
    
        $this->post(route('agreement.store'), $agreementData)
            ->assertSessionHasErrors(['start_date', 'end_date']);
    });
    
    test('valida duplicidad de RIF', function () {
        Agreement::factory()->create(['tax_id' => 'J-DUPLICADO']);
    
        $newData = Agreement::factory()->raw([
            'tax_id' => 'J-DUPLICADO',
            'start_date' => now()->format('Y-m-d'), 
            'end_date' => now()->addYear()->format('Y-m-d'), 
        ]);
    
        $this->post(route('agreement.store'), $newData)
            ->assertSessionHasErrors(['tax_id']);
    });

});

describe('Lógica de Actualización', function () {

    test('actualiza un convenio correctamente', function () {
        $agreement = Agreement::factory()->create([
            'name' => 'Nombre Viejo',
            'is_active' => true
        ]);
    
        // Preparamos los datos
        $updateData = [
            'name' => 'Nombre Editado',
            'legal_name' => $agreement->legal_name,
            'tax_id' => $agreement->tax_id,
            'contact_person' => $agreement->contact_person,
            'contact_email' => $agreement->contact_email,
            'contact_phone' => $agreement->contact_phone,
    
            'start_date' => $agreement->start_date->format('Y-m-d'),
            'end_date' => $agreement->end_date->format('Y-m-d'),
            
            'observations' => 'Actualizado',
            'is_active' => false,
        ];
    
        $this->put(route('agreement.update', $agreement), $updateData)
            ->assertRedirect(route('agreement.index'))
            ->assertSessionHas('success', 'Acuerdo actualizado correctamente.');
    
        $this->assertDatabaseHas('agreements', [
            'id' => $agreement->id,
            'name' => 'Nombre Editado',
            'is_active' => 0,
        ]);
    });
    
    test('permite mantener el mismo RIF propio al actualizar', function () {
        $agreement = Agreement::factory()->create(['tax_id' => 'J-PROPIO']);
    
        $updateData = $agreement->toArray();
        
        $updateData['name'] = 'Nombre Nuevo Solamente';
        $updateData['is_active'] = true;
        
        $updateData['start_date'] = $agreement->start_date->format('Y-m-d');
        $updateData['end_date'] = $agreement->end_date->format('Y-m-d');
    
        $this->put(route('agreement.update', $agreement), $updateData)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('agreement.index'));
    });

});

describe('Eliminación', function () {

    test('realiza borrado suave (Soft Delete) del convenio', function () {
        $agreement = Agreement::factory()->create();
    
        $this->delete(route('agreement.destroy', $agreement))
            ->assertRedirect(route('agreement.index'))
            ->assertSessionHas('success', 'Convenio eliminado correctamente.');
    
        $this->assertSoftDeleted('agreements', [
            'id' => $agreement->id
        ]);
    });

});

describe('Control de Acceso', function () {

    test('bloquea acceso a usuarios no autenticados', function () {
        auth()->logout();
    
        $this->get(route('agreement.index'))
            ->assertRedirect(route('login'));
    });

});