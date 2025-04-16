<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentController extends Controller
{
    private $headerStructure = [
        '1. Индекс Модуля',
        '2. Наименование дисциплины',
        '3. ФИО преподавателя',

        // Раздел 4
        [
            'title' => '4. Распределение по семестрам',
            'children' => [
                '4.1 экзамены',
                '4.2 зачеты',
                '4.3 курсовые работы',
                '4.4 контрольные работы'
            ]
        ],

        // Раздел 5
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

        // Раздел 6
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

        // Раздел 7
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

        // Раздел 8
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
                $headersRow = $trimmedRow;
                continue;
            }

            if ($dataStarted && !empty(array_filter($trimmedRow))) {
                $filteredRows[] = $trimmedRow;
            }
        }

        $flattenedHeaders = $this->flattenHeaders($this->headerStructure);
        $data = [];

        foreach ($filteredRows as $row) {
            $dataRow = [];
            foreach ($flattenedHeaders as $index => $header) {
                $dataRow[$header] = $row[$index] ?? '-';
            }
            $data[] = $dataRow;
        }


        return view('document', [
            'data' => $data,
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
