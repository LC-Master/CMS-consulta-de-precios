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
            $table->primary(['campaign_id', 'center_id']);
            $table->foreignUuid('campaign_id')->constrained();
            $table->foreignUuid('center_id')->constrained();
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
