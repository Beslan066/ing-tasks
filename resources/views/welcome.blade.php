@extends('layouts.app')

@section('content')

    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp
        <!-- Заголовок и статистика -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
        <nav class="hidden max-[500px]:block">
                            <ol class="flex items-center gap-1.5">
                                <li>
                                    <a class="inline-flex items-center gap-1.5 text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                       href="{{ route('welcome') }}">
                                        Главная
                                        <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                                                  stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                </li>
                                <li class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}" x-text="pageName">Мои задачи</li>
                            </ol>
                        </nav>
        <div class="max-[500px]:hidden">
            @if($backgroundEnabled && $backgroundImage)
                <h2 class="text-3xl font-bold text-white max-[500px]:text-[26px]">Мои задачи</h2>
                <p class="text-white text-sm max-[500px]:text-[13px]">Ваши личные задачи не видны на странице Команда</p>
            @else
                <h2 class="text-3xl font-bold max-[500px]:text-[26px]" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Мои задачи</h2>
                <p class="text-gray-700 text-sm max-[500px]:text-[13px]">Ваши личные задачи не видны на странице Команда</p>
            @endif
        </div>

        <div class="flex space-x-4 w-full md:w-auto">
            <button onclick="openPersonalTaskModal()"
        class="flex-1 md:flex-none text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 text-sm md:text-base max-[500px]:basis-1/2 bg-gradient-to-br from-emerald-500 to-emerald-600 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
    <i class="fas fa-plus"></i>
    <span>Добавить</span>
</button>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="mb-4 max-[500px]:mb-1 relative">
        <div class="flex items-center gap-2 flex-wrap">
<!-- class="max-[450px]:fixed max-[450px]:bottom-3 max-[450px]:left-3" -->
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
                     class="hidden absolute left-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border z-50 max-[500px]:w-[95vw] max-[500px]:left-[-100%] max-[500px]:transform max-[500px]:translate-x-[5%] max-[450px]:left-[-0%]  max-[450px]:translate-x-[0%]">
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
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                           data-filter-type="priority" value="high">
                                    <span class="text-sm text-gray-700">Высокий</span>
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
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded">
                                    <input type="checkbox" class="filter-checkbox rounded border-gray-300 accent-green-600"
                                           data-filter-type="deadline" value="today">
                                    <span class="text-sm text-gray-700">Сегодня</span>
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
                                class="w-full text-white py-2 rounded-lg text-sm font-medium bg-gradient-to-br from-emerald-500 to-emerald-600 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_15px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
                            Применить фильтры
                        </button>
                        <!-- <button onclick="applyFiltersAndClose()"
        class="flex-1 md:flex-none text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 text-sm md:text-base max-[500px]:basis-1/2 bg-gradient-to-br from-emerald-500 to-emerald-600 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
   Применить фильтры
