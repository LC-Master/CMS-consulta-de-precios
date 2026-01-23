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
        Schema::create('campaign_agreements', function (Blueprint $table) {
            $table->foreignUuid('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('agreement_id')->constrained()->onDelete('cascade');
            $table->primary(['campaign_id', 'agreement_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_agreement');
    }
};
