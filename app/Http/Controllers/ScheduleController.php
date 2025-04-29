<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\LearningOutcome;
use App\Models\Schedule;
use App\Models\SemesterDetail;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function show(Request $request)
    {
        $groupId = $request->input('group_id');
        $semester = $request->input('semester', 3);
        $week = $request->input('week', 1);

        if (!$groupId) {
            $groups = Group::all();
            return view('schedule.select', compact('groups', 'semester', 'week'));
        }

        $group = Group::findOrFail($groupId);

        $bellSchedule = [
            'monday' => [
                'class_hour' => ['start' => '08:15', 'end' => '08:35'],
                1 => ['start' => '08:40', 'end' => '10:00'],
                2 => ['start' => '10:10', 'end' => '11:30'],
                3 => ['start' => '12:15', 'end' => '13:35'],
                4 => ['start' => '13:40', 'end' => '15:00'],
                5 => ['start' => '15:10', 'end' => '16:30'],
                6 => ['start' => '16:40', 'end' => '18:00'],
                7 => ['start' => '18:05', 'end' => '19:25'],
            ],
            'other_days' => [
                1 => ['start' => '08:15', 'end' => '09:35'],
                2 => ['start' => '09:45', 'end' => '11:05'],
                3 => ['start' => '11:50', 'end' => '13:10'],
                4 => ['start' => '13:15', 'end' => '14:35'],
                5 => ['start' => '14:45', 'end' => '16:05'],
                6 => ['start' => '16:15', 'end' => '17:35'],
                7 => ['start' => '17:40', 'end' => '19:00'],
            ],
        ];

        $schedule = Schedule::with(['learningOutcome.module'])
            ->where('group_id', $groupId)
            ->where('semester', $semester)
            ->where('week', $week)
            ->get()
            ->groupBy('day')
            ->map(function ($daySchedules) {
                return $daySchedules->mapWithKeys(function ($item) {
                    $moduleIndex = $item->learningOutcome && $item->learningOutcome->module
                        ? $item->learningOutcome->module->index
                        : 'Не указан модуль';

                    return [$item->pair_number => [
                        'module_index' => $moduleIndex,
                        'discipline_name' => $item->learningOutcome ? $item->learningOutcome->discipline_name : 'Не указана дисциплина',
                        'teacher_name' => $item->learningOutcome ? ($item->learningOutcome->teacher_name ?? 'вакансия') : 'вакансия',
                        'type' => $item->type,
                        'id' => $item->id,
                    ]];
                })->all();
            })->toArray();

        if (empty($schedule)) {
            $schedule = $this->generateSchedule($group, $semester, $week, $bellSchedule);
        }

        $groups = Group::all();

        return view('schedule.index', compact('schedule', 'group', 'groups', 'semester', 'week', 'bellSchedule'));
    }

    private function generateSchedule($group, $semester, $week, $bellSchedule)
    {
        $schedule = [
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
        ];

        $lessons = $this->getLessons($group, $semester);
        $days = array_keys($schedule);

        // Распределение пар: 2 дня по 3 пары, 3 дня по 4 пары
        $pairsPerDay = [3, 3, 4, 4, 4];
        shuffle($pairsPerDay);

        foreach ($days as $index => $day) {
            $pairsCount = $pairsPerDay[$index];
            $dailyLessons = $lessons;
            shuffle($dailyLessons);
            $pairNumber = 1;

            while ($pairNumber <= $pairsCount && !empty($dailyLessons)) {
                foreach ($dailyLessons as $key => $lesson) {
                    if ($this->isTeacherAvailable($lesson['teacher_name'], $day, $pairNumber, $week, $semester)) {
                        $scheduleItem = Schedule::create([
                            'group_id' => $group->id,
                            'learning_outcome_id' => $lesson['learning_outcome_id'],
                            'day' => $day,
                            'pair_number' => $pairNumber,
                            'type' => $lesson['type'],
                            'week' => $week,
                            'semester' => $semester,
                        ]);

                        $schedule[$day][$pairNumber] = [
                            'module_index' => $lesson['module_index'],
                            'discipline_name' => $lesson['discipline_name'],
                            'teacher_name' => $lesson['teacher_name'],
                            'type' => $lesson['type'],
                            'id' => $scheduleItem->id,
                        ];

                        unset($dailyLessons[$key]);
                        $pairNumber++;
                        break;
                    } else {
                        // Если есть конфликт, пробуем следующую пару
                        $pairNumber++;
                        if ($pairNumber > 4) break; // Максимум 4 пары
                    }
                }
            }
        }

        return $schedule;
    }

    private function isTeacherAvailable($teacherName, $day, $pairNumber, $week, $semester)
    {
        return !Schedule::where('week', $week)
            ->where('semester', $semester)
            ->where('day', $day)
            ->where('pair_number', $pairNumber)
            ->whereHas('learningOutcome', function ($query) use ($teacherName) {
                $query->where('teacher_name', $teacherName);
            })
            ->exists();
    }

    private function getLessons($group, $semester)
    {
        $lessons = [];
        foreach ($group->modules as $module) {
            foreach ($module->learningOutcomes as $lo) {
                $semesterDetail = $lo->semesterDetails()->where('semester_number', $semester)->first();
                if ($semesterDetail && $semesterDetail->total_hours > 0) {
                    $lessons[] = [
                        'learning_outcome_id' => $lo->id,
                        'module_index' => $module->index,
                        'discipline_name' => $lo->discipline_name,
                        'teacher_name' => $lo->teacher_name ?? 'вакансия',
                        'type' => 'theoretical',
                        'hours_per_week' => $semesterDetail->hours_per_week,
                    ];
                }
            }
        }
        return $lessons;
    }
    public function editSchedule(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);

        if ($request->isMethod('post')) {
            $schedule->update([
                'learning_outcome_id' => $request->input('learning_outcome_id'),
            ]);

            $group = Group::find($schedule->group_id);
            $weekSchedule = $this->generateSchedule($group, $schedule->semester, $schedule->week, []);
            $conflicts = $this->checkScheduleConflicts($weekSchedule, $schedule->semester, $schedule->week);

            if (!empty($conflicts)) {
                return redirect()->route('schedule', ['group_id' => $schedule->group_id, 'semester' => $schedule->semester])
                    ->with('warnings', $conflicts);
            }

            return redirect()->route('schedule', ['group_id' => $schedule->group_id, 'semester' => $schedule->semester])
                ->with('success', 'Пара успешно обновлена');
        }

        $learningOutcomes = LearningOutcome::all();
        return view('schedule.edit', compact('schedule', 'learningOutcomes'));
    }

    private function checkScheduleConflicts($schedule, $semester, $week)
    {
        $conflicts = [];

        $teacherSchedules = Schedule::where('semester', $semester)
            ->where('week', $week)
            ->with('learningOutcome')
            ->get()
            ->groupBy(function ($item) {
                return $item->learningOutcome->teacher_name . '_' . $item->day . '_' . $item->pair_number;
            });

        foreach ($teacherSchedules as $key => $group) {
            if ($group->count() > 1) {
                $conflicts[] = "Преподаватель {$group->first()->learningOutcome->teacher_name} ведет пары одновременно у нескольких групп в {$group->first()->day} на {$group->first()->pair_number}-й паре.";
            }
        }

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $bm01Count = 0;

        foreach ($days as $day) {
            $daySchedule = $schedule[$day] ?? [];
            $totalPairs = count($daySchedule);
            if ($totalPairs < 3) {
                $conflicts[] = "Недостаточно пар в {$day}: {$totalPairs} < 3.";
            }
            foreach ($daySchedule as $pair) {
                if ($pair['module_index'] === 'БМ 0.1') {
                    $bm01Count++;
                }
            }
        }

        if ($bm01Count > 2) {
            $conflicts[] = "Превышено количество пар из 'БМ 0.1' в неделю: {$bm01Count} > 2.";
        }

        return $conflicts;
    }
    public function swapSchedules(Request $request)
    {
        $source = $request->input('source');
        $target = $request->input('target');
        $groupId = $request->input('group_id');
        $semester = $request->input('semester');
        $week = $request->input('week');

        $sourceSchedule = Schedule::where('group_id', $groupId)
            ->where('semester', $semester)
            ->where('week', $week)
            ->where('day', $source['day'])
            ->where('pair_number', $source['pair'])
            ->first();

        $targetSchedule = Schedule::where('group_id', $groupId)
            ->where('semester', $semester)
            ->where('week', $week)
            ->where('day', $target['day'])
            ->where('pair_number', $target['pair'])
            ->first();

        if (!$sourceSchedule || !$targetSchedule) {
            return response()->json(['success' => false, 'message' => 'Одна из пар не найдена']);
        }

        $temp = $sourceSchedule->learning_outcome_id;
        $sourceSchedule->learning_outcome_id = $targetSchedule->learning_outcome_id;
        $targetSchedule->learning_outcome_id = $temp;

        $sourceSchedule->save();
        $targetSchedule->save();

        $group = Group::find($groupId);
        $weekSchedule = $this->generateScheduleArray($group, $semester, $week); // Новый метод, см. ниже
        $conflicts = $this->checkScheduleConflicts($weekSchedule, $semester, $week);

        if (!empty($conflicts)) {
            $sourceSchedule->learning_outcome_id = $temp;
            $targetSchedule->learning_outcome_id = $sourceSchedule->learning_outcome_id;
            $sourceSchedule->save();
            $targetSchedule->save();
            return response()->json(['success' => false, 'message' => implode(', ', $conflicts)]);
        }

        return response()->json(['success' => true]);
    }

    private function generateScheduleArray($group, $semester, $week)
    {
        return Schedule::with(['learningOutcome.module'])
            ->where('group_id', $group->id)
            ->where('semester', $semester)
            ->where('week', $week)
            ->get()
            ->groupBy('day')
            ->map(function ($daySchedules) {
                return $daySchedules->mapWithKeys(function ($item) {
                    $moduleIndex = $item->learningOutcome && $item->learningOutcome->module
                        ? $item->learningOutcome->module->index
                        : 'Не указан модуль';

                    return [$item->pair_number => [
                        'module_index' => $moduleIndex,
                        'discipline_name' => $item->learningOutcome ? $item->learningOutcome->discipline_name : 'Не указана дисциплина',
                        'teacher_name' => $item->learningOutcome ? ($item->learningOutcome->teacher_name ?? 'вакансия') : 'вакансия',
                        'type' => $item->type,
                        'id' => $item->id,
                    ]];
                })->all();
            })->toArray();
    }
}
