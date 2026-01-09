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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->foreignUuid('status_id')->constrained();
            $table->foreignUuid('department_id')->constrained();
            $table->foreignUuid('agreement_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();

            $table->index(['start_at', 'end_at']);
            $table->index(['status_id', 'start_at']);
            $table->index(['department_id', 'status_id']);
            $table->index('deleted_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
