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
    <div class="mb-4 max-[500px]:mb-1">
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Кнопка фильтров -->
            <div class="relative">
                @if($backgroundEnabled && $backgroundImage)
                    <button onclick="toggleFiltersDropdown()"
                        class="bg-transparent/20 border-none text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <span id="activeFiltersCount"
                            class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden">0</span>
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform" id="filtersChevron"></i>
                    </button>
                @else
                    <button onclick="toggleFiltersDropdown()"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <span id="activeFiltersCount"
                            class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden">0</span>
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform" id="filtersChevron"></i>
                    </button>
                @endif

                <!-- Выпадающая панель фильтров -->
                <div id="filtersDropdown"
                    class="hidden absolute left-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border z-50">
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

                        <!-- Файлы -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('filesSection')">
                                <span class="font-medium text-gray-700 text-sm">Файлы</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="filesSectionIcon"></i>
                            </div>
                            <div id="filesSection" class="mt-3 space-y-2">
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="has-files" value="true">
                                    <span class="text-sm text-gray-700">Есть файлы</span>
                                    <span class="text-sm">📎</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="has-files" value="false">
                                    <span class="text-sm text-gray-700">Нет файлов</span>
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

            <!-- Поиск -->
            @if($backgroundEnabled && $backgroundImage)
                <div class="relative flex-1 max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white text-sm"></i>
                    <input type="text" id="taskSearchInput" placeholder="Поиск по названию..."
                        class="w-full pl-9 pr-3 py-2 border-none rounded-lg text-sm text-white focus:outline-none bg-transparent/20 placeholder:text-white">
                </div>
            @else
                <div class="relative flex-1 max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="taskSearchInput" placeholder="Поиск по названию..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            @endif
        </div>

        <!-- Активные фильтры (чипсы) -->
        <div id="activeFiltersContainer" class="flex flex-wrap gap-2 mt-3 min-h-[32px] max-[500px]:min-h-[10px]">
            <!-- Сюда динамически добавляются активные фильтры -->
        </div>
    </div>

    <!-- Доска с задачами -->
    <div class="grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-4 gap-6">
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
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 max-[500px]:hidden canban-col-title">
                    <h3 class="font-semibold text-white">Новые</h3>
                    <span
                        class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['new'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="new">
                @foreach($tasksByStatus['new'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move  min-h-[100px] flex flex-col justify-between"
                        draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                        data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}">
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
                        @if($task->files_count > 0)
                            <div class="mb-2 flex items-center text-xs text-gray-500">
                                <i class="fas fa-paperclip mr-1"></i>
                                <span>Файлы: {{ $task->files_count }}</span>
                            </div>
                        @endif
                        @if($task->deadline)
                            <div class="mb-3 max-[500px]:hidden">
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
                                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                                    class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
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
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 max-[500px]:hidden canban-col-title">
                    <h3 class="font-semibold text-white">В работе</h3>
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['in_progress'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="in-progress">
                @foreach($tasksByStatus['in_progress'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between"
                        draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                        data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}">
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
                        @if($task->files_count > 0)
                            <div class="mb-2 flex items-center text-xs text-gray-500">
                                <i class="fas fa-paperclip mr-1"></i>
                                <span>Файлы: {{ $task->files_count }}</span>
                            </div>
                        @endif
                        @if($task->deadline)
                            <div class="mb-3 max-[500px]:hidden">
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
                                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                                    class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
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
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 max-[500px]:hidden canban-col-title">
                    <h3 class="font-semibold text-white">На проверке</h3>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['review'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="review">
                @foreach($tasksByStatus['review'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between"
                        draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                        data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}">
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
                        @if($task->files_count > 0)
                            <div class="mb-2 flex items-center text-xs text-gray-500">
                                <i class="fas fa-paperclip mr-1"></i>
                                <span>Файлы: {{ $task->files_count }}</span>
                            </div>
                        @endif
                        @if($task->deadline)
                            <div class="mb-3 max-[500px]:hidden">
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
                                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                                    class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
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
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 max-[500px]:hidden canban-col-title">
                    <h3 class="font-semibold text-white">Завершено</h3>
                    <span
                        class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['done'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="done">
                @foreach($tasksByStatus['done'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow opacity-80 cursor-move flex flex-col justify-between"
                        draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                        data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                        data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                        data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}">
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
                        @if($task->files_count > 0)
                            <div class="mb-2 flex items-center text-xs text-gray-500">
                                <i class="fas fa-paperclip mr-1"></i>
                                <span>Файлы: {{ $task->files_count }}</span>
                            </div>
                        @endif
                        @if($task->actual_hours)
                            <div class="mb-3 max-[500px]:hidden">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-hourglass-end mr-2"></i>
                                    Затрачено времени: {{ $task->actual_hours }}ч
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                                class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? '' : 'Без отдела') }}</span>
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

    <!-- Модальные окна -->
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
            hasFiles: []
        };

        // Маппинг значений для фильтрации
        const priorityMap = {
            'critical': 'критический',
            'high': 'высокий',
            'medium': 'средний',
            'low': 'низкий'
        };

        // Toggle filters dropdown
        function toggleFiltersDropdown() {
            const dropdown = document.getElementById('filtersDropdown');
            const chevron = document.getElementById('filtersChevron');

            if (dropdown && chevron) {
                dropdown.classList.toggle('hidden');
                chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        // Toggle filter sections
        function toggleFilterSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById(sectionId + 'Icon');

            if (section && icon) {
                section.classList.toggle('hidden');
                icon.style.transform = section.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
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
                hasFiles: []
            };

            document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => {
                const type = checkbox.dataset.filterType;
                const value = checkbox.value;
                if (activeFilters[type]) {
                    activeFilters[type].push(value);
                }
            });

            updateFiltersCounter();
        }

        // Update counter on filters button
        function updateFiltersCounter() {
            const totalFilters = activeFilters.priority.length + activeFilters.deadline.length + activeFilters.hasFiles.length;
            const counterBadge = document.getElementById('activeFiltersCount');

            if (counterBadge) {
                if (totalFilters > 0) {
                    counterBadge.textContent = totalFilters;
                    counterBadge.classList.remove('hidden');
                } else {
                    counterBadge.classList.add('hidden');
                }
            }
        }

        // Display active filters as chips
        function updateActiveFiltersDisplay() {
            const container = document.getElementById('activeFiltersContainer');
            if (!container) return;

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

            // Files filters
            activeFilters.hasFiles.forEach(hasFile => {
                const label = hasFile === 'true' ? '📎 Есть файлы' : 'Нет файлов';
                addFilterChip(container, 'has-files', hasFile, label);
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
            const index = activeFilters[type].indexOf(value);
            if (index !== -1) {
                activeFilters[type].splice(index, 1);
            }

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
                hasFiles: []
            };

            document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Clear search input
            const searchInput = document.getElementById('taskSearchInput');
            if (searchInput) {
                searchInput.value = '';
            }

            updateActiveFiltersDisplay();
            updateFiltersCounter();
            applyFilters();

            const dropdown = document.getElementById('filtersDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                const chevron = document.getElementById('filtersChevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Apply all filters to tasks
        function applyFilters() {
            const taskCards = document.querySelectorAll('.task-card');
            const searchTerm = document.getElementById('taskSearchInput')?.value.toLowerCase().trim() || '';

            taskCards.forEach(card => {
                let show = true;

                // Priority filter
                if (activeFilters.priority.length > 0) {
                    const priorityValue = card.dataset.priority;
                    let matchesPriority = false;
                    for (const filter of activeFilters.priority) {
                        const russianValue = priorityMap[filter];
                        if (priorityValue === russianValue) {
                            matchesPriority = true;
                            break;
                        }
                    }
                    if (!matchesPriority) {
                        show = false;
                    }
                }

                // Deadline filter
                if (show && activeFilters.deadline.length > 0) {
                    const deadlineDateStr = card.dataset.deadline;
                    let matchesDeadline = false;

                    for (const filter of activeFilters.deadline) {
                        if (filter === 'overdue') {
                            if (deadlineDateStr) {
                                const deadlineDate = new Date(deadlineDateStr);
                                const today = new Date();
                                today.setHours(0, 0, 0, 0);
                                if (deadlineDate < today) {
                                    matchesDeadline = true;
                                    break;
                                }
                            }
                        } else if (filter === 'today') {
                            if (deadlineDateStr && isToday(new Date(deadlineDateStr))) {
                                matchesDeadline = true;
                                break;
                            }
                        } else if (filter === 'tomorrow') {
                            if (deadlineDateStr && isTomorrow(new Date(deadlineDateStr))) {
                                matchesDeadline = true;
                                break;
                            }
                        } else if (filter === 'week') {
                            if (deadlineDateStr && isThisWeek(new Date(deadlineDateStr))) {
                                matchesDeadline = true;
                                break;
                            }
                        }
                    }

                    if (!matchesDeadline) {
                        show = false;
                    }
                }

                // Files filter
                if (show && activeFilters.hasFiles.length > 0) {
                    const hasFilesValue = card.dataset.hasFiles;
                    let matchesFiles = false;
                    for (const filter of activeFilters.hasFiles) {
                        if (hasFilesValue === filter) {
                            matchesFiles = true;
                            break;
                        }
                    }
                    if (!matchesFiles) {
                        show = false;
                    }
                }

                // Search filter - ИСПРАВЛЕНО
                if (show && searchTerm) {
                    const taskName = card.dataset.taskName || '';
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
            today.setHours(0, 0, 0, 0);
            date.setHours(0, 0, 0, 0);
            return date.getTime() === today.getTime();
        }

        function isTomorrow(date) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(0, 0, 0, 0);
            date.setHours(0, 0, 0, 0);
            return date.getTime() === tomorrow.getTime();
        }

        function isThisWeek(date) {
            const today = new Date();
            const currentDay = today.getDay();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - currentDay + (currentDay === 0 ? -6 : 1));
            startOfWeek.setHours(0, 0, 0, 0);

            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            endOfWeek.setHours(23, 59, 59, 999);

            return date >= startOfWeek && date <= endOfWeek;
        }

        // Update column counters
        function updateColumnCounters() {
            const columns = document.querySelectorAll('.board-column');

            columns.forEach(column => {
                const taskContainer = column.querySelector('.task-container');
                if (taskContainer) {
                    const visibleTasks = taskContainer.querySelectorAll('.task-card:not([style*="display: none"])').length;
                    const counterSpan = column.querySelector('.stat-count');
                    if (counterSpan) {
                        counterSpan.textContent = visibleTasks;
                    }
                }
            });
        }

        // Функция для отладки
        function debugSearch() {
            console.log('=== DEBUG SEARCH ===');
            const searchTerm = document.getElementById('taskSearchInput')?.value;
            console.log('Search term:', searchTerm);
            const taskCards = document.querySelectorAll('.task-card');
            taskCards.forEach((card, i) => {
                const taskName = card.dataset.taskName;
                const display = card.style.display;
                console.log(`${i + 1}. "${taskName}" - display: ${display}`);
            });
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('taskSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyFilters();
                });
                searchInput.addEventListener('keyup', function () {
                    applyFilters();
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function (event) {
                const dropdown = document.getElementById('filtersDropdown');
                const filterButton = event.target.closest('[onclick="toggleFiltersDropdown()"]');
                const isInsideDropdown = dropdown && dropdown.contains(event.target);

                if (dropdown && !dropdown.classList.contains('hidden') && !filterButton && !isInsideDropdown) {
                    dropdown.classList.add('hidden');
                    const chevron = document.getElementById('filtersChevron');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }
            });

            // Инициализация перетаскивания
            initDragAndDrop();

            // Для отладки - раскомментировать при необходимости
            // window.debugSearch = debugSearch;
        });

        // Drag and drop functionality
        function initDragAndDrop() {
            const taskCards = document.querySelectorAll('.task-card');
            const columns = document.querySelectorAll('.board-column');

            taskCards.forEach(card => {
                card.setAttribute('draggable', 'true');
                card.addEventListener('dragstart', dragStart);
                card.addEventListener('dragend', dragEnd);
            });

            columns.forEach(column => {
                column.addEventListener('dragover', dragOver);
                column.addEventListener('drop', drop);
            });
        }

        let draggedItem = null;

        function dragStart(e) {
            draggedItem = this;
            e.dataTransfer.setData('text/plain', this.dataset.task);
            this.style.opacity = '0.5';
        }

        function dragEnd(e) {
            if (draggedItem) {
                draggedItem.style.opacity = '';
                draggedItem = null;
            }
        }

        function dragOver(e) {
            e.preventDefault();
        }

        function drop(e) {
            e.preventDefault();
            const column = this.closest('.board-column');
            if (!column || !draggedItem) return;

            const newStatus = column.dataset.status;
            const taskId = draggedItem.dataset.task;

            let statusMap = {
                'new': 'назначена',
                'in-progress': 'в работе',
                'review': 'на проверке',
                'done': 'выполнена'
            };

            const newStatusValue = statusMap[newStatus];
            if (!newStatusValue) return;

            updateTaskStatus(taskId, newStatusValue);
        }

        async function updateTaskStatus(taskId, newStatus) {
            try {
                const response = await fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при перемещении задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при перемещении задачи');
            }
        }

        // Rest of your existing task management functions
        function openPersonalTaskModal() {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');

            if (!modal || !form) return;

            const titleElement = modal.querySelector('h3');
            const descElement = modal.querySelector('p');
            if (titleElement) titleElement.textContent = 'Новая личная задача';
            if (descElement) descElement.textContent = 'Создайте задачу для себя';

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
            if (!submitBtn) return;

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

            if (!modal) return;

            const executorField = document.querySelector('select[name="user_id"]')?.closest('.space-y-2');
            const departmentField = document.querySelector('select[name="department_id"]')?.closest('.space-y-2');
            const statusField = document.querySelector('select[name="status"]')?.closest('.space-y-2');

            if (executorField) executorField.style.display = 'block';
            if (departmentField) departmentField.style.display = 'block';
            if (statusField) statusField.style.display = 'block';

            const titleElement = modal.querySelector('h3');
            const descElement = modal.querySelector('p');
            if (titleElement) titleElement.textContent = 'Новая задача';
            if (descElement) descElement.textContent = 'Заполните информацию о задаче';

            if (form) {
                form.onsubmit = null;
                form.reset();
            }

            modal.classList.add('hidden');
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
            const timeModal = document.getElementById('timeModal');
            if (timeModal) timeModal.classList.remove('hidden');
        }

        async function submitForReview() {
            const actualHours = document.getElementById('actualHours')?.value;
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
            const rejectModal = document.getElementById('rejectModal');
            if (rejectModal) rejectModal.classList.remove('hidden');
        }

        async function submitRejection() {
            const reason = document.getElementById('rejectReason')?.value.trim();
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
            const timeModal = document.getElementById('timeModal');
            const actualHours = document.getElementById('actualHours');
            if (timeModal) timeModal.classList.add('hidden');
            if (actualHours) actualHours.value = '';
            currentTaskId = null;
        }

        function closeRejectModal() {
            const rejectModal = document.getElementById('rejectModal');
            const rejectReason = document.getElementById('rejectReason');
            if (rejectModal) rejectModal.classList.add('hidden');
            if (rejectReason) rejectReason.value = '';
            currentTaskId = null;
        }

        function openTaskViewModal(taskId) {
            fetch(`/tasks/${taskId}`)
                .then(response => response.text())
                .then(html => {
                    const content = document.getElementById('taskModalContent');
                    const modal = document.getElementById('taskViewModal');
                    if (content) content.innerHTML = html;
                    if (modal) modal.classList.remove('hidden');
                })
                .catch(error => console.error('Ошибка:', error));
        }

        function closeTaskViewModal() {
            const modal = document.getElementById('taskViewModal');
            const content = document.getElementById('taskModalContent');
            if (modal) modal.classList.add('hidden');
            if (content) content.innerHTML = '';
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

        .canban-col-title {
            background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);
        }
    </style>
@endsection