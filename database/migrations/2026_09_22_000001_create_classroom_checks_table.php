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
        Schema::create('classroom_checks', function (Blueprint $table) {
            $table->id();
            $table->integer('tutor_id')->index();
            $table->integer('auditory_id')->index();
            $table->date('check_date');
            $table->time('lesson_start');
            $table->time('lesson_finish');
            $table->enum('check_type', ['entrance', 'exit'])->default('entrance');
            $table->enum('status', ['ok', 'discrepancy'])->default('ok');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('classroom_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_id')->constrained('classroom_checks')->onDelete('cascade');
            $table->integer('id_product')->index();
            $table->string('inv_number')->nullable();
            $table->boolean('is_present')->default(true);
            $table->enum('condition', ['ok', 'damaged', 'missing'])->default('ok');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_check_items');
        Schema::dropIfExists('classroom_checks');
    }
};
