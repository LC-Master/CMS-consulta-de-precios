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
        Schema::create('media', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("disk")->default("local");
            $table->string('path'); 
            $table->string('mime_type');
            $table->unsignedBigInteger('size'); 
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('checksum')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
