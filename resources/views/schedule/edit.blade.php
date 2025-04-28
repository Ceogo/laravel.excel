<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать пару</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .hover-scale {
            transition: transform 0.2s;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white sidebar md:transform-none z-50" id="sidebar">
        <div class="p-4">
            <h2 class="text-2xl font-bold">Навигация</h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('document') }}" class="flex items-center px-4 py-3 hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Учебный план
            </a>
            <a href="{{ route('edit_data') }}" class="flex items-center px-4 py-3 hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15.828l-2.828.586.586-2.828L16.586 6.586z"></path></svg>
                Редактирование данных
            </a>
            <a href="{{ route('schedule') }}" class="flex items-center px-4 py-3 hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Расписание
            </a>
            <a href="{{ route('upload') }}" class="flex items-center px-4 py-3 hover:bg-gray-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6M9 19h6m-3-3v6"></path></svg>
                Загрузка документа
            </a>
            <a href="{{ isset($schedule) ? route('schedule.edit', $schedule->id) : '#' }}" class="flex items-center px-4 py-3 hover:bg-gray-700 transition {{ !isset($schedule) ? 'opacity-50 cursor-not-allowed' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Редактировать пару
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="md:ml-64 p-6">
        <div class="container mx-auto fade-in">
            <!-- Burger Menu Button -->
            <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Редактировать пару</h2>
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto hover-scale">
                <form method="POST" action="{{ route('schedule.edit', $schedule->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="learning_outcome_id" class="block text-sm font-medium text-gray-700">Дисциплина:</label>
                        <select name="learning_outcome_id" id="learning_outcome_id" class="mt-1 block w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                            @foreach ($learningOutcomes as $lo)
                                <option value="{{ $lo->id }}" {{ $lo->id == $schedule->learning_outcome_id ? 'selected' : '' }}>
                                    {{ $lo->discipline_name }} ({{ $lo->teacher_name ?? 'вакансия' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-open');
        });
    </script>
</body>
</html>