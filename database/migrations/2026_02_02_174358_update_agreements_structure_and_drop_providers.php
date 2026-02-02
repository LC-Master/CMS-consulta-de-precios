<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agreements', function (Blueprint $table) {

            $table->dropForeign(['provider_id']); 
            $table->dropColumn('provider_id');

            $table->foreignUuid('supplier_id')
                  ->nullable()
                  ->constrained(table: 'Supplier', column: 'id')
                  ->nullOnDelete(); 
        });

        Schema::dropIfExists('providers');
    }

    public function down(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('agreements', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');

            $table->foreignUuid('provider_id')->nullable()->constrained('providers');
        });
    }
};