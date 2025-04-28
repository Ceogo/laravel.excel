<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .pm-lesson {
            background-color: #ffe5e5;
        }
        .class-hour {
            background-color: #e9ecef;
        }
        .table-header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
        }
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
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
        .draggable:hover {
            cursor: move;
            background-color: #e0e7ff;
        }
        .drag-over {
            background-color: #dbeafe;
            border: 2px dashed #3b82f6;
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
            <div id="edit-pair-dropzone" class="flex items-center px-4 py-3 hover:bg-gray-700 transition dropzone" ondragover="event.preventDefault()" ondrop="handleEditDrop(event)" ondragenter="this.classList.add('bg-blue-600')" ondragleave="this.classList.remove('bg-blue-600')">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Редактировать пару
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="md:ml-64 p-6">
        <div class="container mx-auto fade-in">
            <!-- Burger Menu Button -->
            <button id="toggleSidebar" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800 text-white rounded">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">Расписание для группы: {{ $group->name }}</h2>
            <p class="text-center text-gray-600 mb-6">Семестр: {{ $semester }}, Неделя: {{ $week }}</p>

            @if (session('success'))
                <div class="alert-success p-4 rounded-lg shadow-md text-center mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warnings'))
                <div class="alert-warning p-4 rounded-lg shadow-md text-center mb-6">
                    @foreach (session('warnings') as $warning)
                        <p>{{ $warning }}</p>
                    @endforeach
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-lg mb-6 hover-scale">
                <form method="GET" action="{{ route('schedule') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="group_id" class="block text-sm font-medium text-gray-700">Группа:</label>
                        <select name="group_id" id="group_id" class="mt-1 block w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ $g->id == $group->id ? 'selected' : '' }}>{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700">Семестр:</label>
                        <select name="semester" id="semester" class="mt-1 block w-full p-2 border rounded focus:ring-2 focus:ring-blue-500">
                            <option value="3" {{ $semester == 3 ? 'selected' : '' }}>3-й семестр</option>
                            <option value="4" {{ $semester == 4 ? 'selected' : '' }}>4-й семестр</option>
                        </select>
                    </div>
                    <div>
                        <label for="week" class="block text-sm font-medium text-gray-700">Неделя:</label>
                        <input type="number" name="week" id="week" class="mt-1 block w-full p-2 border rounded focus:ring-2 focus:ring-blue-500" value="{{ $week }}" min="1" required>
                    </div>
                    <div class="md:col-span-3 text-center mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">Показать расписание</button>
                    </div>
                </form>
            </div>

            @if(empty($schedule['monday']) && empty($schedule['tuesday']) && empty($schedule['wednesday']) && empty($schedule['thursday']) && empty($schedule['friday']))
                <div class="alert-warning p-4 rounded-lg shadow-md text-center">
                    Расписание не сгенерировано. Проверьте наличие данных.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="table-header">
                                <th class="p-3">Пара</th>
                                <th class="p-3">Время</th>
                                <th class="p-3">Понедельник</th>
                                <th class="p-3">Вторник</th>
                                <th class="p-3">Среда</th>
                                <th class="p-3">Четверг</th>
                                <th class="p-3">Пятница</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="class-hour">
                                <td class="p-3 border">-</td>
                                <td class="p-3 border">{{ $bellSchedule['monday']['class_hour']['start'] }}–{{ $bellSchedule['monday']['class_hour']['end'] }}</td>
                                <td class="p-3 border">Классный час</td>
                                <td colspan="4" class="p-3 border"></td>
                            </tr>
                            @for($pair = 1; $pair <= 7; $pair++)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 border">{{ $pair }}</td>
                                    <td class="p-3 border">
                                        {{ $bellSchedule['other_days'][$pair]['start'] }}–
                                        {{ $bellSchedule['other_days'][$pair]['end'] }}
                                        @if($pair == 1)
                                            <br><small>(Пн: {{ $bellSchedule['monday'][$pair]['start'] }}–{{ $bellSchedule['monday'][$pair]['end'] }})</small>
                                        @endif
                                    </td>
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                                        <td class="p-3 border {{ isset($schedule[$day][$pair]['module_index']) && strpos($schedule[$day][$pair]['module_index'], 'ПМ') === 0 ? 'pm-lesson' : '' }} {{ isset($schedule[$day][$pair]) ? 'draggable' : '' }}"
                                            @if(isset($schedule[$day][$pair]))
                                                draggable="true"
                                                ondragstart="event.dataTransfer.setData('source', '{{ $day }}_{{ $pair }}'); event.dataTransfer.setData('scheduleId', '{{ $schedule[$day][$pair]['id'] }}')"
                                                ondragover="event.preventDefault()"
                                                ondrop="handleSwapDrop(event, '{{ $day }}_{{ $pair }}')"
                                                ondragenter="this.classList.add('drag-over')"
                                                ondragleave="this.classList.remove('drag-over')"
                                            @endif
                                        >
                                            @if(isset($schedule[$day][$pair]))
                                                <strong>{{ $schedule[$day][$pair]['discipline_name'] }}</strong><br>
                                                {{ $schedule[$day][$pair]['teacher_name'] }}<br>
                                                <small>
                                                    {{ $schedule[$day][$pair]['type'] == 'theoretical' ? 'Теория' : ($schedule[$day][$pair]['type'] == 'lab_practical' ? 'ЛПР' : 'КР/КП') }}
                                                </small>
                                                <br>
                                                <a href="{{ route('schedule.edit', $schedule[$day][$pair]['id']) }}" class="text-blue-600 hover:underline">Редактировать</a>
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
    </div>

    <script>
        // Toggle Sidebar
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        toggleSidebar.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-open');
        });

        // Handle Swap Drop (перестановка пар)
        function handleSwapDrop(event, target) {
            event.preventDefault();
            const source = event.dataTransfer.getData('source');
            const [sourceDay, sourcePair] = source.split('_');
            const [targetDay, targetPair] = target.split('_');

            fetch('{{ route("schedule.swap") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    source: { day: sourceDay, pair: sourcePair },
                    target: { day: targetDay, pair: targetPair },
                    group_id: '{{ $group->id }}',
                    semester: '{{ $semester }}',
                    week: '{{ $week }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Обновляем страницу
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                alert('Ошибка при перестановке: ' + error.message);
            });

            event.target.classList.remove('drag-over');
        }

        // Handle Edit Drop (перетаскивание в сайдбар для редактирования)
        function handleEditDrop(event) {
            event.preventDefault();
            const scheduleId = event.dataTransfer.getData('scheduleId');
            if (scheduleId) {
                window.location.href = '{{ route("schedule.edit", ":id") }}'.replace(':id', scheduleId);
            }
            event.target.classList.remove('bg-blue-600');
        }
    </script>
</body>
</html>