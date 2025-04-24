<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать пару</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <h2>Редактировать пару</h2>
        <form method="POST" action="{{ route('schedule.edit', $schedule->id) }}">
            @csrf
            <div class="mb-3">
                <label for="learning_outcome_id" class="form-label">Дисциплина:</label>
                <select name="learning_outcome_id" id="learning_outcome_id" class="form-select">
                    @foreach ($learningOutcomes as $lo)
                        <option value="{{ $lo->id }}" {{ $lo->id == $schedule->learning_outcome_id ? 'selected' : '' }}>
                            {{ $lo->discipline_name }} ({{ $lo->teacher_name ?? 'вакансия' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</body>
</html>
