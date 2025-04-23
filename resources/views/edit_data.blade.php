<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование данных</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Редактирование данных учебного плана</h2>
        <form method="POST" action="{{ route('save_data') }}">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Включить</th>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $index => $row)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" name="enable_row[{{ $index }}]" checked class="form-check-input">
                                </div>
                            </td>
                            @foreach($headers as $header)
                                <td>
                                    <input type="text" name="data[{{ $index }}][{{ $header }}]" value="{{ $row[$header] ?? '' }}" class="form-control">
                                </td>
                            @endforeach
                            <!-- Скрытые поля для module_index и module_name -->
                            <input type="hidden" name="data[{{ $index }}][module_index]" value="{{ $row['module_index'] ?? 'UNKNOWN_' . $index }}">
                            <input type="hidden" name="data[{{ $index }}][module_name]" value="{{ $row['module_name'] ?? 'Неизвестный модуль' }}">
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</body>
</html>
