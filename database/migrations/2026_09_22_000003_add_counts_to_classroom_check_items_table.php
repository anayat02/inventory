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
        Schema::table('classroom_check_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('id_product');
            $table->integer('db_count')->default(0)->after('product_name');
            $table->integer('fact_count')->default(0)->after('db_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classroom_check_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'db_count', 'fact_count']);
        });
    }
};
