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
        Schema::create('store_sync_states', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')->unique()->index();
            $table->string('url')->nullable();
            $table->foreignUuid('placeholder_id')->nullable()->constrained('media');
            $table->boolean('is_syncing')->default(false);
            $table->timestamp('sync_started_at')->nullable();
            $table->timestamp('sync_ended_at')->nullable();
            $table->json('disk')->nullable();
            $table->timestamp('uptimed_at')->nullable();
            $table->string('last_sync_status')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_sync_states');
    }
};
