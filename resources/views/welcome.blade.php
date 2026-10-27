@extends ('layouts.app')
@push ('styles')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"
    />
    @vite ('resources/css/pages/welcome.page.css')
@endpush
@section ('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp
    <!-- Заголовок и статистика -->
    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4"
    >
        <nav class="hidden max-[500px]:block">
            <ol class="flex items-center gap-1.5">
                <li>
                    <a
                        class="inline-flex items-center gap-1.5 text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                        href="{{ route('welcome') }}"
                    >
                        Главная
                        <svg
                            class="stroke-current"
                            width="17"
                            height="16"
                            viewBox="0 0 17 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                                stroke=""
                                stroke-width="1.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></path>
                        </svg>
                    </a>
                </li>
                <li
                    class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}"
                    x-text="pageName"
                >
                    Мои задачи
                </li>
            </ol>
        </nav>
        <div class="max-[500px]:hidden">
            @if ($backgroundEnabled && $backgroundImage)
                <h2
                    class="text-3xl font-bold text-white max-[500px]:text-[26px]"
                >
                    Мои задачи
                </h2>
                <p class="text-white text-sm max-[500px]:text-[13px]">Ваши личные задачи не видны на странице Команда</p>
            @else
                <h2
                    class="text-3xl font-bold max-[500px]:text-[26px]"
                    style="
                        background: linear-gradient(
                            135deg,
                            #10b981 0%,
                            #059669 100%
                        );
                        -webkit-background-clip: text;
                        -webkit-text-fill-color: transparent;
                    "
                >
                    Мои задачи
                </h2>
                <p class="text-gray-700 text-sm max-[500px]:text-[13px]">Ваши личные задачи не видны на странице Команда</p>
            @endif
        </div>

        <div class="flex space-x-4 w-full md:w-auto">
            <x-ui.button
                class="flex-1 px-3 py-2 md:px-6 md:py-2 text-[16px]"
                variant="action"
                icon="fas fa-plus"
                onclick="window.openPersonalTaskModal()"
            >
                <span>Добавить</span>
            </x-ui.button>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="mb-4 max-[500px]:mb-1 relative">
        <div class="flex items-center gap-2 flex-wrap">
            <!-- class="max-[450px]:fixed max-[450px]:bottom-3 max-[450px]:left-3" -->
            <!-- Кнопка фильтров -->
            <div class="relative">
                @if ($backgroundEnabled && $backgroundImage)
                    <button
                        onclick="toggleFiltersDropdown()"
                        class="bg-transparent/20 border-none text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm"
                    >
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <span
                            id="activeFiltersCount"
                            class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden"
                            >0</span
                        >
                        <i
                            class="fas fa-chevron-down ml-1 text-xs transition-transform"
                            id="filtersChevron"
                        ></i>
                    </button>
                @else
                    <button
                        onclick="toggleFiltersDropdown()"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm"
                    >
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <span
                            id="activeFiltersCount"
                            class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden"
                            >0</span
                        >
                        <i
                            class="fas fa-chevron-down ml-1 text-xs transition-transform"
                            id="filtersChevron"
                        ></i>
                    </button>
                @endif

                <!-- Выпадающая панель фильтров -->
                <div
                    id="filtersDropdown"
                    class="hidden absolute left-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border z-50 max-[500px]:w-[95vw] max-[500px]:left-[-100%] max-[500px]:transform max-[500px]:translate-x-[5%] max-[450px]:left-[-0%] max-[450px]:translate-x-[0%]"
                >
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-gray-800">
                                Фильтрация задач
                            </h3>
                            <button
                                onclick="clearAllFilters()"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                <i class="fas fa-undo-alt mr-1"></i>Сбросить
                            </button>
                        </div>
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        <!-- Приоритет -->
                        <div class="p-4 border-b border-gray-100">
                            <div
                                class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('prioritySection')"
                            >
                                <span class="font-medium text-gray-700 text-sm"
                                    >Приоритет</span
                                >
                                <i
                                    class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="prioritySectionIcon"
                                ></i>
                            </div>
                            <div id="prioritySection" class="mt-3 space-y-2">
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority"
                                        value="critical"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Критический</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority"
                                        value="high"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Высокий</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority"
                                        value="medium"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Средний</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="priority"
                                        value="low"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Низкий</span
                                    >
                                </label>
                            </div>
                        </div>

                        <!-- Сроки -->
                        <div class="p-4 border-b border-gray-100">
                            <div
                                class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('deadlineSection')"
                            >
                                <span class="font-medium text-gray-700 text-sm"
                                    >Сроки</span
                                >
                                <i
                                    class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="deadlineSectionIcon"
                                ></i>
                            </div>
                            <div id="deadlineSection" class="mt-3 space-y-2">
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline"
                                        value="overdue"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Просроченные</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline"
                                        value="today"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Сегодня</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline"
                                        value="tomorrow"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Завтра</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="deadline"
                                        value="week"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >На этой неделе</span
                                    >
                                </label>
                            </div>
                        </div>

                        <!-- Файлы -->
                        <div class="p-4 border-b border-gray-100">
                            <div
                                class="flex justify-between items-center cursor-pointer"
                                onclick="toggleFilterSection('filesSection')"
                            >
                                <span class="font-medium text-gray-700 text-sm"
                                    >Файлы</span
                                >
                                <i
                                    class="fas fa-chevron-down text-gray-400 text-xs transition-transform"
                                    id="filesSectionIcon"
                                ></i>
                            </div>
                            <div id="filesSection" class="mt-3 space-y-2">
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="has-files"
                                        value="true"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Есть файлы</span
                                    >
                                </label>
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                                >
                                    <input
                                        type="checkbox"
                                        class="filter-checkbox rounded border-gray-300 accent-green-600"
                                        data-filter-type="has-files"
                                        value="false"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Нет файлов</span
                                    >
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-b-lg">
                        <x-ui.button
                            variant="action"
                            onclick="applyFiltersAndClose()"
                            class="w-full"
                        >
                            Применить фильтры
                        </x-ui.button>
                    </div>
                </div>
            </div>

            <!-- Поиск -->
            @if ($backgroundEnabled && $backgroundImage)
                <div class="relative flex-1 max-w-xs">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white text-sm"
                    ></i>
                    <input
                        type="text"
                        id="taskSearchInput"
                        placeholder="Поиск по названию..."
                        class="w-full pl-9 pr-3 py-2 border-none rounded-lg text-sm text-white bg-transparent/20 placeholder:text-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:shadow-[0_0_15px_rgba(16,185,129,0.5)]"
                    />
                </div>
            @else
                <div class="relative flex-1 max-w-xs">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"
                    ></i>
                    <input
                        type="text"
                        id="taskSearchInput"
                        placeholder="Поиск по названию..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm transition-all duration-300 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:shadow-[0_0_15px_rgba(16,185,129,0.3)]"
                    />
                </div>
            @endif
        </div>

        <!-- Активные фильтры (чипсы) -->
        <div
            id="activeFiltersContainer"
            class="flex flex-wrap gap-2 mt-3 min-h-[32px] max-[500px]:min-h-[10px]"
        >
            <!-- Сюда динамически добавляются активные фильтры -->
        </div>
    </div>

    <!-- Доска с задачами -->
    <div class="sw-v overflow-hidden w-full">
        <div
            class="sw-v-wrapper flex lg:grid lg:grid-cols-4 xl:grid-cols-4 gap-6 max-[500px]:gap-0"
        >
            <!-- Колонка "Новые" -->
            <div
                class="rounded-lg p-4 board-column bg-transparent max-[600px]:p-0"
                data-status="new"
            >
                @if ($backgroundEnabled && $backgroundImage)
                    <div
                        class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2"
                    >
                        <h3 class="font-semibold text-white">Новые</h3>
                        <span
                            class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['new'] }}</span
                        >
                    </div>
                @else
                    <div
                        class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title"
                    >
                        <h3 class="font-semibold text-white">Новые</h3>
                        <span
                            class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['new'] }}</span
                        >
                    </div>
                @endif

                <!-- КНОПКА БЫСТРОГО ДОБАВЛЕНИЯ -->
                <div id="quickAddBlock" class="mb-4">
                    <button
                        onclick="showQuickAddForm()"
                        id="showQuickAddBtn"
                        class="w-full group relative overflow-hidden bg-transparent/10 hover:from-green-50 hover:to-white border-2 border-dashed border-gray-300 hover:border-green-400 rounded-xl p-2 text-gray-500 hover:text-green-600 transition-all duration-300 flex items-center justify-center space-x-2"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-green-500/0 via-green-500/0 to-green-500/0 group-hover:from-green-500/5 group-hover:via-green-500/10 group-hover:to-green-500/5 transition-all duration-500"
                        ></div>
                        <i
                            class="fas fa-plus-circle text-green-500 text-xl group-hover:scale-110 transition-transform duration-300"
                        ></i>
                        <span class="text-sm font-medium">Быстрая задача</span>
                    </button>

                    <!-- ФОРМА БЫСТРОГО ДОБАВЛЕНИЯ -->
                    <div id="quickAddForm" class="hidden mt-2">
                        <div
                            class="bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
                            id="quickAddFormInner"
                        >
                            <div class="p-4 space-y-4">
                                <div class="relative">
                                    <i
                                        class="fas fa-tasks absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"
                                    ></i>
                                    <input
                                        type="text"
                                        id="quickTaskName"
                                        placeholder="Что нужно сделать?"
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm"
                                        autocomplete="off"
                                    />
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="relative">
                                        <i
                                            class="fas fa-flag absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"
                                        ></i>
                                        <select
                                            id="quickTaskPriority"
                                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm appearance-none cursor-pointer"
                                        >
                                            <option value="низкий">
                                                Низкий
                                            </option>
                                            <option value="средний" selected>
                                                ⚡ Средний
                                            </option>
                                            <option value="высокий">
                                                Высокий
                                            </option>
                                            <option value="критический">
                                                Критический
                                            </option>
                                        </select>
                                        <i
                                            class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs pointer-events-none"
                                        ></i>
                                    </div>

                                    <div class="relative">
                                        <i
                                            class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"
                                        ></i>
                                        <input
                                            type="datetime-local"
                                            id="quickTaskDeadline"
                                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm cursor-pointer"
                                        />
                                    </div>
                                </div>

                                <div class="relative">
                                    <i
                                        class="fas fa-align-left absolute left-3 top-3 text-gray-400 text-sm"
                                    ></i>
                                    <textarea
                                        id="quickTaskDescription"
                                        rows="2"
                                        placeholder="Добавить описание..."
                                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 text-sm resize-none"
                                    ></textarea>
                                </div>

                                <div
                                    class="flex space-x-3 pt-2 max-[500px]:flex-col max-[500px]:space-x-0 max-[500px]:space-y-2"
                                >
                                    <x-ui.button
                                        class="flex-1"
                                        variant="action"
                                        icon="fas fa-check-circle"
                                        onclick="createQuickTask()"
                                    >
                                        Создать
                                    </x-ui.button>

                                    <x-ui.button
                                        variant="secondary"
                                        class="flex-1 bg-gray-100 text-gray-600"
                                        icon="fas fa-times"
                                        onclick="hideQuickAddForm()"
                                    >
                                        Отмена
                                    </x-ui.button>
                                </div>
                            </div>

                            <div
                                class="bg-gray-50 px-4 py-2 border-t border-gray-100 max-[500px]:hidden"
                            >
                                <div
                                    class="flex items-center justify-between text-xs text-gray-400"
                                >
                                    <span
                                        ><i class="fas fa-keyboard mr-1"></i>
                                        Enter — создать</span
                                    >
                                    <span
                                        ><i class="fas fa-arrow-left mr-1"></i>
                                        Esc — отмена</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 task-container" data-status="new">
                    @foreach ($tasksByStatus['new'] as $task)
                        <div
                            class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between {{ $task->status == 'просрочена' ? 'border-l-4 border-red-500' : '' }}"
                            draggable="true"
                            data-task="{{ $task->id }}"
                            data-priority="{{ $task->priority ?? 'medium' }}"
                            data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                            data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                            data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                            data-author-id="{{ $task->author_id }}"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <a
                                    href="/team/tasks/{{ $task->id }}"
                                    onclick="openTaskViewModal({{ $task->id }}); return false;"
                                >
                                    <h4
                                        class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                    >
                                        {{ $task->name }}
                                    </h4>
                                </a>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <button
                                            onclick="toggleTaskMenu(event, {{ $task->id }})"
                                            class="text-gray-500 hover:text-gray-700 p-1"
                                            title="Действия"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div
                                            id="taskMenu-{{ $task->id }}"
                                            class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50"
                                        >
                                            <div class="py-1">
                                                @if ($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button
                                                        onclick="openEditModal({{ $task->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-edit mr-2 text-blue-500"
                                                        ></i>
                                                        Редактировать
                                                    </button>
                                                @endif
                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                @if ($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button
                                                        onclick="archiveTask({{ $task->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-archive mr-2 text-yellow-500"
                                                        ></i>
                                                        В архив
                                                    </button>
                                                @endif
                                                <button
                                                    onclick="openCreateSubtaskModal({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-list mr-2 text-green-500"
                                                    ></i
                                                    >Подзадача
                                                </button>
                                                <button
                                                    onclick="window.startTask({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-play mr-2 text-green-500"
                                                    ></i>
                                                    Начать
                                                </button>
                                                <button
                                                    onclick="window.showRejectModal({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-times-circle mr-2"
                                                    ></i>
                                                    Отказаться
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($task->files_count > 0)
                                <div
                                    class="mb-2 flex items-center text-xs text-gray-500"
                                >
                                    <i class="fas fa-paperclip mr-1"></i>
                                    <span>Файлы: {{ $task->files_count }}</span>
                                </div>
                            @endif

                            @if ($task->deadline)
                                <div class="mb-3 max-[500px]:hidden">
                                    <div
                                        class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}"
                                    >
                                        <i class="fas fa-clock mr-2"></i>
                                        {{ $task->deadline->format('d.m.Y H:i') }}
                                        @if ($task->deadline->isPast())
                                            <span class="ml-1"
                                                >(Просрочено)</span
                                            >
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div
                                class="flex justify-between items-center max-[1300px]:items-end"
                            >
                                <div
                                    class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row"
                                >
                                    <span
                                        style="
                                            background: linear-gradient(
                                                180deg,
                                                #1a1f2e 0%,
                                                #161b28 100%
                                            );
                                        "
                                        class="text-xs px-2 py-1 rounded text-white"
                                        >{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span
                                    >
                                    @php
                                    $prioritySignals = [
                                        'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                                        'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                        'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                        'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                                    ];
                                    $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                                @endphp

                                    @if (!$task->trashed())
                                        <div
                                            class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}"
                                        >
                                            <div
                                                class="flex items-end gap-[3px] h-5"
                                            >
                                                <div
                                                    class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"
                                                ></div>
                                                <div
                                                    class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"
                                                ></div>
                                                <div
                                                    class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"
                                                ></div>
                                                <div
                                                    class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"
                                                ></div>
                                            </div>
                                            <span
                                                class="text-xs font-medium {{ $signal['text'] }}"
                                                >{{ ucfirst($task->priority) }}</span
                                            >
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400"
                                            >—</span
                                        >
                                    @endif
                                </div>
                                <div class="flex space-x-1">
                                    <div
                                        class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                        title="{{ $task->author->name }}"
                                    >
                                        {{ substr($task->author->name, 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Колонка "В работе" -->
            <div
                class="rounded-lg p-4 board-column max-[600px]:p-0"
                data-status="in-progress"
            >
                @if ($backgroundEnabled && $backgroundImage)
                    <div
                        class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2"
                    >
                        <h3 class="font-semibold text-white">В работе</h3>
                        <span
                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['in_progress'] }}</span
                        >
                    </div>
                @else
                    <div
                        class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title"
                    >
                        <h3 class="font-semibold text-white">В работе</h3>
                        <span
                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['in_progress'] }}</span
                        >
                    </div>
                @endif

                <div class="space-y-4 task-container" data-status="in-progress">
                    @foreach ($tasksByStatus['in_progress'] as $task)
                        @php
                        $prioritySignals = [
                            'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                        ];
                        $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                    @endphp

                        <div
                            class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between {{ $task->status == 'просрочена' ? 'border-l-4 border-red-500' : '' }}"
                            draggable="true"
                            data-task="{{ $task->id }}"
                            data-priority="{{ $task->priority ?? 'medium' }}"
                            data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                            data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                            data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                            data-author-id="{{ $task->author_id }}"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <h4
                                    class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                    onclick="openTaskViewModal({{ $task->id }})"
                                >
                                    {{ $task->name }}
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <button
                                            onclick="toggleTaskMenu(event, {{ $task->id }})"
                                            class="text-gray-500 hover:text-gray-700 p-1"
                                            title="Действия"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div
                                            id="taskMenu-{{ $task->id }}"
                                            class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50"
                                        >
                                            <div class="py-1">
                                                @if ($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button
                                                        onclick="openEditModal({{ $task->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-edit mr-2 text-blue-500"
                                                        ></i>
                                                        Редактировать
                                                    </button>
                                                @endif
                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                @if ($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button
                                                        onclick="archiveTask({{ $task->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-archive mr-2 text-yellow-500"
                                                        ></i>
                                                        В архив
                                                    </button>
                                                @endif
                                                <button
                                                    onclick="window.sendForReview({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-check-circle mr-2 text-green-500"
                                                    ></i>
                                                    Отправить на проверку
                                                </button>
                                                <button
                                                    onclick="window.showRejectModal({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-times-circle mr-2"
                                                    ></i>
                                                    Отказаться
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($task->files_count > 0)
                                <div
                                    class="mb-2 flex items-center text-xs text-gray-500"
                                >
                                    <i class="fas fa-paperclip mr-1"></i>
                                    <span>Файлы: {{ $task->files_count }}</span>
                                </div>
                            @endif

                            @if ($task->deadline)
                                <div class="mb-3 max-[500px]:hidden">
                                    <div
                                        class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}"
                                    >
                                        <i class="fas fa-clock mr-2"></i>
                                        {{ $task->deadline->format('d.m.Y H:i') }}
                                        @if ($task->deadline->isPast())
                                            <span class="ml-1"
                                                >(Просрочено)</span
                                            >
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div
                                class="flex justify-between items-center max-[1300px]:items-end"
                            >
                                <div
                                    class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row"
                                >
                                    <span
                                        style="
                                            background: linear-gradient(
                                                180deg,
                                                #1a1f2e 0%,
                                                #161b28 100%
                                            );
                                        "
                                        class="text-xs px-2 py-1 rounded text-white"
                                        >{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span
                                    >

                                    <!-- НОВЫЙ СТИКЕР ПРИОРИТЕТА (как на второй странице) -->
                                    <div
                                        class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}"
                                    >
                                        <div
                                            class="flex items-end gap-[3px] h-5"
                                        >
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"
                                            ></div>
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"
                                            ></div>
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"
                                            ></div>
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"
                                            ></div>
                                        </div>
                                        <span
                                            class="text-xs font-medium {{ $signal['text'] }}"
                                            >{{ ucfirst($task->priority) }}</span
                                        >
                                    </div>

                                    @if ($task->category)
                                        <span
                                            class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded"
                                            >{{ $task->category->name }}</span
                                        >
                                    @endif
                                    @if ($task->status == 'просрочена')
                                        <span
                                            class="bg-red-500 text-white text-xs px-2 py-1 rounded"
                                            >⚠️ Просрочена</span
                                        >
                                    @endif
                                </div>
                                <div class="flex space-x-1">
                                    <div
                                        class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                        title="{{ $task->author->name }}"
                                    >
                                        {{ substr($task->author->name, 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Колонка "На проверке" -->
            <div
                class="rounded-lg p-4 board-column max-[600px]:p-0"
                data-status="review"
            >
                @if ($backgroundEnabled && $backgroundImage)
                    <div
                        class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2"
                    >
                        <h3 class="font-semibold text-white">На проверке</h3>
                        <span
                            class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['review'] }}</span
                        >
                    </div>
                @else
                    <div
                        class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title"
                    >
                        <h3 class="font-semibold text-white">На проверке</h3>
                        <span
                            class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['review'] }}</span
                        >
                    </div>
                @endif

                <div class="space-y-4 task-container" data-status="review">
                    @foreach ($tasksByStatus['review'] as $task)
                        @php
                        $prioritySignals = [
                            'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                        ];
                        $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                    @endphp

                        <div
                            class="task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between {{ $task->status == 'просрочена' ? 'border-l-4 border-red-500' : '' }}"
                            draggable="true"
                            data-task="{{ $task->id }}"
                            data-priority="{{ $task->priority ?? 'medium' }}"
                            data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                            data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                            data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                            data-author-id="{{ $task->author_id }}"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <h4
                                    class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                    onclick="openTaskViewModal({{ $task->id }})"
                                >
                                    {{ $task->name }}
                                </h4>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <button
                                            onclick="toggleTaskMenu(event, {{ $task->id }})"
                                            class="text-gray-500 hover:text-gray-700 p-1"
                                            title="Действия"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div
                                            id="taskMenu-{{ $task->id }}"
                                            class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50"
                                        >
                                            <div class="py-1">
                                                @if ($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button
                                                        onclick="openEditModal({{ $task->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-edit mr-2 text-blue-500"
                                                        ></i>
                                                        Редактировать
                                                    </button>
                                                @endif
                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                @if ($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                    <button
                                                        onclick="archiveTask({{ $task->id }})"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                                                    >
                                                        <i
                                                            class="fas fa-archive mr-2 text-yellow-500"
                                                        ></i>
                                                        В архив
                                                    </button>
                                                @endif
                                                <button
                                                    onclick="window.approveTask({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-green-500 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-check-circle mr-2"
                                                    ></i>
                                                    Завершить
                                                </button>
                                                <button
                                                    onclick="window.showRejectModal({{ $task->id }})"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center"
                                                >
                                                    <i
                                                        class="fas fa-times-circle mr-2"
                                                    ></i>
                                                    Отказаться
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($task->files_count > 0)
                                <div
                                    class="mb-2 flex items-center text-xs text-gray-500"
                                >
                                    <i class="fas fa-paperclip mr-1"></i>
                                    <span>Файлы: {{ $task->files_count }}</span>
                                </div>
                            @endif

                            @if ($task->deadline)
                                <div class="mb-3 max-[500px]:hidden">
                                    <div
                                        class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}"
                                    >
                                        <i class="fas fa-clock mr-2"></i>
                                        {{ $task->deadline->format('d.m.Y H:i') }}
                                        @if ($task->deadline->isPast())
                                            <span class="ml-1"
                                                >(Просрочено)</span
                                            >
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($task->actual_hours)
                                <div class="mb-3">
                                    <div
                                        class="flex items-center text-sm text-gray-500"
                                    >
                                        <i
                                            class="fas fa-hourglass-end mr-2"
                                        ></i>
                                        Фактическое время: {{ $task->actual_hours }}ч
                                    </div>
                                </div>
                            @endif

                            <div
                                class="flex justify-between items-center max-[1300px]:items-end max-[500px]:items-center"
                            >
                                <div
                                    class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row"
                                >
                                    <span
                                        style="
                                            background: linear-gradient(
                                                180deg,
                                                #1a1f2e 0%,
                                                #161b28 100%
                                            );
                                        "
                                        class="text-xs px-2 py-1 rounded text-white"
                                        >{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span
                                    >

                                    <!-- НОВЫЙ СТИКЕР ПРИОРИТЕТА (как на второй странице) -->
                                    <div
                                        class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}"
                                    >
                                        <div
                                            class="flex items-end gap-[3px] h-5"
                                        >
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"
                                            ></div>
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"
                                            ></div>
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"
                                            ></div>
                                            <div
                                                class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"
                                            ></div>
                                        </div>
                                        <span
                                            class="text-xs font-medium {{ $signal['text'] }}"
                                            >{{ ucfirst($task->priority) }}</span
                                        >
                                    </div>

                                    @if ($task->status == 'просрочена')
                                        <span
                                            class="bg-red-500 text-white text-xs px-2 py-1 rounded"
                                            >⚠️ Просрочена</span
                                        >
                                    @endif
                                </div>
                                <div class="flex space-x-1">
                                    <div
                                        class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                        title="{{ $task->author->name }}"
                                    >
                                        {{ substr($task->author->name, 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Колонка "Завершено" -->
            <div
                class="rounded-lg p-4 board-column bg-transparent max-[600px]:p-0"
                data-status="done"
            >
                @if ($backgroundEnabled && $backgroundImage)
                    <div
                        class="flex justify-between items-center mb-4 border-none backdrop-blur-md bg-transparent/20 rounded-lg p-2"
                    >
                        <h3 class="font-semibold text-white">Завершено</h3>
                        <span
                            class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['done'] }}</span
                        >
                    </div>
                @else
                    <div
                        class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title"
                    >
                        <h3 class="font-semibold text-white">Завершено</h3>
                        <span
                            class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded stat-count"
                            >{{ $stats['done'] }}</span
                        >
                    </div>
                @endif

                <div class="space-y-4 task-container" data-status="done">
                    @foreach ($tasksByStatus['done'] as $task)
                        <div
                            class="task-card bg-white p-4 rounded-lg shadow opacity-80 cursor-move flex flex-col justify-between"
                            draggable="true"
                            data-task="{{ $task->id }}"
                            data-priority="{{ $task->priority ?? 'medium' }}"
                            data-deadline="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                            data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                            data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                            data-author-id="{{ $task->author_id }}"
                        >
                            <div class="flex justify-between items-start mb-2">
                                <h4
                                    class="font-medium cursor-pointer hover:text-blue-600 flex-1"
                                    onclick="openTaskViewModal({{ $task->id }})"
                                >
                                    {{ $task->name }}
                                </h4>
                                <div class="flex space-x-1">
                                    <div
                                        class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                                        title="{{ $task->author->name }}"
                                    >
                                        {{ substr($task->author->name, 0, 2) }}
                                    </div>
                                </div>
                            </div>

                            @if ($task->files_count > 0)
                                <div
                                    class="mb-2 flex items-center text-xs text-gray-500"
                                >
                                    <i class="fas fa-paperclip mr-1"></i>
                                    <span>Файлы: {{ $task->files_count }}</span>
                                </div>
                            @endif

                            @if ($task->actual_hours)
                                <div class="mb-3 max-[500px]:hidden">
                                    <div
                                        class="flex items-center text-sm text-gray-500"
                                    >
                                        <i
                                            class="fas fa-hourglass-end mr-2"
                                        ></i>
                                        Затрачено времени: {{ $task->actual_hours }}ч
                                    </div>
                                </div>
                            @endif

                            <div
                                class="flex justify-between items-center max-[1100px]:flex-col max-[1100px]:items-start max-[1100px]:gap-2"
                            >
                                <span
                                    style="
                                        background: linear-gradient(
                                            180deg,
                                            #1a1f2e 0%,
                                            #161b28 100%
                                        );
                                    "
                                    class="text-xs px-2 py-1 rounded text-white"
                                    >{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span
                                >
                                <span class="text-xs text-gray-500"
                                    >Завершено</span
                                >
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div
            class="swiper-pagination flex justify-center pt-5 px-0 !bottom-2 min-[501px]:hidden"
        ></div>
    </div>

    <!-- Модальные окна -->
    @include ('partials.modal.task.show')
    @include ('partials.modal.task.create-subtask')
    @include ('partials.modal.task.reject-task')
    @include ('partials.modal.task.submit-for-review')

    <!-- Модальное окно создания задачи -->
    @include ('partials.modal.task.create')

    <!-- Модальное окно файлового менеджера -->
    @include ('partials.modal.task.task-files-edit-modal')
    <!-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ ЗАДАЧИ  -->
    @include ('partials.modal.welcome.edit-task-modal')

    <!-- Модальное окно подтверждения архивации -->
    @include ('partials.modal.task.confirm-archive')

    <!-- Модальное окно подтверждения восстановления -->
    @include ('partials.modal.task.confirm-restore')

    <!-- Модальное окно подтверждения полного удаления -->
    @include ('partials.modal.task.confirm-force-delete')
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@dragdroptouch/drag-drop-touch@latest/dist/drag-drop-touch.esm.min.js"
        type="module"
    ></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            function closeTaskStorageManager() {
                console.log(
                    "welcome.blade.php: Закрытие модального окна хранилища файлов",
                );
                const modal = document.getElementById("fileManagerModal");
                if (modal) {
                    modal.classList.add("hidden");
                    document.body.classList.remove("overflow-hidden");
                }
            }
        });
        // ==================== ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ====================
            let currentEditTaskId = null;
            // !VSECURE
            async function openEditModal(taskId) {
                currentEditTaskId = taskId;
                taskEditSelectedFiles = [];

                try {
                    const response = await fetch(`/tasks/${taskId}/get`, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            Accept: "application/json",
                        },
                    });

                    const data = await response.json();

                    if (!data.success) {
                        showNotification("Ошибка при загрузке задачи", "error");
                        return;
                    }

                    const task = data.task;

                    const currentUserId = {{ auth()->id() }};
                    const isLeader = {{ auth()->user()->isLeader() ? 'true' : 'false' }};

                    if (task.author_id !== currentUserId && !isLeader) {
                        showNotification(
                            "Вы не можете редактировать эту задачу. Редактировать может только автор задачи или руководитель.",
                            "error",
                        );
                        return;
                    }

                    document.getElementById("editTaskId").value = task.id;
                    document.getElementById("editTaskName").value = task.name;
                    document.getElementById("editTaskDescription").value =
                        task.description || "";
                    document.getElementById("editTaskDepartment").value =
                        task.department_id || "";
                    document.getElementById("editTaskCategory").value =
                        task.category_id || "";
                    document.getElementById("editTaskPriority").value =
                        task.priority || "средний";
                    document.getElementById("editTaskStatus").value = task.status;
                    document.getElementById("editTaskDeadline").value = task.deadline
                        ? task.deadline.slice(0, 16)
                        : "";
                    document.getElementById("editTaskEstimatedHours").value =
                        task.estimated_hours || "";
                    document.getElementById("editTaskActualHours").value =
                        task.actual_hours || "";

                    // ===== Исполнитель =====
                    const form = document.getElementById("editTaskForm");
                    const userId = task.user_id ?? "";

                    let userField = form.querySelector('[name="user_id"]');

                    // если компонент не отдал поле с name="user_id", создаём скрытое
                    if (!userField) {
                        userField = document.createElement("input");
                        userField.type = "hidden";
                        userField.name = "user_id";
                        form.appendChild(userField);
                    }

                    // сброс состояния от прошлого открытия модалки
                    userField.disabled = false;
                    userField.style.pointerEvents = "";
                    userField.removeAttribute("tabindex");

                    if (userField.tagName === "SELECT") {
                        userField
                            .querySelectorAll("option[data-dynamic]")
                            .forEach((o) => o.remove());

                        // если исполнителя нет в списке $assignableUsers, добавляем опцию
                        if (
                            userId !== "" &&
                            !userField.querySelector(`option[value="${userId}"]`)
                        ) {
                            const opt = new Option(
                                task.user?.name ?? `Пользователь #${userId}`,
                                userId,
                            );
                            opt.dataset.dynamic = "1";
                            userField.appendChild(opt);
                        }
                    }

                    userField.value = userId;
                    userField.dispatchEvent(new Event("change", { bubbles: true }));

                    // у личной задачи исполнитель всегда автор: менять нельзя,
                    // но поле остаётся в форме и уходит в запросе
                    if (task.is_personal) {
                        userField.style.pointerEvents = "none";
                        userField.setAttribute("tabindex", "-1");
                    }
                    // =======================

                    taskEditSelectedFiles = task.files?.length ? task.files : [];
                    window.updateTaskEditSelectedFilesDisplay();

                    document.getElementById("editTaskModal").classList.remove("hidden");
                    document.body.classList.add("overflow-y-hidden");
                } catch (error) {
                    console.error("Ошибка:", error);
                    showNotification("Ошибка при загрузке задачи", "error");
                }
            }
        // ==================== ФУНКЦИИ ДЛЯ ЛИЧНЫХ ЗАДАЧ ====================

        // Обрабатываем кнопку "Назад" в браузере
        window.addEventListener("popstate", function (event) {
            const modal = document.getElementById("taskViewModal");

            if (modal && !modal.classList.contains("hidden")) {
                closeTaskViewModal("/home");
            }
        });

        // Закрытие по Escape
        document.addEventListener("keydown", function (e) {
            const modal = document.getElementById("taskViewModal");
            if (e.key === "Escape" && modal && !modal.classList.contains("hidden")) {
                closeTaskViewModal("/home");
            }
        });

        // Закрытие по клику на фон
        document.addEventListener("click", function (e) {
            const modal = document.getElementById("taskViewModal");
            if (e.target === modal) {
                closeTaskViewModal("/home");
            }
        });

        // При загрузке страницы проверяем URL и открываем модалку если нужно
        document.addEventListener("DOMContentLoaded", function () {
            const match = window.location.pathname.match(/\/tasks\/(\d+)/);

            if (match && !window.location.pathname.includes("/page/")) {
                const taskId = match[1];
                // Открываем модальное окно с задачей
                setTimeout(function () {
                    openTaskViewModal(taskId);
                }, 100);
            }
        });

        document.addEventListener("click", function (e) {
            if (e.target.id === "taskViewModal") closeTaskViewModal("/home");
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") closeTaskViewModal("/home");
        });

        // ==================== ИНИЦИАЛИЗАЦИЯ ====================
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("taskSearchInput");
            if (searchInput) {
                searchInput.addEventListener("input", applyFilters);
                searchInput.addEventListener("keyup", applyFilters);
            }

            document.addEventListener("click", function (event) {
                const dropdown = document.getElementById("filtersDropdown");
                const filterButton = event.target.closest(
                    '[onclick="toggleFiltersDropdown()"]',
                );
                const isInsideDropdown = dropdown && dropdown.contains(event.target);

                if (
                    dropdown &&
                    !dropdown.classList.contains("hidden") &&
                    !filterButton &&
                    !isInsideDropdown
                ) {
                    dropdown.classList.add("hidden");
                    const chevron = document.getElementById("filtersChevron");
                    if (chevron) chevron.style.transform = "rotate(0deg)";
                }
            });

            initDragAndDrop();

            const uploadNewFilesInput = document.getElementById("uploadNewFilesInput");
            if (uploadNewFilesInput) {
                uploadNewFilesInput.addEventListener("change", function (e) {
                    const files = Array.from(e.target.files);
                    const container = document.getElementById("uploadFilesContainer");
                    const listContainer = document.getElementById("uploadFilesList");

                    if (files.length > 0 && container) {
                        listContainer?.classList.remove("hidden");
                        let html = "";
                        files.forEach((file, index) => {
                            const fileIcon = getFileIcon(file.name.split(".").pop());
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
                        listContainer?.classList.add("hidden");
                        container.innerHTML = "";
                    }
                });
            }

            const editUploadNewFilesInput = document.getElementById(
                "editUploadNewFilesInput",
            );
            if (editUploadNewFilesInput) {
                editUploadNewFilesInput.addEventListener("change", function (e) {
                    const files = Array.from(e.target.files);
                    const container = document.getElementById(
                        "editUploadFilesContainer",
                    );
                    const listContainer = document.getElementById(
                        "editUploadFilesList",
                    );

                    if (files.length > 0 && container) {
                        listContainer?.classList.remove("hidden");
                        let html = "";
                        files.forEach((file, index) => {
                            const fileIcon = getFileIcon(file.name.split(".").pop());
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
                        listContainer?.classList.add("hidden");
                        container.innerHTML = "";
                    }
                });
            }

            const uploadArea = document.querySelector(".file-upload-area");
            if (uploadArea) {
                uploadArea.addEventListener("dragover", function (e) {
                    e.preventDefault();
                    this.classList.add("drag-over");
                });

                uploadArea.addEventListener("dragleave", function (e) {
                    this.classList.remove("drag-over");
                });

                uploadArea.addEventListener("drop", function (e) {
                    e.preventDefault();
                    this.classList.remove("drag-over");
                    const files = Array.from(e.dataTransfer.files);
                    const input = document.getElementById("uploadNewFilesInput");
                    if (input && files.length > 0) {
                        const dataTransfer = new DataTransfer();
                        files.forEach((file) => dataTransfer.items.add(file));
                        input.files = dataTransfer.files;
                        input.dispatchEvent(new Event("change"));
                    }
                });
            }
        });

        document.addEventListener("click", function (e) {
            if (e.target.id === "taskViewModal") closeTaskViewModal("/home");
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") closeTaskViewModal("/home");
        });
    </script>

@endsection

@push ('scripts')
    @vite ('resources/js/pages/welcome.page.js')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (window.DragDropTouch) {
                window.DragDropTouch._HOLD_DELAY = 150;
                console.log("Полифил успешно найден и настроен!", 1);
            } else {
                console.log("drag not found — полифил всё еще не загрузился.");
            }

            if (window.innerWidth > 500) return;

            const sliderElement = document.querySelector(".sw-v");

            if (sliderElement) {
                window.mySwiper = new Swiper(".sw-v", {
                    wrapperClass: "sw-v-wrapper",
                    slideClass: "board-column",
                    centeredSlides: true,
                    centeredSlidesBounds: true,
                    slidesPerView: "auto",
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
                        el: ".swiper-pagination",
                        clickable: true,
                        bulletClass:
                            "swiper-pagination-bullet bg-gray-400 opacity-50 mx-1 inline-block rounded-full w-2 h-2",
                        bulletActiveClass:
                            "!bg-[#22c55e] !opacity-100 w-4 rounded-lg transition-all duration-300",
                    },
                });
                console.log("Swiper успешно запущен для мобильного экрана!");
            }
        });
    </script>

    <script>
        // Закрытие модальных окон по клику на фон
        document.addEventListener("click", function (e) {
            if (e.target.id === "confirmArchiveModal")
                closeConfirmArchiveModal();
            if (e.target.id === "confirmRestoreModal")
                closeConfirmRestoreModal();
            if (e.target.id === "confirmForceDeleteModal")
                closeConfirmForceDeleteModal();
        });

        // Закрытие по Escape
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                closeConfirmArchiveModal();
                closeConfirmRestoreModal();
                closeConfirmForceDeleteModal();
            }
        });
    </script>
@endpush
