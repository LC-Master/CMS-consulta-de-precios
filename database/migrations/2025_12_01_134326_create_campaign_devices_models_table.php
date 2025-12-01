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
        Schema::create('campaign_devices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('campaign_id')->unsigned()->index()->nullable();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->bigInteger('device_id')->unsigned()->index()->nullable();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_devices');
    }
};
