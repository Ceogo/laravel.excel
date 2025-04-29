<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_outcome_id')->constrained()->onDelete('cascade');
            $table->string('day'); // Например: 'monday'
            $table->integer('pair_number'); // Номер пары (1–7)
            $table->string('type'); // 'theoretical', 'lab_practical', 'course_project'
            $table->integer('week'); // Номер недели в семестре
            $table->integer('semester'); // Номер семестра
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('cabinet_id')->nullable()->constrained('cabinets')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
