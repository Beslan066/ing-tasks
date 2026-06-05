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
                <p class="text-white text-sm">Ваши1 личные задачи не видны на странице Команда</p>
            @else
                <h2 class="text-3xl font-bold text-[#16a34a]">Мои задачи</h2>
                <p class="text-gray-700 text-sm">Ваши 1личные задачи не видны на странице Команда</p>
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
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
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
     <div class="sw-v overflow-hidden w-full">
    <div class="sw-v-wrapper flex lg:grid lg:grid-cols-4 xl:grid-cols-4 gap-6 max-[500px]:gap-0">
        <!-- Колонка "Новые" -->
        <div class="rounded-lg p-4 board-column bg-transparent max-[600px]:p-0" data-status="new">
            @if($backgroundEnabled && $backgroundImage)
                <div class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2">
                    <h3 class="font-semibold text-white">Новые</h3>
                    <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['new'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title">
                    <h3 class="font-semibold text-white">Новые</h3>
                    <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['new'] }}</span>
                </div>
            @endif

            <!-- КНОПКА БЫСТРОГО ДОБАВЛЕНИЯ -->
            <div id="quickAddBlock" class="mb-4">
                <button onclick="showQuickAddForm()"
                        id="showQuickAddBtn"
                        class="w-full group relative overflow-hidden bg-transparent/10
                        hover:from-green-50 hover:to-white border-2 border-dashed border-gray-300 hover:border-green-400 rounded-xl p-2 text-gray-500 hover:text-green-600 transition-all duration-300 flex items-center justify-center space-x-2">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/0 via-green-500/0 to-green-500/0
                    group-hover:from-green-500/5 group-hover:via-green-500/10 group-hover:to-green-500/5 transition-all duration-500"></div>
                    <i class="fas fa-plus-circle text-green-500 text-xl group-hover:scale-110 transition-transform duration-300"></i>
                    <span class="text-sm font-medium">Быстрая задача</span>
                </button>

                <!-- ФОРМА БЫСТРОГО ДОБАВЛЕНИЯ -->
                <div id="quickAddForm" class="hidden mt-2">
                    <div class="bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="quickAddFormInner">

                        <div class="p-4 space-y-4">
                            <div class="relative">
                                <i class="fas fa-tasks absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text"
                                       id="quickTaskName"
                                       placeholder="Что нужно сделать?"
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm"
                                       autocomplete="off">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <i class="fas fa-flag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <select id="quickTaskPriority"
                                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm appearance-none cursor-pointer">
                                        <option value="низкий">Низкий</option>
                                        <option value="средний" selected>⚡ Средний</option>
                                        <option value="высокий">Высокий</option>
                                        <option value="критический">Критический</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                </div>

                                <div class="relative">
                                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="datetime-local"
                                           id="quickTaskDeadline"
                                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm cursor-pointer">
                                </div>
                            </div>

                            <div class="relative">
                                <i class="fas fa-align-left absolute left-3 top-3 text-gray-400 text-sm"></i>
                                <textarea id="quickTaskDescription"
                                          rows="2"
                                          placeholder="Добавить описание..."
                                          class="w-full pl-10 pr-4 py-2 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm resize-none"></textarea>
                            </div>

                            <div class="flex space-x-3 pt-2">
                                <button onclick="createQuickTask()"
                                        class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2.5 rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Создать</span>
                                </button>
                                <button onclick="hideQuickAddForm()"
                                        class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium flex items-center justify-center space-x-1">
                                    <i class="fas fa-times"></i>
                                    <span>Отмена</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-100">
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span><i class="fas fa-keyboard mr-1"></i> Enter — создать</span>
                                <span><i class="fas fa-arrow-left mr-1"></i> Esc — отмена</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4 task-container" data-status="new">
                @foreach($tasksByStatus['new'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between {{ $task->status == 'просрочена' ? 'border-l-4 border-red-500' : '' }}"
                         draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                         data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                         data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                         data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                         data-author-id="{{ $task->author_id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <button onclick="toggleTaskMenu(event, {{ $task->id }})" class="text-gray-500 hover:text-gray-700 p-1" title="Действия">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="taskMenu-{{ $task->id }}" class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50">
                                        <div class="py-1">
                                            @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                                                </button>
                                            @endif
                                            <button onclick="startTask({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-play mr-2 text-green-500"></i> Начать
                                            </button>
                                            <button onclick="showRejectModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-times-circle mr-2"></i> Отказаться
                                            </button>
                                        </div>
                                    </div>
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
                                <div class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $task->deadline->format('d.m.Y H:i') }}
                                    @if($task->deadline->isPast())
                                        <span class="ml-1">(Просрочено)</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1 max-[500px]:flex-wrap max-[500px]:gap-1 max-[500px]:space-x-0">
                        <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                              class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>
                                @if($task->priority === 'высокий')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">‼️ Высокий</span>
                                @elseif($task->priority === 'критический')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">🚨 Критический</span>
                                @endif
                                @if($task->status == 'просрочена')
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">⚠️ Просрочена</span>
                                @endif
                            </div>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                     title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "В работе" -->
        <div class="rounded-lg p-4 board-column max-[600px]:p-0" data-status="in-progress">
            @if($backgroundEnabled && $backgroundImage)
                <div class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2">
                    <h3 class="font-semibold text-white">В работе</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['in_progress'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title">
                    <h3 class="font-semibold text-white">В работе</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['in_progress'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="in-progress">
                @foreach($tasksByStatus['in_progress'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between {{ $task->status == 'просрочена' ? 'border-l-4 border-red-500' : '' }}"
                         draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                         data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                         data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                         data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                         data-author-id="{{ $task->author_id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <button onclick="toggleTaskMenu(event, {{ $task->id }})" class="text-gray-500 hover:text-gray-700 p-1" title="Действия">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="taskMenu-{{ $task->id }}" class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50">
                                        <div class="py-1">
                                            @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                                                </button>
                                            @endif
                                            <button onclick="sendForReview({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-check-circle mr-2 text-green-500"></i> Отправить на проверку
                                            </button>
                                            <button onclick="showRejectModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-times-circle mr-2"></i> Отказаться
                                            </button>
                                        </div>
                                    </div>
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
                                <div class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $task->deadline->format('d.m.Y H:i') }}
                                    @if($task->deadline->isPast())
                                        <span class="ml-1">(Просрочено)</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1 max-[500px]:flex-wrap max-[500px]:gap-1 max-[500px]:space-x-0">
                            <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                                  class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>
                                @if($task->category)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $task->category->name }}</span>
                                @endif
                                @if($task->status == 'просрочена')
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">⚠️ Просрочена</span>
                                @endif
                            </div>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                     title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "На проверке" -->
        <div class="rounded-lg p-4 board-column max-[600px]:p-0" data-status="review">
            @if($backgroundEnabled && $backgroundImage)
                <div class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2">
                    <h3 class="font-semibold text-white">На проверке</h3>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['review'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title">
                    <h3 class="font-semibold text-white">На проверке</h3>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['review'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="review">
                @foreach($tasksByStatus['review'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between {{ $task->status == 'просрочена' ? 'border-l-4 border-red-500' : '' }}"
                         draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                         data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                         data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                         data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                         data-author-id="{{ $task->author_id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <button onclick="toggleTaskMenu(event, {{ $task->id }})" class="text-gray-500 hover:text-gray-700 p-1" title="Действия">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="taskMenu-{{ $task->id }}" class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50">
                                        <div class="py-1">
                                            @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                                                </button>
                                            @endif
                                            <button onclick="showRejectModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-times-circle mr-2"></i> Отказаться
                                            </button>
                                        </div>
                                    </div>
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
                                <div class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
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
                            <div class="flex space-x-1 max-[500px]:flex-wrap max-[500px]:gap-1 max-[500px]:space-x-0">
                            <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                                  class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>
                                @if($task->status == 'просрочена')
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded">⚠️ Просрочена</span>
                                @endif
                            </div>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                     title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "Завершено" -->
        <div class="rounded-lg p-4 board-column bg-transparent max-[600px]:p-0" data-status="done">
            @if($backgroundEnabled && $backgroundImage)
                <div class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2">
                    <h3 class="font-semibold text-white">Завершено</h3>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['done'] }}</span>
                </div>
            @else
                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title">
                    <h3 class="font-semibold text-white">Завершено</h3>
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count">{{ $stats['done'] }}</span>
                </div>
            @endif

            <div class="space-y-4 task-container" data-status="done">
                @foreach($tasksByStatus['done'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow opacity-80 cursor-move flex flex-col justify-between"
                         draggable="true" data-task="{{ $task->id }}" data-priority="{{ $task->priority ?? 'medium' }}"
                         data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                         data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                         data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                         data-author-id="{{ $task->author_id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600 flex-1"
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
                              class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>
                            <span class="text-xs text-gray-500">Завершено</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
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

    <!-- Модальное окно создания задачи -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-md">
        <div class="bg-white modal-content rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-100">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Новая задача</h3>
                        <p class="text-sm text-gray-500 mt-1">Заполните информацию о задаче</p>
                    </div>
                    <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-200 p-2 rounded-xl hover:bg-gray-100">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <form id="taskForm" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="selected_files" id="selectedFiles" value="[]">

                <!-- Основная информация -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-tag text-green-500 mr-2 text-xs"></i>Название задачи *
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tasks text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="text" name="name"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400 hover:border-gray-300"
                                   placeholder="Введите название задачи" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-flag text-green-500 mr-2 text-xs"></i>Приоритет *
                        </label>
                        <div class="relative group">
                            <select name="priority"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                                <option value="низкий">Низкий</option>
                                <option value="средний" selected>Средний</option>
                                <option value="высокий">Высокий</option>
                                <option value="критический">Критический</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Описание -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-align-left text-green-500 mr-2 text-xs"></i>Описание
                    </label>
                    <textarea name="description"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 resize-none bg-white placeholder-gray-400"
                              rows="4" placeholder="Подробное описание задачи..."></textarea>
                </div>

                <!-- Отдел и категория -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-building text-green-500 mr-2 text-xs"></i>Отдел *
                        </label>
                        <div class="relative group">
                            <select name="department_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                                <option value="" class="text-gray-400">Выберите отдел</option>
                                @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-folder text-green-500 mr-2 text-xs"></i>Категория
                        </label>
                        <div class="relative group">
                            <select name="category_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                                <option value="" class="text-gray-400">Выберите категорию</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Исполнитель и сроки -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-user-check text-green-500 mr-2 text-xs"></i>Исполнитель
                        </label>
                        <div class="relative group">
                            <select name="user_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                                <option value="" class="text-gray-400">Не назначено</option>
                                @foreach($assignableUsers ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-calendar-alt text-green-500 mr-2 text-xs"></i>Дедлайн
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="datetime-local" name="deadline"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white cursor-pointer hover:border-gray-300">
                        </div>
                    </div>
                </div>

                <!-- Оценка времени и статус -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-hourglass-half text-green-500 mr-2 text-xs"></i>Планируемые часы
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-stopwatch text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="number" name="estimated_hours" min="0" step="0.5"
                                   class="w-full px-4 py-3 pl-8 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400"
                                   placeholder="0.0">
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm">часов</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-chart-line text-green-500 mr-2 text-xs"></i>Статус *
                        </label>
                        <div class="relative group">
                            <select name="status"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                    required>
                                @php
                                    $availableStatuses = array_filter(\App\Models\Task::getStatuses(), function($status) {
                                        return $status !== 'в работе';
                                    });
                                @endphp
                                @foreach($availableStatuses as $status)
                                    <option value="{{ $status }}" {{ $status == 'назначена' ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вкладки для файлов -->
                <div class="space-y-4">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-6" aria-label="Tabs">
                            <button type="button"
                                    onclick="switchFileTab('storage')"
                                    id="storageTab"
                                    class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button active transition-all duration-200"
                                    data-tab="storage">
                                <i class="fas fa-database mr-2"></i>Из хранилища
                            </button>
                            <button type="button"
                                    onclick="switchFileTab('upload')"
                                    id="uploadTab"
                                    class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button transition-all duration-200"
                                    data-tab="upload">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Новая загрузка
                            </button>
                        </nav>
                    </div>

                    <!-- Контейнер для файлов из хранилища -->
                    <div id="storageTabContent" class="tab-content active">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700">Выберите файлы из хранилища</h4>
                                <p class="text-xs text-gray-500 mt-1">Файлы будут прикреплены к задаче</p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" onclick="openTaskStorageManager()"
                                        class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                    <i class="fas fa-folder-open mr-2"></i>Открыть хранилище
                                </button>
                                <button type="button" onclick="clearSelectedFiles()"
                                        class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-red-50 hover:border-red-300 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                    <i class="fas fa-times mr-2"></i>Очистить
                                </button>
                            </div>
                        </div>

                        <!-- Выбранные файлы -->
                        <div id="selectedFilesContainer" class="space-y-3 min-h-[100px]">
                            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-500">Файлы не выбраны</p>
                                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                            </div>
                        </div>

                        <!-- Счетчик файлов -->
                        <div id="fileCounter" class="hidden text-sm text-gray-600 mt-3">
                            <i class="fas fa-paperclip mr-1"></i>
                            <span id="fileCount">0</span> файлов выбрано
                        </div>
                    </div>

                    <!-- Контейнер для загрузки новых файлов -->
                    <div id="uploadTabContent" class="tab-content hidden">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700">Загрузите новые файлы</h4>
                            <p class="text-xs text-gray-500 mt-1">Файлы будут сохранены в хранилище и прикреплены к задаче</p>
                        </div>

                        <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 bg-gradient-to-br from-gray-50 to-white hover:from-green-50 hover:to-white cursor-pointer group"
                             onclick="document.getElementById('uploadNewFilesInput').click()">
                            <input type="file" name="new_files[]" multiple class="hidden" id="uploadNewFilesInput">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                                </div>
                                <p class="text-base font-medium text-gray-700 mb-2">Нажмите или перетащите файлы сюда</p>
                                <p class="text-sm text-gray-500">Поддерживаются: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP</p>
                                <p class="text-xs text-gray-400 mt-1">Максимальный размер: 10MB на файл</p>
                            </div>
                        </div>

                        <!-- Список новых файлов -->
                        <div id="uploadFilesList" class="space-y-3 mt-4 hidden">
                            <h5 class="text-sm font-semibold text-gray-700">Выбранные файлы:</h5>
                            <div id="uploadFilesContainer" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeTaskModal()"
                            class="px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-300 font-medium transition-all duration-200">
                        Отмена
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i>Создать задачу
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно файлового менеджера -->
    <div id="fileManagerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
        <div class="bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Файловое хранилище</h3>
                    <p class="text-sm text-gray-500 mt-1">Выберите файлы для прикрепления к задаче</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-600 bg-green-50 px-3 py-1 rounded-full">
                        Выбрано: <span id="selectedCount" class="font-semibold text-green-600">0</span>
                    </span>
                    <button onclick="closeTaskStorageManager()" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="fileManagerSearch" placeholder="Поиск по названию файла..."
                                   class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-hidden">
                <div class="h-full flex">
                    <div class="flex-1 overflow-y-auto p-4" id="fileManagerContent">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div class="col-span-full text-center py-12">Загрузка...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-white">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">Файловое хранилище</div>
                    <div class="flex space-x-3">
                        <button onclick="closeTaskStorageManager()"
                                class="px-5 py-2.5 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Отмена</button>
                        <button type="button" id="confirmFileSelectionBtn" onclick="confirmFileSelection()" class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700">
                            <i class="fas fa-check mr-2"></i>Выбрать (<span id="confirmCount">0</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ ЗАДАЧИ ==================== -->
    <div id="editTaskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-md">
        <div class="bg-white modal-content rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
            <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-100">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                            Редактирование задачи
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Измените информацию о задаче</p>
                    </div>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-200 p-2 rounded-xl hover:bg-gray-100">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <form id="editTaskForm" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="task_id" id="editTaskId">
                <input type="hidden" name="selected_files" id="editSelectedFiles" value="[]">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-tag text-green-500 mr-2 text-xs"></i>Название задачи *
                        </label>
                        <input type="text" name="name" id="editTaskName" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-align-left text-green-500 mr-2 text-xs"></i>Описание
                        </label>
                        <textarea name="description" id="editTaskDescription" rows="4"
                                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-building text-green-500 mr-2 text-xs"></i>Отдел *
                        </label>
                        <select name="department_id" id="editTaskDepartment" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-white focus:outline-none focus:border-green-400">
                            <option value="">Выберите отдел</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-folder text-green-500 mr-2 text-xs"></i>Категория
                        </label>
                        <select name="category_id" id="editTaskCategory"
                                class="w-full px-4 py-3 border-2 border-gray-200 bg-white rounded-xl focus:outline-none focus:border-green-400">
                            <option value="">Без категории</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-user-check text-green-500 mr-2 text-xs"></i>Исполнитель
                        </label>
                        <select name="user_id" id="editTaskUser"
                                class="w-full px-4 py-3 border-2 border-gray-200 bg-white rounded-xl focus:outline-none focus:border-green-400">
                            <option value="">Не назначен</option>
                            @foreach($assignableUsers ?? [] as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-flag text-green-500 mr-2 text-xs"></i>Приоритет *
                        </label>
                        <select name="priority" id="editTaskPriority" required
                                class="w-full px-4 py-3 border-2 border-gray-200 bg-white rounded-xl focus:outline-none focus:border-green-400">
                            <option value="низкий">Низкий</option>
                            <option value="средний">Средний</option>
                            <option value="высокий">Высокий</option>
                            <option value="критический">Критический</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-chart-line text-green-500 mr-2 text-xs"></i>Статус *
                        </label>
                        <select name="status" id="editTaskStatus" required
                                class="w-full px-4 py-3 border-2 border-gray-200 bg-white rounded-xl focus:outline-none focus:border-green-400">
                            <option value="назначена">Назначена</option>
                            <option value="в работе">В работе</option>
                            <option value="на проверке">На проверке</option>
                            <option value="выполнена">Выполнена</option>
                            <option value="просрочена">Просрочена</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-calendar-alt text-green-500 mr-2 text-xs"></i>Дедлайн
                        </label>
                        <input type="datetime-local" name="deadline" id="editTaskDeadline"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-hourglass-half text-green-500 mr-2 text-xs"></i>Планируемые часы
                        </label>
                        <input type="number" name="estimated_hours" id="editTaskEstimatedHours" step="0.5" min="0"
                               class="w-full px-4 pl-8 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-clock text-green-500 mr-2 text-xs"></i>Фактические часы
                        </label>
                        <input type="number" name="actual_hours" id="editTaskActualHours" step="0.5" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400">
                    </div>
                </div>

                <!-- Вкладки для файлов в редактировании -->
                <div class="space-y-4">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-6" aria-label="Tabs">
                            <button type="button"
                                    onclick="switchEditFileTab('storage')"
                                    id="editStorageTab"
                                    class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button active transition-all duration-200"
                                    data-tab="storage">
                                <i class="fas fa-database mr-2"></i>Из хранилища
                            </button>
                            <button type="button"
                                    onclick="switchEditFileTab('upload')"
                                    id="editUploadTab"
                                    class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button transition-all duration-200"
                                    data-tab="upload">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Новая загрузка
                            </button>
                        </nav>
                    </div>

                    <!-- Контейнер для файлов из хранилища -->
                    <div id="editStorageTabContent" class="tab-content active">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700">Выберите файлы из хранилища</h4>
                                <p class="text-xs text-gray-500 mt-1">Файлы будут прикреплены к задаче</p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" onclick="openTaskEditFileManager()"
                                        class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                    <i class="fas fa-folder-open mr-2"></i>Открыть хранилище
                                </button>
                                <button type="button" onclick="clearEditSelectedFiles()"
                                        class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-red-50 hover:border-red-300 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                    <i class="fas fa-times mr-2"></i>Очистить
                                </button>
                            </div>
                        </div>

                        <!-- Выбранные файлы -->
                        <div id="editSelectedFilesContainer" class="space-y-3 min-h-[100px]">
                            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-500">Файлы не выбраны</p>
                                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                            </div>
                        </div>

                        <!-- Счетчик файлов -->
                        <div id="editFileCounter" class="hidden text-sm text-gray-600 mt-3">
                            <i class="fas fa-paperclip mr-1"></i>
                            <span id="editFileCount">0</span> файлов выбрано
                        </div>
                    </div>

                    <!-- Контейнер для загрузки новых файлов -->
                    <div id="editUploadTabContent" class="tab-content hidden">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700">Загрузите новые файлы</h4>
                            <p class="text-xs text-gray-500 mt-1">Файлы будут сохранены в хранилище и прикреплены к задаче</p>
                        </div>

                        <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 bg-gradient-to-br from-gray-50 to-white hover:from-green-50 hover:to-white cursor-pointer group"
                             onclick="document.getElementById('editUploadNewFilesInput').click()">
                            <input type="file" name="new_files[]" multiple class="hidden" id="editUploadNewFilesInput">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                                </div>
                                <p class="text-base font-medium text-gray-700 mb-2">Нажмите или перетащите файлы сюда</p>
                                <p class="text-sm text-gray-500">Поддерживаются: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP</p>
                                <p class="text-xs text-gray-400 mt-1">Максимальный размер: 10MB на файл</p>
                            </div>
                        </div>

                        <!-- Список новых файлов -->
                        <div id="editUploadFilesList" class="space-y-3 mt-4 hidden">
                            <h5 class="text-sm font-semibold text-gray-700">Выбранные файлы:</h5>
                            <div id="editUploadFilesContainer" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()"
                            class="px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i>Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script
  src="https://cdn.jsdelivr.net/npm/@dragdroptouch/drag-drop-touch@latest/dist/drag-drop-touch.esm.min.js"
  type="module"
></script>
    <script>
        // ==================== ПЕРЕМЕННЫЕ ====================
        let taskSelectedFiles = [];
        let taskAllFiles = [];
        let taskEditSelectedFiles = [];
        let taskEditAllFiles = [];

        // Переменные для фильтров
        let currentTaskId = null;
        let activeFilters = {
            priority: [],
            deadline: [],
            hasFiles: []
        };

        const priorityMap = {
            'critical': 'критический',
            'high': 'высокий',
            'medium': 'средний',
            'low': 'низкий'
        };

        // ==================== ФУНКЦИИ ДЛЯ МЕНЮ С ТРЕМЯ ТОЧКАМИ ====================
        function toggleTaskMenu(event, taskId) {
            event.stopPropagation();
            document.querySelectorAll('.task-menu').forEach(menu => {
                if (menu.id !== `taskMenu-${taskId}`) {
                    menu.classList.add('hidden');
                }
            });
            const menu = document.getElementById(`taskMenu-${taskId}`);
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.task-menu') && !event.target.closest('[onclick*="toggleTaskMenu"]')) {
                document.querySelectorAll('.task-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================
        function getFileIcon(extension) {
            const ext = (extension || '').toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(ext)) return '🖼️';
            if (['pdf'].includes(ext)) return '📄';
            if (['doc', 'docx'].includes(ext)) return '📝';
            if (['xls', 'xlsx', 'csv'].includes(ext)) return '📊';
            if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) return '📦';
            if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) return '🎵';
            if (['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(ext)) return '🎬';
            return '📎';
        }

        function getFileTypeClass(extension) {
            const ext = (extension || '').toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(ext)) return { bg: 'bg-blue-100' };
            if (['pdf'].includes(ext)) return { bg: 'bg-red-100' };
            if (['doc', 'docx'].includes(ext)) return { bg: 'bg-blue-100' };
            if (['xls', 'xlsx', 'csv'].includes(ext)) return { bg: 'bg-green-100' };
            return { bg: 'bg-gray-100' };
        }

        function formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // ==================== ФУНКЦИИ ДЛЯ СОЗДАНИЯ ЗАДАЧИ ====================
        function updateTaskSelectedFilesDisplay() {
            const container = document.getElementById('selectedFilesContainer');
            const fileCounter = document.getElementById('fileCounter');
            const fileCount = document.getElementById('fileCount');

            if (!container) return;

            if (taskSelectedFiles.length === 0) {
                container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Файлы не выбраны</p>
                    <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                </div>`;
                if (fileCounter) fileCounter.classList.add('hidden');
            } else {
                let html = '';
                taskSelectedFiles.forEach(file => {
                    const fileIcon = getFileIcon(file.extension);
                    const fileType = getFileTypeClass(file.extension);
                    html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 ${fileType.bg} rounded flex items-center justify-center">
                                <span class="text-lg">${fileIcon}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            </div>
                        </div>
                        <button onclick="removeTaskSelectedFile(${file.id})" class="text-red-500 hover:text-red-700 p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;
                });
                container.innerHTML = html;
                if (fileCount) fileCount.textContent = taskSelectedFiles.length;
                if (fileCounter) fileCounter.classList.remove('hidden');
            }
        }

        function removeTaskSelectedFile(fileId) {
            taskSelectedFiles = taskSelectedFiles.filter(f => f.id !== fileId);
            updateTaskSelectedFilesDisplay();
            updateTaskSelectedCount();
        }

        function clearTaskSelectedFiles() {
            if (taskSelectedFiles.length === 0) return;
            if (confirm(`Удалить все выбранные файлы (${taskSelectedFiles.length})?`)) {
                taskSelectedFiles = [];
                updateTaskSelectedFilesDisplay();
                updateTaskSelectedCount();
            }
        }

        function switchFileTab(tabName) {
            document.querySelectorAll('#taskModal .tab-button').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.tab === tabName) btn.classList.add('active');
            });
            document.querySelectorAll('#taskModal .tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            const activeContent = document.getElementById(tabName + 'TabContent');
            if (activeContent) activeContent.classList.remove('hidden');
        }

        async function openTaskStorageManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                await loadTaskStorageFiles();
            }
        }

        async function loadTaskStorageFiles() {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Загрузка...</div>`;

            try {
                const response = await fetch('/tasks/file-storage/get-files', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) throw new Error('Ошибка загрузки файлов');

                const files = await response.json();
                console.log('Получено файлов:', files.length);

                taskAllFiles = files;
                renderTaskFiles(taskAllFiles);

                const searchInput = document.getElementById('fileManagerSearch');
                if (searchInput) {
                    searchInput.removeEventListener('input', handleTaskFileSearch);
                    searchInput.addEventListener('input', handleTaskFileSearch);
                }
            } catch (error) {
                console.error('Ошибка загрузки файлов:', error);
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">Ошибка загрузки</div>`;
            }
        }

        function handleTaskFileSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            if (!taskAllFiles) return;
            const filtered = taskAllFiles.filter(file => file.name.toLowerCase().includes(searchTerm));
            renderTaskFiles(filtered);
        }

        function renderTaskFiles(files) {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;

            if (!files || files.length === 0) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Нет файлов</div>`;
                return;
            }

            let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
            files.forEach(file => {
                const isSelected = taskSelectedFiles.some(f => f.id === file.id);
                const fileIcon = getFileIcon(file.extension);
                const fileType = getFileTypeClass(file.extension);
                html += `
                    <div class="file-card bg-white border ${isSelected ? 'border-green-500 shadow-md' : 'border-gray-200'} rounded-lg p-3 cursor-pointer" onclick="toggleTaskFileSelection(${file.id})">
                        <div class="flex justify-end mb-2">
                            <div class="w-5 h-5 rounded border ${isSelected ? 'bg-green-500 border-green-500' : 'border-gray-300'} flex items-center justify-center">
                                ${isSelected ? '<i class="fas fa-check text-white text-xs"></i>' : ''}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 ${fileType.bg} rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">${fileIcon}</span>
                            </div>
                            <p class="text-sm font-medium truncate">${escapeHtml(file.name)}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            <p class="text-xs text-gray-400 mt-1">${formatDate(file.created_at)}</p>
                        </div>
                        <div class="flex justify-center space-x-2 mt-2 pt-2 border-t border-gray-100">
                            <button type="button" onclick="event.stopPropagation(); downloadTaskFile(${file.id})"
                                    class="text-gray-400 hover:text-green-600 p-1" title="Скачать">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;
            updateTaskSelectedCount();
        }

        function toggleTaskFileSelection(fileId) {
            let file = taskAllFiles.find(f => f.id === fileId);
            if (!file) return;

            const index = taskSelectedFiles.findIndex(f => f.id === fileId);
            if (index === -1) {
                taskSelectedFiles.push(file);
            } else {
                taskSelectedFiles.splice(index, 1);
            }

            renderTaskFiles(taskAllFiles);
            updateTaskSelectedCount();
        }

        function updateTaskSelectedCount() {
            const selectedCountSpan = document.getElementById('selectedCount');
            const confirmCountSpan = document.getElementById('confirmCount');
            if (selectedCountSpan) selectedCountSpan.textContent = taskSelectedFiles.length;
            if (confirmCountSpan) confirmCountSpan.textContent = taskSelectedFiles.length;
        }

        async function downloadTaskFile(fileId) {
            window.open(`/file-storage/download/${fileId}`, '_blank');
        }

        function closeTaskStorageManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ====================
        let currentEditTaskId = null;

        async function openEditModal(taskId) {
            currentEditTaskId = taskId;
            taskEditSelectedFiles = [];

            try {
                const response = await fetch(`/tasks/${taskId}/get`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const task = data.task;

                    const currentUserId = {{ auth()->id() }};
                    const isLeader = {{ auth()->user()->isLeader() ? 'true' : 'false' }};

                    if (task.author_id !== currentUserId && !isLeader) {
                        alert('Вы не можете редактировать эту задачу. Редактировать может только автор задачи или руководитель.');
                        return;
                    }

                    document.getElementById('editTaskId').value = task.id;
                    document.getElementById('editTaskName').value = task.name;
                    document.getElementById('editTaskDescription').value = task.description || '';
                    document.getElementById('editTaskDepartment').value = task.department_id || '';
                    document.getElementById('editTaskCategory').value = task.category_id || '';
                    document.getElementById('editTaskUser').value = task.user_id || '';
                    document.getElementById('editTaskPriority').value = task.priority || 'средний';
                    document.getElementById('editTaskStatus').value = task.status;
                    document.getElementById('editTaskDeadline').value = task.deadline ? task.deadline.slice(0, 16) : '';
                    document.getElementById('editTaskEstimatedHours').value = task.estimated_hours || '';
                    document.getElementById('editTaskActualHours').value = task.actual_hours || '';

                    if (task.files && task.files.length > 0) {
                        taskEditSelectedFiles = task.files;
                        console.log('Загружены файлы задачи:', taskEditSelectedFiles.map(f => f.id));
                        updateTaskEditSelectedFilesDisplay();
                    } else {
                        updateTaskEditSelectedFilesDisplay();
                    }

                    document.getElementById('editTaskModal').classList.remove('hidden');
                } else {
                    alert(data.message || 'Ошибка при загрузке задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при загрузке данных задачи');
            }
        }

        function closeEditModal() {
            document.getElementById('editTaskModal').classList.add('hidden');
            document.getElementById('editTaskForm').reset();
            currentEditTaskId = null;
            taskEditSelectedFiles = [];
            document.getElementById('editUploadNewFilesInput').value = '';
            document.getElementById('editUploadFilesList').classList.add('hidden');
        }

        function updateTaskEditSelectedFilesDisplay() {
            const container = document.getElementById('editSelectedFilesContainer');
            const fileCounter = document.getElementById('editFileCounter');
            const fileCount = document.getElementById('editFileCount');

            if (!container) return;

            if (taskEditSelectedFiles.length === 0) {
                container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Файлы не выбраны</p>
                    <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                </div>`;
                if (fileCounter) fileCounter.classList.add('hidden');
            } else {
                let html = '';
                taskEditSelectedFiles.forEach(file => {
                    const fileIcon = getFileIcon(file.extension);
                    const fileType = getFileTypeClass(file.extension);
                    html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="w-10 h-10 ${fileType.bg} rounded flex items-center justify-center">
                                <span class="text-lg">${fileIcon}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                <span class="text-xs text-gray-500">${formatFileSize(file.size)}</span>
                            </div>
                        </div>
                        <button onclick="removeTaskEditSelectedFile(${file.id})" class="text-red-500 hover:text-red-700 p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;
                });
                container.innerHTML = html;
                if (fileCount) fileCount.textContent = taskEditSelectedFiles.length;
                if (fileCounter) fileCounter.classList.remove('hidden');
            }
        }

        function removeTaskEditSelectedFile(fileId) {
            taskEditSelectedFiles = taskEditSelectedFiles.filter(f => f.id !== fileId);
            updateTaskEditSelectedFilesDisplay();
        }

        function clearTaskEditSelectedFiles() {
            if (taskEditSelectedFiles.length === 0) return;
            if (confirm(`Удалить все выбранные файлы (${taskEditSelectedFiles.length})?`)) {
                taskEditSelectedFiles = [];
                updateTaskEditSelectedFilesDisplay();
            }
        }

        function switchEditFileTab(tabName) {
            const tabButtons = document.querySelectorAll('#editTaskModal .tab-button');
            const tabContents = document.querySelectorAll('#editTaskModal .tab-content');
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-tab') === tabName) btn.classList.add('active');
            });
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            const activeContent = document.getElementById('edit' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'TabContent');
            if (activeContent) activeContent.classList.remove('hidden');
        }

        async function openTaskEditFileManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                await loadTaskEditFiles();
            }
        }

        async function loadTaskEditFiles() {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Загрузка...</div>`;

            try {
                const response = await fetch('/tasks/file-storage/get-files', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                if (!response.ok) throw new Error('Ошибка');
                taskEditAllFiles = await response.json();
                console.log('Загружены все файлы из хранилища:', taskEditAllFiles.length);
                console.log('Текущие выбранные файлы (taskEditSelectedFiles):', taskEditSelectedFiles.map(f => f.id));
                renderTaskEditFiles(taskEditAllFiles);

                const searchInput = document.getElementById('fileManagerSearch');
                if (searchInput) {
                    searchInput.removeEventListener('input', handleTaskEditFileSearch);
                    searchInput.addEventListener('input', handleTaskEditFileSearch);
                }
            } catch (error) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">Ошибка загрузки</div>`;
            }
        }

        function handleTaskEditFileSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            if (!taskEditAllFiles) return;
            const filtered = taskEditAllFiles.filter(file => file.name.toLowerCase().includes(searchTerm));
            renderTaskEditFiles(filtered);
        }

        function renderTaskEditFiles(files) {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;
            if (!files || files.length === 0) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Нет файлов</div>`;
                return;
            }

            let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
            files.forEach(file => {
                const isSelected = taskEditSelectedFiles.some(f => f.id === file.id);
                const fileIcon = getFileIcon(file.extension);
                const fileType = getFileTypeClass(file.extension);
                html += `
                    <div class="file-card bg-white border ${isSelected ? 'border-green-500 shadow-md' : 'border-gray-200'} rounded-lg p-3">
                        <div class="flex justify-end mb-2">
                            <input type="checkbox"
                                   value="${file.id}"
                                   class="task-edit-file-checkbox w-5 h-5 rounded border-gray-300 cursor-pointer"
                                   ${isSelected ? 'checked' : ''}>
                        </div>
                        <div class="text-center cursor-pointer" onclick="toggleTaskEditFileSelection(${file.id})">
                            <div class="w-16 h-16 ${fileType.bg} rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">${fileIcon}</span>
                            </div>
                            <p class="text-sm font-medium truncate">${escapeHtml(file.name)}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            <p class="text-xs text-gray-400 mt-1">${formatDate(file.created_at)}</p>
                        </div>
                        <div class="flex justify-center space-x-2 mt-2 pt-2 border-t border-gray-100">
                            <button type="button" onclick="event.stopPropagation(); downloadTaskFile(${file.id})"
                                    class="text-gray-400 hover:text-green-600 p-1" title="Скачать">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;

            document.querySelectorAll('#fileManagerContent .task-edit-file-checkbox').forEach(checkbox => {
                checkbox.removeEventListener('change', handleTaskEditCheckboxChange);
                checkbox.addEventListener('change', handleTaskEditCheckboxChange);
            });

            updateTaskEditSelectedCount();
        }

        function handleTaskEditCheckboxChange(e) {
            e.stopPropagation();
            const fileId = parseInt(this.value);
            const file = taskEditAllFiles.find(f => f.id === fileId);
            if (file) {
                if (this.checked) {
                    if (!taskEditSelectedFiles.some(f => f.id === fileId)) {
                        taskEditSelectedFiles.push(file);
                    }
                } else {
                    taskEditSelectedFiles = taskEditSelectedFiles.filter(f => f.id !== fileId);
                }
                const card = this.closest('.file-card');
                if (card) {
                    if (this.checked) {
                        card.classList.add('border-green-500', 'shadow-md');
                        card.classList.remove('border-gray-200');
                    } else {
                        card.classList.remove('border-green-500', 'shadow-md');
                        card.classList.add('border-gray-200');
                    }
                }
                updateTaskEditSelectedCount();
                console.log('taskEditSelectedFiles после изменения:', taskEditSelectedFiles.map(f => f.id));
            }
        }

        function toggleTaskEditFileSelection(fileId) {
            let file = taskEditAllFiles.find(f => f.id === fileId);
            if (!file) return;

            const index = taskEditSelectedFiles.findIndex(f => f.id === fileId);
            if (index === -1) {
                taskEditSelectedFiles.push(file);
            } else {
                taskEditSelectedFiles.splice(index, 1);
            }

            const checkbox = document.querySelector(`#fileManagerContent .task-edit-file-checkbox[value="${fileId}"]`);
            if (checkbox) {
                checkbox.checked = index === -1;
                const card = checkbox.closest('.file-card');
                if (card) {
                    if (checkbox.checked) {
                        card.classList.add('border-green-500', 'shadow-md');
                        card.classList.remove('border-gray-200');
                    } else {
                        card.classList.remove('border-green-500', 'shadow-md');
                        card.classList.add('border-gray-200');
                    }
                }
            }

            updateTaskEditSelectedCount();
            console.log('taskEditSelectedFiles после toggle:', taskEditSelectedFiles.map(f => f.id));
        }

        function updateTaskEditSelectedCount() {
            const selectedCountSpan = document.getElementById('selectedCount');
            const confirmCountSpan = document.getElementById('confirmCount');
            const confirmBtn = document.getElementById('confirmFileSelectionBtn');

            const count = taskEditSelectedFiles.length;

            if (selectedCountSpan) selectedCountSpan.textContent = count;
            if (confirmCountSpan) confirmCountSpan.textContent = count;

            if (confirmBtn) {
                if (count === 0) {
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // ==================== ОСНОВНАЯ ФУНКЦИЯ ПОДТВЕРЖДЕНИЯ ====================
        window.confirmFileSelection = function() {
            const isEditModalVisible = document.getElementById('editTaskModal') && !document.getElementById('editTaskModal').classList.contains('hidden');
            const isCreateModalVisible = document.getElementById('taskModal') && !document.getElementById('taskModal').classList.contains('hidden');

            if (isEditModalVisible) {
                if (taskEditSelectedFiles.length === 0) {
                    alert('Пожалуйста, выберите хотя бы один файл');
                    return;
                }
                console.log('Подтверждение выбора. Файлы для сохранения:', taskEditSelectedFiles.map(f => f.id));
                updateTaskEditSelectedFilesDisplay();
                closeTaskStorageManager();
            } else if (isCreateModalVisible) {
                if (taskSelectedFiles.length === 0) {
                    alert('Пожалуйста, выберите хотя бы один файл');
                    return;
                }
                const selectedFilesInput = document.getElementById('selectedFiles');
                if (selectedFilesInput) {
                    selectedFilesInput.value = JSON.stringify(taskSelectedFiles);
                }
                updateTaskSelectedFilesDisplay();
                switchFileTab('storage');
                closeTaskStorageManager();
            }
        };

        // ==================== ОБРАБОТКА ФОРМЫ РЕДАКТИРОВАНИЯ ====================
        document.getElementById('editTaskForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const taskId = document.getElementById('editTaskId').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn?.innerHTML;

            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Сохранение...';
                submitBtn.disabled = true;
            }

            try {
                const formData = new FormData(this);
                formData.append('_method', 'POST');

                const selectedFileIds = taskEditSelectedFiles.map(f => f.id);
                console.log('Отправляемые ID файлов на сервер:', selectedFileIds);

                formData.append('selected_files', JSON.stringify(selectedFileIds));

                const newFilesInput = document.getElementById('editUploadNewFilesInput');
                if (newFilesInput && newFilesInput.files.length > 0) {
                    for (let i = 0; i < newFilesInput.files.length; i++) {
                        formData.append('new_files[]', newFilesInput.files[i]);
                    }
                    console.log('Новых файлов для загрузки:', newFilesInput.files.length);
                }

                const response = await fetch(`/tasks/${taskId}/update`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                console.log('Ответ сервера:', data);

                if (data.success) {
                    alert('Задача успешно обновлена!');
                    closeEditModal();
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при обновлении задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при обновлении задачи: ' + error.message);
            } finally {
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }
        });

        // ==================== ОБРАБОТКА ФОРМЫ СОЗДАНИЯ ЗАДАЧИ ====================
        (function() {
            const taskForm = document.getElementById('taskForm');

            if (taskForm) {
                taskForm.addEventListener('submit', function(e) {
                    const isPersonalModal = document.getElementById('taskModal').classList.contains('hidden') === false &&
                        document.querySelector('#taskModal h3')?.textContent === 'Новая личная задача';

                    if (isPersonalModal) {
                        e.preventDefault();
                        e.stopPropagation();

                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn?.innerHTML;

                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Создание...';
                            submitBtn.disabled = true;
                        }

                        // Добавляем is_personal = true
                        formData.append('is_personal', '1');

                        // Убеждаемся что department_id есть (если у пользователя есть отдел)
                        @if(isset($user) && $user->department_id)
                        if (!formData.has('department_id') || !formData.get('department_id')) {
                            formData.append('department_id', '{{ $user->department_id }}');
                        }
                        @endif

                        fetch('/tasks/personal/store', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showNotification("Личная задача успешно создана!", "success");
                                    closeTaskModal();
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showNotification(data.message || 'Ошибка при создании задачи', "error");
                                }
                            })
                            .catch(error => {
                                console.error('Ошибка:', error);
                                showNotification("Ошибка при создании задачи", "error");
                            })
                            .finally(() => {
                                if (submitBtn) {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                            });
                    }
                });
            }
        })();

        // ==================== ФУНКЦИИ ФИЛЬТРАЦИИ ====================
        function toggleFiltersDropdown() {
            const dropdown = document.getElementById('filtersDropdown');
            const chevron = document.getElementById('filtersChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('hidden');
                chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        function toggleFilterSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById(sectionId + 'Icon');
            if (section && icon) {
                section.classList.toggle('hidden');
                icon.style.transform = section.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        function collectFiltersFromCheckboxes() {
            activeFilters = { priority: [], deadline: [], hasFiles: [] };
            document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => {
                const type = checkbox.dataset.filterType;
                const value = checkbox.value;
                if (activeFilters[type]) activeFilters[type].push(value);
            });
            updateFiltersCounter();
        }

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

        function updateActiveFiltersDisplay() {
            const container = document.getElementById('activeFiltersContainer');
            if (!container) return;
            container.innerHTML = '';
            activeFilters.priority.forEach(priority => {
                const label = priority === 'critical' ? '🚨 Критический' : (priority === 'high' ? '‼️ Высокий' : (priority === 'medium' ? 'Средний' : 'Низкий'));
                addFilterChip(container, 'priority', priority, label);
            });
            activeFilters.deadline.forEach(deadline => {
                const label = deadline === 'overdue' ? '⚠️ Просроченные' : (deadline === 'today' ? '📅 Сегодня' : (deadline === 'tomorrow' ? 'Завтра' : 'На этой неделе'));
                addFilterChip(container, 'deadline', deadline, label);
            });
            activeFilters.hasFiles.forEach(hasFile => {
                const label = hasFile === 'true' ? '📎 Есть файлы' : 'Нет файлов';
                addFilterChip(container, 'has-files', hasFile, label);
            });
        }

        function addFilterChip(container, type, value, label) {
            const chip = document.createElement('div');
            chip.className = 'inline-flex items-center bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full';
            chip.innerHTML = `<span>${label}</span><button onclick="removeFilter('${type}', '${value}')" class="ml-2 text-gray-500 hover:text-gray-700"><i class="fas fa-times-circle text-xs"></i></button>`;
            container.appendChild(chip);
        }

        function removeFilter(type, value) {
            const index = activeFilters[type].indexOf(value);
            if (index !== -1) activeFilters[type].splice(index, 1);
            const checkbox = document.querySelector(`.filter-checkbox[data-filter-type="${type}"][value="${value}"]`);
            if (checkbox) checkbox.checked = false;
            updateActiveFiltersDisplay();
            updateFiltersCounter();
            applyFilters();
        }

        function clearAllFilters() {
            activeFilters = { priority: [], deadline: [], hasFiles: [] };
            document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => checkbox.checked = false);
            const searchInput = document.getElementById('taskSearchInput');
            if (searchInput) searchInput.value = '';
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

        function applyFiltersAndClose() {
            collectFiltersFromCheckboxes();
            updateActiveFiltersDisplay();
            applyFilters();
            toggleFiltersDropdown();
        }

        function applyFilters() {
            const taskCards = document.querySelectorAll('.task-card');
            const searchTerm = document.getElementById('taskSearchInput')?.value.toLowerCase().trim() || '';

            taskCards.forEach(card => {
                let show = true;

                if (activeFilters.priority.length > 0) {
                    const priorityValue = card.dataset.priority;
                    let matchesPriority = false;
                    for (const filter of activeFilters.priority) {
                        if (priorityValue === priorityMap[filter]) { matchesPriority = true; break; }
                    }
                    if (!matchesPriority) show = false;
                }

                if (show && activeFilters.deadline.length > 0) {
                    const deadlineDateStr = card.dataset.deadline;
                    let matchesDeadline = false;
                    for (const filter of activeFilters.deadline) {
                        if (filter === 'overdue' && deadlineDateStr) {
                            const deadlineDate = new Date(deadlineDateStr);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            if (deadlineDate < today) { matchesDeadline = true; break; }
                        } else if (filter === 'today' && deadlineDateStr && isToday(new Date(deadlineDateStr))) {
                            matchesDeadline = true; break;
                        } else if (filter === 'tomorrow' && deadlineDateStr && isTomorrow(new Date(deadlineDateStr))) {
                            matchesDeadline = true; break;
                        } else if (filter === 'week' && deadlineDateStr && isThisWeek(new Date(deadlineDateStr))) {
                            matchesDeadline = true; break;
                        }
                    }
                    if (!matchesDeadline) show = false;
                }

                if (show && activeFilters.hasFiles.length > 0) {
                    const hasFilesValue = card.dataset.hasFiles;
                    let matchesFiles = false;
                    for (const filter of activeFilters.hasFiles) {
                        if (hasFilesValue === filter) { matchesFiles = true; break; }
                    }
                    if (!matchesFiles) show = false;
                }

                if (show && searchTerm) {
                    const taskName = card.dataset.taskName || '';
                    if (!taskName.includes(searchTerm)) show = false;
                }

                card.style.display = show ? '' : 'none';
            });

            updateColumnCounters();
        }

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

        function updateColumnCounters() {
            const columns = document.querySelectorAll('.board-column');
            columns.forEach(column => {
                const taskContainer = column.querySelector('.task-container');
                if (taskContainer) {
                    const visibleTasks = taskContainer.querySelectorAll('.task-card:not([style*="display: none"])').length;
                    const counterSpan = column.querySelector('.stat-count');
                    if (counterSpan) counterSpan.textContent = visibleTasks;
                }
            });
        }

        // ==================== DRAG AND DROP ====================
        function initDragAndDrop() {
            const taskCards = document.querySelectorAll('.task-card');
            const columns = document.querySelectorAll('.board-column');

            taskCards.forEach(card => {
                card.setAttribute('draggable', 'true');
                card.removeEventListener('dragstart', dragStart);
                card.removeEventListener('dragend', dragEnd);
                card.addEventListener('dragstart', dragStart);
                card.addEventListener('dragend', dragEnd);
            });

            columns.forEach(column => {
                column.removeEventListener('dragover', dragOver);
                column.removeEventListener('dragleave', dragLeave);
                column.removeEventListener('drop', drop);
                column.addEventListener('dragover', dragOver);
                column.addEventListener('dragleave', dragLeave);
                column.addEventListener('drop', drop);
            });
        }

        let draggedItem = null;
let swiperSlideTimeout = null;

// В самой функции dragStart добавьте строку для принудительного создания "призрака"
function dragStart(e) {
    draggedItem = this;
    e.dataTransfer.setData('text/plain', this.dataset.task);
    this.style.opacity = '0.5';
 if (window.mySwiper) {
        window.mySwiper.detachEvents(); // полностью отключает реакцию на палец для Swiper
    }

    // Фикс для iOS/Android: помогаем полифилу понять, какой именно элемент мы тащим
    if (e.dataTransfer.setDragImage) {
        e.dataTransfer.setDragImage(this, 0, 0);
    }
}
        function dragEnd(e) {
            if (draggedItem) {
                draggedItem.style.opacity = '';
                draggedItem = null;
            }
              if (window.mySwiper) {
        window.mySwiper.attachEvents();
    }
        }


function dragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';

    const column = this.closest('.board-column');
    if (column) {
        column.classList.add('drag-over-active');

        // ПРОВЕРКА ДЛЯ SWIPER:
        // Если Swiper существует и инициализирован
        if (window.mySwiper && typeof window.mySwiper.slideTo === 'function') {

            // Находим все колонки на доске
            const allColumns = Array.from(document.querySelectorAll('.board-column'));
            // Определяем порядковый номер (индекс) текущей колонки
            const columnIndex = allColumns.indexOf(column);

            // Если мы ведем карточку над новой колонкой и скрипт еще не запланировал переключение
            if (columnIndex !== -1 && window.mySwiper.activeIndex !== columnIndex && !swiperSlideTimeout) {

                // Делаем небольшую задержку в 400мс, чтобы слайд переключался,
                // только если пользователь осознанно задерживает карточку у края экрана
                swiperSlideTimeout = setTimeout(() => {
                    window.mySwiper.slideTo(columnIndex, 300); // 300 — скорость анимации в мс
                    swiperSlideTimeout = null;
                }, 400);
            }
        }
    }
}

        function dragLeave(e) {
    const column = this.closest('.board-column');
    if (column) {
        column.classList.remove('drag-over-active');
    }

    // СБРОС ТАЙМЕРА: если пользователь увёл карточку, отменяем переключение
    if (swiperSlideTimeout) {
        clearTimeout(swiperSlideTimeout);
        swiperSlideTimeout = null;
    }
}

        function drop(e) {
            e.preventDefault();

            if (swiperSlideTimeout) {
        clearTimeout(swiperSlideTimeout);
        swiperSlideTimeout = null;

             if (window.mySwiper) {
        window.mySwiper.attachEvents();
    }
    }

            const column = this.closest('.board-column');
            if (column) {
                column.classList.remove('drag-over-active');
            }

            if (!draggedItem) return;

            const newStatus = column.dataset.status;
            const taskId = draggedItem.dataset.task;
            const currentColumn = draggedItem.closest('.board-column');
            const currentStatus = currentColumn ? currentColumn.getAttribute('data-status') : null;

            if (currentStatus === newStatus) {
                draggedItem.style.opacity = '1';
                draggedItem = null;
                return;
            }

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

        // ==================== ФУНКЦИИ ДЛЯ ЛИЧНЫХ ЗАДАЧ ====================
        function openPersonalTaskModal() {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');

            if (!modal || !form) return;

            // Отключаем HTML5 валидацию формы
            form.setAttribute('novalidate', 'novalidate');

            // Удаляем старые скрытые поля если есть
            const oldHiddenDept = form.querySelector('input[name="department_id"][type="hidden"]');
            if (oldHiddenDept) oldHiddenDept.remove();

            const oldIsPersonal = form.querySelector('input[name="is_personal"]');
            if (oldIsPersonal) oldIsPersonal.remove();

            // Добавляем скрытое поле department_id с отделом пользователя
            @if(isset($user) && $user->department_id)
            const hiddenDeptInput = document.createElement('input');
            hiddenDeptInput.type = 'hidden';
            hiddenDeptInput.name = 'department_id';
            hiddenDeptInput.value = '{{ $user->department_id }}';
            form.appendChild(hiddenDeptInput);
            @endif

            // Добавляем скрытое поле is_personal
            const isPersonalInput = document.createElement('input');
            isPersonalInput.type = 'hidden';
            isPersonalInput.name = 'is_personal';
            isPersonalInput.value = '1';
            form.appendChild(isPersonalInput);

            const titleElement = modal.querySelector('h3');
            const descElement = modal.querySelector('p');
            if (titleElement) titleElement.textContent = 'Новая личная задача';
            if (descElement) descElement.textContent = 'Создайте задачу для себя';

            // Скрываем ненужные поля
            const executorField = document.querySelector('select[name="user_id"]')?.closest('.space-y-2');
            const departmentField = document.querySelector('select[name="department_id"]:not([type="hidden"])')?.closest('.space-y-2');
            const statusField = document.querySelector('select[name="status"]')?.closest('.space-y-2');

            if (executorField) executorField.style.display = 'none';
            if (departmentField) departmentField.style.display = 'none';
            if (statusField) statusField.style.display = 'none';

            modal.classList.remove('hidden');
        }

        function closeTaskModal() {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');

            if (!modal) return;

            // Восстанавливаем валидацию формы
            form.removeAttribute('novalidate');

            // Восстанавливаем видимость полей
            const executorField = document.querySelector('select[name="user_id"]')?.closest('.space-y-2');
            const departmentField = document.querySelector('select[name="department_id"]:not([type="hidden"])')?.closest('.space-y-2');
            const statusField = document.querySelector('select[name="status"]')?.closest('.space-y-2');

            if (executorField) executorField.style.display = 'block';
            if (departmentField) departmentField.style.display = 'block';
            if (statusField) statusField.style.display = 'block';

            // Удаляем скрытые поля
            const hiddenDept = form.querySelector('input[name="department_id"][type="hidden"]');
            if (hiddenDept) hiddenDept.remove();

            const hiddenIsPersonal = form.querySelector('input[name="is_personal"]');
            if (hiddenIsPersonal) hiddenIsPersonal.remove();

            const titleElement = modal.querySelector('h3');
            const descElement = modal.querySelector('p');
            if (titleElement) titleElement.textContent = 'Новая задача';
            if (descElement) descElement.textContent = 'Заполните информацию о задаче';

            if (form) {
                form.reset();
            }

            taskSelectedFiles = [];
            updateTaskSelectedFilesDisplay();

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

        // ==================== ИНИЦИАЛИЗАЦИЯ ====================
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('taskSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
                searchInput.addEventListener('keyup', applyFilters);
            }

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

            initDragAndDrop();

            const uploadNewFilesInput = document.getElementById('uploadNewFilesInput');
            if (uploadNewFilesInput) {
                uploadNewFilesInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    const container = document.getElementById('uploadFilesContainer');
                    const listContainer = document.getElementById('uploadFilesList');

                    if (files.length > 0 && container) {
                        listContainer?.classList.remove('hidden');
                        let html = '';
                        files.forEach((file, index) => {
                            const fileIcon = getFileIcon(file.name.split('.').pop());
                            html += `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200" data-file-index="${index}">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                            <span class="text-lg">${fileIcon}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="this.closest('[data-file-index]')?.remove()" class="text-red-500 hover:text-red-700 p-1">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else if (files.length === 0 && container) {
                        listContainer?.classList.add('hidden');
                        container.innerHTML = '';
                    }
                });
            }

            const editUploadNewFilesInput = document.getElementById('editUploadNewFilesInput');
            if (editUploadNewFilesInput) {
                editUploadNewFilesInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    const container = document.getElementById('editUploadFilesContainer');
                    const listContainer = document.getElementById('editUploadFilesList');

                    if (files.length > 0 && container) {
                        listContainer?.classList.remove('hidden');
                        let html = '';
                        files.forEach((file, index) => {
                            const fileIcon = getFileIcon(file.name.split('.').pop());
                            html += `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200" data-file-index="${index}">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                                            <span class="text-lg">${fileIcon}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="this.closest('[data-file-index]')?.remove()" class="text-red-500 hover:text-red-700 p-1">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else if (files.length === 0 && container) {
                        listContainer?.classList.add('hidden');
                        container.innerHTML = '';
                    }
                });
            }

            const uploadArea = document.querySelector('.file-upload-area');
            if (uploadArea) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('drag-over');
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    this.classList.remove('drag-over');
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('drag-over');
                    const files = Array.from(e.dataTransfer.files);
                    const input = document.getElementById('uploadNewFilesInput');
                    if (input && files.length > 0) {
                        const dataTransfer = new DataTransfer();
                        files.forEach(file => dataTransfer.items.add(file));
                        input.files = dataTransfer.files;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.id === 'taskViewModal') closeTaskViewModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeTaskViewModal();
        });

        // ==================== УВЕДОМЛЕНИЯ ====================
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white'}`;
            notification.innerHTML = `<div class="flex items-center"><i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i><span>${message}</span></div>`;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) notification.parentNode.removeChild(notification);
                }, 300);
            }, 5000);
        }
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

        .tab-button {
            border-color: transparent;
            color: #6b7280;
        }
        .tab-button:hover {
            color: #374151;
            border-color: #d1d5db;
        }
        .tab-button.active {
            border-color: #10b981;
            color: #10b981;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .file-card {
            transition: all 0.2s ease;
        }
        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .file-upload-area.drag-over {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            transform: scale(0.98);
        }
        .modal-content {
            animation: fadeIn 0.3s ease-out;
        }

        .task-menu {
            animation: fadeIn 0.15s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .board-column.drag-over {
            background-color: rgba(229, 231, 235, 0.5) !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .board-column.drag-over-active {
            background-color: rgba(16, 185, 129, 0.2) !important;
            border: 2px dashed #10b981;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake {
            animation: shake 0.3s ease-in-out;
        }
        @media (max-width: 500px) {
    .board-column {
        flex-shrink: 0 !important;
        width: 82% !important; /* Уменьшили, чтобы освободить место слева и справа */
        max-width: 82% !important;
    }
    .task-card, .task-card * {
        touch-action: none !important;
        -webkit-user-select: none !important;
        user-select: none !important;
        -webkit-touch-callout: none !important;
}
}
    </style>

@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <style>
        .sw-v-wrapper {
            display: flex;
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 1;
            transition-property: transform;
            box-sizing: content-box;
        }
    </style>
@endpush


{{-- ВСТАВЛЯЕМ СКРИПТЫ SWIPER --}}
@push('scripts')
   <script>
document.addEventListener('DOMContentLoaded', () => {

    if (window.DragDropTouch) {
        window.DragDropTouch._HOLD_DELAY = 20;
        console.log('Полифил успешно найден и настроен!',1);
    } else {
        console.log('drag not found — полифил всё еще не загрузился.');
    }

    // 2. Проверяем ширину экрана для Swiper
    if (window.innerWidth > 500) return;

    // 3. Ищем элемент
    const sliderElement = document.querySelector('.sw-v');

    // 4. Инициализируем Swiper
    if (sliderElement) {
        window.mySwiper = new Swiper('.sw-v', {
            wrapperClass: 'sw-v-wrapper',
            slideClass: 'board-column',
    centeredSlides: true,
            slidesPerView: 'auto',
            spaceBetween: 10,
            loop: false,
            noSwiping: true,
            noSwipingClass: 'task-card',
            observer: true,
            observeParents: true,
            watchSlidesProgress: true,
        });
        console.log('Swiper успешно запущен для мобильного экрана!');
    }
});
</script>
@endpush
