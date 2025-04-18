<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\SemesterDistribution;
use App\Models\RupDetail;
use App\Models\AcademicYearDetail;
use App\Models\SemesterDetail;
use App\Models\YearTotal;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    private $headerStructure = [
        '1. Индекс Модуля',
        '2. Наименование дисциплины',
        '3. ФИО преподавателя',
        [
            'title' => '4. Распределение по семестрам',
            'children' => [
                '4.1 экзамены',
                '4.2 зачеты',
                '4.3 курсовые работы',
                '4.4 контрольные работы'
            ]
        ],
        [
            'title' => '5. По РУП',
            'children' => [
                '5.1 Кредиты',
                '5.2 Всего часов',
                '5.3 Теоретические занятия',
                '5.4 Лабораторно-практические занятия',
                '5.5 Курсовые работы',
                '5.6 Произв-ое обуч-ие Проф-ая практика'
            ]
        ],
        [
            'title' => '6. На текущий учебный год',
            'children' => [
                '6.1 Всего часов',
                '6.2 Из них теоретических',
                '6.3 Из них ЛПР',
                '6.4 Из них курсовые работы',
                '6.5 Производственное обучение'
            ]
        ],
        [
            'title' => '7. 3 семестр',
            'children' => [
                '7.1 кол-во недель',
                '7.2 часов в неделю',
                '7.3 всего часов',
                '7.4 из них теоретических',
                '7.5 из них ЛПР',
                '7.6 из них КР/КП',
                '7.7 проверка КП/КР',
                '7.8 Производственное обучение',
                [
                    'title' => '7.9 Подвоение',
                    'children' => [
                        '7.9.1 ЛПР',
                        '7.9.2 КП/КП',
                        '7.9.3 проверка КР/КП'
                    ]
                ],
                '7.10 консультации',
                '7.11 экзамены',
                '7.12 итого за 1 семестр'
            ]
        ],
        [
            'title' => '8. 4 семестр',
            'children' => [
                '8.1 кол-во недель',
                '8.2 часов в неделю',
                '8.3 всего часов',
                '8.4 из них теоретических',
                '8.5 из них ЛПР',
                '8.6 из них КР/КП',
                '8.7 проверка КП/КР',
                '8.8 Производственное обучение',
                [
                    'title' => '8.9 Подвоение',
                    'children' => [
                        '8.9.1 ЛПР',
                        '8.9.2 КП/КП',
                        '8.9.3 проверка КР/КП'
                    ]
                ],
                '8.10 консультации',
                '8.11 экзамены',
                '8.12 итого за 2 семестр'
            ]
        ],
        '9. Итого за год'
    ];

    public function index(Request $request)
    {
        $file = $request->file('file');
        $csvData = file_get_contents($file->getPathName());
        $rows = array_map(function($row) {
            return str_getcsv($row, ';');
        }, explode("\n", $csvData));

        $filteredRows = [];
        $dataStarted = false;

        foreach ($rows as $row) {
            $trimmedRow = array_map('trim', $row);
            if (!$dataStarted && isset($trimmedRow[0]) && str_starts_with($trimmedRow[0], 'БМ')) {
                $dataStarted = true;
                continue;
            }
            if ($dataStarted && !empty(array_filter($trimmedRow))) {
                $filteredRows[] = $trimmedRow;
            }
        }

        $flattenedHeaders = $this->flattenHeaders($this->headerStructure);

        foreach ($filteredRows as $row) {
            $dataRow = [];
            foreach ($flattenedHeaders as $index => $header) {
                $dataRow[$header] = $row[$index] ?? '-';
            }

            // Save to database
            $module = Module::create([
                'index' => $dataRow['1. Индекс Модуля'],
                'discipline_name' => $dataRow['2. Наименование дисциплины'],
                'teacher_name' => $dataRow['3. ФИО преподавателя'],
            ]);

            SemesterDistribution::create([
                'module_id' => $module->id,
                'exams' => $dataRow['4.1 экзамены'] !== '-' ? (int)$dataRow['4.1 экзамены'] : null,
                'credits' => $dataRow['4.2 зачеты'] !== '-' ? (int)$dataRow['4.2 зачеты'] : null,
                'course_works' => $dataRow['4.3 курсовые работы'] !== '-' ? (int)$dataRow['4.3 курсовые работы'] : null,
                'control_works' => $dataRow['4.4 контрольные работы'] !== '-' ? (int)$dataRow['4.4 контрольные работы'] : null,
            ]);

            RupDetail::create([
                'module_id' => $module->id,
                'credits' => $dataRow['5.1 Кредиты'] !== '-' ? (float)$dataRow['5.1 Кредиты'] : null,
                'total_hours' => $dataRow['5.2 Всего часов'] !== '-' ? (int)$dataRow['5.2 Всего часов'] : null,
                'theoretical_hours' => $dataRow['5.3 Теоретические занятия'] !== '-' ? (int)$dataRow['5.3 Теоретические занятия'] : null,
                'lab_practical_hours' => $dataRow['5.4 Лабораторно-практические занятия'] !== '-' ? (int)$dataRow['5.4 Лабораторно-практические занятия'] : null,
                'course_works' => $dataRow['5.5 Курсовые работы'] !== '-' ? (int)$dataRow['5.5 Курсовые работы'] : null,
                'professional_practice' => $dataRow['5.6 Произв-ое обуч-ие Проф-ая практика'] !== '-' ? (int)$dataRow['5.6 Произв-ое обуч-ие Проф-ая практика'] : null,
            ]);

            AcademicYearDetail::create([
                'module_id' => $module->id,
                'total_hours' => $dataRow['6.1 Всего часов'] !== '-' ? (int)$dataRow['6.1 Всего часов'] : null,
                'theoretical_hours' => $dataRow['6.2 Из них теоретических'] !== '-' ? (int)$dataRow['6.2 Из них теоретических'] : null,
                'lab_practical_hours' => $dataRow['6.3 Из них ЛПР'] !== '-' ? (int)$dataRow['6.3 Из них ЛПР'] : null,
                'course_works' => $dataRow['6.4 Из них курсовые работы'] !== '-' ? (int)$dataRow['6.4 Из них курсовые работы'] : null,
                'professional_training' => $dataRow['6.5 Производственное обучение'] !== '-' ? (int)$dataRow['6.5 Производственное обучение'] : null,
            ]);

            // 3rd Semester
            SemesterDetail::create([
                'module_id' => $module->id,
                'semester_number' => 3,
                'weeks_count' => $dataRow['7.1 кол-во недель'] !== '-' ? (int)$dataRow['7.1 кол-во недель'] : null,
                'hours_per_week' => $dataRow['7.2 часов в неделю'] !== '-' ? (int)$dataRow['7.2 часов в неделю'] : null,
                'total_hours' => $dataRow['7.3 всего часов'] !== '-' ? (int)$dataRow['7.3 всего часов'] : null,
                'theoretical_hours' => $dataRow['7.4 из них теоретических'] !== '-' ? (int)$dataRow['7.4 из них теоретических'] : null,
                'lab_practical_hours' => $dataRow['7.5 из них ЛПР'] !== '-' ? (int)$dataRow['7.5 из них ЛПР'] : null,
                'course_projects' => $dataRow['7.6 из них КР/КП'] !== '-' ? (int)$dataRow['7.6 из них КР/КП'] : null,
                'project_verification' => $dataRow['7.7 проверка КП/КР'] !== '-' ? (int)$dataRow['7.7 проверка КП/КР'] : null,
                'professional_training' => $dataRow['7.8 Производственное обучение'] !== '-' ? (int)$dataRow['7.8 Производственное обучение'] : null,
                'lab_practical_duplication' => $dataRow['7.9.1 ЛПР'] !== '-' ? (int)$dataRow['7.9.1 ЛПР'] : null,
                'project_duplication' => $dataRow['7.9.2 КП/КП'] !== '-' ? (int)$dataRow['7.9.2 КП/КП'] : null,
                'verification_duplication' => $dataRow['7.9.3 проверка КР/КП'] !== '-' ? (int)$dataRow['7.9.3 проверка КР/КП'] : null,
                'consultations' => $dataRow['7.10 консультации'] !== '-' ? (int)$dataRow['7.10 консультации'] : null,
                'exams' => $dataRow['7.11 экзамены'] !== '-' ? (int)$dataRow['7.11 экзамены'] : null,
                'semester_total' => $dataRow['7.12 итого за 1 семестр'] !== '-' ? (int)$dataRow['7.12 итого за 1 семестр'] : null,
            ]);

            // 4th Semester
            SemesterDetail::create([
                'module_id' => $module->id,
                'semester_number' => 4,
                'weeks_count' => $dataRow['8.1 кол-во недель'] !== '-' ? (int)$dataRow['8.1 кол-во недель'] : null,
                'hours_per_week' => $dataRow['8.2 часов в неделю'] !== '-' ? (int)$dataRow['8.2 часов в неделю'] : null,
                'total_hours' => $dataRow['8.3 всего часов'] !== '-' ? (int)$dataRow['8.3 всего часов'] : null,
                'theoretical_hours' => $dataRow['8.4 из них теоретических'] !== '-' ? (int)$dataRow['8.4 из них теоретических'] : null,
                'lab_practical_hours' => $dataRow['8.5 из них ЛПР'] !== '-' ? (int)$dataRow['8.5 из них ЛПР'] : null,
                'course_projects' => $dataRow['8.6 из них КР/КП'] !== '-' ? (int)$dataRow['8.6 из них КР/КП'] : null,
                'project_verification' => $dataRow['8.7 проверка КП/КР'] !== '-' ? (int)$dataRow['8.7 проверка КП/КР'] : null,
                'professional_training' => $dataRow['8.8 Производственное обучение'] !== '-' ? (int)$dataRow['8.8 Производственное обучение'] : null,
                'lab_practical_duplication' => $dataRow['8.9.1 ЛПР'] !== '-' ? (int)$dataRow['8.9.1 ЛПР'] : null,
                'project_duplication' => $dataRow['8.9.2 КП/КП'] !== '-' ? (int)$dataRow['8.9.2 КП/КП'] : null,
                'verification_duplication' => $dataRow['8.9.3 проверка КР/КП'] !== '-' ? (int)$dataRow['8.9.3 проверка КР/КП'] : null,
                'consultations' => $dataRow['8.10 консультации'] !== '-' ? (int)$dataRow['8.10 консультации'] : null,
                'exams' => $dataRow['8.11 экзамены'] !== '-' ? (int)$dataRow['8.11 экзамены'] : null,
                'semester_total' => $dataRow['8.12 итого за 2 семестр'] !== '-' ? (int)$dataRow['8.12 итого за 2 семестр'] : null,
            ]);

            YearTotal::create([
                'module_id' => $module->id,
                'total_hours' => $dataRow['9. Итого за год'] !== '-' ? (int)$dataRow['9. Итого за год'] : null,
            ]);
        }

        return view('document', [
            'data' => Module::with([
                'semesterDistribution',
                'rupDetail',
                'academicYearDetail',
                'semesterDetails',
                'yearTotal'
            ])->get(),
            'headers' => $this->headerStructure,
            'flattenedHeaders' => $flattenedHeaders,
            'headerRows' => $this->buildHeaderRows($this->headerStructure)
        ]);
    }

    private function flattenHeaders($structure)
    {
        $result = [];
        foreach ($structure as $header) {
            if (is_array($header)) {
                if (isset($header['children'])) {
                    foreach ($header['children'] as $child) {
                        if (is_array($child)) {
                            $result = array_merge($result, $this->flattenHeaders([$child]));
                        } else {
                            $result[] = $child;
                        }
                    }
                }
            } else {
                $result[] = $header;
            }
        }
        return $result;
    }

    private function buildHeaderRows($headers)
    {
        $maxLevel = $this->getMaxLevel($headers);
        $matrix = [];
        $position = 0;
        $this->buildHeaderMatrix($headers, $matrix, 0, $position, $maxLevel);
        return $matrix;
    }

    private function buildHeaderMatrix($structure, &$matrix, $level, &$position, $maxLevel)
    {
        foreach ($structure as $header) {
            if (is_array($header) && isset($header['children'])) {
                $span = $this->countColumns($header['children']);
                $matrix[$level][$position] = [
                    'title' => $header['title'],
                    'colspan' => $span,
                    'rowspan' => 1
                ];
                $this->buildHeaderMatrix($header['children'], $matrix, $level + 1, $position, $maxLevel);
            } else {
                $matrix[$level][$position] = [
                    'title' => is_string($header) ? $header : $header['title'],
                    'colspan' => 1,
                    'rowspan' => $maxLevel - $level + 1
                ];
                $position++;
            }
        }
    }

    private function countColumns($structure)
    {
        $count = 0;
        foreach ($structure as $item) {
            if (is_array($item) && isset($item['children'])) {
                $count += $this->countColumns($item['children']);
            } else {
                $count++;
            }
        }
        return $count;
    }

    private function getMaxLevel($structure, $currentLevel = 0)
    {
        $maxLevel = $currentLevel;
        foreach ($structure as $item) {
            if (is_array($item) && isset($item['children'])) {
                $childLevel = $this->getMaxLevel($item['children'], $currentLevel + 1);
                if ($childLevel > $maxLevel) {
                    $maxLevel = $childLevel;
                }
            }
        }
        return $maxLevel;
    }
}