</button> -->
                    </div>
                </div>
            </div>

            <!-- Поиск -->
            @if($backgroundEnabled && $backgroundImage)
                <div class="relative flex-1 max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white text-sm"></i>
                    <input type="text" id="taskSearchInput" placeholder="Поиск по названию..."
                        class="w-full pl-9 pr-3 py-2 border-none rounded-lg text-sm text-white bg-transparent/20 placeholder:text-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:shadow-[0_0_15px_rgba(16,185,129,0.5)]">
                </div>
            @else
                <div class="relative flex-1 max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="taskSearchInput" placeholder="Поиск по названию..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm transition-all duration-300 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:shadow-[0_0_15px_rgba(16,185,129,0.3)]">
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
                                        class="flex-1 text-white px-4 py-2.5 rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 text-sm font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center space-x-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-text-fill-color: white;">
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

                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-100 max-[500px]:hidden">
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
                            <a href="/team/tasks/{{ $task->id }}"
                               onclick="openTaskViewModal({{ $task->id }}); return false;">
                                <h4 class="font-medium cursor-pointer hover:text-blue-600 flex-1">
                                    {{ $task->name }}
                                </h4>
                            </a>
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
                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button onclick="archiveTask({{ $task->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-archive mr-2 text-yellow-500"></i> В архив
                                                    </button>
                                                @endif
                                            <button onclick="openCreateSubtaskModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <i class="fas fa-list mr-2 text-green-500"></i>Подзадача
                                            </button>
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

                        <div class="flex justify-between items-center max-[1300px]:items-end">
                            <div class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row">
                        <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                              class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>
                                @php
                                    $prioritySignals = [
                                        'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                                        'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                        'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                        'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                                    ];
                                    $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                                @endphp

                                @if(!$task->trashed())
                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}">
                                        <div class="flex items-end gap-[3px] h-5">
                                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"></div>
                                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"></div>
                                        </div>
                                        <span class="text-xs font-medium {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
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
                    @php
                        $prioritySignals = [
                            'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                        ];
                        $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                    @endphp

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
                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button onclick="archiveTask({{ $task->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-archive mr-2 text-yellow-500"></i> В архив
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

                        <div class="flex justify-between items-center max-[1300px]:items-end">
                             <div class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row">
                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                      class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>

                                <!-- НОВЫЙ СТИКЕР ПРИОРИТЕТА (как на второй странице) -->
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}">
                                    <div class="flex items-end gap-[3px] h-5">
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"></div>
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"></div>
                                    </div>
                                    <span class="text-xs font-medium {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                                </div>

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
                    @php
                        $prioritySignals = [
                            'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                        ];
                        $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                    @endphp

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
                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button onclick="archiveTask({{ $task->id }})"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                        <i class="fas fa-archive mr-2 text-yellow-500"></i> В архив
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

                         <div class="flex justify-between items-center max-[1300px]:items-end max-[500px]:items-center">
                            <div class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row">
                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                      class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>

                                <!-- НОВЫЙ СТИКЕР ПРИОРИТЕТА (как на второй странице) -->
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}">
                                    <div class="flex items-end gap-[3px] h-5">
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"></div>
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                                        <div class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"></div>
                                    </div>
                                    <span class="text-xs font-medium {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                                </div>

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

                        <div class="flex justify-between items-center max-[1100px]:flex-col max-[1100px]:items-start max-[1100px]:gap-2">
                        <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                              class="text-xs px-2 py-1 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>
                            <span class="text-xs text-gray-500">Завершено</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
   <div class="swiper-pagination flex justify-center pt-5 px-0 !bottom-2 min-[501px]:hidden"></div>
   </div>

    <!-- Модальные окна -->
    @include('partials.modal.task.show')
    @include('partials.modal.task.create-subtask')

    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 max-[500px]:p-6">
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
    @include('partials.modal.task.create')

    <!-- Модальное окно файлового менеджера -->
    @include('partials.modal.task.task-files-edit-modal')
    <!-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ ЗАДАЧИ  -->
    @include('partials.modal.welcome.edit-task-modal')

    <!-- Модальное окно подтверждения архивации -->
    <div id="confirmArchiveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm max-[500px]:p-6">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmArchiveModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-archive text-3xl text-yellow-600"></i>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Архивация задачи</h3>
                <p class="text-gray-600 text-center mb-6" id="archiveTaskMessage">
                    Вы уверены, что хотите отправить эту задачу в архив?
                </p>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Архивированные задачи можно будет восстановить на странице "Все задачи"
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button onclick="closeConfirmArchiveModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmArchive()"
                            class="flex-1 px-4 py-2.5 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition font-medium">
                        <i class="fas fa-archive mr-2"></i>Архивировать
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения восстановления -->
    <div id="confirmRestoreModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmRestoreModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash-restore text-3xl text-green-600"></i>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Восстановление задачи</h3>
                <p class="text-gray-600 text-center mb-6">
                    Задача будет восстановлена и появится на канбан-доске. Продолжить?
                </p>

                <div class="flex space-x-3">
                    <button onclick="closeConfirmRestoreModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmRestore()"
                            class="flex-1 px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition font-medium">
                        <i class="fas fa-check mr-2"></i>Восстановить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения полного удаления -->
    <div id="confirmForceDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmForceDeleteModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Удаление задачи</h3>
                <p class="text-gray-600 text-center mb-4">
                    Вы действительно хотите удалить эту задачу <span class="font-bold text-red-600">НАВСЕГДА</span>?
                </p>

                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Это действие невозможно отменить. Задача и все связанные с ней данные будут удалены безвозвратно.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button onclick="closeConfirmForceDeleteModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmForceDelete()"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-medium">
                        <i class="fas fa-trash-alt mr-2"></i>Удалить навсегда
                    </button>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script
  src="https://cdn.jsdelivr.net/npm/@dragdroptouch/drag-drop-touch@latest/dist/drag-drop-touch.esm.min.js"
  type="module"
