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
        Schema::table('store_sync_states', function (Blueprint $table) {
            $table->text('last_sync_error')->nullable()->after('sync_status');
            $table->timestamp('last_error_at')->nullable()->after('last_sync_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('StoreSyncState', function (Blueprint $table) {
            $table->dropColumn(['last_sync_error', 'last_error_at']);
        });
    }
};
