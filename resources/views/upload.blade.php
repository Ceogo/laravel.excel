<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Загрузка документа</h2>
        <form action="{{ route('document') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file"><br><br>
            <button>Отправить</button>
        </form>
    </div>
</body>
</html>
