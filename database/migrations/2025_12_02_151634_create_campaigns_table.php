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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid();
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->foreignUuid('status_id')->nullable()->constrained();
            $table->foreignUuid('department_id')->nullable()->constrained();
            $table->foreignUuid('agreement_id')->nullable()->constrained();
            $table->uuid('created_by')->nullable(false);
            $table->uuid('updated_by')->nullable(false);
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
