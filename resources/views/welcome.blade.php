@extends('layouts.app')

@section('content')

    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp

    <!-- Заголовок и статистика -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
        <div>
            @if($backgroundEnabled && $backgroundImage)
                <h2 class="text-3xl font-bold text-white">Мои задачи</h2>
                <p class="text-white text-sm">Ваши личные задачи не видны на странице Команда</p>
            @else
                <h2 class="text-3xl font-bold text-[#16a34a]">Мои задачи</h2>
                <p class="text-gray-700 text-sm">Ваши личные задачи не видны на странице Команда</p>
            @endif
        </div>

        <div class="flex space-x-4 w-full md:w-auto">
            <button onclick="openPersonalTaskModal()"
                class="flex-1 md:flex-none bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:from-green-700 hover:to-green-600 transition text-sm md:text-base">
                <i class="fas fa-plus"></i>
                <span>Добавить</span>
            </button>
        </div>
    </div>

    <!-- YouGile-style Filters -->
    <div class="mb-6 max-[500px]:mb-1">
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Кнопка фильтров -->
            <div class="relative">
                <button onclick="toggleFiltersDropdown()"
                    class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition text-sm">
                    <i class="fas fa-filter"></i>
                    <span>Фильтры</span>
                    <span id="activeFiltersCount"
                        class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden">0</span>
                    <i class="fas fa-chevron-down ml-1 text-xs transition-transform" id="filtersChevron"></i>
                </button>

                <!-- Выпадающая панель фильтров -->
                <div id="filtersDropdown"
                    class="hidden absolute left-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800">Фильтрация задач</h3>
                            <button onclick="clearAllFilters()" class="text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-undo-alt mr-1"></i>Сбросить
                            </button>
                        </div>
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        <!-- Приоритет -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('prioritySection')">
                                <span class="font-medium text-gray-700 text-sm">Приоритет</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="prioritySectionIcon"></i>
                            </div>
                            <div id="prioritySection" class="mt-3 space-y-2">
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300  accent-green-600"
                                        data-filter-type="priority" value="critical">
                                    <span class="text-sm text-gray-700">Критический</span>
                                    <span class="text-sm">🚨</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority" value="high">
                                    <span class="text-sm text-gray-700">Высокий</span>
                                    <span class="text-sm">‼️</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority" value="medium">
                                    <span class="text-sm text-gray-700">Средний</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority" value="low">
                                    <span class="text-sm text-gray-700">Низкий</span>
                                </label>
                            </div>
                        </div>

                        <!-- Сроки -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('deadlineSection')">
                                <span class="font-medium text-gray-700 text-sm">Сроки</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="deadlineSectionIcon"></i>
                            </div>
                            <div id="deadlineSection" class="mt-3 space-y-2">
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline" value="overdue">
                                    <span class="text-sm text-gray-700">Просроченные</span>
                                    <span class="text-sm text-red-500">⚠️</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline" value="today">
                                    <span class="text-sm text-gray-700">Сегодня</span>
                                    <span class="text-sm text-orange-500">📅</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline" value="tomorrow">
                                    <span class="text-sm text-gray-700">Завтра</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline" value="week">
                                    <span class="text-sm text-gray-700">На этой неделе</span>
                                </label>
                            </div>
                        </div>

                        <!-- Описание -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('descriptionSection')">
                                <span class="font-medium text-gray-700 text-sm">Описание</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="descriptionSectionIcon"></i>
                            </div>
                            <div id="descriptionSection" class="mt-3 space-y-2">
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="has-description" value="true">
                                    <span class="text-sm text-gray-700">Есть описание</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="has-description" value="false">
                                    <span class="text-sm text-gray-700">Нет описания</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-b-lg">
                        <button onclick="applyFiltersAndClose()"
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                            Применить фильтры
                        </button>
                    </div>
                </div>
            </div>

            <!-- Поиск как в YouGile -->
            <div class="relative flex-1 max-w-xs">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="taskSearchInput" placeholder="Поиск по названию..."
                    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>

        <!-- Активные фильтры (чипсы) -->
        <div id="activeFiltersContainer" class="flex flex-wrap gap-2 mt-3 min-h-[32px] max-[500px]:min-h-[10px]">
            <!-- Сюда динамически добавляются активные фильтры -->
        </div>
    </div>

    <!-- Доска с задачами -->
    <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Колонка "Новые" -->
        <div class="rounded-lg p-4 board-column bg-transparent max-[600px]:p-0" data-status="new">
            @if($backgroundEnabled && $backgroundImage)
                <div
                    class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-white">Новые</h3>
                    <span
                        class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['new'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-2 border-white rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-gray-700">Новые</h3>
                    <span
                        class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['new'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="new">
                @foreach($tasksByStatus['new'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true"
                        data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-description="{{ $task->description ? 'true' : 'false' }}"
                        data-task-name="{{ strtolower($task->name) }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                    title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1 max-[500px]:flex-wrap max-[500px]:gap-1  max-[500px]:space-x-0">
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
                                @if($task->priority === 'высокий')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">‼️ Высокий</span>
                                @elseif($task->priority === 'критический')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">🚨 Критический</span>
                                @endif
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded hidden max-[500px]:inline">
                                    {{ $task->status ?? 'Без статуса' }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($task->deadline && $task->deadline->isPast())
                                    <span class="text-xs text-red-600">⚠️ Просрочена</span>
                                @endif
                                <button onclick="startTask({{ $task->id }})">
                                    <i class="fa-solid fa-check" style="color: #166534;"></i>
                                </button>
                                <button onclick="showRejectModal({{ $task->id }})"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    <i class="fa-solid fa-rectangle-xmark" style="color: #dc2626;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "В работе" -->
        <div class="rounded-lg p-4 board-column max-[600px]:p-0" data-status="in-progress">
            @if($backgroundEnabled && $backgroundImage)
                <div
                    class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-white">В работе</h3>
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['in_progress'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-2 border-white rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-gray-700">В работе</h3>
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['in_progress'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="in-progress">
                @foreach($tasksByStatus['in_progress'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true"
                        data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-description="{{ $task->description ? 'true' : 'false' }}"
                        data-task-name="{{ strtolower($task->name) }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs"
                                    title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}
                        </p>

                        @if($task->deadline)
                            <div class="mb-3">
                                <div
                                    class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $task->deadline->format('d.m.Y H:i') }}
                                    @if($task->deadline->isPast())
                                        <span class="ml-1">(Просрочено)</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1 max-[500px]:flex-wrap max-[500px]:gap-1  max-[500px]:space-x-0">
                                <span
                                    class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
                                @if($task->category)
                                    <span
                                        class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $task->category->name }}</span>
                                @endif
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded hidden max-[500px]:inline">
                                    {{ $task->status ?? 'Без статуса' }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="sendForReview({{ $task->id }})" class=" text-white px-3 py-1 rounded text-sm ">
                                    <i class="fa-solid fa-check" style="color: #166534;"></i>
                                </button>
                                <button onclick="showRejectModal({{ $task->id }})"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fa-solid fa-rectangle-xmark" style="color: #dc2626;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "На проверке" -->
        <div class="rounded-lg p-4 board-column max-[600px]:p-0" data-status="review">
            @if($backgroundEnabled && $backgroundImage)
                <div
                    class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-white shadow-2xs">На проверке</h3>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['review'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-2 border-white rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-gray-700">На проверке</h3>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['review'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="review">
                @foreach($tasksByStatus['review'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true"
                        data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-description="{{ $task->description ? 'true' : 'false' }}"
                        data-task-name="{{ strtolower($task->name) }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs"
                                    title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}
                        </p>

                        @if($task->actual_hours)
                            <div class="mb-3">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-hourglass-end mr-2"></i>
                                    Фактическое время: {{ $task->actual_hours }}ч
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1 max-[500px]:flex-wrap max-[500px]:gap-1  max-[500px]:space-x-0">
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Ожидает проверки
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "Завершено" -->
        <div class="rounded-lg p-4 board-column bg-transparent max-[600px]:p-0" data-status="done">
            @if($backgroundEnabled && $backgroundImage)
                <div
                    class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-white">Завершено</h3>
                    <span
                        class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['done'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-2 border-white rounded-lg p-2 max-[500px]:hidden">
                    <h3 class="font-semibold text-gray-700">Завершено</h3>
                    <span
                        class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['done'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="done">
                @foreach($tasksByStatus['done'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow opacity-80 cursor-move" draggable="true"
                        data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-description="{{ $task->description ? 'true' : 'false' }}"
                        data-task-name="{{ strtolower($task->name) }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white text-xs"
                                    title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}
                        </p>

                        @if($task->actual_hours)
                            <div class="mb-3">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-hourglass-end mr-2"></i>
                                    Затрачено времени: {{ $task->actual_hours }}ч
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span
                                class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded hidden max-[500px]:inline">
                                {{ $task->status ?? 'Без статуса' }}
                            </span>
                            <span class="text-xs text-gray-500">Завершено</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="fixed bottom-6">
        @if($backgroundEnabled && $backgroundImage)
            <p><a href="{{route('allTasks')}}" class="underline text-white">Все задачи</a></p>
        @else
            <p><a href="{{route('allTasks')}}" class="underline text-gray-700">Все задачи</a></p>
        @endif
    </div>

    <!-- Модальные окна (оставлены без изменений) -->
    <div id="taskViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Просмотр задачи</h3>
                <button onclick="closeTaskViewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="taskModalContent"></div>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Отказ от задачи</h3>
            <p class="text-gray-600 mb-4">Пожалуйста, укажите причину отказа от задачи:</p>
            <textarea id="rejectReason" placeholder="Причина отказа..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 h-24 resize-none"></textarea>
            <div class="flex space-x-3">
                <button onclick="submitRejection()"
                    class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">Подтвердить отказ</button>
                <button onclick="closeRejectModal()"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">Отмена</button>
            </div>
        </div>
    </div>

    <div id="timeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Отправка на проверку</h3>
            <p class="text-gray-600 mb-4">Укажите фактическое время работы над задачей:</p>
            <input type="number" id="actualHours" step="0.5" min="0" placeholder="Часы"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
            <div class="flex space-x-3">
                <button onclick="submitForReview()"
                    class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">Отправить на
                    проверку</button>
                <button onclick="closeTimeModal()"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">Отмена</button>
            </div>
        </div>
    </div>

    @include('partials.modal.task.create')

    <script>
        let currentTaskId = null;
        let activeFilters = {
            priority: [],
            deadline: [],
            hasDescription: []
        };

        // Toggle filters dropdown
        function toggleFiltersDropdown() {
            const dropdown = document.getElementById('filtersDropdown');
            const chevron = document.getElementById('filtersChevron');

            dropdown.classList.toggle('hidden');
            chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Toggle filter sections
        function toggleFilterSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById(sectionId + 'Icon');

            section.classList.toggle('hidden');
            icon.style.transform = section.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Apply filters and close dropdown
        function applyFiltersAndClose() {
            collectFiltersFromCheckboxes();
            updateActiveFiltersDisplay();
            applyFilters();
            toggleFiltersDropdown();
        }

        // Collect filters from checkboxes
        function collectFiltersFromCheckboxes() {
            activeFilters = {
                priority: [],
                deadline: [],
                hasDescription: []
            };

            document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => {
                const type = checkbox.dataset.filterType;
                const value = checkbox.value;
                if (activeFilters[type]) {
                    activeFilters[type].push(value);
                }
            });

            // Update counter badge
            updateFiltersCounter();
        }

        // Update counter on filters button
        function updateFiltersCounter() {
            const totalFilters = activeFilters.priority.length + activeFilters.deadline.length + activeFilters.hasDescription.length;
            const counterBadge = document.getElementById('activeFiltersCount');

            if (totalFilters > 0) {
                counterBadge.textContent = totalFilters;
                counterBadge.classList.remove('hidden');
            } else {
                counterBadge.classList.add('hidden');
            }
        }

        // Display active filters as chips
        function updateActiveFiltersDisplay() {
            const container = document.getElementById('activeFiltersContainer');
            container.innerHTML = '';

            // Priority filters
            activeFilters.priority.forEach(priority => {
                const label = priority === 'critical' ? '🚨 Критический' :
                    (priority === 'high' ? '‼️ Высокий' :
                        (priority === 'medium' ? 'Средний' : 'Низкий'));
                addFilterChip(container, 'priority', priority, label);
            });

            // Deadline filters
            activeFilters.deadline.forEach(deadline => {
                const label = deadline === 'overdue' ? '⚠️ Просроченные' :
                    (deadline === 'today' ? '📅 Сегодня' :
                        (deadline === 'tomorrow' ? 'Завтра' : 'На этой неделе'));
                addFilterChip(container, 'deadline', deadline, label);
            });

            // Description filters
            activeFilters.hasDescription.forEach(desc => {
                const label = desc === 'true' ? 'Есть описание' : 'Нет описания';
                addFilterChip(container, 'has-description', desc, label);
            });
        }

        // Add individual filter chip
        function addFilterChip(container, type, value, label) {
            const chip = document.createElement('div');
            chip.className = 'inline-flex items-center bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full';
            chip.innerHTML = `
                                                                                                                                                                            <span>${label}</span>
                                                                                                                                                                            <button onclick="removeFilter('${type}', '${value}')" class="ml-2 text-gray-500 hover:text-gray-700">
                                                                                                                                                                                <i class="fas fa-times-circle text-xs"></i>
                                                                                                                                                                            </button>
                                                                                                                                                                        `;
            container.appendChild(chip);
        }

        // Remove specific filter
        function removeFilter(type, value) {
            // Remove from activeFilters
            const index = activeFilters[type].indexOf(value);
            if (index !== -1) {
                activeFilters[type].splice(index, 1);
            }

            // Uncheck corresponding checkbox
            const checkbox = document.querySelector(`.filter-checkbox[data-filter-type="${type}"][value="${value}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }

            updateActiveFiltersDisplay();
            updateFiltersCounter();
            applyFilters();
        }

        // Clear all filters
        function clearAllFilters() {
            activeFilters = {
                priority: [],
                deadline: [],
                hasDescription: []
            };

            document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            updateActiveFiltersDisplay();
            updateFiltersCounter();
            applyFilters();

            // Close dropdown if open
            const dropdown = document.getElementById('filtersDropdown');
            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                document.getElementById('filtersChevron').style.transform = 'rotate(0deg)';
            }
        }

        // Apply all filters to tasks
        function applyFilters() {
            const taskCards = document.querySelectorAll('.task-card');

            taskCards.forEach(card => {
                let show = true;

                // Priority filter
                if (activeFilters.priority.length > 0) {
                    const priority = card.dataset.priority;
                    if (!activeFilters.priority.includes(priority)) {
                        show = false;
                    }
                }

                // Deadline filter
                if (show && activeFilters.deadline.length > 0) {
                    const deadlineDate = card.dataset.deadline;
                    let matchesDeadline = false;

                    for (const filter of activeFilters.deadline) {
                        if (filter === 'overdue' && deadlineDate && new Date(deadlineDate) < new Date()) {
                            matchesDeadline = true;
                            break;
                        } else if (filter === 'today' && deadlineDate && isToday(new Date(deadlineDate))) {
                            matchesDeadline = true;
                            break;
                        } else if (filter === 'tomorrow' && deadlineDate && isTomorrow(new Date(deadlineDate))) {
                            matchesDeadline = true;
                            break;
                        } else if (filter === 'week' && deadlineDate && isThisWeek(new Date(deadlineDate))) {
                            matchesDeadline = true;
                            break;
                        }
                    }

                    if (!matchesDeadline) {
                        show = false;
                    }
                }

                // Description filter
                if (show && activeFilters.hasDescription.length > 0) {
                    const hasDescription = card.dataset.hasDescription;
                    if (!activeFilters.hasDescription.includes(hasDescription)) {
                        show = false;
                    }
                }

                // Search filter
                const searchTerm = document.getElementById('taskSearchInput').value.toLowerCase();
                if (show && searchTerm) {
                    const taskName = card.dataset.taskName;
                    if (!taskName.includes(searchTerm)) {
                        show = false;
                    }
                }

                card.style.display = show ? '' : 'none';
            });

            updateColumnCounters();
        }

        // Helper date functions
        function isToday(date) {
            const today = new Date();
            return date.getDate() === today.getDate() &&
                date.getMonth() === today.getMonth() &&
                date.getFullYear() === today.getFullYear();
        }

        function isTomorrow(date) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            return date.getDate() === tomorrow.getDate() &&
                date.getMonth() === tomorrow.getMonth() &&
                date.getFullYear() === tomorrow.getFullYear();
        }

        function isThisWeek(date) {
            const today = new Date();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            startOfWeek.setHours(0, 0, 0, 0);

            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            endOfWeek.setHours(23, 59, 59, 999);

            return date >= startOfWeek && date <= endOfWeek;
        }

        // Update column counters
        function updateColumnCounters() {
            document.querySelectorAll('.board-column').forEach(column => {
                const visibleTasks = column.querySelectorAll('.task-card:not([style*="display: none"])').length;
                const counterSpan = column.querySelector('.stat-count');
                if (counterSpan) {
                    counterSpan.textContent = visibleTasks;
                }
            });
        }

        // Search input handler
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('taskSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyFilters();
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function (event) {
                const dropdown = document.getElementById('filtersDropdown');
                const filterButton = event.target.closest('[onclick="toggleFiltersDropdown()"]');

                if (!dropdown.contains(event.target) && !filterButton && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    document.getElementById('filtersChevron').style.transform = 'rotate(0deg)';
                }
            });
        });

        // Rest of your existing functions (openPersonalTaskModal, createPersonalTask, etc.)
        // ... (keep all your existing task management functions here)

        function openPersonalTaskModal() {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');

            modal.querySelector('h3').textContent = 'Новая личная задача';
            modal.querySelector('p').textContent = 'Создайте задачу для себя';

            const executorField = document.querySelector('select[name="user_id"]')?.closest('.space-y-2');
            const departmentField = document.querySelector('select[name="department_id"]')?.closest('.space-y-2');
            const statusField = document.querySelector('select[name="status"]')?.closest('.space-y-2');

            const departmentSelect = document.querySelector('select[name="department_id"]');
            const statusSelect = document.querySelector('select[name="status"]');

            if (executorField) executorField.style.display = 'none';

            if (departmentField && departmentSelect) {
                @if($user->department_id && $user->department)
                    departmentSelect.removeAttribute('required');
                    departmentSelect.innerHTML = `<option value="{{ $user->department_id }}" selected>{{ $user->department->name }}</option>`;
                    departmentSelect.disabled = true;
                    departmentField.style.display = 'block';
                @else
                    departmentSelect.removeAttribute('required');
                    departmentField.style.display = 'none';
                @endif
                                                                                                                                                                            }

            if (statusField && statusSelect) {
                statusSelect.innerHTML = `<option value="назначена" selected>назначена</option>`;
                statusSelect.disabled = true;
                statusField.style.display = 'block';
            }

            form.onsubmit = createPersonalTask;
            modal.classList.remove('hidden');
        }

        async function createPersonalTask(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            formData.set('user_id', '{{ $user->id }}');
            formData.set('author_id', '{{ $user->id }}');
            formData.set('status', 'назначена');

            @if($user->department_id)
                formData.set('department_id', '{{ $user->department_id }}');
            @endif

                                                                                                                                                                            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Создание...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/tasks/personal/store', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('Личная задача успешно создана!');
                    closeTaskModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert(data.message || 'Ошибка при создании задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при создании задачи');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        function closeTaskModal() {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');

            const executorField = document.querySelector('select[name="user_id"]')?.closest('.space-y-2');
            const departmentField = document.querySelector('select[name="department_id"]')?.closest('.space-y-2');
            const statusField = document.querySelector('select[name="status"]')?.closest('.space-y-2');

            if (executorField) executorField.style.display = 'block';

            if (departmentField) departmentField.style.display = 'block';
            if (statusField) statusField.style.display = 'block';

            modal.querySelector('h3').textContent = 'Новая задача';
            modal.querySelector('p').textContent = 'Заполните информацию о задаче';

            form.onsubmit = null;
            modal.classList.add('hidden');
            form.reset();
        }

        async function startTask(taskId) {
            try {
                const response = await fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'в работе' })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Задача переведена в работу!');
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при обновлении статуса');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при обновлении статуса');
            }
        }

        async function sendForReview(taskId) {
            currentTaskId = taskId;
            document.getElementById('timeModal').classList.remove('hidden');
        }

        async function submitForReview() {
            const actualHours = document.getElementById('actualHours').value;
            if (!actualHours || actualHours <= 0) {
                alert('Пожалуйста, укажите корректное время работы');
                return;
            }

            try {
                const response = await fetch(`/tasks/${currentTaskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'на проверке', actual_hours: actualHours })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Задача отправлена на проверку!');
                    closeTimeModal();
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при отправке на проверку');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при отправке на проверку');
            }
        }

        function showRejectModal(taskId) {
            currentTaskId = taskId;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        async function submitRejection() {
            const reason = document.getElementById('rejectReason').value.trim();
            if (!reason) {
                alert('Пожалуйста, укажите причину отказа');
                return;
            }

            try {
                const response = await fetch(`/tasks/${currentTaskId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ reason })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Вы отказались от задачи');
                    closeRejectModal();
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при отказе от задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при отказе от задачи');
            }
        }

        function closeTimeModal() {
            document.getElementById('timeModal').classList.add('hidden');
            document.getElementById('actualHours').value = '';
            currentTaskId = null;
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectReason').value = '';
            currentTaskId = null;
        }

        function openTaskViewModal(taskId) {
            fetch(`/tasks/${taskId}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('taskModalContent').innerHTML = html;
                    document.getElementById('taskViewModal').classList.remove('hidden');
                });
        }

        function closeTaskViewModal() {
            document.getElementById('taskViewModal').classList.add('hidden');
            document.getElementById('taskModalContent').innerHTML = '';
        }

        document.addEventListener('click', function (e) {
            if (e.target.id === 'taskViewModal') closeTaskViewModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeTaskViewModal();
        });
    </script>

    <style>
        .task-card {
            transition: all 0.2s ease-in-out;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .board-column {
            min-height: 600px;
        }

        @media(max-width:500px) {
            .board-column {
                min-height: auto;
            }
        }

        #filtersDropdown {
            transition: all 0.2s ease;
        }
    </style>
@endsection