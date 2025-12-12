<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            
            // 1. Datos Legales
            $table->string('legal_name')->nullable()
                  ->after('name')
                  ->comment('Razón Social de la empresa');

            $table->string('tax_id')->nullable()
                  ->after('legal_name')
                  ->comment('RIF, NIT o documento fiscal');

            // 2. Contacto
            $table->string('contact_person')->nullable()
                  ->after('tax_id');

            $table->string('contact_email')->nullable()
                  ->after('contact_person');

            $table->string('contact_phone')->nullable()
                  ->after('contact_email');

            // 3. Vigencia
            $table->date('start_date')->nullable()
                  ->after('contact_phone');

            $table->date('end_date')->nullable()
                  ->after('start_date');

            $table->boolean('is_active')->default(true)
                  ->after('end_date');

            $table->text('observations')->nullable()
                  ->after('is_active');

             $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name',
                'tax_id',
                'contact_person',
                'contact_email',
                'contact_phone',
                'start_date',
                'end_date',
                'is_active',
                'observations',
            ]);
        });
    }
};
