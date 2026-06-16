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
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 max-[500px]:grid-cols-1 max-[500px]:gap-2">
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('')">
                    <div class="text-2xl font-bold text-white">{{ $stats['total'] }}</div>
                    <div class="text-sm text-white">Всего задач</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('назначена')">
                    <div class="text-2xl font-bold text-white">{{ $stats['new'] }}</div>
                    <div class="text-sm text-white">Новые</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('в работе')">
                    <div class="text-2xl font-bold text-white">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-white">В работе</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('на проверке')">
                    <div class="text-2xl font-bold text-white">{{ $stats['review'] }}</div>
                    <div class="text-sm text-white">На проверке</div>
                </div>
                <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('выполнена')">
                    <div class="text-2xl font-bold text-white">{{ $stats['done'] }}</div>
                    <div class="text-sm text-white">Завершено</div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 max-[500px]:grid-cols-1 max-[500px]:gap-2">
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('')">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600">Всего задач</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('назначена')">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['new'] }}</div>
                    <div class="text-sm text-gray-600">Новые</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('в работе')">
                    <div class="text-2xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-gray-600">В работе</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
                    onclick="filterByStatus('на проверке')">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['review'] }}</div>
                    <div class="text-sm text-gray-600">На проверке</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center hover:shadow-lg transition cursor-pointer max-[500px]:flex max-[500px]:flex-row-reverse max-[500px]:justify-between max-[500px]:items-center"
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
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
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

        // Закрыть модальное окно просмотра задачи
        function closeTaskViewModal() {
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

            // Возвращаемся на ту же страницу, откуда открыли модалку
            const returnUrl = window.returnUrl || window.location.pathname;
            window.history.pushState({}, '', returnUrl);
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
