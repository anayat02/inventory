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
        Schema::table('classroom_checks', function (Blueprint $table) {
            $table->integer('keyboard_count')->nullable()->after('comment');
            $table->integer('mouse_count')->nullable()->after('keyboard_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classroom_checks', function (Blueprint $table) {
            $table->dropColumn(['keyboard_count', 'mouse_count']);
        });
    }
};
