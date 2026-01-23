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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('subject_id')->nullable();
            $table->string('subject_type')->nullable();

            $table->string('causer_id');
            $table->string('user_name');
            $table->string('user_email');

            $table->string('action', 100);
            $table->string('level', 20);
            $table->text('message')->nullable();
            $table->json('properties')->nullable();

            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_id', 'subject_type']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
