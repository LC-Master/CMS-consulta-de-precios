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
            $table->string('name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->unsignedBigInteger('duration_seconds')->nullable();
            $table->string('checksum')->nullable();

            $table->foreignId('created_by')->constrained('users');

            $table->index(['disk', 'path']);
            $table->index('mime_type');
            $table->index('checksum');

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
