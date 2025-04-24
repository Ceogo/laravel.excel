<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 6px;
            font-size: 0.85rem;
        }
        .pm-lesson {
            background-color: #ffe5e5;
        }
        .class-hour {
            background-color: #e9ecef;
        }
        .schedule-table {
            border-collapse: collapse;
        }
        .alert {
            max-width: 600px;
            margin: 20px auto;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <h2 class="mb-3 text-center">Расписание для группы: {{ $group->name }}</h2>
        <p class="text-center text-muted mb-4">Семестр: {{ $semester }}, Неделя: {{ $week }}</p>

        <div class="form-container">
            <form method="GET" action="{{ route('schedule') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="group_id" class="form-label">Группа:</label>
                        <select name="group_id" id="group_id" class="form-select" required>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ $g->id == $group->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="semester" class="form-label">Семестр:</label>
                        <select name="semester" id="semester" class="form-select">
                            <option value="3" {{ $semester == 3 ? 'selected' : '' }}>3-й семестр</option>
                            <option value="4" {{ $semester == 4 ? 'selected' : '' }}>4-й семестр</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="week" class="form-label">Неделя:</label>
                        <input type="number" name="week" id="week" class="form-control" value="{{ $week }}" min="1" required>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <button type="submit" class="btn btn-primary">Показать расписание</button>
                </div>
            </form>
        </div>

        @if(empty($schedule['monday']) && empty($schedule['tuesday']) && empty($schedule['wednesday']) && empty($schedule['thursday']) && empty($schedule['friday']))
            <div class="alert alert-warning text-center">
                Расписание не сгенерировано. Проверьте наличие данных.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover schedule-table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Пара</th>
                            <th scope="col">Время</th>
                            <th scope="col">Понедельник</th>
                            <th scope="col">Вторник</th>
                            <th scope="col">Среда</th>
                            <th scope="col">Четверг</th>
                            <th scope="col">Пятница</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="class-hour">
                            <td>-</td>
                            <td>{{ $bellSchedule['monday']['class_hour']['start'] }}–{{ $bellSchedule['monday']['class_hour']['end'] }}</td>
                            <td>Классный час</td>
                            <td colspan="4"></td>
                        </tr>
                        @for($pair = 1; $pair <= 7; $pair++)
                            <tr>
                                <td>{{ $pair }}</td>
                                <td>
                                    {{ $bellSchedule['other_days'][$pair]['start'] }}–
                                    {{ $bellSchedule['other_days'][$pair]['end'] }}
                                    @if($pair == 1)
                                        <br><small>(Пн: {{ $bellSchedule['monday'][$pair]['start'] }}–{{ $bellSchedule['monday'][$pair]['end'] }})</small>
                                    @endif
                                </td>
                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                    <td class="{{ isset($schedule[$day][$pair]['module_index']) && strpos($schedule[$day][$pair]['module_index'], 'ПМ') === 0 ? 'pm-lesson' : '' }}">
                                        @if(isset($schedule[$day][$pair]))
                                            <strong>{{ $schedule[$day][$pair]['discipline_name'] }}</strong><br>
                                            {{ $schedule[$day][$pair]['teacher_name'] }}<br>
                                            <small>
                                                {{ $schedule[$day][$pair]['type'] == 'theoretical' ? 'Теория' : ($schedule[$day][$pair]['type'] == 'lab_practical' ? 'ЛПР' : 'КР/КП') }}
                                            </small>
                                            <br>
                                            <a href="{{ route('schedule.edit', $schedule[$day][$pair]['id']) }}">Редактировать</a>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
