<!DOCTYPE html>
<html>
<head>
    <title>Учебный план</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .group-header { background-color: #f0f8ff;  }
        .sub-header { background-color: #e9ecef; }
        .nested-header { background-color: #f8f9fa; }
        th { vertical-align: middle; text-align: center; }
        td { white-space: nowrap; padding: 8px; }
        table { table-layout: auto; width: 100%; }
        .group-header[rowspan="3"] { height: 100%; vertical-align: top; }
        .group-header[rowspan="2"] { height: 66%; min-height: 80px; }
        .group-header[rowspan="1"] { height: 33%; min-height: 40px; }
        .vertical-align-middle {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Данные учебного плана</h2>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    @foreach($headerRows as $level => $columns)
                        <tr>
                            @php ksort($columns); @endphp
                            @foreach($columns as $col)
                                <th colspan="{{ $col['colspan'] }}" rowspan="{{ $col['rowspan'] }}" class="text-center align-middle border">
                                    {{ $col['title'] }}
                                </th>
                            @endforeach
                        </tr>
                    @endforeach
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            @foreach($flattenedHeaders as $header)
                                <td class="py-2">{{ $row[$header] ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
