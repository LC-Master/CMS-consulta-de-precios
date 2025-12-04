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
        Schema::create('time_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignUuid('media_id')
                ->constrained();
            $table->time('scheduled_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_line_items');
    }
};
