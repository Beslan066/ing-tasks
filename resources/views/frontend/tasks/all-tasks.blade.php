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
            <div>
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Все задачи</h2>
                    <p class="text-white text-sm">Архив всех ваших задач</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Все задачи</h2>
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
            <div  class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 max-[500px]:grid-cols-1 max-[500px]:gap-2 max-[500px]:flex max-[500px]:overflow-x-auto max-[500px]:snap-x max-[500px]:snap-mandatory scrollbar-none">
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('')">
                    <div class="text-2xl font-bold text-white">{{ $stats['total'] }}</div>
                    <div class="text-sm text-white">Всего задач</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('назначена')">
                    <div class="text-2xl font-bold text-white">{{ $stats['new'] }}</div>
                    <div class="text-sm text-white">Новые</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('в работе')">
                    <div class="text-2xl font-bold text-white">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-white">В работе</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('на проверке')">
                    <div class="text-2xl font-bold text-white">{{ $stats['review'] }}</div>
                    <div class="text-sm text-white">На проверке</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('выполнена')">
                    <div class="text-2xl font-bold text-white">{{ $stats['done'] }}</div>
                    <div class="text-sm text-white">Завершено</div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 max-[500px]:grid-cols-1 max-[500px]:gap-2 max-[500px]:flex max-[500px]:overflow-x-auto max-[500px]:snap-x max-[500px]:snap-mandatory scrollbar-none">
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('')">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600">Всего задач</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('назначена')">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['new'] }}</div>
                    <div class="text-sm text-gray-600">Новые</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('в работе')">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-gray-600">В работе</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
                    onclick="filterByStatus('на проверке')">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['review'] }}</div>
                    <div class="text-sm text-gray-600">На проверке</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-col-reverse max-[500px]:justify-between max-[500px]:items-center max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[76px] shrink-0 snap-start max-[500px]:p-2"
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
                              class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700 transition">
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

    <!-- Модальные окна -->
    @include('partials.modal.task.show')
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
 <!-- МОДАЛЬНОЕ ОКНО РЕДАКТИРОВАНИЯ ЗАДАЧИ  -->
    <div id="editTaskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-md">
        <div class="bg-white modal-content rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
            <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-100">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent max-[500px]:text-xl">
                            Редактирование задачи
                        </h3>
                        <p class="text-sm text-gray-500 mt-1 max-[500px]:text-[12px]">Измените информацию о задаче</p>
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
                                <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300 max-[500px]:hidden">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                                </div>
                                <p class="text-base font-medium text-gray-700 mb-2">Нажмите или рперетащите файлы сюда</p>
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
    <script>
        let currentTaskId = null;
        let currentRows = [];

        // Инициализация
        document.addEventListener('DOMContentLoaded', function () {
            updateVisibleCount();
        });

        // Переключение панели фильтров
        function toggleFilters() {
                const dropdown = document.getElementById('filtersPanel');
                const chevron = document.getElementById('filterIcon');

                if (!dropdown || !chevron) return;

                if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    dropdown.classList.remove('fade-out-x');
                    dropdown.classList.add('fade-in-x');
                    chevron.style.transform = 'rotate(180deg)';
                    console.log('if')
                } else {
                    console.log('else')
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

        // ==================== ФУНКЦИЯ ДЛЯ ОТКРЫТИЯ МОДАЛЬНОГО ОКНА ====================
        // Открыть модальное окно просмотра задачи - эта функция уже написана в app.blade.php
    //     async function openTaskViewModal(taskId) {
    //         console.log('openTaskViewModal all-tasks.blade')
    //         const modal = document.getElementById('taskViewModal');
    //         const content = document.getElementById('taskModalContent');

    //         if (!modal || !content) {
    //             console.error('Модальное окно не найдено');
    //             return;
    //         }

    //         // Сохраняем ID задачи и текущий URL для возврата
    //         window.currentTaskId = taskId;
    //         window.taskId = taskId;
    //         window.returnUrl = window.location.pathname; // запоминаем текущую страницу

    //         // МЕНЯЕМ URL БЕЗ ПЕРЕЗАГРУЗКИ СТРАНИЦЫ
    //         const newUrl = `/team/tasks/${taskId}`;
    //         window.history.pushState({ taskId: taskId, modalOpen: true, returnUrl: window.returnUrl }, '', newUrl);

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

        // Закрыть модальное окно просмотра задачи - эта функция уже написана в app.blade.php
        function closeTaskViewModal() {
    console.log('closeTaskViewModal all-tasks.blade.php');
    const modal = document.getElementById('taskViewModal');
    const content = document.getElementById('taskModalContent');

    if (modal) {
        modal.classList.add('hidden');
        modal.style.backdropFilter = '';
    }

    if (content) {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                <p class="text-gray-500 mt-2">Загрузка задачи...</p>
            </div>
        `;
    }

    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = window.returnUrl || '/';
    }

    document.body.classList.remove('overflow-y-hidden');
}
        // Обрабатываем кнопку "Назад" в браузере
        window.addEventListener('popstate', function(event) {
            const modal = document.getElementById('taskViewModal');

            if (event.state && event.state.modalOpen) {
                if (modal && !modal.classList.contains('hidden')) {
                    closeTaskViewModal();
                }
            } else if (modal && !modal.classList.contains('hidden')) {
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

        // При загрузке страницы проверяем, не открыта ли прямая ссылка
        document.addEventListener('DOMContentLoaded', function() {
            const match = window.location.pathname.match(/\/tasks\/page\/(\d+)/);
            if (match) {
                // Если открыта прямая ссылка на страницу задачи - ничего не делаем,
                // контроллер сам покажет полную страницу
                console.log('Прямая ссылка на задачу', match[1]);
            }
        });


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

        function updateVisibleCount() {
            const visible = document.querySelectorAll('.task-row:not([style*="display: none"])').length;
            document.getElementById('visibleCount').textContent = visible;
        }

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
                    closeTaskViewModal();
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
            closeTaskViewModal();
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
            closeTaskViewModal();
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

        document.addEventListener('click', function (e) {
            if (e.target.id === 'taskViewModal') {
                closeTaskViewModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeTaskViewModal();
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
        .scrollbar-none::-webkit-scrollbar {
    display: none; /* Скрывает скроллбар в Chrome, Safari и Opera */
}
.scrollbar-none {
-ms-overflow-style: none;  /* Скрывает скроллбар в IE и Edge */
    scrollbar-width: none;
}
    </style>
@endpush
