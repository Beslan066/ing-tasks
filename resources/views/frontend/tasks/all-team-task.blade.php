@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp
        <!-- !!!class="w-[calc(100% - 250px)]" -->
    <div>
        <!-- Заголовок -->
        <div
            class="flex justify-between items-center mb-6 max-[800px]:flex-col max-[800px]:items-baseline max-[800px]:space-y-4">
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
                                <li class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}" x-text="pageName">Все задачи</li>
                            </ol>
                        </nav>
            <div class="max-[500px]:hidden">
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Все задачи компании</h2>
                    <p class="text-white text-sm">Архив всех ваших задач</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Все задачи компании</h2>
                    <p class="text-gray-700 text-sm">Архив всех ваших задач</p>
                @endif
            </div>

            <div class="flex space-x-3 max-[710px]:w-full">
                <button onclick="toggleFilters()"
                        class="flex-1 md:flex-none bg-white border border-gray-300 text-gray-700 px-3 py-2
                        md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50
                       transition text-sm md:text-base max-[710px]:basis-[50%]">
                    <i class="fas fa-filter"></i>
                    <span>Фильтры</span>
                    <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform"></i>
                </button>
                <a href="{{ route('welcome') }}"
                   class="  text-white px-4 py-2 rounded-lg transition flex items-center space-x-2 max-[710px]:basis-[50%] max-[500px]:text-sm"
                   style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Назад к доске</span>
                </a>
            </div>
        </div>

        <!-- Статистика -->
        @if($backgroundEnabled && $backgroundImage)
            <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none min-[500px]:grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-6 mb-6 md:mb-8 max-[500px]:no-scrollbar">
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('')">
                    <div class="text-2xl font-bold text-white">{{ $stats['total'] }}</div>
                    <div class="text-sm text-white">Всего задач</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('назначена')">
                    <div class="text-2xl font-bold text-white">{{ $stats['new'] }}</div>
                    <div class="text-sm text-white">Новые</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('в работе')">
                    <div class="text-2xl font-bold text-white">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-white">В работе</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('на проверке')">
                    <div class="text-2xl font-bold text-white">{{ $stats['review'] }}</div>
                    <div class="text-sm text-white">На проверке</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('выполнена')">
                    <div class="text-2xl font-bold text-white">{{ $stats['done'] }}</div>
                    <div class="text-sm text-white">Завершено</div>
                </div>
            </div>
        @else
            <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-none min-[500px]:grid grid-cols-2  sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5  gap-3 md:gap-6 mb-6 md:mb-8 max-[500px]:no-scrollbar">
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('')">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600">Всего задач</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('назначена')">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['new'] }}</div>
                    <div class="text-sm text-gray-600">Новые</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('в работе')">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-gray-600">В работе</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('на проверке')">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['review'] }}</div>
                    <div class="text-sm text-gray-600">На проверке</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer shrink-0 snap-start max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[70px] max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center max-[400px]:flex-col-reverse max-[400px]:p-2"
                     onclick="filterByStatus('выполнена')">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['done'] }}</div>
                    <div class="text-sm text-gray-600">Завершено</div>
                </div>
            </div>
        @endif

        <!-- Фильтры (скрыты по умолчанию) -->
        @if($backgroundEnabled && $backgroundImage)
            <div id="filtersPanel"
                 class="backdrop-blur-md bg-transparent/20 rounded-lg shadow mb-6 p-4 hidden transition-all duration-300">
                <div class="flex flex-wrap gap-4">

                    <div class="flex-[2] min-w-[250px]">
                        <label class="block font-medium text-white mb-1">Поиск</label>
                        <input type="text" id="searchTask" placeholder="Поиск по названию или описанию..."
                               class="w-full border-none placeholder:text-white rounded-lg px-3 py-2 bg-transparent/20 text-white">
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-white mb-1">Статус</label>
                        <select id="statusFilter"
                                class="w-full border-none rounded-lg px-3 py-2 backdrop-blur-md bg-transparent/20 text-white">
                            <option value="">Все статусы</option>
                            <option value="назначена">Новые</option>
                            <option value="в работе">В работе</option>
                            <option value="на проверке">На проверке</option>
                            <option value="выполнена">Завершено</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-white mb-1">Отдел</label>
                        <select id="departmentFilter"
                                class="w-full border-none rounded-lg px-3 py-2  backdrop-blur-md bg-transparent/20 text-white">
                            <option value="">Все отделы</option>
                            @foreach($user->company->departments ?? [] as $department)
                                <option class="text-gray-800" value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button onclick="applyFilters()"
                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                            <i class="fas fa-search mr-2"></i>Применить
                        </button>
                        <button onclick="resetFilters()"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                            <i class="fas fa-undo mr-2"></i>Сбросить
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <!-- Сортировка -->
                    <div class="flex items-center space-x-4 ">
                        <select id="sortBy" onchange="sortTasks()"
                                class="border-none rounded-lg px-3 py-2 bg-transparent/20 text-white">
                            <option class="text-gray-800" value="created_at_desc">Сортировка: новые сначала</option>
                            <option class="text-gray-800" value="created_at_asc">Сортировка: старые сначала</option>
                            <option class="text-gray-800" value="deadline_asc">Сортировка: ближайшие</option>
                            <option class="text-gray-800" value="deadline_desc">Сортировка: Дедлайн(дальние)</option>
                            <option class="text-gray-800" value="name_asc">Названию (А-Я)</option>
                            <option class="text-gray-800" value="name_desc">Названию (Я-А)</option>
                            <option class="text-gray-800" value="status_asc">Статус</option>
                            <option class="text-gray-800" value="priority_desc">Приоритет (высокие сначала)</option>
                        </select>
                    </div>
                </div>
            </div>
        @else
            <div id="filtersPanel" class="bg-white rounded-lg shadow mb-6 p-4 hidden transition-all duration-300">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                        <select id="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option class="text-gray-800" value="">Все статусы</option>
                            <option class="text-gray-800" value="назначена">Новые</option>
                            <option class="text-gray-800" value="в работе">В работе</option>
                            <option class="text-gray-800" value="на проверке">На проверке</option>
                            <option class="text-gray-800" value="выполнена">Завершено</option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                        <select id="departmentFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Все отделы</option>
                            @foreach($user->company->departments ?? [] as $department)
                                <option class="text-gray-800" value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-[2] min-w-[250px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                        <input type="text" id="searchTask" placeholder="Поиск по названию или описанию..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>

                    <div class="flex items-end gap-2">
                        <button onclick="applyFilters()"
                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                            <i class="fas fa-search mr-2"></i>Применить
                        </button>
                        <button onclick="resetFilters()"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                            <i class="fas fa-undo mr-2"></i>Сбросить
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <!-- Сортировка -->
                    <div class="flex items-center space-x-4 ">
                        <select id="sortBy" onchange="sortTasks()"
                                class="border border-gray-300 rounded-lg px-3 py-2">
                            <option class="text-gray-800" value="created_at_desc">Сортировка: новые сначала</option>
                            <option class="text-gray-800" value="created_at_asc">Сортировка: старые сначала</option>
                            <option class="text-gray-800" value="deadline_asc">Сортировка: ближайшие</option>
                            <option class="text-gray-800" value="deadline_desc">Сортировка: Дедлайн(дальние)</option>
                            <option class="text-gray-800" value="name_asc">Названию (А-Я)</option>
                            <option class="text-gray-800" value="name_desc">Названию (Я-А)</option>
                            <option class="text-gray-800" value="status_asc">Статус</option>
                            <option class="text-gray-800" value="priority_desc">Приоритет (высокие сначала)</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
        <!-- Список задач -->

        @if($backgroundEnabled && $backgroundImage)
            <div class="border-none">
                <div class="w-full max-w-[100%] overflow-x-auto backdrop-blur-md bg-transparent/20 rounded-lg border-none"
                     style="border-style: unset !important;">
                    <table class="min-w-[900px] w-full border-none max-[500px]:min-w-0" style="border-style: unset !important;">
                        <thead class="backdrop-blur-md bg-transparent/20" style="border-style: unset !important;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                onclick="sortByColumn('name')">
                                Задача <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 max-[500px]:hidden"
                                onclick="sortByColumn('status')">
                                Статус <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 max-[500px]:hidden"
                                onclick="sortByColumn('priority')">
                                Приоритет <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider max-[500px]:hidden">
                                Отдел
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 max-[500px]:hidden"
                                onclick="sortByColumn('deadline')">
                                Дедлайн <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия
                            </th>
                        </tr>
                        </thead>
                        <tbody id="tasksTableBody" class="backdrop-blur-md bg-transparent/20 "
                               style="border-style: unset !important;">
                        @foreach($allTasks as $task)
                            <tr class="task-row hover:bg-gray-50 text-white hover:text-gray-800 border-none"
                                data-id="{{ $task->id }}" data-status="{{ $task->status }}"
                                data-department="{{ $task->department_id }}" data-name="{{ $task->name }}"
                                data-priority="{{ $task->priority }}"
                                data-deadline="{{ $task->deadline ? $task->deadline->timestamp : 9999999999 }}"
                                data-created-at="{{ $task->created_at->timestamp }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium ">
                                        <a href="/team/tasks/{{ $task->id }}"
                                           onclick="openTaskViewModal({{ $task->id }}); return false;">
                                            {{ $task->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-[500px]:hidden">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->status === 'назначена' ? 'bg-blue-100 text-blue-800' : ($task->status === 'в работе' ? 'bg-purple-100 text-purple-800' : ($task->status === 'на проверке' ? 'bg-yellow-100 text-yellow-800' : ($task->status === 'выполнена' ? 'bg-green-100 text-green-800' : 'bg-red-400 text-white'))) }}">
                                            {{ $task->status }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 max-[500px]:hidden">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->priority === 'низкий' ? 'bg-gray-100 text-gray-800' : ($task->priority === 'средний' ? 'bg-blue-100 text-blue-800' : ($task->priority === 'высокий' ? 'bg-orange-100 text-orange-800' : ($task->priority === 'критический' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                            {{ $task->priority }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-[500px]:hidden">
                                    {{ $task->department->name ?? ($task->is_personal ? 'Личная' : '-') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-[500px]:hidden">
                                    @if($task->deadline)
                                        <span
                                            class="{{ $task->deadline->isPast() && $task->status !== 'выполнена' ? 'text-red-600 font-semibold' : '' }}">
                                                {{ $task->deadline->format('d.m.Y') }}
                                            </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium max-[500px]:flex max-[500px]:justify-center max-[500px]:items-center">
                                    <button onclick="openTaskViewModal({{ $task->id }})"
                                            class="text-green-600 hover:text-green-900 mr-3 max-[500px]:mr-0">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="px-6 py-4">
                    {{ $allTasks->links() }}
                </div>
            </div>
        @else
            <div>
                <div class="w-full max-w-[100%] overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-[900px] w-full divide-y divide-gray-200 max-[500px]:min-w-0">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                onclick="sortByColumn('name')">
                                Задача <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 max-[500px]:hidden"
                                onclick="sortByColumn('status')">
                                Статус <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 max-[500px]:hidden"
                                onclick="sortByColumn('priority')">
                                Приоритет <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider max-[500px]:hidden">
                                Отдел
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 max-[500px]:hidden"
                                onclick="sortByColumn('deadline')">
                                Дедлайн <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия
                            </th>
                        </tr>
                        </thead>
                        <tbody id="tasksTableBody" class="bg-white divide-y divide-gray-200">
                        @foreach($allTasks as $task)
                            <tr class="task-row hover:bg-gray-50" data-id="{{ $task->id }}" data-status="{{ $task->status }}"
                                data-department="{{ $task->department_id }}" data-name="{{ $task->name }}"
                                data-priority="{{ $task->priority }}"
                                data-deadline="{{ $task->deadline ? $task->deadline->timestamp : 9999999999 }}"
                                data-created-at="{{ $task->created_at->timestamp }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="javascript:void(0)" onclick="openTaskViewModal({{ $task->id }})"
                                           class="hover:text-blue-600">
                                            {{ $task->name }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($task->description, 60) }}</div>
                                </td>
                                <td class="px-6 py-4 max-[500px]:hidden">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->status === 'назначена' ? 'bg-blue-100 text-blue-800' : ($task->status === 'в работе' ? 'bg-purple-100 text-purple-800' : ($task->status === 'на проверке' ? 'bg-yellow-100 text-yellow-800' : ($task->status === 'выполнена' ? 'bg-green-100 text-green-800' : 'bg-red-400 text-white'))) }}">
                                            {{ $task->status }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 max-[500px]:hidden">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->priority === 'низкий' ? 'bg-gray-100 text-gray-800' : ($task->priority === 'средний' ? 'bg-blue-100 text-blue-800' : ($task->priority === 'высокий' ? 'bg-orange-100 text-orange-800' : ($task->priority === 'критический' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                            {{ $task->priority }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-[500px]:hidden">
                                    {{ $task->department->name ?? ($task->is_personal ? 'Личная' : '-') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-[500px]:hidden">
                                    @if($task->deadline)
                                        <span
                                            class="{{ $task->deadline->isPast() && $task->status !== 'выполнена' ? 'text-red-600 font-semibold' : '' }}">
                                                {{ $task->deadline->format('d.m.Y') }}
                                            </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium max-[500px]:text-center">
                                    <button onclick="openTaskViewModal({{ $task->id }})"
                                            class="text-green-600 hover:text-green-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="px-6 py-4">
                    {{ $allTasks->links() }}
                </div>
            </div>
        @endif
    </div>

    @include('partials.modal.task.show')

    @include('partials.modal.task.edit-modal')
 <!-- Модальное окно файлового менеджера -->
    @include('partials.modal.task.task-files-edit-modal')
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 max-[500px]:p-6">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Отказ от задачи</h3>
            <p class="text-gray-600 mb-4">Пожалуйста, укажите причину отказа от задачи:</p>
            <textarea id="rejectReason" placeholder="Причина отказа..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 h-24 resize-none"></textarea>
            <div class="flex space-x-3">
                <button onclick="submitRejection()" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                    Подтвердить отказ
                </button>
                <button onclick="closeRejectModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    Отмена
                </button>
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
                        class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                    Отправить на проверку
                </button>
                <button onclick="closeTimeModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    Отмена
                </button>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
 @vite('resources/js/pages/all-team-tasks.page.js')
    <script>
        let currentTaskId = null;
        let currentRows = [];

        // Переключение панели фильтров
        function toggleFilters() {
            const panel = document.getElementById('filtersPanel');
            const icon = document.getElementById('filterIcon');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                icon.classList.add('fa-chevron-up');
                icon.classList.remove('fa-chevron-down');
            } else {
                panel.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
        // Применить фильтры
        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const department = document.getElementById('departmentFilter').value;
            const search = document.getElementById('searchTask').value.toLowerCase();

            const rows = document.querySelectorAll('.task-row');
            let visible = 0;

            rows.forEach(row => {
                let show = true;

                if (status && row.dataset.status !== status) {
                    show = false;
                }

                if (department && row.dataset.department != department) {
                    show = false;
                }

                if (search) {
                    const taskName = row.querySelector('td:first-child .font-medium').textContent.toLowerCase();
                    const taskDesc = row.querySelector('td:first-child .text-gray-500').textContent.toLowerCase();
                    if (!taskName.includes(search) && !taskDesc.includes(search)) {
                        show = false;
                    }
                }

                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('visibleCount').textContent = visible;
        }

        // Сбросить фильтры
        function resetFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('departmentFilter').value = '';
            document.getElementById('searchTask').value = '';
            applyFilters();
        }

        // Фильтр по статусу из статистики
        function filterByStatus(status) {
            document.getElementById('statusFilter').value = status;
            applyFilters();
        }

        // Сортировка задач
        function sortTasks() {
            const sortBy = document.getElementById('sortBy').value;
            const tbody = document.getElementById('tasksTableBody');
            const rows = Array.from(document.querySelectorAll('.task-row'));

            rows.sort((a, b) => {
                let aVal, bVal;

                switch (sortBy) {
                    case 'created_at_desc':
                        aVal = parseInt(a.dataset.createdAt);
                        bVal = parseInt(b.dataset.createdAt);
                        return bVal - aVal;
                    case 'created_at_asc':
                        aVal = parseInt(a.dataset.createdAt);
                        bVal = parseInt(b.dataset.createdAt);
                        return aVal - bVal;
                    case 'deadline_asc':
                        aVal = parseInt(a.dataset.deadline);
                        bVal = parseInt(b.dataset.deadline);
                        return aVal - bVal;
                    case 'deadline_desc':
                        aVal = parseInt(a.dataset.deadline);
                        bVal = parseInt(b.dataset.deadline);
                        return bVal - aVal;
                    case 'name_asc':
                        aVal = a.dataset.name.toLowerCase();
                        bVal = b.dataset.name.toLowerCase();
                        return aVal.localeCompare(bVal);
                    case 'name_desc':
                        aVal = a.dataset.name.toLowerCase();
                        bVal = b.dataset.name.toLowerCase();
                        return bVal.localeCompare(aVal);
                    case 'status_asc':
                        const statusOrder = { 'назначена': 1, 'в работе': 2, 'на проверке': 3, 'выполнена': 4 };
                        aVal = statusOrder[a.dataset.status] || 5;
                        bVal = statusOrder[b.dataset.status] || 5;
                        return aVal - bVal;
                    case 'priority_desc':
                        const priorityOrder = { 'критический': 1, 'высокий': 2, 'средний': 3, 'низкий': 4 };
                        aVal = priorityOrder[a.dataset.priority] || 5;
                        bVal = priorityOrder[b.dataset.priority] || 5;
                        return aVal - bVal;
                    default:
                        return 0;
                }
            });

            rows.forEach(row => tbody.appendChild(row));
            applyFilters();
        }

        // Сортировка по клику на заголовок
        function sortByColumn(column) {
            const sortSelect = document.getElementById('sortBy');
            switch (column) {
                case 'name':
                    sortSelect.value = sortSelect.value === 'name_asc' ? 'name_desc' : 'name_asc';
                    break;
                case 'status':
                    sortSelect.value = 'status_asc';
                    break;
                case 'priority':
                    sortSelect.value = 'priority_desc';
                    break;
                case 'deadline':
                    sortSelect.value = sortSelect.value === 'deadline_asc' ? 'deadline_desc' : 'deadline_asc';
                    break;
            }
            sortTasks();
        }

        // Открыть модальное окно просмотра задачи
        async function openTaskViewModal(taskId) {
            const modal = document.getElementById('taskViewModal');
            const content = document.getElementById('taskModalContent');

            if (!modal || !content) {
                console.error('Модальное окно не найдено');
                return;
            }

            // Сохраняем ID задачи и текущий URL для возврата
            window.currentTaskId = taskId;
            window.taskId = taskId;
            window.returnUrl = window.location.pathname; // запоминаем текущую страницу

            // МЕНЯЕМ URL БЕЗ ПЕРЕЗАГРУЗКИ СТРАНИЦЫ
            const newUrl = `/team/tasks/${taskId}`;
            window.history.pushState({ taskId: taskId, modalOpen: true, returnUrl: window.returnUrl }, '', newUrl);

            // Показываем загрузчик
            content.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
            <p class="text-gray-500 mt-2">Загрузка задачи...</p>
        </div>
    `;

            // Показываем модальное окно
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

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const html = await response.text();
                content.innerHTML = html;

            } catch (error) {
                console.error('Ошибка:', error);
                content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                <p class="text-gray-500 mt-2">Не удалось загрузить задачу</p>
                <p class="text-sm text-gray-400 mt-1">${error.message}</p>
                <button onclick="openTaskViewModal(${taskId})"
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Повторить
                </button>
            </div>
        `;
            }
        }


        // Обрабатываем кнопку "Назад" в браузере
        window.addEventListener('popstate', function(event) {
            const modal = document.getElementById('taskViewModal');

            if (event.state && event.state.modalOpen) {
                if (modal && !modal.classList.contains('hidden')) {
                    closeTaskViewModal('/team/all-tasks');
                }
            } else if (modal && !modal.classList.contains('hidden')) {
                closeTaskViewModal('/team/all-tasks');
            }
        });

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('taskViewModal');
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeTaskViewModal('/team/all-tasks');
            }
        });

        // Закрытие по клику на фон
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('taskViewModal');
            if (e.target === modal) {
                closeTaskViewModal('/team/all-tasks');
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

        // При загрузке страницы проверяем, не открыта ли прямая ссылка
        document.addEventListener('DOMContentLoaded', function() {
            const match = window.location.pathname.match(/\/tasks\/page\/(\d+)/);
            if (match) {
                // Если открыта прямая ссылка на страницу задачи - ничего не делаем,
                // контроллер сам покажет полную страницу
                console.log('Прямая ссылка на задачу', match[1]);
            }
        });

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function (m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function (c) {
                return c;
            });
        }

        function getStatusColor(status) {
            const colors = {
                'не назначена': 'bg-gray-100 text-gray-800',
                'назначена': 'bg-blue-100 text-blue-800',
                'в работе': 'bg-purple-100 text-purple-800',
                'на проверке': 'bg-yellow-100 text-yellow-800',
                'выполнена': 'bg-green-100 text-green-800',
                'просрочена': 'bg-red-500 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getPriorityStyle(priority) {
            const styles = {
                'низкий': {
                    level: 1,
                    bg: 'bg-green-50',
                    border: 'border-green-200',
                    filled: 'bg-green-500',
                    empty: 'bg-green-200',
                    text: 'text-green-700'
                },
                'средний': {
                    level: 2,
                    bg: 'bg-blue-50',
                    border: 'border-blue-200',
                    filled: 'bg-blue-500',
                    empty: 'bg-blue-100',
                    text: 'text-blue-700'
                },
                'высокий': {
                    level: 3,
                    bg: 'bg-orange-50',
                    border: 'border-orange-200',
                    filled: 'bg-orange-500',
                    empty: 'bg-orange-100',
                    text: 'text-orange-700'
                },
                'критический': {
                    level: 4,
                    bg: 'bg-red-50',
                    border: 'border-red-200',
                    filled: 'bg-red-500',
                    empty: 'bg-red-100',
                    text: 'text-red-700'
                }
            };

            return styles[priority] || styles['средний'];
        }


        function formatDateTime(dateString) {
            if (!dateString) return 'Не указано';
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
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
                    closeTaskViewModal('/team/all-tasks');
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при обновлении статуса');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при обновлении статуса');
            }
        }

        function sendForReview(taskId) {
            currentTaskId = taskId;
            closeTaskViewModal('/team/all-tasks');
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
                    body: JSON.stringify({
                        status: 'на проверке',
                        actual_hours: actualHours
                    })
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
            closeTaskViewModal('/team/all-tasks');
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
        async function approveTask(taskId) {
            try {
                const response = await fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'выполнена'
                    })
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

        document.addEventListener('click', function (e) {
            if (e.target.id === 'taskViewModal') {
                closeTaskViewModal('/team/all-tasks');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeTaskViewModal('/team/all-tasks');
            }
        });
    </script>
    <script>
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
                        showNotification('Вы не можете редактировать эту задачу. Редактировать может только автор задачи или руководитель.','error');
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
                        window.taskEditState.selected = task.files;
                        console.log('Загружены файлы задачи11:',  window.taskEditState.selected.map(f => f.id));
                        window.updateTaskEditSelectedFilesDisplay();
                    } else {
                        window.updateTaskEditSelectedFilesDisplay();
                    }

                    document.getElementById('editTaskModal').classList.remove('hidden');
                } else {
                    showNotification(data.message || 'Ошибка при загрузке задачи','error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Ошибка при загрузке данных задачи','error');
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
    </script>
    <script>
         async function openEditFileManager() {
            console.log('openEditModal')
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                window.taskEditState.temp = [...window.taskEditState.selected];
                await loadEditFiles();
            }
        }

        async function loadEditFiles() {
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                    <p class="text-gray-500 mt-2">Загрузка файло1111в...</p>
                </div>`;
console.log('1')
            try {
                console.log('2')
                const response = await fetch('/tasks/file-storage/get-files', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                console.log('3')
                if (!response.ok) throw new Error('Ошибка загрузки');
                console.log('4')
                window.taskEditState.all = await response.json();
                console.log('5')
                window.window.taskEditState.all = window.taskEditState.all;
                renderEditFileManagerFiles(window.taskEditState.all);
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
                const isSelected = window.taskEditState.selected.some(f => f.id === file.id);
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

            updateEditFileManagerUI();
        }

        function toggleEditFileSelection(fileId) {
            const file = window.taskEditState.all.find(f => f.id === fileId);
            if (!file) return;

            const index = window.taskEditState.temp.findIndex(f => f.id === fileId);
            if (index === -1) {
                window.taskEditState.temp.push(file);
            } else {
                window.taskEditState.temp.splice(index, 1);
            }

            const fileCards = document.querySelectorAll('#fileManagerContent .file-card');
            fileCards.forEach(card => {
                const onclickAttr = card.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes(fileId.toString())) {
                    const isSelected = window.taskEditState.temp.some(f => f.id === fileId);
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

            const count = window.taskEditState.temp.length;

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

        window.confirmEditFileSelectionForEdit = function() {
            console.log('confirmFromMainAdmin')
            window.taskEditState.selected = [...window.taskEditState.temp];
            updateEditSelectedFilesDisplay();

            const fileManagerModal = document.getElementById('fileManagerModal');
            if (fileManagerModal) {
                fileManagerModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            showNotification(`Выбрано ${window.taskEditState.selected.length} файлов`, 'success');
        };

        function initEditFileManagerFilters() {
            const searchInput = document.getElementById('fileManagerSearch');
            const typeFilter = document.getElementById('fileManagerTypeFilter');
            const sortBy = document.getElementById('fileManagerSortBy');

            const filter = () => {
                if (!window.taskEditState.all) return;
                let filtered = [...window.taskEditState.all];
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
    </script>
@endpush

@push('styles')
    <style>
        .task-row:last-child td {
            border-bottom: none !important;
        }

        /* Если используется класс divide-y */
        .divide-y> :last-child {
            border-bottom-width: 0 !important;
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

        .hidden {
            display: none;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
@endpush
