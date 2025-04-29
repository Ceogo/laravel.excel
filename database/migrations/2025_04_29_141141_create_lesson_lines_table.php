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
        Schema::create('lesson_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_outcome_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->integer('target_week');
            $table->boolean('is_processed')->default(false);
            $table->timestamps();

            $table->index(['group_id', 'target_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_lines');
    }
};
