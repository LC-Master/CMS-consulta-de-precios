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
        Schema::create('campaign_centers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('center_id')->constrained()->onDelete('cascade');
            $table->unique(['campaign_id', 'center_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_centers');
    }
};
