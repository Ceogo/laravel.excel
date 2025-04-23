<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учебный план</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        .header-group {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Учебный план</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @foreach($groups as $group)
            <h3>Группа: {{ $group->name }} ({{ $group->specialty_code ?? 'Не указан' }})</h3>
            <p>Специальность: {{ $group->specialty_name ?? 'Не указана' }}</p>
            <p>Количество обучающихся: {{ $group->students_count ?? 'Не указано' }}</p>
            <table class="table table-bordered table-sm">
                <thead>
                    <!-- Первый уровень заголовков -->
                    <tr class="header-group">
                        @foreach($headers as $header)
                            @if(is_array($header))
                                <th colspan="{{ $headerCounts[$header['title']] ?? 1 }}">{{ $header['title'] }}</th>
                            @else
                                <th rowspan="2">{{ $header }}</th>
                            @endif
                        @endforeach
                    </tr>
                    <!-- Второй уровень заголовков -->
                    <tr class="header-group">
                        @foreach($headers as $header)
                            @if(is_array($header))
                                @foreach($header['children'] as $child)
                                    @if(is_array($child))
                                        @foreach($child['children'] as $subChild)
                                            <th>{{ $subChild }}</th>
                                        @endforeach
                                    @else
                                        <th>{{ $child }}</th>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($group->modules as $module)
                        <tr>
                            <td>{{ $module->index }}</td>
                            <td>{{ $module->name }}</td>
                            <td colspan="{{ count($flattenedHeaders) - 2 }}"></td>
                        </tr>
                        @foreach($module->learningOutcomes as $lo)
                            <tr>
                                <td>{{ $lo->index }}</td>
                                <td>{{ $lo->discipline_name }}</td>
                                <td>{{ $lo->teacher_name ?? 'вакансия' }}</td>
                                <td>{{ $lo->semesterDistribution->exams ?? '-' }}</td>
                                <td>{{ $lo->semesterDistribution->credits ?? '-' }}</td>
                                <td>{{ $lo->semesterDistribution->course_works ?? '-' }}</td>
                                <td>{{ $lo->semesterDistribution->control_works ?? '-' }}</td>
                                <td>{{ $lo->rupDetail->credits ?? '-' }}</td>
                                <td>{{ $lo->rupDetail->total_hours ?? '-' }}</td>
                                <td>{{ $lo->rupDetail->theoretical_hours ?? '-' }}</td>
                                <td>{{ $lo->rupDetail->lab_practical_hours ?? '-' }}</td>
                                <td>{{ $lo->rupDetail->course_works ?? '-' }}</td>
                                <td>{{ $lo->rupDetail->professional_practice ?? '-' }}</td>
                                <td>{{ $lo->academicYearDetail->total_hours ?? '-' }}</td>
                                <td>{{ $lo->academicYearDetail->theoretical_hours ?? '-' }}</td>
                                <td>{{ $lo->academicYearDetail->lab_practical_hours ?? '-' }}</td>
                                <td>{{ $lo->academicYearDetail->course_works ?? '-' }}</td>
                                <td>{{ $lo->academicYearDetail->professional_training ?? '-' }}</td>
                                @foreach($lo->semesterDetails as $semesterDetail)
                                    @if($semesterDetail->semester_number == 3)
                                        <td>{{ $semesterDetail->weeks_count ?? '-' }}</td>
                                        <td>{{ $semesterDetail->hours_per_week ?? '-' }}</td>
                                        <td>{{ $semesterDetail->total_hours ?? '-' }}</td>
                                        <td>{{ $semesterDetail->theoretical_hours ?? '-' }}</td>
                                        <td>{{ $semesterDetail->lab_practical_hours ?? '-' }}</td>
                                        <td>{{ $semesterDetail->course_projects ?? '-' }}</td>
                                        <td>{{ $semesterDetail->project_verification ?? '-' }}</td>
                                        <td>{{ $semesterDetail->professional_training ?? '-' }}</td>
                                        <td>{{ $semesterDetail->lab_practical_duplication ?? '-' }}</td>
                                        <td>{{ $semesterDetail->project_duplication ?? '-' }}</td>
                                        <td>{{ $semesterDetail->verification_duplication ?? '-' }}</td>
                                        <td>{{ $semesterDetail->consultations ?? '-' }}</td>
                                        <td>{{ $semesterDetail->exams ?? '-' }}</td>
                                        <td>{{ $semesterDetail->semester_total ?? '-' }}</td>
                                    @endif
                                @endforeach
                                @foreach($lo->semesterDetails as $semesterDetail)
                                    @if($semesterDetail->semester_number == 4)
                                        <td>{{ $semesterDetail->weeks_count ?? '-' }}</td>
                                        <td>{{ $semesterDetail->hours_per_week ?? '-' }}</td>
                                        <td>{{ $semesterDetail->total_hours ?? '-' }}</td>
                                        <td>{{ $semesterDetail->theoretical_hours ?? '-' }}</td>
                                        <td>{{ $semesterDetail->lab_practical_hours ?? '-' }}</td>
                                        <td>{{ $semesterDetail->course_projects ?? '-' }}</td>
                                        <td>{{ $semesterDetail->project_verification ?? '-' }}</td>
                                        <td>{{ $semesterDetail->professional_training ?? '-' }}</td>
                                        <td>{{ $semesterDetail->lab_practical_duplication ?? '-' }}</td>
                                        <td>{{ $semesterDetail->project_duplication ?? '-' }}</td>
                                        <td>{{ $semesterDetail->verification_duplication ?? '-' }}</td>
                                        <td>{{ $semesterDetail->consultations ?? '-' }}</td>
                                        <td>{{ $semesterDetail->exams ?? '-' }}</td>
                                        <td>{{ $semesterDetail->semester_total ?? '-' }}</td>
                                    @endif
                                @endforeach
                                <td>{{ $lo->yearTotal->total_hours ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
