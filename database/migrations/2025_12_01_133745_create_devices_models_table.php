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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('center_id')->unsigned()->index()->nullable();
            $table->foreign('center_id')->references('id')->on('centers');
            $table->string('device_name');
            $table->string('device_type');
            $table->text('description');
            $table->tinyInteger('isactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
