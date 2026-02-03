<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('agreements', 'supplier_id')) {
            Schema::table('agreements', function (Blueprint $table) {
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Exception $e) {

                }
                
                $table->dropColumn('supplier_id');
            });
        }

        Schema::table('agreements', function (Blueprint $table) {
            $table->integer('supplier_id')->nullable();

            $table->foreign('supplier_id')
                  ->references('ID')
                  ->on('Supplier');
        });
    }

    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            if (Schema::hasColumn('agreements', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
        });

    }
};