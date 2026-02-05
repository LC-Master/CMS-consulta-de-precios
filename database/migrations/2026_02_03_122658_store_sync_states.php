<?php

use App\Enums\SyncStatusEnum;
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
            $table->string('communication_key')->nullable();
            $table->foreignUuid('placeholder_id')->nullable()->constrained('media');
            $table->timestamp('sync_started_at')->nullable();
            $table->timestamp('sync_ended_at')->nullable();
            $table->json('disk')->nullable();
            $table->timestamp('uptimed_at')->nullable();
            $table->enum('sync_status', array_column(SyncStatusEnum::cases(), 'value'))
                ->default(SyncStatusEnum::PENDING->value)
                ->nullable();
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
