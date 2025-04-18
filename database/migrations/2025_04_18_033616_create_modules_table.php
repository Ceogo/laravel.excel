<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('index'); // 1. Индекс Модуля
            $table->string('discipline_name'); // 2. Наименование дисциплины
            $table->string('teacher_name'); // 3. ФИО преподавателя
            $table->timestamps();
        });

        Schema::create('semester_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->integer('exams')->nullable(); // 4.1 экзамены
            $table->integer('credits')->nullable(); // 4.2 зачеты
            $table->integer('course_works')->nullable(); // 4.3 курсовые работы
            $table->integer('control_works')->nullable(); // 4.4 контрольные работы
            $table->timestamps();
        });

        Schema::create('rup_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->float('credits')->nullable(); // 5.1 Кредиты
            $table->integer('total_hours')->nullable(); // 5.2 Всего часов
            $table->integer('theoretical_hours')->nullable(); // 5.3 Теоретические занятия
            $table->integer('lab_practical_hours')->nullable(); // 5.4 Лабораторно-практические занятия
            $table->integer('course_works')->nullable(); // 5.5 Курсовые работы
            $table->integer('professional_practice')->nullable(); // 5.6 Произв-ое обуч-ие Проф-ая практика
            $table->timestamps();
        });

        Schema::create('academic_year_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->integer('total_hours')->nullable(); // 6.1 Всего часов
            $table->integer('theoretical_hours')->nullable(); // 6.2 Из них теоретических
            $table->integer('lab_practical_hours')->nullable(); // 6.3 Из них ЛПР
            $table->integer('course_works')->nullable(); // 6.4 Из них курсовые работы
            $table->integer('professional_training')->nullable(); // 6.5 Производственное обучение
            $table->timestamps();
        });

        Schema::create('semester_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->integer('semester_number'); // 7 or 8 for 3rd or 4th semester
            $table->integer('weeks_count')->nullable(); // 7.1/8.1 кол-во недель
            $table->integer('hours_per_week')->nullable(); // 7.2/8.2 часов в неделю
            $table->integer('total_hours')->nullable(); // 7.3/8.3 всего часов
            $table->integer('theoretical_hours')->nullable(); // 7.4/8.4 из них теоретических
            $table->integer('lab_practical_hours')->nullable(); // 7.5/8.5 из них ЛПР
            $table->integer('course_projects')->nullable(); // 7.6/8.6 из них КР/КП
            $table->integer('project_verification')->nullable(); // 7.7/8.7 проверка КП/КР
            $table->integer('professional_training')->nullable(); // 7.8/8.8 Производственное обучение
            $table->integer('lab_practical_duplication')->nullable(); // 7.9.1/8.9.1 ЛПР
            $table->integer('project_duplication')->nullable(); // 7.9.2/8.9.2 КП/КП
            $table->integer('verification_duplication')->nullable(); // 7.9.3/8.9.3 проверка КР/КП
            $table->integer('consultations')->nullable(); // 7.10/8.10 консультации
            $table->integer('exams')->nullable(); // 7.11/8.11 экзамены
            $table->integer('semester_total')->nullable(); // 7.12/8.12 итого за семестр
            $table->timestamps();
        });

        Schema::create('year_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->integer('total_hours')->nullable(); // 9. Итого за год
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('year_totals');
        Schema::dropIfExists('semester_details');
        Schema::dropIfExists('academic_year_details');
        Schema::dropIfExists('rup_details');
        Schema::dropIfExists('semester_distributions');
        Schema::dropIfExists('modules');
    }
};
