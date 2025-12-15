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
        Schema::create('agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('legal_name')->nullable()
                ->after('name')
                ->comment('Razón Social de la empresa');
            $table->string('tax_id')->nullable()
                ->after('legal_name')
                ->comment('RIF, NIT o documento fiscal');
            $table->string('contact_person')->nullable()
                ->after('tax_id');
            $table->string('contact_email')->nullable()
                ->after('contact_person');
            $table->string('contact_phone')->nullable()
                ->after('contact_email');
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
        Schema::dropIfExists('agreements');
    }
};
