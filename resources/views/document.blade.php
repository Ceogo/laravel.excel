<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учебный план</title>
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
        .table-header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
        }
        .table-row:nth-child(even) {
            background-color: #f9fafb;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
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

            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Учебный план</h2>
            @if (session('success'))
                <div class="alert-success p-4 rounded-lg mb-6 shadow-md text-center">
                    {{ session('success') }}
                </div>
            @endif
            @foreach($groups as $group)
                <div class="bg-white p-6 rounded-lg shadow-lg mb-8 hover-scale">
                    <h3 class="text-2xl font-semibold text-gray-700">Группа: {{ $group->name }} ({{ $group->specialty_code ?? 'Не указан' }})</h3>
                    <p class="text-gray-600">Специальность: {{ $group->specialty_name ?? 'Не указана' }}</p>
                    <p class="text-gray-600">Количество обучающихся: {{ $group->students_count ?? 'Не указано' }}</p>
                    <div class="overflow-x-auto mt-4">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="table-header">
                                    @foreach($headers as $header)
                                        @if(is_array($header))
                                            <th colspan="{{ $headerCounts[$header['title']] ?? 1 }}" class="p-3 text-sm">{{ $header['title'] }}</th>
                                        @else
                                            <th rowspan="2" class="p-3 text-sm">{{ $header }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr class="table-header">
                                    @foreach($headers as $header)
                                        @if(is_array($header))
                                            @foreach($header['children'] as $child)
                                                @if(is_array($child))
                                                    @foreach($child['children'] as $subChild)
                                                        <th class="p-3 text-sm">{{ $subChild }}</th>
                                                    @endforeach
                                                @else
                                                    <th class="p-3 text-sm">{{ $child }}</th>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->modules as $module)
                                    <tr class="table-row">
                                        <td class="p-3 border">{{ $module->index }}</td>
                                        <td class="p-3 border">{{ $module->name }}</td>
                                        <td colspan="{{ count($flattenedHeaders) - 2 }}" class="p-3 border"></td>
                                    </tr>
                                    @foreach($module->learningOutcomes as $lo)
                                        <tr class="table-row">
                                            <td class="p-3 border">{{ $lo->index }}</td>
                                            <td class="p-3 border">{{ $lo->discipline_name }}</td>
                                            <td class="p-3 border">{{ $lo->teacher_name ?? 'вакансия' }}</td>
                                            <td class="p-3 border">{{ $lo->semesterDistribution->exams ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->semesterDistribution->credits ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->semesterDistribution->course_works ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->semesterDistribution->control_works ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->rupDetail->credits ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->rupDetail->total_hours ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->rupDetail->theoretical_hours ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->rupDetail->lab_practical_hours ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->rupDetail->course_works ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->rupDetail->professional_practice ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->academicYearDetail->total_hours ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->academicYearDetail->theoretical_hours ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->academicYearDetail->lab_practical_hours ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->academicYearDetail->course_works ?? '-' }}</td>
                                            <td class="p-3 border">{{ $lo->academicYearDetail->professional_training ?? '-' }}</td>
                                            @foreach($lo->semesterDetails as $semesterDetail)
                                                @if($semesterDetail->semester_number == 3)
                                                    <td class="p-3 border">{{ $semesterDetail->weeks_count ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->hours_per_week ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->total_hours ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->theoretical_hours ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->lab_practical_hours ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->course_projects ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->project_verification ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->professional_training ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->lab_practical_duplication ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->project_duplication ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->verification_duplication ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->consultations ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->exams ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->semester_total ?? '-' }}</td>
                                                @endif
                                            @endforeach
                                            @foreach($lo->semesterDetails as $semesterDetail)
                                                @if($semesterDetail->semester_number == 4)
                                                    <td class="p-3 border">{{ $semesterDetail->weeks_count ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->hours_per_week ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->total_hours ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->theoretical_hours ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->lab_practical_hours ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->course_projects ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->project_verification ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->professional_training ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->lab_practical_duplication ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->project_duplication ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->verification_duplication ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->consultations ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->exams ?? '-' }}</td>
                                                    <td class="p-3 border">{{ $semesterDetail->semester_total ?? '-' }}</td>
                                                @endif
                                            @endforeach
                                            <td class="p-3 border">{{ $lo->yearTotal->total_hours ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
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