></script>
    <script>
        //  ПЕРЕМЕННЫЕ
        let taskSelectedFiles = [];
        let taskAllFiles = [];

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
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">Ошибка загрузки1</div>`;
            }
        }

        function handleTaskFileSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            if (!taskAllFiles) return;
            const filtered = taskAllFiles.filter(file => file.name.toLowerCase().includes(searchTerm));
            renderTaskFiles(filtered);
        }

        function renderTaskFiles(files) {
            console.log('renderTaskFiles welcom')
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
                      <div class="file-card bg-white border-2 ${isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-lg p-3 transition-all duration-200 hover:shadow-md cursor-pointer"
                 onclick="toggleEditFileSelection(${file.id})">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-5 h-5 rounded ${isSelected ? 'bg-green-500' : 'border-2 border-gray-300'} flex items-center justify-center">
                        ${isSelected ? '<i class="fas fa-check text-white text-xs"></i>' : ''}
                    </div>
                    <button type="button"
                            onclick="event.stopPropagation(); downloadEditFile(${file.id})"
                            class="text-gray-400 hover:text-green-600 p-1 transition-colors"
                            title="Скачать">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 ${fileType.bg} rounded-lg flex items-center justify-center mx-auto mb-2">
                        <img src="/storage/${file.path}" alt="${escapeHtml(file.name)}" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-medium text-gray-800 truncate" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</p>
                    <p class="text-xs text-gray-500 mt-1">${formatFileSize(file.size)}</p>
                    <p class="text-xs text-gray-400">${formatDate(file.created_at)}</p>
                </div>
            </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;
            updateTaskSelectedCount();
        }

        function toggleTaskFileSelection(fileId) {
            console.log('toggleTaskFileSelectionwelcome')
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
            console.log('welcome.blade.php: Закрытие модального окна хранилища файлов');
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ====================
        let currentEditTaskId = null;

        async function openEditModal(taskId) {
            console.log('openEditTaskModal2')
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
                        showNotification('Вы не можете редактировать эту задачу. Редактировать может только автор задачи или руководитель.',"error");
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
                    document.body.classList.add('overflow-y-hidden')
                } else {
                    showNotification('Ошибка при загрузке задачи','error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                 showNotification('Ошибка при загрузке задачи','error');
            }
        }

        function closeEditModal() {
            document.getElementById('editTaskModal').classList.add('hidden');
            document.getElementById('editTaskForm').reset();
            currentEditTaskId = null;
            taskEditSelectedFiles = [];
            document.getElementById('editUploadNewFilesInput').value = '';
            document.getElementById('editUploadFilesList').classList.add('hidden');
            document.body.classList.remove('overflow-y-hidden')
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
                window.taskEditState.all = await response.json();
                console.log('Загружены все файлы из хранилища:', window.taskEditState.all.length);
                console.log('Текущие выбранные файлы (taskEditSelectedFiles):', taskEditSelectedFiles.map(f => f.id));
                renderTaskEditFiles(window.taskEditState.all);

                const searchInput = document.getElementById('fileManagerSearch');
                if (searchInput) {
                    searchInput.removeEventListener('input', handleTaskEditFileSearch);
                    searchInput.addEventListener('input', handleTaskEditFileSearch);
                }
            } catch (error) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">Ошибка загрузки2</div>`;
            }
        }

        function handleTaskEditFileSearch(e) {
            const searchTerm = e.target.value.toLowerCase();
            if (!window.taskEditState.all) return;
            const filtered = window.taskEditState.all.filter(file => file.name.toLowerCase().includes(searchTerm));
            renderTaskEditFiles(filtered);
        }


        function handleTaskEditCheckboxChange(e) {
            e.stopPropagation();
            const fileId = parseInt(this.value);
            const file = window.taskEditState.all.find(f => f.id === fileId);
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
                console.log('taskEditSelectedFiles после измененddия:', taskEditSelectedFiles.map(f => f.id));
            }
        }

        function toggleTaskEditFileSelection(fileId) {
            let file = window.taskEditState.all.find(f => f.id === fileId);
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
                    showNotification('Пожалуйста, выберите хотя бы один файл',"info");
                    return;
                }
                console.log('Подтверждение выбора. Файлы для сохранения:', taskEditSelectedFiles.map(f => f.id));
                updateTaskEditSelectedFilesDisplay();
                closeTaskStorageManager();
            } else if (isCreateModalVisible) {
                if (taskSelectedFiles.length === 0) {
                    showNotification('Пожалуйста, выберите хотя бы один файл',"info");
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


        // ==================== ФУНКЦИИ ФИЛЬТРАЦИИ ====================
      function toggleFiltersDropdown() {
    const dropdown = document.getElementById('filtersDropdown');
    const chevron = document.getElementById('filtersChevron');

    if (!dropdown || !chevron) return;
    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        dropdown.classList.remove('fade-out-x');
        dropdown.classList.add('fade-in-x');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        dropdown.classList.remove('fade-in-x');
        dropdown.classList.add('fade-out-x');

        chevron.style.transform = 'rotate(0deg)';

        setTimeout(() => {
            if (dropdown.classList.contains('fade-out-x')) {
                dropdown.classList.add('hidden');
            }
        }, 200);
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
                    // location.reload();
                    console.log('changed task status ')
                    showNotification('Статус задачи обновлен','success');
                } else {
                    showNotification(data.message || 'Ошибка при перемещении задачи','error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при перемещении задачи','error');
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ ЛИЧНЫХ ЗАДАЧ ====================
        function openPersonalTaskModal() {
            console.log('openPersonalTaskModal welcome.page')
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
            document.body.classList.add('overflow-y-hidden');
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
document.body.classList.remove('overflow-y-hidden');

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
                    showNotification('Задача переведена в работу!','success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при обновлении статуса','error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                 showNotification('Ошибка при обновлении статуса','error');
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
                 showNotification('Пожалуйста, укажите корректное время работы','warning');
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
                     showNotification('Задача отправлена на проверку!','success');
                    closeTimeModal();
                    location.reload();
                } else {
                     showNotification(data.message || 'Ошибка при отправке на проверку','error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при отправке на проверку','error');
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
                showNotification('Пожалуйста, укажите причину отказа','error');
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
                    showNotification('Вы отказались от задачи','warning');
                    closeRejectModal();
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при отказе от задачи','error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                 showNotification('Ошибка при отказе от задачи','error');
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

        // Открыть модальное окно просмотра задачи- эта функция уже написана в  app.blade.php
    //     async function openTaskViewModal(taskId) {
    //         console.log('openTaskViewModal')
    //         const modal = document.getElementById('taskViewModal');
    //         const content = document.getElementById('taskModalContent');

    //         if (!modal || !content) {
    //             console.error('Модальное окно не найдено');
    //             return;
    //         }

    //         // Сохраняем ID задачи
    //         window.currentTaskId = taskId;
    //         window.taskId = taskId;

    //         // МЕНЯЕМ URL БЕЗ ПЕРЕЗАГРУЗКИ СТРАНИЦЫ
    //         const newUrl = `/team/tasks/${taskId}`;
    //         window.history.pushState({ taskId: taskId, modalOpen: true }, '', newUrl);

    //         // Показываем загрузчик
    //         content.innerHTML = `
    //     <div class="text-center py-8">
    //         <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
    //         <p class="text-gray-500 mt-2">Загрузка задачи...</p>
    //     </div>
    // `;

    //         // Показываем модальное окно
    //         modal.style.backdropFilter = 'blur(10px)';
    //         modal.classList.remove('hidden');

    //         try {
    //             // ПРАВИЛЬНЫЙ URL - без /view и без /page
    //             const response = await fetch(`/tasks/${taskId}`, {
    //                 method: 'GET',
    //                 headers: {
    //                     'X-Requested-With': 'XMLHttpRequest',
    //                     'Accept': 'text/html'
    //                 }
    //             });

    //             if (!response.ok) {
    //                 throw new Error(`HTTP error! status: ${response.status}`);
    //             }

    //             const html = await response.text();
    //             content.innerHTML = html;

    //         } catch (error) {
    //             console.error('Ошибка:', error);
    //             content.innerHTML = `
    //         <div class="text-center py-8">
    //             <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
    //             <p class="text-gray-500 mt-2">Не удалось загрузить задачу</p>
    //             <p class="text-sm text-gray-400 mt-1">${error.message}</p>
    //             <button onclick="openTaskViewModal(${taskId})"
    //                     class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
    //                 <i class="fas fa-sync-alt mr-2"></i>Повторить
    //             </button>
    //         </div>
    //     `;
    //         }
    //     }

        // Закрыть модальное окно просмотра задачи - эта функция уже написана в  app.blade.php
        // function closeTaskViewModal() {
        //     const modal = document.getElementById('taskViewModal');
        //     const content = document.getElementById('taskModalContent');

        //     if (modal) {
        //         modal.classList.add('hidden');
        //         modal.style.backdropFilter = '';
        //     }

        //     if (content) {
        //         content.innerHTML = `
        //     <div class="text-center py-8">
        //         <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
        //         <p class="text-gray-500 mt-2">Загрузка задачи...</p>
        //     </div>
        // `;
        //     }

        //     // Меняем URL обратно на /team/tasks без перезагрузки
        //     window.history.pushState({}, '', '/home');
        // }

        // Обрабатываем кнопку "Назад" в браузере
        window.addEventListener('popstate', function(event) {
            const modal = document.getElementById('taskViewModal');

            if (modal && !modal.classList.contains('hidden')) {
                closeTaskViewModal();
            }
        });

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('taskViewModal');
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeTaskViewModal();
            }
        });

        // Закрытие по клику на фон
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('taskViewModal');
            if (e.target === modal) {
                closeTaskViewModal();
            }
        });

        // При загрузке страницы проверяем URL и открываем модалку если нужно
        document.addEventListener('DOMContentLoaded', function() {
            const match = window.location.pathname.match(/\/tasks\/(\d+)/);

            if (match && !window.location.pathname.includes('/page/')) {
                const taskId = match[1];
                // Открываем модальное окно с задачей
                setTimeout(function() {
                    openTaskViewModal(taskId);
                }, 100);
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.id === 'taskViewModal') closeTaskViewModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeTaskViewModal();
        });

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

    </script>

    <style>
        .swiper-notification {
            display: none !important;
        }
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
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.modal-content::-webkit-scrollbar {
    display: none;
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
        width: 87% !important;
        max-width: 87% !important;
    }
    .board-column:first-child,
    .board-column:last-child {
        width: 92% !important;
        max-width: 92% !important;
    }

    .task-card, .task-card * {
        touch-action: pan-x pan-y !important;
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


@push('scripts')
 @vite('resources/js/pages/welcome.page.js')
   <script>
document.addEventListener('DOMContentLoaded', () => {

    if (window.DragDropTouch) {
        window.DragDropTouch._HOLD_DELAY = 150;
        console.log('Полифил успешно найден и настроен!',1);
    } else {
        console.log('drag not found — полифил всё еще не загрузился.');
    }


    if (window.innerWidth > 500) return;

    const sliderElement = document.querySelector('.sw-v');


    if (sliderElement) {
      window.mySwiper = new Swiper('.sw-v', {
    wrapperClass: 'sw-v-wrapper',
    slideClass: 'board-column',
    centeredSlides: true,
    centeredSlidesBounds: true,
    slidesPerView: 'auto',
    spaceBetween: 10,
    loop: false,

    /* УДАЛЕНО / ЗАКОММЕНТИРОВАНО: */
    /* noSwiping: true, */
    /* noSwipingClass: 'task-card', */

    observer: true,
    observeParents: true,
    watchSlidesProgress: true,
     touchStartPreventDefault: false,
     pagination: {
        el: '.swiper-pagination',
        clickable: true,
        bulletClass: 'swiper-pagination-bullet bg-gray-400 opacity-50 mx-1 inline-block rounded-full w-2 h-2',
        bulletActiveClass: '!bg-[#22c55e] !opacity-100 w-4 rounded-lg transition-all duration-300'
    },
});
        console.log('Swiper успешно запущен для мобильного экрана!');
    }
});
</script>

<script>
    // Переменные для хранения ID задачи в модальных окнах
    let pendingArchiveTaskId = null;
    let pendingRestoreTaskId = null;
    let pendingForceDeleteTaskId = null;

    // ==================== АРХИВАЦИЯ С МОДАЛЬНЫМ ОКНОМ ====================
    function archiveTask(taskId) {
        pendingArchiveTaskId = taskId;
        const modal = document.getElementById('confirmArchiveModal');
        const modalContent = document.getElementById('confirmArchiveModalContent');

        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    function closeConfirmArchiveModal() {
        const modal = document.getElementById('confirmArchiveModal');
        const modalContent = document.getElementById('confirmArchiveModalContent');

        if (modal) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                pendingArchiveTaskId = null;
            }, 300);
        }
    }

    async function confirmArchive() {
        if (!pendingArchiveTaskId) return;

        // Показываем индикатор загрузки на кнопке
        const confirmBtn = document.querySelector('#confirmArchiveModal .bg-yellow-500');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Архивация...';
        confirmBtn.disabled = true;

        try {
            const response = await fetch(`/tasks/${pendingArchiveTaskId}/archive`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
                closeConfirmArchiveModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                closeConfirmArchiveModal();
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при архивации задачи', 'error');
            closeConfirmArchiveModal();
        } finally {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    }

    // ==================== ВОССТАНОВЛЕНИЕ С МОДАЛЬНЫМ ОКНОМ ====================
    function restoreTask(taskId) {
        pendingRestoreTaskId = taskId;
        const modal = document.getElementById('confirmRestoreModal');
        const modalContent = document.getElementById('confirmRestoreModalContent');

        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    function closeConfirmRestoreModal() {
        const modal = document.getElementById('confirmRestoreModal');
        const modalContent = document.getElementById('confirmRestoreModalContent');

        if (modal) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                pendingRestoreTaskId = null;
            }, 300);
        }
    }

    async function confirmRestore() {
        if (!pendingRestoreTaskId) return;

        const confirmBtn = document.querySelector('#confirmRestoreModal .bg-green-500');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Восстановление...';
        confirmBtn.disabled = true;

        try {
            const response = await fetch(`/tasks/${pendingRestoreTaskId}/restore`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
                closeConfirmRestoreModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                closeConfirmRestoreModal();
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при восстановлении задачи', 'error');
            closeConfirmRestoreModal();
        } finally {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    }

    // ==================== ПОЛНОЕ УДАЛЕНИЕ С МОДАЛЬНЫМ ОКНОМ ====================
    function forceDeleteTask(taskId) {
        pendingForceDeleteTaskId = taskId;
        const modal = document.getElementById('confirmForceDeleteModal');
        const modalContent = document.getElementById('confirmForceDeleteModalContent');

        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    function closeConfirmForceDeleteModal() {
        const modal = document.getElementById('confirmForceDeleteModal');
        const modalContent = document.getElementById('confirmForceDeleteModalContent');

        if (modal) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                pendingForceDeleteTaskId = null;
            }, 300);
        }
    }

    async function confirmForceDelete() {
        if (!pendingForceDeleteTaskId) return;

        const confirmBtn = document.querySelector('#confirmForceDeleteModal .bg-red-500');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Удаление...';
        confirmBtn.disabled = true;

        try {
            const response = await fetch(`/tasks/${pendingForceDeleteTaskId}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
                closeConfirmForceDeleteModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                closeConfirmForceDeleteModal();
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при удалении задачи', 'error');
            closeConfirmForceDeleteModal();
        } finally {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    }


    // Закрытие модальных окон по клику на фон
    document.addEventListener('click', function(e) {
        if (e.target.id === 'confirmArchiveModal') closeConfirmArchiveModal();
        if (e.target.id === 'confirmRestoreModal') closeConfirmRestoreModal();
        if (e.target.id === 'confirmForceDeleteModal') closeConfirmForceDeleteModal();
    });

    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmArchiveModal();
            closeConfirmRestoreModal();
            closeConfirmForceDeleteModal();
        }
    });
</script>
@endpush
