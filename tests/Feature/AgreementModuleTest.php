<?php

use App\Models\User;
use App\Models\Agreement;
use App\Models\Supplier;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {

    if (!Schema::hasTable('Supplier')) {
        Schema::create('Supplier', function (Blueprint $table) {
            $table->string('ID')->primary();
            $table->string('SupplierName')->nullable();
            $table->string('AccountNumber')->nullable();
            $table->string('ContactName')->nullable();
            $table->string('EmailAddress')->nullable();
            $table->string('PhoneNumber')->nullable();
            $table->text('Notes')->nullable();
            $table->dateTime('LastUpdated')->nullable();
            $table->timestamps();
        });
    }


    Supplier::create([
        'ID' => 'SUP-001',
        'SupplierName' => 'PROVEEDOR TEST',
        'AccountNumber' => 'J-00000000',
        'ContactName' => 'Contacto Test',
        'EmailAddress' => 'test@proveedor.com',
        'PhoneNumber' => '0414000000',
        'Notes' => 'Nota de prueba'
    ]);


    $this->admin = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $role = Role::firstOrCreate(['name' => 'admin']);
    
    $permissions = [
        'agreement.list',
        'agreement.show',
        'agreement.create',
        'agreement.update',
        'agreement.delete'
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    $role->syncPermissions($permissions);
    $this->admin->assignRole('admin');

    $this->actingAs($this->admin)
         ->withSession(['auth.password_confirmed_at' => time()]);
});

describe('Vistas y Navegación', function () {

    test('renderiza la lista de acuerdos comerciales (Index)', function () {
        Agreement::factory()->count(3)->create([
            'supplier_id' => 'SUP-001'
        ]);

        $this->get(route('agreement.index'))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Index')
                ->has('agreements.data', 3) 
                ->has('statuses') 
            );
    });

    test('filtra acuerdos por nombre o RIF', function () {
        Agreement::factory()->create(['name' => 'Empresa Alpha', 'tax_id' => 'J-11111111', 'supplier_id' => 'SUP-001']);
        Agreement::factory()->create(['name' => 'Empresa Beta', 'tax_id' => 'J-22222222', 'supplier_id' => 'SUP-001']);

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
                ->has('defaultSuppliers')
            );
    });

    test('muestra los detalles de un acuerdo (Show)', function () {
        $agreement = Agreement::factory()->create(['supplier_id' => 'SUP-001']);
    
        $this->get(route('agreement.show', $agreement))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Show')
                ->where('agreement.name', $agreement->name)
            );
    });
    
    test('renderiza el formulario de edición', function () {
        $agreement = Agreement::factory()->create(['supplier_id' => 'SUP-001']);
    
        $this->get(route('agreement.edit', $agreement))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Agreements/Edit')
                ->where('agreement.id', $agreement->id)
                ->has('defaultSuppliers')
            );
    });
});

describe('Lógica de Creación', function () {

    test('registra un nuevo acuerdo correctamente', function () {
        $agreementData = [
            'name' => 'Acuerdo 2026',
            'legal_name' => 'Soluciones C.A.',
            'tax_id' => 'J-123456789',
            'contact_person' => 'Carlos Gerente',
            'contact_email' => 'gerencia@gmail.com',
            'contact_phone' => '04141234567',
            'observations' => 'Sin observaciones',
            'supplier_id' => 'SUP-001',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ];
    
        $this->post(route('agreement.store'), $agreementData)
            ->assertRedirect(route('agreement.index'))
            ->assertSessionHas('success', 'Acuerdo creado correctamente.');
    
        $this->assertDatabaseHas('agreements', [
            'name' => 'Acuerdo 2026',
            'tax_id' => 'J-123456789',
            'is_active' => 1,
        ]);
    });
    
    test('valida duplicidad de RIF', function () {
        Agreement::factory()->create(['tax_id' => 'J-DUPLICADO', 'supplier_id' => 'SUP-001']);
    
        $newData = Agreement::factory()->raw([
            'tax_id' => 'J-DUPLICADO',
            'supplier_id' => 'SUP-001'
        ]);
    
        $this->post(route('agreement.store'), $newData)
            ->assertSessionHasErrors(['tax_id']);
    });

});

describe('Lógica de Actualización', function () {

    test('actualiza un acuerdo correctamente', function () {
        $agreement = Agreement::factory()->create([
            'name' => 'Nombre Viejo',
            'is_active' => true,
            'supplier_id' => 'SUP-001'
        ]);
    
        $updateData = [
            'name' => 'Nombre Editado',
            'legal_name' => $agreement->legal_name,
            'tax_id' => $agreement->tax_id,
            'contact_person' => $agreement->contact_person,
            'contact_email' => 'gerencia_actualizada@gmail.com', 
            'contact_phone' => $agreement->contact_phone,
            'observations' => 'Actualizado',
            'is_active' => false, 
            'supplier_id' => 'SUP-001'
        ];
    
        $this->put(route('agreement.update', $agreement), $updateData)
            ->assertRedirect(route('agreement.index'))
            ->assertSessionHas('success', 'Acuerdo actualizado correctamente.');
    
        $this->assertDatabaseHas('agreements', [
            'id' => $agreement->id,
            'name' => 'Nombre Editado',
            'contact_email' => 'gerencia_actualizada@gmail.com',
            'is_active' => 0,
        ]);
    });
    
    test('permite mantener el mismo RIF propio al actualizar', function () {
        $agreement = Agreement::factory()->create(['tax_id' => 'J-PROPIO', 'supplier_id' => 'SUP-001']);
    
        $updateData = $agreement->toArray();
        
        $updateData['name'] = 'Nombre Nuevo Solamente';
        $updateData['is_active'] = true;
        $updateData['observations'] = 'Sin cambios';
        
        $updateData['contact_email'] = 'pruebas@gmail.com'; 
    
        $this->put(route('agreement.update', $agreement), $updateData)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('agreement.index'));
    });

});

describe('Eliminación', function () {

    test('realiza borrado suave (Soft Delete) del acuerdo', function () {
        $agreement = Agreement::factory()->create(['supplier_id' => 'SUP-001']);
    
        $this->delete(route('agreement.destroy', $agreement))
            ->assertRedirect(route('agreement.index'))
            ->assertSessionHas('success', 'Acuerdo eliminado correctamente.');
    
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