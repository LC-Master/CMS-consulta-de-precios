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
        Schema::create('center_media_errors', function (Blueprint $table) {
            $table->uuid("id")->primary();

            $table->foreignUuid('center_id')->constrained()->cascadeOnDelete();

            $table->uuid('media_id')->nullable();

            $table->string('name');
            $table->string('checksum')->nullable();

            $table->string('error_type')->nullable();
            $table->unsignedTinyInteger('error_count')->default(1);
            $table->timestamp('last_seen_at')->useCurrent();

            $table->timestamps();

            $table->index(['center_id', 'media_id']);
            $table->index('checksum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_media_errors');
    }
};
