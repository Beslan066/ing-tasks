@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
        $company = auth()->check() ? auth()->user()->company : null;
        $viewMode = request()->get('view_mode', session('task_view_mode', 'list')); // 'list' or 'kanban'
    @endphp

    <!-- Страница статистики компании -->
    <div id="company-stats">
        <div class="flex flex-col md:flex-row justify-between items-start mb-6 md:mb-8 gap-4">
            <div>
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Задачи компании</h2>
                    <p class="text-white text-sm">Обзор производительности и задач</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Задачи компании</h2>
                    <p class="text-gray-700 text-sm">Обзор производительности и задач</p>
                @endif
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto items-start">
                @if(auth()->user()->isManager() || auth()->user()->isSupervisor())
                    @if($company && $company->license_type === 'basic')
                        @include('partials.subscription')
                    @endif
                @endif

                <!-- Переключатель режимов отображения -->
                    @if($backgroundEnabled && $backgroundImage)
                        <div class="flex rounded-lg overflow-hidden border-none ">
                            <button onclick="setViewMode('list')"
                                    id="viewModeListBtn"
                                    class="px-3 py-2 text-sm font-medium transition-all duration-200 flex items-center space-x-1
                                {{ $viewMode === 'list' ? 'bg-green-600 text-white' : 'bg-white text-gray-700' }}">
                                <i class="fas fa-list"></i>
                                <span>Список</span>
                            </button>
                            <button onclick="setViewMode('kanban')"
                                    id="viewModeKanbanBtn"
                                    class="px-3 py-2 text-sm font-medium transition-all duration-200 flex items-center space-x-1
                                {{ $viewMode === 'kanban' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 ' }}">
                                <i class="fas fa-columns"></i>
                                <span>Канбан</span>
                            </button>
                        </div>
                    @else
                        <div class="flex rounded-lg overflow-hidden border-none ">
                            <button onclick="setViewMode('list')"
                                    id="viewModeListBtn"
                                    class="px-3 py-2 text-sm font-medium transition-all duration-200 flex items-center space-x-1
                                {{ $viewMode === 'list' ? 'bg-green-600 text-white' : 'bg-white text-gray-700' }}">
                                <i class="fas fa-list"></i>
                                <span>Список</span>
                            </button>
                            <button onclick="setViewMode('kanban')"
                                    id="viewModeKanbanBtn"
                                    class="px-3 py-2 text-sm font-medium transition-all duration-200 flex items-center space-x-1
                                {{ $viewMode === 'kanban' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 ' }}">
                                <i class="fas fa-columns"></i>
                                <span>Канбан</span>
                            </button>
                        </div>
                   @endif


                @if($backgroundEnabled && $backgroundImage)
                    <button id="filterToggle" onclick="toggleFiltersDropdown()"
                            class="flex-1 md:flex-none bg-transparent/20 border-none text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 transition text-sm md:text-base">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform"></i>
                    </button>
                @else
                    <button id="filterToggle" onclick="toggleFiltersDropdown()"
                            class="flex-1 md:flex-none bg-white border border-gray-300 text-gray-700 px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition text-sm md:text-base">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform"></i>
                    </button>
                @endif

                <button id="newTaskBtn"
                        class="flex-1 md:flex-none bg-gradient-to-r from-green-600 to-green-500 text-white px-3
                        py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:from-green-700
                        hover:to-green-600 transition text-sm md:text-base" onclick="openTaskModal()">
                    <i class="fas fa-plus"></i>
                    <span>Новая задача</span>
                </button>
            </div>
        </div>

        <!-- Модальное окно улучшения до Премиум -->
        @include('partials.modal.buy-premium')

        <!-- Фильтры и поиск (список) -->
        @if($backgroundEnabled && $backgroundImage)
            <div id="filtersPanel" class="backdrop-blur-md bg-transparent/20 rounded-lg border-gray-200 hidden mb-[20px]">
                <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <form method="GET" action="{{ route('tasks.admin') }}" id="filterForm">
                        <input type="hidden" name="view_mode" id="filterFormViewMode" value="{{ $viewMode }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                            <!-- Поиск -->
                            <div class="sm:col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-white mb-1">Поиск</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="w-full border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none backdrop-blur-md bg-transparent/10"
                                       placeholder="Название или описание...">
                            </div>

                            <!-- Статус -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-1">Статус</label>
                                <select name="status"
                                        class="w-full border border-gray-800 rounded-lg appearance-none px-3 py-2 text-sm text-white focus:outline-none backdrop-blur-md bg-transparent/10">
                                    <option value="">Все статусы</option>
                                    @foreach($filterData['statuses'] as $status)
                                        <option class="text-gray-800"
                                                value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Исполнитель -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-1">Исполнитель</label>
                                <select name="user_id"
                                        class="w-full border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none backdrop-blur-md bg-transparent/10">
                                    <option value="">Все исполнители</option>
                                    @foreach($filterData['users'] as $userItem)
                                        <option class="text-gray-800"
                                                value="{{ $userItem->id }}" {{ request('user_id') == $userItem->id ? 'selected' : '' }}>
                                            {{ $userItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Отдел -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-1">Отдел</label>
                                <select name="department_id"
                                        class="w-full border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none backdrop-blur-md bg-transparent/10">
                                    <option value="">Все отделы</option>
                                    @foreach($filterData['departments'] as $department)
                                        <option class="text-gray-800"
                                                value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Приоритет -->
                            <div>
                                <label class="block text-sm font-medium text-white mb-1">Приоритет</label>
                                <select name="priority"
                                        class="w-full border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none backdrop-blur-md bg-transparent/10">
                                    <option value="">Все приоритеты</option>
                                    @foreach($filterData['priorities'] as $priority)
                                        <option class="text-gray-800"
                                                value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                            {{ $priority }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Категория -->
                            <div>
                                <label class="block text-sm text-white mb-1">Категория</label>
                                <select name="category_id"
                                        class="w-full border border-gray-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none backdrop-blur-md bg-transparent/10">
                                    <option value="">Все категории</option>
                                    @foreach($filterData['categories'] as $category)
                                        <option class="text-gray-800"
                                                value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Кнопки фильтра -->
                            <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                <button type="button" onclick="updateViewModeBeforeSubmit()"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm md:text-base">
                                    Применить фильтры
                                </button>
                                <button type="button" onclick="resetWithCurrentMode()"
                                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center text-sm md:text-base">
                                    Сбросить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div id="filtersPanel" class="bg-white rounded-lg border-gray-200 hidden mb-[20px]">
                <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <form method="GET" action="{{ route('tasks.admin') }}" id="filterFormList">
                        <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                            <!-- Поиск -->
                            <div class="sm:col-span-2 lg:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-green-600 bg-white"
                                       placeholder="Название или описание...">
                            </div>

                            <!-- Статус -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                                <select name="status"
                                        class="w-full border border-gray-300 rounded-lg appearance-none px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-green-600 bg-white">
                                    <option value="">Все статусы</option>
                                    @foreach($filterData['statuses'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Исполнитель -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Исполнитель</label>
                                <select name="user_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-green-600 bg-white">
                                    <option value="">Все исполнители</option>
                                    @foreach($filterData['users'] as $userItem)
                                        <option value="{{ $userItem->id }}" {{ request('user_id') == $userItem->id ? 'selected' : '' }}>
                                            {{ $userItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Отдел -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                                <select name="department_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-green-500 bg-white">
                                    <option value="">Все отделы</option>
                                    @foreach($filterData['departments'] as $department)
                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Приоритет -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label>
                                <select name="priority"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-green-600 bg-white">
                                    <option value="">Все приоритеты</option>
                                    @foreach($filterData['priorities'] as $priority)
                                        <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                            {{ $priority }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Категория -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                                <select name="category_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm md:text-base focus:outline-none focus:ring-1 focus:ring-green-600 bg-white">
                                    <option value="">Все категории</option>
                                    @foreach($filterData['categories'] as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Кнопки фильтра -->
                            <div class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                <button type="button" onclick="updateViewModeBeforeSubmit()"
                                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm md:text-base">
                                    Применить фильтры
                                </button>
                                <button type="button" onclick="resetWithCurrentMode()"
                                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center text-sm md:text-base">
                                    Сбросить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @include('partials.main.statistic-card')

        <!-- Режим отображения: Список -->
        <div id="listViewContainer" class="{{ $viewMode === 'list' ? '' : 'hidden' }}">
            @if($backgroundEnabled && $backgroundImage)
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                        <div class="text-gray-500 text-sm md:text-base">
                            Показано {{ $tasks->count() }} из {{ $tasks->total() }} задач
                        </div>

                        <div class="w-full sm:w-auto">
                            <div>
                                <a href="{{route('allTasks')}}" class="bg-transparent/20 border-none text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                                    <span>Все задачи</span>
                                    <span id="activeFiltersCount"
                                          class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden">0</span>
                                </a>
                            </div>
                            <select id="sortSelect"
                                    class="w-full sm:w-48 border-none rounded-lg px-3 py-2 text-white focus:outline-none backdrop-blur-md bg-transparent/20">
                                <option class="text-gray-800" value="created_at_desc">Новые сначала</option>
                                <option class="text-gray-800" value="created_at_asc">Старые сначала</option>
                                <option class="text-gray-800" value="deadline_asc">Ближайший дедлайн</option>
                                <option class="text-gray-800" value="deadline_desc">Дальний дедлайн</option>
                                <option class="text-gray-800" value="priority_desc">Высокий приоритет</option>
                                <option class="text-gray-800" value="name_asc">По названию (А-Я)</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto -mx-4 md:mx-0 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                        <div class="inline-block min-w-full align-middle max-[500px]:min-w-[unset] w-full">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full hidden md:table">
                                    <thead class="bg-transparent/20">
                                    <tr class="border-none">
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Задача</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Исполнитель</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Отдел</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Приоритет</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Автор</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дедлайн</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-transparent/10">
                                    @forelse($tasks as $task)
                                        <tr class="hover:bg-gray-50 transition text-white hover:text-gray-900 @if($task->trashed()) bg-red-50 border-l-4 border-red-400 @endif">
                                            <td class="px-3 py-4 cursor-pointer hover:text-gray-900">
                                                <div class="flex items-start">
                                                    <div class="ml-2 hover:text-gray-900">
                                                        <div class="text-sm font-medium flex items-center flex-wrap gap-1">
                                                            <a href="/team/tasks/{{ $task->id }}"
                                                               onclick="openTaskViewModal({{ $task->id }}); return false;">
                                                                <span class="truncate max-w-[250px]">{{ $task->name }}</span>
                                                            </a>
                                                            @if($task->trashed())
                                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full whitespace-nowrap">
                                                                    <i class="fas fa-trash mr-1"></i>Удалена
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex flex-wrap gap-1 mt-2">
                                                            @if($task->category)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[{{$task->category->color}}] text-white">
                                                                    {{ $task->category->name }}
                                                                </span>
                                                            @endif
                                                            @if($task->rejections_count > 0)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                                      title="Количество отказов: {{ $task->rejections_count }}">
                                                                    <i class="fas fa-user-slash mr-1"></i>
                                                                    {{ $task->rejections_count }}
                                                                </span>
                                                            @endif
                                                            @if($task->trashed() && $task->deletedBy)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                                      title="Удалил: {{ $task->deletedBy->name }}">
                                                                    <i class="fas fa-user-times mr-1"></i>
                                                                    Удалил: {{ $task->deletedBy->name }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 cursor-pointer whitespace-nowrap" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                @if($task->trashed())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Удалена</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $task->status === 'в работе' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $task->status === 'не назначена' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $task->status === 'просрочена' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $task->status === 'на проверке' ? 'bg-orange-100 text-orange-800' : '' }}">
                                                        {{ $task->status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 cursor-pointer" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                @if($task->user)
                                                    <div class="flex items-center">
                                                        <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                            {{ substr($task->user->name, 0, 2) }}
                                                        </div>
                                                        <div class="ml-2">
                                                            <div class="text-sm font-medium truncate max-w-[100px]">{{ $task->user->name }}</div>
                                                            <div class="text-xs text-gray-500 truncate max-w-[100px]">{{ $task->user->email }}</div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Не назначен</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 cursor-pointer whitespace-nowrap text-sm text-gray-500" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                {{ $task->department->name ?? ($task->is_personal ? 'Личная задача' : 'Без отдела') }}
                                            </td>
                                            <td class="px-3 py-4 cursor-pointer whitespace-nowrap" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
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
                                            </td>
                                            <td class="px-3 py-4 cursor-pointer whitespace-nowrap" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                @if($task->author)
                                                    <div class="text-sm font-medium truncate max-w-[100px]">{{ $task->author->name }}</div>
                                                @else
                                                    <span class="text-sm">Нет автора</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 cursor-pointer" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                @if($task->deadline && !$task->trashed())
                                                    <div class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }} text-sm">
                                                        {{ $task->deadline->format('d.m.Y H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">{{ $task->getTimeRemaining() }}</div>
                                                @else
                                                    <span class="text-gray-400 text-sm">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                @if($task->trashed())
                                                    <span class="text-gray-400 text-sm">Действия недоступны</span>
                                                @else
                                                    <div class="flex space-x-2 action-buttons">
                                                        <button onclick="openEditModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Редактировать">
                                                            <i class="fa-solid fa-file-pen"></i>
                                                        </button>
                                                        <button onclick="openCreateSubtaskModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Подзадача">
                                                            <i class="fa-solid fa-list"></i>
                                                        </button>
                                                        @if($task->status === 'на проверке')
                                                            <button onclick="returnToWork({{ $task->id }})" class="text-orange-600 hover:text-orange-900 p-1 text-sm" title="Вернуть на доработку">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        @endif
                                                        <!-- КНОПКА АРХИВАЦИИ -->
                                                        <button onclick="archiveTask({{ $task->id }})" class="text-yellow-600 hover:text-yellow-900 p-1" title="В архив">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                        @if($task->author_id === Auth::id())
                                                            <button onclick="openDeleteModal({{ $task->id }})" class="text-red-600 hover:text-red-900 p-1" title="Удалить">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        @else
                                                            <button class="text-gray-400 cursor-not-allowed p-1" title="Можно удалять только свои задачи">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Задачи не найдены</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                <!-- Мобильный вид таблицы (карточки) -->
                                <div class="md:hidden space-y-3 p-4">
                                    @forelse($tasks as $task)
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition @if($task->trashed()) border-l-4 border-l-red-400 bg-red-50 @endif"
                                             onclick="if(!event.target.closest('.action-buttons-mobile')) openTaskViewModal({{ $task->id }})">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center flex-wrap gap-2 mb-1">

                                                        <h3 class="font-semibold text-gray-900 truncate max-[450px]:!whitespace-normal max-[450px]:!overflow-visible max-[500px]:!text-wrap">{{ $task->name }}</h3>
                                                        @if($task->trashed())
                                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full"><i class="fas fa-trash mr-1"></i>Удалена</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-600 mb-2 line-clamp-2 max-[500px]:!hidden">{{ $task->description }}</div>
                                                </div>
                                                <div class="flex space-x-1 action-buttons-mobile">
                                                    @if(!$task->trashed())
                                                        <button onclick="openEditModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Редактировать">
                                                            <i class="fa-solid fa-file-pen"></i>
                                                        </button>
                                                        @if($task->author_id === Auth::id())
                                                            <button onclick="openDeleteModal({{ $task->id }})" class="text-red-600 hover:text-red-900 p-1" title="Удалить">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-6 max-[500px]:hidden">
                                                    <span class="text-sm text-gray-600">Статус:</span>
                                                    @if($task->trashed())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Удалена</span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : '' }}
                                                            {{ $task->status === 'в работе' ? 'bg-blue-100 text-blue-800' : '' }}
                                                            {{ $task->status === 'не назначена' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                            {{ $task->status === 'просрочена' ? 'bg-red-100 text-red-800' : '' }}
                                                            {{ $task->status === 'на проверке' ? 'bg-orange-100 text-orange-800' : '' }}">
                                                            {{ $task->status }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 max-[500px]:hidden">
                                                    <span class="text-sm text-gray-600">Исполнитель:</span>
                                                    @if($task->user)
                                                        <div class="flex items-center">
                                                            <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium mr-2">{{ substr($task->user->name, 0, 2) }}</div>
                                                            <span class="text-sm font-medium">{{ $task->user->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-500">Не назначен</span>
                                                    @endif
                                                </div>
                                                <div class="grid grid-cols-2 gap-2 max-[500px]:grid-cols-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm text-gray-600">Отдел:</span>
                                                        <span class="text-sm">{{ $task->department->name ?? '—' }}</span>
                                                    </div>
                                                    @php
                                                        $priorityStyles = [
                                                            'низкий' => ['level' => 1, 'color' => 'gray', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'filled' => 'bg-gray-500', 'empty' => 'bg-gray-200', 'text' => 'text-gray-700'],
                                                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                                                        ];
                                                        $style = $priorityStyles[$task->priority] ?? $priorityStyles['средний'];
                                                    @endphp
                                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $style['bg'] }} border {{ $style['border'] }}">
                                                        <div class="flex items-end gap-[3px] h-5">
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 1 ? $style['filled'] : $style['empty'] }} h-2"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 2 ? $style['filled'] : $style['empty'] }} h-3"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 3 ? $style['filled'] : $style['empty'] }} h-4"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 4 ? $style['filled'] : $style['empty'] }} h-5"></div>
                                                        </div>
                                                        <span class="text-xs font-medium {{ $style['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2 max-[500px]:grid-cols-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm text-gray-600">Автор:</span>
                                                        <span class="text-sm truncate">{{ $task->author->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm text-gray-600">Дедлайн:</span>
                                                        @if($task->deadline && !$task->trashed())
                                                            <div class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                                                <div class="text-sm">{{ $task->deadline->format('d.m.Y') }}</div>
                                                                <div class="text-xs text-gray-400">{{ $task->getTimeRemaining() }}</div>
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400 text-sm">—</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex flex-wrap gap-1 pt-2 border-t max-[500px]:!hidden">
                                                    @if($task->category)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $task->category->name }}</span>
                                                    @endif
                                                    @if($task->rejections_count > 0)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Количество отказов: {{ $task->rejections_count }}">
                                                            <i class="fas fa-user-slash mr-1"></i>{{ $task->rejections_count }}
                                                        </span>
                                                    @endif
                                                    @if($task->trashed() && $task->deletedBy)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800" title="Удалил: {{ $task->deletedBy->name }}">
                                                            <i class="fas fa-user-times mr-1"></i>Удалил: {{ $task->deletedBy->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(!$task->trashed() && $task->status === 'на проверке')
                                                    <div class="pt-2 border-t">
                                                        <button onclick="returnToWork({{ $task->id }})" class="w-full bg-orange-100 text-orange-800 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-200 transition flex items-center justify-center space-x-2">
                                                            <i class="fas fa-redo"></i><span>Вернуть на доработку</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                                            <i class="fas fa-tasks text-gray-300 text-4xl mb-3"></i>
                                            <p class="text-gray-500">Задачи не найдены</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($tasks->hasPages())
                    <div class="mt-4 md:mt-6">{{ $tasks->links('vendor.pagination.tailwind') }}</div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                        <div class="text-gray-500 text-sm md:text-base">Показано {{ $tasks->count() }} из {{ $tasks->total() }} задач</div>
                        <div class="w-full sm:w-auto">
                            <select id="sortSelect" class="w-full sm:w-48 border border-gray-200 rounded-lg pr-1 px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base">
                                <option value="created_at_desc">Новые сначала</option>
                                <option value="created_at_asc">Старые сначала</option>
                                <option value="deadline_asc">Ближайший дедлайн</option>
                                <option value="deadline_desc">Дальний дедлайн</option>
                                <option value="priority_desc">Высокий приоритет</option>
                                <option value="name_asc">По названию (А-Я)</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto -mx-4 md:mx-0 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                        <div class="inline-block min-w-full align-middle max-[500px]:min-w-0 max-[500px]:w-full">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <div class="hidden md:block">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Задача</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Исполнитель</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Отдел</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Приоритет</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Автор</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дедлайн</th>
                                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($tasks as $task)
                                            <tr class="hover:bg-gray-50 transition @if($task->trashed()) bg-red-50 border-l-4 border-red-400 @endif">
                                                <td class="px-3 py-4">
                                                    <div class="flex items-start cursor-pointer" onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                        <div class="ml-2">
                                                            <div class="text-sm font-medium text-gray-900 flex items-center flex-wrap gap-1">
                                                                <span class="truncate max-w-[150px]">{{ $task->name }}</span>
                                                                @if($task->trashed())<span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full whitespace-nowrap"><i class="fas fa-trash mr-1"></i>Удалена</span>@endif
                                                            </div>
                                                            <div class="text-xs text-gray-500 truncate max-w-[200px] mt-1">{{ $task->description }}</div>
                                                            <div class="flex flex-wrap gap-1 mt-2">
                                                                @if($task->category)<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[{{$task->category->color}}] text-white">{{ $task->category->name }}</span>@endif
                                                                @if($task->rejections_count > 0)<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Количество отказов: {{ $task->rejections_count }}"><i class="fas fa-user-slash mr-1"></i>{{ $task->rejections_count }}</span>@endif
                                                                @if($task->trashed() && $task->deletedBy)<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800" title="Удалил: {{ $task->deletedBy->name }}"><i class="fas fa-user-times mr-1"></i>Удалил: {{ $task->deletedBy->name }}</span>@endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    @if($task->trashed())
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Удалена</span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : '' }}
                                                            {{ $task->status === 'в работе' ? 'bg-blue-100 text-blue-800' : '' }}
                                                            {{ $task->status === 'не назначена' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                            {{ $task->status === 'просрочена' ? 'bg-red-100 text-red-800' : '' }}
                                                            {{ $task->status === 'на проверке' ? 'bg-orange-100 text-orange-800' : '' }}">
                                                            {{ $task->status }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-4">
                                                    @if($task->user)
                                                        <div class="flex items-center">
                                                            <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium">{{ substr($task->user->name, 0, 2) }}</div>
                                                            <div class="ml-2"><div class="text-sm font-medium text-gray-900 truncate max-w-[100px]">{{ $task->user->name }}</div><div class="text-xs text-gray-500 truncate max-w-[100px]">{{ $task->user->email }}</div></div>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-500">Не назначен</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->department->name ?? ($task->is_personal ? 'Личная задача' : 'Без отдела') }}</td>
                                                <td class="px-3 py-4 whitespace-nowrap">

                                                    @if(!$task->trashed())
                                                    <!-- <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $task->priority }}</span> -->

                                                       @php
                                                        $priorityStyles = [
                                                            'низкий' => ['level' => 1, 'color' => 'gray', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'filled' => 'bg-gray-500', 'empty' => 'bg-gray-200', 'text' => 'text-gray-700'],
                                                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                                                        ];
                                                        $style = $priorityStyles[$task->priority] ?? $priorityStyles['средний'];
                                                    @endphp

                                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $style['bg'] }} border {{ $style['border'] }}">
                                                        <div class="flex items-end gap-[3px] h-5">
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 1 ? $style['filled'] : $style['empty'] }} h-2"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 2 ? $style['filled'] : $style['empty'] }} h-3"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 3 ? $style['filled'] : $style['empty'] }} h-4"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 4 ? $style['filled'] : $style['empty'] }} h-5"></div>
                                                        </div>
                                                        <span class="text-xs font-medium {{ $style['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                    </div>
                                                    @else
                                                    <span class="text-sm text-gray-400">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">@if($task->author)<div class="text-sm font-medium text-gray-900 truncate max-w-[100px]">{{ $task->author->name }}</div>@else<span class="text-sm text-gray-500">Нет автора</span>@endif</td>
                                                <td class="px-3 py-4">@if($task->deadline && !$task->trashed())<div class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }} text-sm">{{ $task->deadline->format('d.m.Y H:i') }}</div><div class="text-xs text-gray-400">{{ $task->getTimeRemaining() }}</div>@else<span class="text-gray-400 text-sm">—</span>@endif</td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    @if($task->trashed())<span class="text-gray-400 text-sm">Действия недоступны</span>
                                                    @else
                                                        <div class="flex space-x-2 action-buttons">
                                                            <button onclick="openEditModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Редактировать"><i class="fa-solid fa-file-pen"></i></button>
                                                            <button onclick="openCreateSubtaskModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Подзадача"><i class="fa-solid fa-list"></i></button>
                                                            @if($task->status === 'на проверке')<button onclick="returnToWork({{ $task->id }})" class="text-orange-600 hover:text-orange-900 p-1 text-sm" title="Вернуть на доработку"><i class="fas fa-redo"></i></button>@endif
                                                            @if($task->author_id === Auth::id())<button onclick="openDeleteModal({{ $task->id }})" class="text-red-600 hover:text-red-900 p-1" title="Удалить"><i class="fa-solid fa-trash"></i></button>@else<button class="text-gray-400 cursor-not-allowed p-1" title="Можно удалять только свои задачи"><i class="fa-solid fa-trash"></i></button>@endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Задачи не найдены</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="md:hidden space-y-3 p-4">
                                    @forelse($tasks as $task)
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm @if($task->trashed()) border-l-4 border-l-red-400 bg-red-50 @endif">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center flex-wrap gap-2 mb-1"><h3 class="font-semibold text-gray-900 truncate max-[450px]:!whitespace-normal max-[450px]:!overflow-visible max-[500px]:!text-wrap">{{ $task->name }}</h3>@if($task->trashed())<span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full"><i class="fas fa-trash mr-1"></i>Удалена</span>@endif</div>
                                                    <div class="text-sm text-gray-600 mb-2 line-clamp-2 max-[500px]:hidden">{{ $task->description }}</div>
                                                </div>
                                                <div class="flex space-x-1">
                                                    @if(!$task->trashed())
                                                        <button onclick="openEditModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Редактировать"><i class="fa-solid fa-file-pen"></i></button>
                                                        <button onclick="openCreateSubtaskModal({{ $task->id }})" class="text-yellow-700 hover:text-yellow-900 p-1" title="Подзадача"><i class="fa-solid fa-list"></i></button>
                                                        @if($task->author_id === Auth::id())<button onclick="openDeleteModal({{ $task->id }})" class="text-red-600 hover:text-red-900 p-1" title="Удалить"><i class="fa-solid fa-trash"></i></button>@endif
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="flex items-center max-[500px]:hidden"><span class="text-sm text-gray-600 w-20">Статус:</span>@if($task->trashed())<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Удалена</span>@else<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : '' }} {{ $task->status === 'в работе' ? 'bg-blue-100 text-blue-800' : '' }} {{ $task->status === 'не назначена' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $task->status === 'просрочена' ? 'bg-red-100 text-red-800' : '' }} {{ $task->status === 'на проверке' ? 'bg-orange-100 text-orange-800' : '' }}">{{ $task->status }}</span>@endif</div>
                                                <div class="flex items-center max-[500px]:gap-1 max-[500px]:hidden"><span class="text-sm text-gray-600 w-20 max-[500px]:w-auto">Исполнитель:</span>@if($task->user)<div class="flex items-center"><div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium mr-2">{{ substr($task->user->name, 0, 2) }}</div><span class="text-sm font-medium">{{ $task->user->name }}</span></div>@else<span class="text-sm text-gray-500">Не назначен</span>@endif</div>
                                                <div class="grid grid-cols-2 gap-2 max-[450px]:grid-cols-1">
                                                    <div class="flex items-center">
                                                        <span class="text-sm text-gray-600 w-16">Отдел:</span>
                                                        <span class="text-sm">{{ $task->department->name ?? ($task->is_personal ? 'Личная задача' : '—') }}</span>
                                                    </div>
                                                    <!-- <div class="flex items-center max-[500px]:gap-1">
                                                        <span class="text-sm text-gray-600 w-16 max-[500px]:w-auto">Приоритет:</span>
                                                        @if(!$task->trashed())<span class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">{{ $task->priority }}</span>
                                                        @else<span class="text-sm text-gray-400">—</span>@endif</div> -->
                                                         @php
                                                        $priorityStyles = [
                                                            'низкий' => ['level' => 1, 'color' => 'gray', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'filled' => 'bg-gray-500', 'empty' => 'bg-gray-200', 'text' => 'text-gray-700'],
                                                            'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                                            'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                                            'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                                                        ];
                                                        $style = $priorityStyles[$task->priority] ?? $priorityStyles['средний'];
                                                    @endphp
                                                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $style['bg'] }} border {{ $style['border'] }}">
                                                        <div class="flex items-end gap-[3px] h-5">
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 1 ? $style['filled'] : $style['empty'] }} h-2"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 2 ? $style['filled'] : $style['empty'] }} h-3"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 3 ? $style['filled'] : $style['empty'] }} h-4"></div>
                                                            <div class="w-1.5 rounded-sm {{ $style['level'] >= 4 ? $style['filled'] : $style['empty'] }} h-5"></div>
                                                        </div>
                                                        <span class="text-xs font-medium {{ $style['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                    </div>
                                                    </div>
                                                <div class="grid grid-cols-2 gap-2 max-[450px]:grid-cols-1"><div class="flex items-center"><span class="text-sm text-gray-600 w-16">Автор:</span><span class="text-sm truncate">{{ $task->author->name ?? '—' }}</span></div><div class="flex items-center max-[500px]:gap-2"><span class="text-sm text-gray-600 w-16 max-[500px]:w-auto">Дедлайн:</span>@if($task->deadline && !$task->trashed())<div class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}"><div class="text-sm">{{ $task->deadline->format('d.m.Y') }}</div><div class="text-xs text-gray-400">{{ $task->getTimeRemaining() }}</div></div>@else<span class="text-gray-400 text-sm">—</span>@endif</div></div>
                                                <div class="flex flex-wrap gap-1 pt-2 border-t max-[500px]:!hidden">@if($task->category)<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[{{$task->category->color}}] text-white">{{ $task->category->name }}</span>@endif@if($task->rejections_count > 0)<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Количество отказов: {{ $task->rejections_count }}"><i class="fas fa-user-slash mr-1"></i>{{ $task->rejections_count }}</span>@endif@if($task->trashed() && $task->deletedBy)<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800" title="Удалил: {{ $task->deletedBy->name }}"><i class="fas fa-user-times mr-1"></i>Удалил: {{ $task->deletedBy->name }}</span>@endif</div>
                                                @if(!$task->trashed() && $task->status === 'на проверке')<div class="pt-2 border-t"><button onclick="returnToWork({{ $task->id }})" class="w-full bg-orange-100 text-orange-800 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-200 transition flex items-center justify-center space-x-2"><i class="fas fa-redo"></i><span>Вернуть на доработку</span></button></div>@endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="bg-white border border-gray-200 rounded-lg p-8 text-center"><i class="fas fa-tasks text-gray-300 text-4xl mb-3"></i><p class="text-gray-500">Задачи не найдены</p></div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($tasks->hasPages())<div class="mt-4 md:mt-6">{{ $tasks->links('vendor.pagination.tailwind') }}</div>@endif
                </div>
            @endif
        </div>

        <!-- Режим отображения: Канбан -->
        <div id="kanbanViewContainer" class="{{ $viewMode === 'kanban' ? '' : 'hidden' }}">
            @php
                // Группируем задачи по статусам для канбана
                $statusMap = [
                    'просрочена' => 'Просроченные',
                    'назначена' => 'Новые',
                    'в работе' => 'В работе',
                    'на проверке' => 'На проверке',
                    'выполнена' => 'Завершено'
                ];
                $statusKeys = ['просрочена', 'назначена', 'в работе', 'на проверке', 'выполнена'];
                $statusDataValues = [
                    'просрочена' => 'overdue',
                    'назначена' => 'new',
                    'в работе' => 'in-progress',
                    'на проверке' => 'review',
                    'выполнена' => 'done'
                ];

                // Получаем просроченные задачи
                $overdueTasks = $tasks->filter(function($task) {
                    return $task->status === 'просрочена';
                });

                $tasksByStatusForKanban = [
                    'просрочена' => $overdueTasks,
                    'назначена' => $tasks->where('status', 'назначена'),
                    'в работе' => $tasks->where('status', 'в работе'),
                    'на проверке' => $tasks->where('status', 'на проверке'),
                    'выполнена' => $tasks->where('status', 'выполнена')
                ];

                // Массив стилей для приоритетов
                $prioritySignals = [
                    'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                    'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                    'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                    'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                ];
            @endphp

            @if($backgroundEnabled && $backgroundImage)
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                        <div class="text-gray-500 text-sm md:text-base">
                            Всего задач: {{ $tasks->total() }}
                        </div>
                        <div class="w-full sm:w-auto flex items-center">
                            <div class="mr-2">
                                <a href="{{route('allTasks')}}" class="bg-transparent/20 border-none text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition text-md">
                                    <span>Все задачи</span>
                                    <span id="activeFiltersCount"
                                          class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden">0</span>
                                </a>
                            </div>
                            <select id="sortSelectKanban"
                                    class="w-full sm:w-48 border-none rounded-lg px-3 py-2 text-white focus:outline-none backdrop-blur-md bg-transparent/20">
                                <option class="text-gray-800" value="created_at_desc">Новые сначала</option>
                                <option class="text-gray-800" value="created_at_asc">Старые сначала</option>
                                <option class="text-gray-800" value="deadline_asc">Ближайший дедлайн</option>
                                <option class="text-gray-800" value="priority_desc">Высокий приоритет</option>
                                <option class="text-gray-800" value="name_asc">По названию (А-Я)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Канбан доска -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        @foreach($statusKeys as $statusKey)
                            <div class="rounded-lg p-3 board-column {{ $backgroundEnabled && $backgroundImage ? 'bg-transparent' : 'bg-gray-50' }}"
                                 data-status="{{ $statusDataValues[$statusKey] }}"
                                 ondragover="dragOver.call(this, event)"
                                 ondragleave="dragLeave.call(this, event)"
                                 ondrop="drop.call(this, event)">

                                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 canban-col-title backdrop-blur-md bg-transparent/20">
                                    <h3 class="font-semibold text-white text-sm">{{ $statusMap[$statusKey] }}</h3>
                                    <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $tasksByStatusForKanban[$statusKey]->count() }}</span>
                                </div>

                                <div class="space-y-3 task-container">
                                    @forelse($tasksByStatusForKanban[$statusKey] as $task)
                                        @php
                                            $prioritySignal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                                        @endphp

                                        {{-- Карточка задачи --}}
                                        <div class="task-card bg-white p-3 rounded-lg shadow cursor-move flex flex-col justify-between {{ $statusKey === 'просрочена' ? 'border-l-4 border-red-500 bg-red-50' : '' }}"
                                             draggable="true"
                                             data-task="{{ $task->id }}"
                                             data-priority="{{ $task->priority ?? 'medium' }}"
                                             data-deadline="{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '' }}"
                                             data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                                             data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                                             data-author-id="{{ $task->author_id }}"
                                             ondragstart="dragStart.call(this, event)"
                                             ondragend="dragEnd.call(this, event)">

                                            <div class="flex justify-between items-start mb-2">
                                                <a href="/team/tasks/{{ $task->id }}"
                                                   onclick="openTaskViewModal({{ $task->id }}); return false;"
                                                   class="flex-1 mr-2">
                                                    <h4 class="font-medium text-sm cursor-pointer hover:text-blue-600 line-clamp-2 {{ $statusKey === 'просрочена' ? 'text-red-600' : '' }}">{{ $task->name }}</h4>
                                                </a>
                                                <div class="flex items-center space-x-1 flex-shrink-0">
                                                    <div class="relative">
                                                        <button onclick="toggleTaskMenu(event, {{ $task->id }})" class="text-gray-500 hover:text-gray-700 p-1" title="Действия">
                                                            <i class="fas fa-ellipsis-v text-xs"></i>
                                                        </button>
                                                        <div id="taskMenu-{{ $task->id }}" class="task-menu hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl border z-50">
                                                            <div class="py-1">
                                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                                    <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-edit mr-2 text-blue-500 text-xs"></i> Редактировать
                                                                    </button>
                                                                @endif
                                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                                        <button onclick="openCreateSubtaskModal({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                            <i class="fas fa-list mr-2 text-blue-500 text-xs"></i> Подзадача
                                                                        </button>
                                                                @endif
                                                                    <!-- КНОПКА АРХИВАЦИИ -->
                                                                    @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                                        <button onclick="archiveTask({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                            <i class="fas fa-archive mr-2 text-yellow-500 text-xs"></i> В архив
                                                                        </button>
                                                                    @endif
                                                                @if($statusKey === 'назначена')
                                                                    <button onclick="startTask({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-play mr-2 text-green-500 text-xs"></i> Начать
                                                                    </button>
                                                                @endif
                                                                @if($statusKey === 'в работе')
                                                                    <button onclick="sendForReview({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-check-circle mr-2 text-green-500 text-xs"></i> На проверку
                                                                    </button>
                                                                @endif

                                                                @if($statusKey === 'на проверке' && auth()->user()->isLeader())
                                                                    <button onclick="approveTask({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-check-double mr-2 text-green-500 text-xs"></i> Одобрить
                                                                    </button>
                                                                    <button onclick="returnTaskToWork({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-redo mr-2 text-orange-500 text-xs"></i> На доработку
                                                                    </button>
                                                                @endif
                                                                @if($statusKey !== 'выполнена' && $statusKey !== 'просрочена')
                                                                    <button onclick="showRejectModal({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-times-circle mr-2 text-xs"></i> Отказаться
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($task->files_count > 0)
                                                <div class="mb-2 flex items-center text-xs text-gray-500">
                                                    <i class="fas fa-paperclip mr-1 text-xs"></i>
                                                    <span>{{ $task->files_count }}</span>
                                                </div>
                                            @endif

                                            @if($task->deadline)
                                                <div class="mb-2">
                                                    <div class="flex items-center text-xs {{ $statusKey === 'просрочена' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                                        <i class="fas fa-clock mr-1 text-xs"></i>
                                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                                        @if($statusKey === 'просрочена')
                                                            <span class="ml-1">(Просрочено)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                                                <div class="flex flex-wrap gap-1">
                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                      class="text-xs px-1.5 py-0.5 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>

                                                    {{-- Индикатор приоритета --}}
                                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md {{ $prioritySignal['bg'] }} border {{ $prioritySignal['border'] }}">
                                                        <div class="flex items-end gap-[2px] h-3">
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 1 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-1"></div>
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 2 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-1.5"></div>
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 3 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-2"></div>
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 4 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-2.5"></div>
                                                        </div>
                                                        <span class="text-xs font-medium {{ $prioritySignal['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                    </div>

                                                    @if($task->category)
                                                        <span class="bg-green-100 text-green-800 text-xs px-1.5 py-0.5 rounded">{{ mb_substr($task->category->name, 0, 10) }}</span>
                                                    @endif
                                                </div>

                                                {{-- Иконка ИСПОЛНИТЕЛЯ (используем те же методы, что в модальном окне) --}}
                                                <div class="flex items-center space-x-1">
                                                    <div class="w-5 h-5 rounded-full {{ $task->user ? $task->user->getAvatarColor() : 'bg-gray-300' }} flex items-center justify-center text-white text-[10px] font-medium"
                                                         title="{{ $task->user?->name ?? 'Не назначен' }}">
                                                        {{ $task->user ? $task->user->getInitials() : '?' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        {{-- Пустая колонка - ничего не выводим --}}
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                        <div class="text-gray-500 text-sm md:text-base">
                            Всего задач: {{ $tasks->total() }}
                        </div>

                        <div class="w-full sm:w-auto flex items-center">
                            <div class="mr-2">
                                <a href="{{route('allTasks')}}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition text-sm">
                                    <span>Все задачи</span>
                                    <span id="activeFiltersCount"
                                          class="bg-green-100 text-green-700 text-xs px-1.5 py-0.5 rounded-full ml-1 hidden">0</span>
                                </a>
                            </div>
                            <select id="sortSelectKanban"
                                    class="w-full sm:w-48 border-none rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base">
                                <option value="created_at_desc">Новые сначала</option>
                                <option value="created_at_asc">Старые сначала</option>
                                <option value="deadline_asc">Ближайший дедлайн</option>
                                <option value="priority_desc">Высокий приоритет</option>
                                <option value="name_asc">По названию (А-Я)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Канбан доска -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        @foreach($statusKeys as $statusKey)
                            <div class="rounded-lg p-3 board-column {{ $backgroundEnabled && $backgroundImage ? 'bg-transparent' : 'bg-gray-50' }}"
                                 data-status="{{ $statusDataValues[$statusKey] }}"
                                 ondragover="dragOver.call(this, event)"
                                 ondragleave="dragLeave.call(this, event)"
                                 ondrop="drop.call(this, event)">

                                {{-- Заголовок колонки с красным фоном для просроченных --}}
                                <div class="flex justify-between items-center mb-4 border-none rounded-lg p-2 {{ $statusKey === 'просрочена' ? 'bg-red-600' : '' }}" style=" background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);">
                                    <h3 class="font-semibold text-white text-sm">{{ $statusMap[$statusKey] }}</h3>
                                    <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded stat-count">{{ $tasksByStatusForKanban[$statusKey]->count() }}</span>
                                </div>

                                <div class="space-y-3 task-container">
                                    @forelse($tasksByStatusForKanban[$statusKey] as $task)
                                        @php
                                            $prioritySignal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                                        @endphp

                                        {{-- Карточка задачи --}}
                                        <div class="task-card bg-white p-3 rounded-lg shadow cursor-move flex flex-col justify-between {{ $statusKey === 'просрочена' ? 'border-l-4 border-red-500 bg-red-50' : '' }}"
                                             draggable="true"
                                             data-task="{{ $task->id }}"
                                             data-priority="{{ $task->priority ?? 'medium' }}"
                                             data-deadline="{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '' }}"
                                             data-has-files="{{ $task->files_count > 0 ? 'true' : 'false' }}"
                                             data-task-name="{{ mb_strtolower($task->name, 'UTF-8') }}"
                                             data-author-id="{{ $task->author_id }}"
                                             ondragstart="dragStart.call(this, event)"
                                             ondragend="dragEnd.call(this, event)">

                                            <div class="flex justify-between items-start mb-2">
                                                <a href="/team/tasks/{{ $task->id }}"
                                                   onclick="openTaskViewModal({{ $task->id }}); return false;"
                                                   class="flex-1 mr-2">
                                                    <h4 class="font-medium text-sm cursor-pointer hover:text-blue-600 line-clamp-2 {{ $statusKey === 'просрочена' ? 'text-red-600' : '' }}">{{ $task->name }}</h4>
                                                </a>
                                                <div class="flex items-center space-x-1 flex-shrink-0">
                                                    <div class="relative">
                                                        <button onclick="toggleTaskMenu(event, {{ $task->id }})" class="text-gray-500 hover:text-gray-700 p-1" title="Действия">
                                                            <i class="fas fa-ellipsis-v text-xs"></i>
                                                        </button>
                                                        <div id="taskMenu-{{ $task->id }}" class="task-menu hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl border z-50">
                                                            <div class="py-1">
                                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                                    <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-edit mr-2 text-blue-500 text-xs"></i> Редактировать
                                                                    </button>
                                                                @endif

                                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                                    <button onclick="openCreateSubtaskModal({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-list mr-2 text-blue-500 text-xs"></i> Подзадача
                                                                    </button>
                                                                @endif

                                                                <!-- КНОПКА АРХИВАЦИИ -->
                                                                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                                                                    <button onclick="archiveTask({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-archive mr-2 text-yellow-500 text-xs"></i> В архив
                                                                    </button>
                                                                @endif

                                                                @if($statusKey === 'назначена')
                                                                    <button onclick="startTask({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-play mr-2 text-green-500 text-xs"></i> Начать
                                                                    </button>
                                                                @endif
                                                                @if($statusKey === 'в работе')
                                                                    <button onclick="sendForReview({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-check-circle mr-2 text-green-500 text-xs"></i> На проверку
                                                                    </button>
                                                                @endif
                                                                @if($statusKey === 'на проверке' && auth()->user()->isLeader())
                                                                    <button onclick="approveTask({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-check-double mr-2 text-green-500 text-xs"></i> Одобрить
                                                                    </button>
                                                                    <button onclick="returnTaskToWork({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-redo mr-2 text-orange-500 text-xs"></i> На доработку
                                                                    </button>
                                                                @endif
                                                                @if($statusKey !== 'выполнена' && $statusKey !== 'просрочена')
                                                                    <button onclick="showRejectModal({{ $task->id }})" class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-gray-100 flex items-center">
                                                                        <i class="fas fa-times-circle mr-2 text-xs"></i> Отказаться
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($task->files_count > 0)
                                                <div class="mb-2 flex items-center text-xs text-gray-500">
                                                    <i class="fas fa-paperclip mr-1 text-xs"></i>
                                                    <span>{{ $task->files_count }}</span>
                                                </div>
                                            @endif

                                            @if($task->deadline)
                                                <div class="mb-2">
                                                    <div class="flex items-center text-xs {{ $statusKey === 'просрочена' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                                        <i class="fas fa-clock mr-1 text-xs"></i>
                                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d.m.Y H:i') }}
                                                        @if($statusKey === 'просрочена')
                                                            <span class="ml-1">(Просрочено)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                                                <div class="flex flex-wrap gap-1">
                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);"
                      class="text-xs px-1.5 py-0.5 rounded text-white">{{ $task->department->name ?? ($task->is_personal ? 'Личная' : 'Без отдела') }}</span>

                                                    {{-- Индикатор приоритета --}}
                                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md {{ $prioritySignal['bg'] }} border {{ $prioritySignal['border'] }}">
                                                        <div class="flex items-end gap-[2px] h-3">
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 1 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-1"></div>
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 2 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-1.5"></div>
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 3 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-2"></div>
                                                            <div class="w-1 rounded-sm {{ $prioritySignal['level'] >= 4 ? $prioritySignal['filled'] : $prioritySignal['empty'] }} h-2.5"></div>
                                                        </div>
                                                        <span class="text-xs font-medium {{ $prioritySignal['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                    </div>

                                                    @if($task->category)
                                                        <span class="bg-green-100 text-green-800 text-xs px-1.5 py-0.5 rounded">{{ mb_substr($task->category->name, 0, 10) }}</span>
                                                    @endif
                                                </div>

                                                {{-- Иконка ИСПОЛНИТЕЛЯ (используем те же методы, что в модальном окне) --}}
                                                <div class="flex items-center space-x-1">
                                                    <div class="w-5 h-5 rounded-full {{ $task->user ? $task->user->getAvatarColor() : 'bg-gray-300' }} flex items-center justify-center text-white text-[10px] font-medium"
                                                         title="{{ $task->user?->name ?? 'Не назначен' }}">
                                                        {{ $task->user ? $task->user->getInitials() : '?' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        {{-- Пустая колонка - ничего не выводим --}}
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Модальное окно подтверждения архивации -->
    <div id="confirmArchiveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
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
                    <button onclick="closeConfirmArchiveModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmArchive()" class="flex-1 px-4 py-2.5 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition font-medium">
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
                    Задача будет восстановлена и появится на доске. Продолжить?
                </p>
                <div class="flex space-x-3">
                    <button onclick="closeConfirmRestoreModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmRestore()" class="flex-1 px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition font-medium">
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
                    <button onclick="closeConfirmForceDeleteModal()" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmForceDelete()" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-medium">
                        <i class="fas fa-trash-alt mr-2"></i>Удалить навсегда
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования задачи -->
    @include('partials.modal.task.edit-modal')

    <!-- Модальное окно возврата на доработку -->
    @include('partials.modal.task.return-to-work')

    <!-- Модальное окно удаления задачи -->
    @include('partials.modal.task.delete-modal')

    @include('partials.modal.task.show')

    <!-- Модальное окно файлового менеджера для редактирования -->
    <div id="fileManagerModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
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
                    <button onclick="closeFileManager()"
                            class="text-gray-400 hover:text-gray-600 p-2 rounded-xl hover:bg-gray-100 transition-all duration-200">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                   id="fileManagerSearch"
                                   placeholder="Поиск по названию файла..."
                                   class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 transition-all duration-200 bg-white">
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <select id="fileManagerTypeFilter"
                                class="px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 bg-white text-sm hover:border-gray-300 transition-all duration-200">
                            <option value="">Все типы</option>
                            <option value="image">Изображения</option>
                            <option value="document">Документы</option>
                            <option value="video">Видео</option>
                            <option value="audio">Аудио</option>
                            <option value="archive">Архивы</option>
                        </select>
                        <select id="fileManagerSortBy"
                                class="px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100 bg-white text-sm hover:border-gray-300 transition-all duration-200">
                            <option value="newest">Сначала новые</option>
                            <option value="oldest">Сначала старые</option>
                            <option value="name_asc">По имени (А-Я)</option>
                            <option value="name_desc">По имени (Я-А)</option>
                            <option value="size_asc">По размеру (↑)</option>
                            <option value="size_desc">По размеру (↓)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-hidden">
                <div class="h-full flex">
                    <div class="flex-1 overflow-y-auto p-4" id="fileManagerContent">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            <div class="col-span-full text-center py-12">
                                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600">Загрузка файлов...</p>
                            </div>
                        </div>
                    </div>

                    <div id="fileManagerPreviewPanel"
                         class="hidden w-96 border-l border-gray-200 bg-gray-50 p-4 overflow-y-auto">
                        <div class="sticky top-0 bg-gray-50 pb-4">
                            <button onclick="closeFilePreview()"
                                    class="mb-4 text-gray-400 hover:text-gray-600 flex items-center transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Назад
                            </button>
                            <div id="filePreviewContent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-white">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-hdd mr-1"></i>
                        Файловое хранилище
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeFileManager()"
                                class="px-5 py-2.5 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-300 font-medium transition-all duration-200">
                            Отмена
                        </button>
                        <button type="button" id="confirmFileSelectionBtn" onclick="confirmEditFileSelectionForEdit()"
                                class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-check mr-2"></i>Выбрать (<span id="confirmCount">0</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modal.task.create')

    <!-- Модальное окно отказа от задачи -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Отказ от задачи</h3>
            <p class="text-gray-600 mb-4">Пожалуйста, укажите причину отказа от задачи:</p>
            <textarea id="rejectReason" placeholder="Причина отказа..." class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 h-24 resize-none"></textarea>
            <div class="flex space-x-3">
                <button onclick="submitRejection()" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">Подтвердить отказ</button>
                <button onclick="closeRejectModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">Отмена</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для указания времени при отправке на проверку -->
    <div id="timeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Отправка на проверку</h3>
            <p class="text-gray-600 mb-4">Укажите фактическое время работы над задачей:</p>
            <input type="number" id="actualHours" step="0.5" min="0" placeholder="Часы" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
            <div class="flex space-x-3">
                <button onclick="submitForReview()" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">Отправить на проверку</button>
                <button onclick="closeTimeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">Отмена</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@dragdroptouch/drag-drop-touch@latest/dist/drag-drop-touch.esm.min.js" type="module"></script>

    <script>
        // ==================== ПЕРЕМЕННЫЕ ====================
        let currentTaskId = null;
        let editSelectedFiles = [];
        let editAllFiles = [];
        let editTempSelectedFiles = [];
        let currentDeleteTaskId = null;
        let currentReturnTaskId = null;

        let taskSelectedFiles = [];
        let taskAllFiles = [];

        // ==================== ПЕРЕКЛЮЧЕНИЕ РЕЖИМОВ ====================
        function setViewMode(mode) {
            const listContainer = document.getElementById('listViewContainer');
            const kanbanContainer = document.getElementById('kanbanViewContainer');
            const listBtn = document.getElementById('viewModeListBtn');
            const kanbanBtn = document.getElementById('viewModeKanbanBtn');
            const filterForm = document.getElementById('filterForm');
            const filterFormList = document.getElementById('filterFormList');

            if (mode === 'list') {
                listContainer.classList.remove('hidden');
                kanbanContainer.classList.add('hidden');
                listBtn.classList.add('bg-green-600', 'text-white');
                listBtn.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                kanbanBtn.classList.remove('bg-green-600', 'text-white');
                kanbanBtn.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
            } else {
                listContainer.classList.add('hidden');
                kanbanContainer.classList.remove('hidden');
                kanbanBtn.classList.add('bg-green-600', 'text-white');
                kanbanBtn.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                listBtn.classList.remove('bg-green-600', 'text-white');
                listBtn.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
            }

            // Используем правильный URL /set-view-mode
            fetch('/set-view-mode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ view_mode: mode })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Обновляем URL в адресной строке
                        const url = new URL(window.location.href);
                        url.searchParams.set('view_mode', mode);
                        window.history.pushState({}, '', url);

                        // Обновляем скрытые поля в формах фильтров
                        if (filterForm) {
                            let viewModeInput = filterForm.querySelector('input[name="view_mode"]');
                            if (!viewModeInput) {
                                viewModeInput = document.createElement('input');
                                viewModeInput.type = 'hidden';
                                viewModeInput.name = 'view_mode';
                                filterForm.appendChild(viewModeInput);
                            }
                            viewModeInput.value = mode;
                        }

                        if (filterFormList) {
                            let viewModeInput = filterFormList.querySelector('input[name="view_mode"]');
                            if (!viewModeInput) {
                                viewModeInput = document.createElement('input');
                                viewModeInput.type = 'hidden';
                                viewModeInput.name = 'view_mode';
                                filterFormList.appendChild(viewModeInput);
                            }
                            viewModeInput.value = mode;
                        }

                        console.log('View mode saved:', mode);
                    }
                })
                .catch(error => console.error('Error saving view mode:', error));
        }

        // ==================== ФУНКЦИИ ДЛЯ МЕНЮ С ТРЕМЯ ТОЧКАМИ ====================
        function toggleTaskMenu(event, taskId) {
            event.stopPropagation();
            document.querySelectorAll('.task-menu').forEach(menu => {
                if (menu.id !== `taskMenu-${taskId}`) menu.classList.add('hidden');
            });
            const menu = document.getElementById(`taskMenu-${taskId}`);
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.task-menu') && !event.target.closest('[onclick*="toggleTaskMenu"]')) {
                document.querySelectorAll('.task-menu').forEach(menu => menu.classList.add('hidden'));
            }
        });

        // ==================== ПЕРЕКЛЮЧЕНИЕ ФИЛЬТРОВ ====================
        // document.getElementById('filterToggle')?.addEventListener('click', function () {
        //     document.getElementById('filtersPanel').classList.toggle('hidden');
        // });
  function toggleFiltersDropdown() {
    const dropdown = document.getElementById('filtersPanel');
    const chevron = document.getElementById('filterIcon');

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
        // ==================== СОРТИРОВКА ====================
        // Обновите обработчики сортировки
        document.getElementById('sortSelect')?.addEventListener('change', function () {
            const value = this.value;
            let sort, order;
            switch (value) {
                case 'created_at_desc': sort = 'created_at'; order = 'desc'; break;
                case 'created_at_asc': sort = 'created_at'; order = 'asc'; break;
                case 'deadline_asc': sort = 'deadline'; order = 'asc'; break;
                case 'deadline_desc': sort = 'deadline'; order = 'desc'; break;
                case 'priority_desc': sort = 'priority'; order = 'desc'; break;
                case 'name_asc': sort = 'name'; order = 'asc'; break;
                default: sort = 'created_at'; order = 'desc';
            }
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sort);
            url.searchParams.set('order', order);

            // Сохраняем текущий режим просмотра
            const currentMode = document.querySelector('#viewModeListBtn').classList.contains('bg-green-600') ? 'list' : 'kanban';
            url.searchParams.set('view_mode', currentMode);

            window.location.href = url.toString();
        });

        document.getElementById('sortSelectKanban')?.addEventListener('change', function () {
            const value = this.value;
            let sort, order;
            switch (value) {
                case 'created_at_desc': sort = 'created_at'; order = 'desc'; break;
                case 'created_at_asc': sort = 'created_at'; order = 'asc'; break;
                case 'deadline_asc': sort = 'deadline'; order = 'asc'; break;
                case 'priority_desc': sort = 'priority'; order = 'desc'; break;
                case 'name_asc': sort = 'name'; order = 'asc'; break;
                default: sort = 'created_at'; order = 'desc';
            }
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sort);
            url.searchParams.set('order', order);
            url.searchParams.set('view_mode', 'kanban');
            window.location.href = url.toString();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const sort = urlParams.get('sort') || 'created_at';
            const order = urlParams.get('order') || 'desc';
            let selectedValue = 'created_at_desc';
            if (sort === 'created_at' && order === 'desc') selectedValue = 'created_at_desc';
            else if (sort === 'created_at' && order === 'asc') selectedValue = 'created_at_asc';
            else if (sort === 'deadline' && order === 'asc') selectedValue = 'deadline_asc';
            else if (sort === 'deadline' && order === 'desc') selectedValue = 'deadline_desc';
            else if (sort === 'priority' && order === 'desc') selectedValue = 'priority_desc';
            else if (sort === 'name' && order === 'asc') selectedValue = 'name_asc';
            const sortSelect = document.getElementById('sortSelect');
            const sortSelectKanban = document.getElementById('sortSelectKanban');
            if (sortSelect) sortSelect.value = selectedValue;
            if (sortSelectKanban) sortSelectKanban.value = selectedValue;
        });

        // ==================== ОБРАБОТКА URL ДЛЯ ОТКРЫТИЯ МОДАЛКИ ====================
        document.addEventListener('DOMContentLoaded', function() {
            const openTaskId = {{ $openTaskId ?? 'null' }};
            if (openTaskId) {
                window.history.pushState({}, '', '/team/tasks');
                setTimeout(function() {
                    if (typeof openTaskViewModal === 'function') openTaskViewModal(openTaskId);
                }, 100);
            }
        });

        // ==================== ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ЗАДАЧИ ====================
        async function openEditModal(taskId) {
            currentTaskId = taskId;
            editSelectedFiles = [];

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
                    document.getElementById('editTaskName').value = task.name;
                    document.getElementById('editTaskDescription').value = task.description || '';

                    const departmentSelect = document.getElementById('editTaskDepartment');
                    if (departmentSelect) {
                        departmentSelect.innerHTML = '<option value="">Выберите отдел</option>';
                        @foreach($filterData['departments'] as $department)
                            departmentSelect.innerHTML += `<option value="{{ $department->id }}">{{ $department->name }}</option>`;
                        @endforeach
                            departmentSelect.value = task.department_id || '';
                    }

                    const categorySelect = document.getElementById('editTaskCategory');
                    if (categorySelect) {
                        categorySelect.innerHTML = '<option value="">Без категории</option>';
                        @foreach($filterData['categories'] as $category)
                            categorySelect.innerHTML += `<option value="{{ $category->id }}">{{ $category->name }}</option>`;
                        @endforeach
                            categorySelect.value = task.category_id || '';
                    }

                    const userSelect = document.getElementById('editTaskUser');
                    if (userSelect) {
                        userSelect.innerHTML = '<option value="">Не назначен</option>';
                        @foreach($filterData['users'] as $userItem)
                            userSelect.innerHTML += `<option value="{{ $userItem->id }}">{{ $userItem->name }}</option>`;
                        @endforeach
                            userSelect.value = task.user_id || '';
                    }

                    document.getElementById('editTaskPriority').value = task.priority || 'средний';
                    document.getElementById('editTaskStatus').value = task.status;
                    document.getElementById('editTaskDeadline').value = task.deadline ? task.deadline.slice(0, 16) : '';
                    document.getElementById('editTaskEstimatedHours').value = task.estimated_hours || '';
                    document.getElementById('editTaskActualHours').value = task.actual_hours || '';

                    if (task.files && task.files.length > 0) {
                        editSelectedFiles = task.files;
                    } else {
                        editSelectedFiles = [];
                    }
                    updateEditSelectedFilesDisplay();

                    if (task.rejections && task.rejections.length > 0) {
                        displayRejections(task.rejections);
                    } else {
                        displayRejections([]);
                    }

                    document.getElementById('editTaskModal').classList.remove('hidden');
                } else {
                    showNotification(data.message || 'Ошибка при загрузке данных задачи', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при загрузке данных задачи', 'error');
            }
        }

        function closeEditModal() {
            document.getElementById('editTaskModal').classList.add('hidden');
            document.getElementById('editUploadNewFilesInput').value = '';
            document.getElementById('editUploadFilesList').classList.add('hidden');
            currentTaskId = null;
            editSelectedFiles = [];
            editTempSelectedFiles = [];
        }

        function updateEditSelectedFilesDisplay() {
            const container = document.getElementById('editSelectedFilesContainer');
            const fileCounter = document.getElementById('editFileCounter');
            const fileCount = document.getElementById('editFileCount');

            if (!container) return;

            if (editSelectedFiles.length === 0) {
                container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Файлы не выбраны</p>
                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
            </div>`;
                if (fileCounter) fileCounter.classList.add('hidden');
            } else {
                let html = '';
                editSelectedFiles.forEach(file => {
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
                    <button onclick="removeEditSelectedFile(${file.id})" class="text-red-500 hover:text-red-700 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
                });
                container.innerHTML = html;
                if (fileCount) fileCount.textContent = editSelectedFiles.length;
                if (fileCounter) fileCounter.classList.remove('hidden');
            }
        }

        function removeEditSelectedFile(fileId) {
            editSelectedFiles = editSelectedFiles.filter(f => f.id !== fileId);
            updateEditSelectedFilesDisplay();
            showNotification('Файл удален', 'info');
        }

        function clearEditSelectedFiles() {
            if (editSelectedFiles.length === 0) {
                showNotification('Нет файлов для очистки', 'info');
                return;
            }
            if (confirm('Удалить все выбранные файлы?')) {
                editSelectedFiles = [];
                updateEditSelectedFilesDisplay();
                showNotification('Все файлы удалены', 'success');
            }
        }

        function switchEditFileTab(tabName) {
            const tabButtons = document.querySelectorAll('#editTaskModal .tab-button');
            const tabContents = document.querySelectorAll('#editTaskModal .tab-content');
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-tab') === tabName) btn.classList.add('active');
            });
            tabContents.forEach(content => content.classList.add('hidden'));
            const activeContent = document.getElementById('edit' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'TabContent');
            if (activeContent) activeContent.classList.remove('hidden');
        }

        // ==================== ФАЙЛОВЫЙ МЕНЕДЖЕР ДЛЯ РЕДАКТИРОВАНИЯ ====================
        async function openEditFileManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                editTempSelectedFiles = [...editSelectedFiles];
                await loadEditFiles();
            }
        }

        async function loadEditFiles() {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12">
        <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
        <p class="text-gray-500 mt-2">Загрузка файлов...</p>
    </div>`;

            try {
                const response = await fetch('/tasks/file-storage/get-files', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                if (!response.ok) throw new Error('Ошибка загрузки');
                editAllFiles = await response.json();
                window.editAllFiles = editAllFiles;
                renderEditFileManagerFiles(editAllFiles);
                initEditFileManagerFilters();
            } catch (error) {
                console.error('Ошибка загрузки файлов:', error);
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">
            <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
            <p>Ошибка загрузки файлов</p>
        </div>`;
            }
        }

        function renderEditFileManagerFiles(files) {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;

            if (!files || files.length === 0) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12">
            <i class="fas fa-folder-open text-3xl text-gray-300 mb-2"></i>
            <p class="text-gray-500">Нет файлов в хранилище</p>
        </div>`;
                return;
            }

            let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
            files.forEach(file => {
                const isSelected = editTempSelectedFiles.some(f => f.id === file.id);
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
                        <span class="text-2xl">${fileIcon}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-800 truncate" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</p>
                    <p class="text-xs text-gray-500 mt-1">${formatFileSize(file.size)}</p>
                    <p class="text-xs text-gray-400">${formatDate(file.created_at)}</p>
                </div>
            </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;

            updateEditFileManagerUI();
        }

        function toggleEditFileSelection(fileId) {
            const file = editAllFiles.find(f => f.id === fileId);
            if (!file) return;

            const index = editTempSelectedFiles.findIndex(f => f.id === fileId);
            if (index === -1) {
                editTempSelectedFiles.push(file);
            } else {
                editTempSelectedFiles.splice(index, 1);
            }

            const fileCards = document.querySelectorAll('#fileManagerContent .file-card');
            fileCards.forEach(card => {
                const onclickAttr = card.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes(fileId.toString())) {
                    const isSelected = editTempSelectedFiles.some(f => f.id === fileId);
                    if (isSelected) {
                        card.classList.add('border-green-500', 'bg-green-50');
                        card.classList.remove('border-gray-200');
                        const checkDiv = card.querySelector('.w-5.h-5');
                        if (checkDiv) {
                            checkDiv.classList.add('bg-green-500');
                            checkDiv.classList.remove('border-2', 'border-gray-300');
                            checkDiv.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                        }
                    } else {
                        card.classList.remove('border-green-500', 'bg-green-50');
                        card.classList.add('border-gray-200');
                        const checkDiv = card.querySelector('.w-5.h-5');
                        if (checkDiv) {
                            checkDiv.classList.remove('bg-green-500');
                            checkDiv.classList.add('border-2', 'border-gray-300');
                            checkDiv.innerHTML = '';
                        }
                    }
                }
            });

            updateEditFileManagerUI();
        }

        function updateEditFileManagerUI() {
            const selectedCount = document.getElementById('selectedCount');
            const confirmCount = document.getElementById('confirmCount');
            const confirmBtn = document.getElementById('confirmFileSelectionBtn');

            const count = editTempSelectedFiles.length;

            if (selectedCount) selectedCount.textContent = count;
            if (confirmCount) confirmCount.textContent = count;

            if (confirmBtn) {
                confirmBtn.disabled = (count === 0);
                if (count === 0) {
                    confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        window.confirmEditFileSelectionForEdit = function () {
            editSelectedFiles = [...editTempSelectedFiles];
            updateEditSelectedFilesDisplay();

            const fileManagerModal = document.getElementById('fileManagerModal');
            if (fileManagerModal) {
                fileManagerModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            showNotification(`Выбрано ${editSelectedFiles.length} файлов`, 'success');
        };

        function initEditFileManagerFilters() {
            const searchInput = document.getElementById('fileManagerSearch');
            const typeFilter = document.getElementById('fileManagerTypeFilter');
            const sortBy = document.getElementById('fileManagerSortBy');

            const filter = () => {
                if (!editAllFiles) return;
                let filtered = [...editAllFiles];
                const searchTerm = searchInput?.value.toLowerCase() || '';
                if (searchTerm) filtered = filtered.filter(f => f.name.toLowerCase().includes(searchTerm));
                if (typeFilter && typeFilter.value) {
                    const type = typeFilter.value;
                    if (type === 'image') filtered = filtered.filter(f => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(f.extension?.toLowerCase()));
                    else if (type === 'document') filtered = filtered.filter(f => ['pdf', 'doc', 'docx', 'txt', 'rtf'].includes(f.extension?.toLowerCase()));
                    else if (type === 'video') filtered = filtered.filter(f => ['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(f.extension?.toLowerCase()));
                    else if (type === 'audio') filtered = filtered.filter(f => ['mp3', 'wav', 'ogg', 'flac', 'm4a'].includes(f.extension?.toLowerCase()));
                    else if (type === 'archive') filtered = filtered.filter(f => ['zip', 'rar', '7z', 'tar', 'gz'].includes(f.extension?.toLowerCase()));
                }
                if (sortBy && sortBy.value) {
                    const sort = sortBy.value;
                    if (sort === 'newest') filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    else if (sort === 'oldest') filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    else if (sort === 'name_asc') filtered.sort((a, b) => a.name.localeCompare(b.name));
                    else if (sort === 'name_desc') filtered.sort((a, b) => b.name.localeCompare(a.name));
                    else if (sort === 'size_asc') filtered.sort((a, b) => (a.size || 0) - (b.size || 0));
                    else if (sort === 'size_desc') filtered.sort((a, b) => (b.size || 0) - (a.size || 0));
                }
                renderEditFileManagerFiles(filtered);
            };

            if (searchInput) {
                searchInput.removeEventListener('input', filter);
                searchInput.addEventListener('input', filter);
            }
            if (typeFilter) {
                typeFilter.removeEventListener('change', filter);
                typeFilter.addEventListener('change', filter);
            }
            if (sortBy) {
                sortBy.removeEventListener('change', filter);
                sortBy.addEventListener('change', filter);
            }
        }

        function downloadEditFile(fileId) {
            window.open(`/file-storage/download/${fileId}`, '_blank');
        }

        function closeFileManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ ЗАГРУЗКИ НОВЫХ ФАЙЛОВ ====================
        document.getElementById('editUploadNewFilesInput')?.addEventListener('change', function (e) {
            const container = document.getElementById('editUploadFilesContainer');
            const list = document.getElementById('editUploadFilesList');
            if (!container) return;
            container.innerHTML = '';
            if (this.files.length > 0 && list) {
                list.classList.remove('hidden');
                Array.from(this.files).forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
                    div.innerHTML = `
                    <span class="text-sm truncate">${escapeHtml(file.name)}</span>
                    <button type="button" onclick="removeEditUploadedFile(${index})" class="text-red-500">✕</button>
                `;
                    container.appendChild(div);
                });
            } else if (list) {
                list.classList.add('hidden');
            }
        });

        function removeEditUploadedFile(index) {
            const input = document.getElementById('editUploadNewFilesInput');
            if (!input) return;
            const dt = new DataTransfer();
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) dt.items.add(file);
            });
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }

        // ==================== СОХРАНЕНИЕ РЕДАКТИРОВАНИЯ ====================
        document.getElementById('editTaskForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn?.innerHTML;
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Сохранение...';
                submitBtn.disabled = true;
            }
            try {
                const formData = new FormData(this);
                const selectedFileIds = editSelectedFiles.map(f => f.id);
                formData.append('selected_files', JSON.stringify(selectedFileIds));

                const newFiles = document.getElementById('editUploadNewFilesInput');
                if (newFiles?.files.length) {
                    for (let i = 0; i < newFiles.files.length; i++) {
                        formData.append('new_files[]', newFiles.files[i]);
                    }
                }

                const response = await fetch(`/tasks/${currentTaskId}/update`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Задача успешно обновлена', 'success');
                    closeEditModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(result.message || 'Ошибка при обновлении задачи', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка: ' + error.message, 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }
        });

        // ==================== ФУНКЦИИ ДЛЯ СОЗДАНИЯ ЗАДАЧИ ====================
        function openTaskModal() {
            const modal = document.getElementById('taskModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeTaskModal() {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');
            if (modal) modal.classList.add('hidden');
            if (form) form.reset();
            taskSelectedFiles = [];
            updateTaskSelectedFilesDisplay();
        }

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

        function downloadTaskFile(fileId) {
            window.open(`/file-storage/download/${fileId}`, '_blank');
        }

        function closeTaskStorageManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        window.confirmFileSelection = function() {
            const isEditModalVisible = document.getElementById('editTaskModal') && !document.getElementById('editTaskModal').classList.contains('hidden');
            const isCreateModalVisible = document.getElementById('taskModal') && !document.getElementById('taskModal').classList.contains('hidden');

            if (isEditModalVisible) {
                if (editTempSelectedFiles.length === 0) {
                    alert('Пожалуйста, выберите хотя бы один файл');
                    return;
                }
                editSelectedFiles = [...editTempSelectedFiles];
                updateEditSelectedFilesDisplay();
                closeFileManager();
            } else if (isCreateModalVisible) {
                const selectedFilesInput = document.getElementById('selectedFiles');
                if (selectedFilesInput) {
                    selectedFilesInput.value = JSON.stringify(taskSelectedFiles);
                }
                updateTaskSelectedFilesDisplay();
                switchFileTab('storage');
                closeFileManager();
            }
        };

        document.getElementById('uploadNewFilesInput')?.addEventListener('change', function(e) {
            const container = document.getElementById('uploadFilesContainer');
            const list = document.getElementById('uploadFilesList');
            if (!container) return;
            container.innerHTML = '';
            if (this.files.length > 0 && list) {
                list.classList.remove('hidden');
                Array.from(this.files).forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
                    div.innerHTML = `<span class="text-sm truncate">${escapeHtml(file.name)}</span><button type="button" onclick="this.parentElement.remove()" class="text-red-500">✕</button>`;
                    container.appendChild(div);
                });
            } else if (list) {
                list.classList.add('hidden');
            }
        });

        // ==================== ФУНКЦИИ ДЛЯ ВОЗВРАТА НА ДОРАБОТКУ ====================
        function returnToWork(taskId) {
            currentReturnTaskId = taskId;
            document.getElementById('returnToWorkModal').classList.remove('hidden');
        }

        function closeReturnModal() {
            document.getElementById('returnToWorkModal').classList.add('hidden');
            document.getElementById('returnComment').value = '';
        }

        async function confirmReturnToWork() {
            const comment = document.getElementById('returnComment').value;
            const submitBtn = document.querySelector('#returnToWorkModal .bg-orange-600');
            const originalText = submitBtn?.innerHTML;

            try {
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отправка...';
                    submitBtn.disabled = true;
                }

                const response = await fetch(`/tasks/${currentReturnTaskId}/return-to-work`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({comment})
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message || 'Задача возвращена на доработку', 'success');
                    closeReturnModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(result.message || 'Ошибка при возврате задачи', 'error');
                    closeReturnModal();
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка: ' + error.message, 'error');
                closeReturnModal();
            } finally {
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ УДАЛЕНИЯ ЗАДАЧИ ====================
        function openDeleteModal(taskId) {
            currentDeleteTaskId = taskId;
            document.getElementById('deleteTaskModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteTaskModal').classList.add('hidden');
        }

        async function confirmDeleteTask() {
            const submitBtn = document.querySelector('#deleteTaskModal .bg-red-600');
            const originalText = submitBtn?.innerHTML;

            try {
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Удаление...';
                    submitBtn.disabled = true;
                }

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                const response = await fetch(`/tasks/${currentDeleteTaskId}/delete`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                if (response.status === 404) throw new Error('Маршрут не найден. Проверьте URL.');

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message || 'Задача успешно удалена', 'success');
                    closeDeleteModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(result.message || 'Ошибка при удалении задачи', 'error');
                    closeDeleteModal();
                }
            } catch (error) {
                console.error('Ошибка при удалении:', error);
                showNotification('Ошибка при удалении задачи: ' + error.message, 'error');
                closeDeleteModal();
            } finally {
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ ПРОСМОТРА ЗАДАЧИ ====================
        async function openTaskViewModal(taskId) {
            const modal = document.getElementById('taskViewModal');
            const content = document.getElementById('taskModalContent');

            if (!modal || !content) {
                console.error('Модальное окно не найдено');
                return;
            }

            window.currentTaskId = taskId;
            window.taskId = taskId;

            const newUrl = `/team/tasks/${taskId}`;
            window.history.pushState({ taskId: taskId, modalOpen: true }, '', newUrl);

            content.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i><p class="text-gray-500 mt-2">Загрузка задачи...</p></div>`;
            modal.style.backdropFilter = 'blur(10px)';
            modal.classList.remove('hidden');

            try {
                const response = await fetch(`/tasks/${taskId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                const html = await response.text();
                content.innerHTML = html;
            } catch (error) {
                console.error('Ошибка:', error);
                content.innerHTML = `<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-3xl text-red-400"></i><p class="text-gray-500 mt-2">Не удалось загрузить задачу</p><p class="text-sm text-gray-400 mt-1">${error.message}</p><button onclick="openTaskViewModal(${taskId})" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"><i class="fas fa-sync-alt mr-2"></i>Повторить</button></div>`;
            }
        }

        function closeTaskViewModal() {
            const modal = document.getElementById('taskViewModal');
            const content = document.getElementById('taskModalContent');

            if (modal) {
                modal.classList.add('hidden');
                modal.style.backdropFilter = '';
            }
            if (content) {
                content.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i><p class="text-gray-500 mt-2">Загрузка задачи...</p></div>`;
            }
            window.history.pushState({}, '', '/team/tasks');
        }

        window.addEventListener('popstate', function(event) {
            const modal = document.getElementById('taskViewModal');
            if (modal && !modal.classList.contains('hidden')) closeTaskViewModal();
        });

        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('taskViewModal');
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeTaskViewModal();
        });

        document.addEventListener('click', function(e) {
            const modal = document.getElementById('taskViewModal');
            if (e.target === modal) closeTaskViewModal();
        });

        // ==================== ФУНКЦИИ ДЛЯ КОММЕНТАРИЕВ ====================
        function submitComment(taskId) {
            const commentText = document.getElementById('commentInput')?.value.trim();
            if (!commentText) { showNotification('Напишите комментарий', 'warning'); return; }

            fetch(`/tasks/${taskId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ comment: commentText })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('commentInput').value = '';
                        openTaskViewModal(taskId);
                        showNotification('Комментарий добавлен', 'success');
                    } else {
                        showNotification(data.message || 'Ошибка', 'error');
                    }
                })
                .catch(() => showNotification('Ошибка', 'error'));
        }

        function submitReply(commentId) {
            const replyText = document.getElementById(`replyText_${commentId}`)?.value.trim();
            const taskId = window.currentTaskId || window.taskId;

            if (!replyText) {
                if (typeof showNotification === 'function') showNotification('Напишите ответ', 'warning');
                return;
            }
            if (!taskId) {
                if (typeof showNotification === 'function') showNotification('Ошибка: ID задачи не определен', 'error');
                return;
            }

            fetch(`/tasks/${taskId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment: replyText, parent_id: commentId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const replyTextarea = document.getElementById(`replyText_${commentId}`);
                        if (replyTextarea) replyTextarea.value = '';
                        cancelReply(commentId);
                        if (typeof openTaskViewModal === 'function') openTaskViewModal(taskId);
                        if (typeof showNotification === 'function') showNotification('Ответ добавлен', 'success');
                    } else {
                        if (typeof showNotification === 'function') showNotification(data.message || 'Ошибка при добавлении ответа', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showNotification === 'function') showNotification('Ошибка при добавлении ответа', 'error');
                });
        }

        function deleteComment(commentId) {
            if (!confirm('Вы уверены, что хотите удалить этот комментарий?')) return;
            const taskId = window.currentTaskId || window.taskId;

            fetch(`/tasks/${taskId}/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof openTaskViewModal === 'function') openTaskViewModal(taskId);
                        if (typeof showNotification === 'function') showNotification('Комментарий удален', 'success');
                    } else {
                        if (typeof showNotification === 'function') showNotification(data.message || 'Ошибка при удалении', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showNotification === 'function') showNotification('Ошибка при удалении комментария', 'error');
                });
        }

        function editComment(commentId) {
            const commentDiv = document.querySelector(`.comment-text[data-comment-text="${commentId}"]`);
            if (!commentDiv) return;
            const currentText = commentDiv.textContent;
            commentDiv.innerHTML = `<textarea id="editText_${commentId}" class="w-full border rounded-lg p-2" rows="3">${escapeHtml(currentText)}</textarea>
        <div class="flex justify-end mt-2 space-x-2">
            <button onclick="cancelEdit(${commentId})" class="px-3 py-1 bg-gray-300 rounded">Отмена</button>
            <button onclick="saveEdit(${commentId})" class="px-3 py-1 bg-blue-500 text-white rounded">Сохранить</button>
        </div>`;
        }

        function saveEdit(commentId) {
            const newText = document.getElementById(`editText_${commentId}`)?.value.trim();
            const taskId = window.currentTaskId || window.taskId;

            if (!newText) {
                if (typeof showNotification === 'function') showNotification('Комментарий не может быть пустым', 'warning');
                return;
            }

            fetch(`/tasks/${taskId}/comments/${commentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment: newText })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof openTaskViewModal === 'function') openTaskViewModal(taskId);
                        if (typeof showNotification === 'function') showNotification('Комментарий обновлен', 'success');
                    } else {
                        if (typeof showNotification === 'function') showNotification(data.message || 'Ошибка при обновлении', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showNotification === 'function') showNotification('Ошибка при обновлении комментария', 'error');
                });
        }

        function showReplyForm(commentId) {
            const form = document.getElementById(`replyForm_${commentId}`);
            if (form) form.classList.remove('hidden');
        }

        function cancelReply(commentId) {
            const form = document.getElementById(`replyForm_${commentId}`);
            if (form) form.classList.add('hidden');
            const textarea = document.getElementById(`replyText_${commentId}`);
            if (textarea) textarea.value = '';
        }

        function cancelEdit(commentId) {
            openTaskViewModal(window.currentTaskId);
        }

        // ==================== ФУНКЦИИ ДЛЯ СТАРТА ЗАДАЧИ, ОТПРАВКИ НА ПРОВЕРКУ, ОДОБРЕНИЯ, ОТКАЗА ====================
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
                    showNotification('Задача переведена в работу!', 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при обновлении статуса', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при обновлении статуса', 'error');
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
                    showNotification('Задача отправлена на проверку!', 'success');
                    closeTimeModal();
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при отправке на проверку', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при отправке на проверку', 'error');
            }
        }

        async function approveTask(taskId) {
            try {
                const response = await fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'выполнена' })
                });
                const data = await response.json();
                if (data.success) {
                    showNotification('Задача одобрена и завершена!', 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при одобрении задачи', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при одобрении задачи', 'error');
            }
        }

        async function returnTaskToWork(taskId) {
            if (confirm('Вернуть задачу на доработку?')) {
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
                        showNotification('Задача возвращена на доработку', 'success');
                        location.reload();
                    } else {
                        showNotification(data.message || 'Ошибка при возврате задачи', 'error');
                    }
                } catch (error) {
                    console.error('Ошибка:', error);
                    showNotification('Ошибка при возврате задачи', 'error');
                }
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
                    showNotification('Вы отказались от задачи', 'success');
                    closeRejectModal();
                    location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при отказе от задачи', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при отказе от задачи', 'error');
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

        // ==================== DRAG AND DROP ДЛЯ КАНБАНА ====================
        let draggedItem = null;
        let swiperSlideTimeout = null;

        function dragStart(e) {
            draggedItem = this;
            e.dataTransfer.setData('text/plain', this.dataset.task);
            this.style.opacity = '0.5';
            if (window.mySwiper) {
                window.mySwiper.detachEvents();
            }
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

                if (window.mySwiper && typeof window.mySwiper.slideTo === 'function') {
                    const allColumns = Array.from(document.querySelectorAll('.board-column'));
                    const columnIndex = allColumns.indexOf(column);
                    if (columnIndex !== -1 && window.mySwiper.activeIndex !== columnIndex && !swiperSlideTimeout) {
                        swiperSlideTimeout = setTimeout(() => {
                            window.mySwiper.slideTo(columnIndex, 300);
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
                    showNotification(data.message || 'Ошибка при перемещении задачи', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при перемещении задачи', 'error');
            }
        }

        function initDragAndDrop() {
            const taskCards = document.querySelectorAll('#kanbanViewContainer .task-card');
            const columns = document.querySelectorAll('#kanbanViewContainer .board-column');

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

        // ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================
        function getFileIcon(ext) {
            const icons = {
                'pdf': '📄', 'doc': '📝', 'docx': '📝', 'xls': '📊', 'xlsx': '📊',
                'jpg': '🖼️', 'png': '🖼️', 'gif': '🖼️', 'zip': '📦', 'rar': '📦',
                'mp3': '🎵', 'mp4': '🎬', 'avi': '🎬', 'mov': '🎬'
            };
            return icons[(ext || '').toLowerCase()] || '📎';
        }

        function getFileTypeClass(ext) {
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

        function displayRejections(rejections) {
            const container = document.getElementById('editRejectionsList');
            const countSpan = document.getElementById('editRejectionsCount');
            if (!container) return;
            if (rejections?.length) {
                if (countSpan) countSpan.textContent = rejections.length;
                container.innerHTML = rejections.map(r => `<div class="bg-red-50 p-3 rounded rejection-item">${escapeHtml(r.reason || 'Отказ')}</div>`).join('');
            } else {
                if (countSpan) countSpan.textContent = '0';
                container.innerHTML = '<div class="text-center py-8"><i class="fas fa-check-circle text-3xl text-gray-300 mb-2"></i><p class="text-gray-500">Отказов нет</p></div>';
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white'}`;
            notification.innerHTML = `<div class="flex items-center"><i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i><span>${message}</span></div>`;
            document.body.appendChild(notification);
            setTimeout(() => { notification.style.transform = 'translateX(0)'; }, 100);
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => { if (notification.parentNode) notification.parentNode.removeChild(notification); }, 300);
            }, 5000);
        }

        // ==================== ИНИЦИАЛИЗАЦИЯ ====================
        document.addEventListener('DOMContentLoaded', function () {
            initDragAndDrop();

            // Swiper для мобильных устройств
            if (window.innerWidth <= 500) {
                const sliderElement = document.querySelector('.sw-v');
                if (sliderElement && !window.mySwiper) {
                    window.mySwiper = new Swiper('.sw-v', {
                        wrapperClass: 'sw-v-wrapper',
                        slideClass: 'board-column',
                        centeredSlides: true,
                        centeredSlidesBounds: true,
                        slidesPerView: 'auto',
                        spaceBetween: 10,
                        loop: false,
                        observer: true,
                        observeParents: true,
                        watchSlidesProgress: true,
                        touchStartPreventDefault: false,
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                            bulletClass: 'swiper-pagination-bullet bg-gray-400 opacity-50 mx-1 inline-block rounded-full w-2 h-2',
                            bulletActiveClass: '!bg-[#22c55e] !opacity-100 w-4 rounded-lg transition-all duration-300'
                        }
                    });
                }
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
    </script>

    @push('scripts')
        <script>
            function copyTaskLink() {
                const taskId = window.currentTaskId;
                if (!taskId) return;
                const url = window.location.origin + '/team/tasks/' + taskId;
                navigator.clipboard.writeText(url);
                showNotification('Ссылка скопирована', 'success');
            }

            function printTask() {
                window.print();
            }

            function updateViewModeBeforeSubmit() {
                const mode = document.querySelector('#viewModeListBtn').classList.contains('bg-green-600') ? 'list' : 'kanban';
                const filterForm = document.getElementById('filterForm');
                const filterFormList = document.getElementById('filterFormList');

                const formToUse = filterForm || filterFormList;
                if (formToUse) {
                    let viewModeInput = formToUse.querySelector('input[name="view_mode"]');
                    if (!viewModeInput) {
                        viewModeInput = document.createElement('input');
                        viewModeInput.type = 'hidden';
                        viewModeInput.name = 'view_mode';
                        formToUse.appendChild(viewModeInput);
                    }
                    viewModeInput.value = mode;
                    formToUse.submit();
                }
            }

            function resetWithCurrentMode() {
                const mode = document.querySelector('#viewModeListBtn').classList.contains('bg-green-600') ? 'list' : 'kanban';
                const url = new URL(window.location.href);
                // Очищаем все параметры фильтрации
                url.searchParams.delete('search');
                url.searchParams.delete('status');
                url.searchParams.delete('user_id');
                url.searchParams.delete('department_id');
                url.searchParams.delete('priority');
                url.searchParams.delete('category_id');
                url.searchParams.delete('sort');
                url.searchParams.delete('order');
                url.searchParams.set('view_mode', mode);
                window.location.href = url.toString();
            }

            // Переменные для хранения ID задачи
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
        .board-column.drag-over {
            background-color: rgba(229, 231, 235, 0.5) !important;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .board-column.drag-over-active {
            background-color: rgba(16, 185, 129, 0.2) !important;
            border: 2px dashed #10b981;
        }
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .rejection-item {
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
            border-left: 4px solid #ef4444;
            transition: all 0.2s ease;
        }
        .rejection-item:hover {
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
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
@endsection
