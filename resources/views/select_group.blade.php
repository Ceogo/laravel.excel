<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выбор группы</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Выберите группу</h2>
        <form method="GET" action="{{ route('schedule') }}">
            <div class="mb-3">
                <label for="group_id">Группа:</label>
                <select name="group_id" id="group_id" class="form-control" required>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="semester">Семестр:</label>
                <select name="semester" id="semester" class="form-control">
                    <option value="3" {{ $semester == 3 ? 'selected' : '' }}>3-й семестр</option>
                    <option value="4" {{ $semester == 4 ? 'selected' : '' }}>4-й семестр</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="week">Неделя:</label>
                <input type="number" name="week" id="week" class="form-control" value="{{ $week }}" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Показать расписание</button>
        </form>
    </div>
</body>
</html>
