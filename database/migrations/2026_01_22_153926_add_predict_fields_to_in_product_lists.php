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
        Schema::table('in_product_lists', function (Blueprint $table) {
            $table->integer('manufacture_year')->nullable()->after('type');
            $table->integer('repair_count')->default(0)->after('manufacture_year');
            $table->integer('movement_count')->default(0)->after('repair_count');
            $table->boolean('needs_replacement')->default(false)->after('movement_count')->comment('Target variable for ML');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('in_product_lists', function (Blueprint $table) {
            $table->dropColumn(['manufacture_year', 'repair_count', 'movement_count', 'needs_replacement']);
        });
    }
};
