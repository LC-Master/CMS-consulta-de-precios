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
        Schema::create('thumbnails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('path');
            $table->string('name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->foreignUuid('media_id')->constrained()->onDelete('cascade');

            $table->index(['media_id', 'created_at']);
            $table->index('mime_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnails');
    }
};
