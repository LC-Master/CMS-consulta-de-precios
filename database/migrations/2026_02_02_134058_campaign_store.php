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
        Schema::create('campaign_store', function (Blueprint $table) {
            $table->foreignUuid('campaign_id')->constrained()->onDelete('cascade');
            $table->string('store_id');
            $table->primary(['campaign_id', 'store_id']);
            $table->index(['store_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_store');
    }
};
