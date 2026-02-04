<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('center_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id');
            $table->jsonb('snapshot_json');
            $table->string('version_hash', 64);

            $table->index(['store_id', 'created_at']);
            $table->index('version_hash');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_snapshots');
    }
};
