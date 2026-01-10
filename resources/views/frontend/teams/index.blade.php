@extends('layouts.app')

@section('content')
    <div id="team">
        <!-- Заголовок и кнопка -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold" style="color: #16a34a;">Команда</h1>
                <p class="text-gray-500 text-sm md:text-base">Участники вашей организации</p>
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button id="toggleFilters" class="flex-1 md:flex-none bg-white border border-gray-300 text-gray-700 px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition text-sm md:text-base">
                    <i class="fas fa-filter"></i>
                    <span>Фильтр</span>
                    <i class="fas fa-chevron-down text-xs ml-1" id="filterArrow"></i>
                </button>
                <button class="flex-1 md:flex-none bg-primary text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-secondary transition text-sm md:text-base" id="newUserBtn">
                    <i class="fas fa-user-plus"></i>
                    <span>Пригласить</span>
                </button>
            </div>
        </div>

        <!-- Скрываемый блок фильтров -->
        <div id="filtersSection" class="bg-white rounded-lg shadow-sm md:shadow-md mb-4 md:mb-6 transition-all duration-300 overflow-hidden" style="max-height: 0; opacity: 0;">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input type="text" id="searchInput" placeholder="Поиск по имени или email..."
                           value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                    <select id="departmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 bg-transparent text-sm md:text-base">
                        <option value="">Все отделы</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }} class="focus:outline-none focus:ring-1 focus:ring-green-500">
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                    <select id="roleFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 bg-transparent text-sm md:text-base">
                        <option value="">Все роли</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Дополнительные фильтры -->
            <div id="advancedFilters" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата регистрации от</label>
                    <input type="date" id="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Дата регистрации до</label>
                    <input type="date" id="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Мин. % выполнения</label>
                    <input type="number" id="completionMin" min="0" max="100" placeholder="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 text-sm md:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Макс. % выполнения</label>
                    <input type="number" id="completionMax" min="0" max="100" placeholder="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 text-sm md:text-base">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 gap-3">
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                    <button id="applyFilters" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base">
                        <i class="fas fa-check"></i>
                        <span>Применить</span>
                    </button>
                    <button id="resetFilters" class="bg-gray-500 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-gray-600 transition flex items-center justify-center space-x-2 text-sm md:text-base">
                        <i class="fas fa-redo"></i>
                        <span>Сбросить</span>
                    </button>
                    <button id="toggleAdvancedFilters" class="bg-purple-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-purple-700 transition flex items-center justify-center space-x-2 text-sm md:text-base">
                        <i class="fas fa-cog"></i>
                        <span>Расширенные</span>
                        <i class="fas fa-chevron-down text-xs ml-1" id="advancedArrow"></i>
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    Найдено сотрудников: {{ $users->total() }}
                </div>
            </div>
        </div>

        <!-- Кнопки экспорта и печати -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
            <div class="text-base md:text-lg font-semibold text-gray-700">
                Список сотрудников
            </div>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <a href="{{ route('team.export-table', array_merge(request()->query(), ['format' => 'excel'])) }}"
                   class="flex-1 sm:flex-none bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base">
                    <i class="fas fa-file-excel"></i>
                    <span>Excel</span>
                </a>
                <a href="{{ route('team.export-table', array_merge(request()->query(), ['format' => 'pdf'])) }}"
                   class="flex-1 sm:flex-none bg-red-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-red-700 transition flex items-center justify-center space-x-2 text-sm md:text-base">
                    <i class="fas fa-file-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('team.print-table', request()->query()) }}"
                   target="_blank"
                   class="flex-1 sm:flex-none bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center space-x-2 text-sm md:text-base">
                    <i class="fas fa-print"></i>
                    <span>Печать</span>
                </a>
            </div>
        </div>

        <!-- Основной контейнер с таблицей -->
        <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
            <!-- Десктопный вид таблицы -->
            <div class="overflow-x-auto -mx-4 md:mx-0 hidden md:block">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => $currentSort == 'name' && $currentDirection == 'asc' ? 'desc' : 'asc']) }}"
                                       class="flex items-center space-x-1 hover:text-blue-600 {{ $currentSort == 'name' ? 'text-blue-600 font-semibold' : '' }}">
                                        <span>Пользователь</span>
                                        @if($currentSort == 'name')
                                            <span class="text-xs">{{ $currentDirection == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Роль</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Отдел</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Всего задач</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Выполнено</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% выполнения</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Просрочено</th>

                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $currentSort == 'created_at' && $currentDirection == 'asc' ? 'desc' : 'asc']) }}"
                                       class="flex items-center space-x-1 hover:text-blue-600 {{ $currentSort == 'created_at' ? 'text-blue-600 font-semibold' : '' }}">
                                        <span>Зарегистрирован</span>
                                        @if($currentSort == 'created_at')
                                            <span class="text-xs">{{ $currentDirection == 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($users) && $users->count() > 0)
                                @foreach($users as $user)
                                    @php
                                        $stats = $user->getTaskCompletionStats();
                                        $overdue = $user->assignedTasks()
                                            ->where('status', '!=', 'выполнена')
                                            ->where('deadline', '<', now())
                                            ->count();
                                    @endphp
                                    <tr class="bg-white border-b hover:bg-gray-50 transition user-row cursor-pointer" data-user-id="{{ $user->id }}">
                                        <td class="px-3 py-4">
                                            <div class="flex items-center space-x-3" >
                                                @if(isset($user->avatar))
                                                    <img src="{{$user->getAvatarUrlAttribute()}}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover" style="width:50px; height: 50px;">
                                                @else
                                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0" style="width:50px; height: 50px;">
                                                        <span class="text-gray-600 text-xs font-medium" >{{ substr($user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4">
                                            @if(isset($user->role))
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                    {{ $user->role->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">Не назначена</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-900">
                                            {{ $user->department->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-4 font-medium text-sm">{{ $stats['total'] }}</td>
                                        <td class="px-3 py-4 text-green-600 font-medium text-sm">{{ $stats['completed'] }}</td>
                                        <td class="px-3 py-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-12 bg-gray-200 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full {{ $stats['completion_rate'] >= 80 ? 'bg-green-600' : ($stats['completion_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-600') }}"
                                                         style="width: {{ min($stats['completion_rate'], 100) }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium {{ $stats['completion_rate'] >= 80 ? 'text-green-600' : ($stats['completion_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ $stats['completion_rate'] }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4">
                                            <span class="text-sm {{ $overdue > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                                {{ $overdue }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y') }}</td>
                                        <td class="px-3 py-4">
                                            <div class="flex items-center space-x-2">
                                                <button class="view-user-btn text-green-600 hover:text-green-800 p-1" data-user-id="{{ $user->id }}" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-800 p-1" title="Изменить">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))

                                                    <form action="{{ route('team.destroy', $user->id) }}"
                                                          method="post"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Вы уверены, что хотите удалить {{ $user->name }} из команды? Пользователю будет отправлено уведомление.')">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium text-gray-500">Сотрудники не найдены</p>
                                            <p class="text-gray-400 text-sm">Попробуйте изменить параметры фильтрации</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Мобильный вид (карточки) -->
            <div class="md:hidden space-y-4">
                @forelse($users as $user)
                    @php
                        $stats = $user->getTaskCompletionStats();
                        $overdue = $user->assignedTasks()
                            ->where('status', '!=', 'выполнена')
                            ->where('deadline', '<', now())
                            ->count();
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm user-row cursor-pointer" data-user-id="{{ $user->id }}">
                        <!-- Заголовок карточки -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center space-x-3">
                                @if(isset($user->avatar))
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                @else
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-1">
                                <button class="view-user-btn text-green-600 hover:text-green-800 p-1" data-user-id="{{ $user->id }}" title="Просмотр">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Информация в карточке -->
                        <div class="space-y-3">
                            <!-- Роль и отдел -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-gray-600 block mb-1">Роль:</span>
                                    @if(isset($user->role))
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Не назначена</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-xs text-gray-600 block mb-1">Отдел:</span>
                                    <span class="text-sm font-medium">{{ $user->department->name ?? '—' }}</span>
                                </div>
                            </div>

                            <!-- Статистика задач -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-gray-600 block mb-1">Всего задач:</span>
                                    <span class="text-sm font-medium">{{ $stats['total'] }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-600 block mb-1">Выполнено:</span>
                                    <span class="text-sm font-medium text-green-600">{{ $stats['completed'] }}</span>
                                </div>
                            </div>

                            <!-- Прогресс выполнения -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs text-gray-600">% выполнения:</span>
                                    <span class="text-xs font-medium {{ $stats['completion_rate'] >= 80 ? 'text-green-600' : ($stats['completion_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $stats['completion_rate'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $stats['completion_rate'] >= 80 ? 'bg-green-600' : ($stats['completion_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-600') }}"
                                         style="width: {{ min($stats['completion_rate'], 100) }}%"></div>
                                </div>
                            </div>

                            <!-- Просрочено и дата регистрации -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-gray-600 block mb-1">Просрочено:</span>
                                    <span class="text-sm font-medium {{ $overdue > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $overdue }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-600 block mb-1">Зарегистрирован:</span>
                                    <span class="text-sm">{{ $user->created_at->format('d.m.Y') }}</span>
                                </div>
                            </div>

                            <!-- Кнопки действий -->
                            <div class="flex justify-between pt-3 border-t">
                                <button class="text-gray-600 hover:text-gray-800 px-3 py-1 rounded-lg text-sm flex items-center space-x-1">
                                    <i class="fas fa-edit text-xs"></i>
                                    <span>Изменить</span>
                                </button>
                                <button class="text-red-600 hover:text-red-800 px-3 py-1 rounded-lg text-sm flex items-center space-x-1">
                                    <i class="fas fa-trash text-xs"></i>
                                    <span>Удалить</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium text-gray-500">Сотрудники не найдены</p>
                        <p class="text-gray-400 text-sm">Попробуйте изменить параметры фильтрации</p>
                    </div>
                @endforelse
            </div>

            <!-- Пагинация -->
            @if($users->hasPages())
                <div class="mt-4 md:mt-6">
                    {{ $users->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Модальное окно пользователя -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="p-4 md:p-6">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900">Детали пользователя</h3>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700 p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div id="modalContent">
                    <!-- Контент будет загружен через AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно приглашения -->
    <div id="inviteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-4 md:p-6">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900">Пригласить сотрудников</h3>
                    <button type="button" id="closeInviteModal" class="text-gray-500 hover:text-gray-700 p-1">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form id="inviteForm">
                    @csrf
                    <div class="space-y-4 md:space-y-6">
                        <!-- Поиск пользователей -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Поиск пользователей
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="userSearch"
                                    placeholder="Введите имя или email пользователя..."
                                    class="w-full px-3 py-2 md:px-4 md:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base"
                                    autocomplete="off"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Результаты поиска -->
                            <div id="searchResults" class="hidden mt-2 border border-gray-200 rounded-lg bg-white shadow-lg max-h-48 md:max-h-60 overflow-y-auto">
                                <!-- Результаты будут появляться здесь -->
                            </div>

                            <!-- Выбранные пользователи -->
                            <div id="selectedUsers" class="mt-3 space-y-2">
                                <!-- Выбранные пользователи будут появляться здесь -->
                            </div>
                        </div>

                        <!-- Роль и отдел -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">
                                    Роль
                                </label>
                                <select
                                    id="inviteRole"
                                    name="role_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base bg-white"
                                >
                                    <option value="">Выберите роль</option>
                                    @foreach($roles as $role)
                                        <option class="" value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">
                                    Отдел
                                </label>
                                <select
                                    id="inviteDepartment"
                                    name="department_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base bg-white"
                                >
                                    <option value="">Выберите отдел</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-4 md:mt-6 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            id="cancelInvite"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium text-sm md:text-base"
                        >
                            Отмена
                        </button>
                        <button
                            type="submit"
                            id="submitInvite"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-base"
                            disabled
                        >
                            <i class="fas fa-paper-plane"></i>
                            <span>Отправить приглашения</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Глобальные переменные для модального окна приглашения
        let selectedUsersData = new Map(); // email -> {email, name}
        let searchTimeout = null;

        // Функция для безопасной вставки данных в HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('userModal');
            const modalContent = document.getElementById('modalContent');
            const closeModal = document.getElementById('closeModal');

            // Управление фильтрами
            const filtersSection = document.getElementById('filtersSection');
            const toggleFiltersBtn = document.getElementById('toggleFilters');
            const filterArrow = document.getElementById('filterArrow');
            const advancedFilters = document.getElementById('advancedFilters');
            const toggleAdvancedFiltersBtn = document.getElementById('toggleAdvancedFilters');
            const advancedArrow = document.getElementById('advancedArrow');

            // Состояние фильтров (по умолчанию скрыто)
            let filtersVisible = localStorage.getItem('teamFiltersVisible') === 'true';
            let advancedFiltersVisible = localStorage.getItem('teamAdvancedFiltersVisible') === 'true';

            // Инициализация состояния фильтров
            function initFilters() {
                if (filtersVisible) {
                    showFilters();
                } else {
                    hideFilters();
                }

                if (advancedFiltersVisible) {
                    showAdvancedFilters();
                } else {
                    hideAdvancedFilters();
                }
            }

            function showFilters() {
                filtersSection.style.maxHeight = filtersSection.scrollHeight + 'px';
                filtersSection.style.opacity = '1';
                filterArrow.className = 'fas fa-chevron-up text-xs ml-1';
                toggleFiltersBtn.innerHTML = `
                    <i class="fas fa-filter"></i>
                    <span>Скрыть фильтры</span>
                    <i class="fas fa-chevron-up text-xs ml-1" id="filterArrow"></i>
                `;
                filtersVisible = true;
                localStorage.setItem('teamFiltersVisible', 'true');
            }

            function hideFilters() {
                filtersSection.style.maxHeight = '0';
                filtersSection.style.opacity = '0';
                filterArrow.className = 'fas fa-chevron-down text-xs ml-1';
                toggleFiltersBtn.innerHTML = `
                    <i class="fas fa-filter"></i>
                    <span>Фильтр</span>
                    <i class="fas fa-chevron-down text-xs ml-1" id="filterArrow"></i>
                `;
                filtersVisible = false;
                localStorage.setItem('teamFiltersVisible', 'false');

                // Скрываем расширенные фильтры при скрытии основных
                if (advancedFiltersVisible) {
                    hideAdvancedFilters();
                }
            }

            function showAdvancedFilters() {
                advancedFilters.classList.remove('hidden');
                advancedArrow.className = 'fas fa-chevron-up text-xs ml-1';
                toggleAdvancedFiltersBtn.innerHTML = `
                    <i class="fas fa-cog"></i>
                    <span>Скрыть расширенные</span>
                    <i class="fas fa-chevron-up text-xs ml-1" id="advancedArrow"></i>
                `;
                advancedFiltersVisible = true;
                localStorage.setItem('teamAdvancedFiltersVisible', 'true');

                // Обновляем высоту основного блока фильтров
                setTimeout(() => {
                    if (filtersVisible) {
                        filtersSection.style.maxHeight = filtersSection.scrollHeight + 'px';
                    }
                }, 10);
            }

            function hideAdvancedFilters() {
                advancedFilters.classList.add('hidden');
                advancedArrow.className = 'fas fa-chevron-down text-xs ml-1';
                toggleAdvancedFiltersBtn.innerHTML = `
                    <i class="fas fa-cog"></i>
                    <span>Расширенные</span>
                    <i class="fas fa-chevron-down text-xs ml-1" id="advancedArrow"></i>
                `;
                advancedFiltersVisible = false;
                localStorage.setItem('teamAdvancedFiltersVisible', 'false');

                // Обновляем высоту основного блока фильтров
                setTimeout(() => {
                    if (filtersVisible) {
                        filtersSection.style.maxHeight = filtersSection.scrollHeight + 'px';
                    }
                }, 10);
            }

            // Обработчики кнопок фильтров
            toggleFiltersBtn.addEventListener('click', function() {
                if (filtersVisible) {
                    hideFilters();
                } else {
                    showFilters();
                }
            });

            toggleAdvancedFiltersBtn.addEventListener('click', function() {
                if (advancedFiltersVisible) {
                    hideAdvancedFilters();
                } else {
                    showAdvancedFilters();
                }
            });

            // Инициализация при загрузке
            initFilters();

            // Обработчики для открытия модального окна
            document.querySelectorAll('.view-user-btn, .user-row').forEach(element => {
                element.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' || e.target.closest('a')) {
                        return;
                    }
                    const userId = this.dataset.userId;
                    loadUserDetails(userId);
                });
            });

            // Закрытие модального окна
            closeModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            // Закрытие по клику вне модального окна
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });

            function loadUserDetails(userId) {
                modalContent.innerHTML = `
                    <div class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-gray-600">Загрузка данных...</span>
                    </div>
                `;

                modal.classList.remove('hidden');

                fetch(`/team/user/${userId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            renderUserModal(data);
                        } else {
                            throw new Error(data.error || 'Unknown error');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading user details:', error);
                        modalContent.innerHTML = `
                            <div class="text-center py-8">
                                <div class="text-red-600 text-xl mb-4">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <p class="text-red-600 font-semibold">Ошибка загрузки данных</p>
                                <p class="text-gray-600 mt-2">${error.message}</p>
                                <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition" onclick="loadUserDetails(${userId})">
                                    Попробовать снова
                                </button>
                            </div>
                        `;
                    });
            }

            function renderUserModal(data) {
                const user = data.user;
                const stats = data.stats;

                modalContent.innerHTML = `
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Левая колонка - Информация о пользователе -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="text-center mb-6">
                    ${user.avatar_url && user.avatar_url !== user.defaultAvatarUrl ?
                    `<img src="${user.avatar_url}" alt="${user.name}" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">` :
                    `<div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #16a34a">
                            <span class="text-white text-2xl font-bold">${user.name.charAt(0)}</span>
                        </div>`
                }
                    <h4 class="text-xl font-bold text-gray-900">${user.name}</h4>
                    <p class="text-gray-600">${user.email}</p>
                    <div class="mt-2">
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            ${user.role ? user.role.name : 'Роль не назначена'}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Отдел:</label>
                        <p class="text-gray-900">${user.department ? user.department.name : 'Не назначен'}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Зарегистрирован:</label>
                        <p class="text-gray-900">${new Date(user.created_at).toLocaleDateString('ru-RU')}</p>
                    </div>
                    ${user.last_login_at ? `
                        <div>
                            <label class="text-sm font-medium text-gray-600">Последний вход:</label>
                            <p class="text-gray-900">${new Date(user.last_login_at).toLocaleString('ru-RU')}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>

        <!-- Правая колонка - Статистика -->
        <div class="lg:col-span-2">
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-4">Статистика выполнения задач</h4>

                <!-- Фильтры периода -->
                <div class="flex space-x-2 mb-4">
                    <button class="period-filter-btn px-3 py-1 text-white rounded text-sm" data-period="week" style="background-color: #16a34a">Неделя</button>
                    <button class="period-filter-btn px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm" data-period="month">Месяц</button>
                    <button class="period-filter-btn px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm" data-period="year">Год</button>
                    <button class="period-filter-btn px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm" data-period="all">Все время</button>
                </div>

                <!-- Карточки статистики -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">${stats.total_tasks}</div>
                        <div class="text-sm text-gray-600">Всего задач</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">${stats.completed_tasks}</div>
                        <div class="text-sm text-gray-600">Выполнено</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold ${getCompletionRateColor(data.completion_rate)}">${data.completion_rate}%</div>
                        <div class="text-sm text-gray-600">Средний % выполнения</div>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold ${stats.overdue_tasks > 0 ? 'text-red-600' : 'text-green-600'}">${stats.overdue_tasks}</div>
                        <div class="text-sm text-gray-600">Просрочено</div>
                    </div>
                </div>

                <!-- Прогресс бар -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-gray-700">Средний процент выполнения задач</span>
                        <span class="font-bold ${getCompletionRateColor(data.completion_rate)}">${data.completion_rate}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500 ${getCompletionRateColor(data.completion_rate).replace('text-', 'bg-')}"
                             style="width: ${Math.min(data.completion_rate, 100)}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>0%</span>
                        <span>50%</span>
                        <span>100%</span>
                    </div>
                </div>
            </div>

            <!-- Кнопки экспорта и печати -->
            <div class="flex space-x-2 mb-6">
                <a href="/team/user/${user.id}/export?type=excel&period=all"
                   class="export-btn px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2">
                    <i class="fas fa-file-excel"></i>
                    <span>Excel</span>
                </a>
                <a href="/team/user/${user.id}/export?type=pdf&period=all"
                   class="export-btn px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center space-x-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="/team/user/${user.id}/print"
                   target="_blank"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                    <i class="fas fa-print"></i>
                    <span>Печать</span>
                </a>
            </div>

            <!-- Список задач -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Задачи</h4>
                <div id="userTasksList">
                    <div class="flex justify-center items-center py-4">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <span class="ml-2 text-gray-600">Загрузка задач...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;

                loadUserTasks(user.id);

                modalContent.querySelectorAll('.period-filter-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        modalContent.querySelectorAll('.period-filter-btn').forEach(b => {
                            b.classList.remove('bg-blue-600', 'text-white');
                            b.classList.add('bg-gray-200', 'text-gray-700');
                        });
                        this.classList.remove('bg-gray-200', 'text-gray-700');
                        this.classList.add('bg-blue-600', 'text-white');

                        loadUserTasks(user.id, this.dataset.period);
                    });
                });
            }

            function loadUserTasks(userId, period = 'month') {
                const tasksList = document.getElementById('userTasksList');

                fetch(`/team/user/${userId}/tasks?period=${period}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            let tasksHtml = '';

                            if (data.period_completion_rate !== undefined) {
                                tasksHtml += `
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium text-blue-800">Статистика за период:</span>
                                            <span class="font-bold ${getCompletionRateColor(data.period_completion_rate)}">
                                                ${data.period_completion_rate}% выполнения
                                            </span>
                                        </div>
                                    </div>
                                `;
                            }

                            if (data.tasks.length === 0) {
                                tasksHtml += '<p class="text-gray-500 text-center py-4">Задачи не найдены</p>';
                            } else {
                                tasksHtml += '<div class="space-y-3 max-h-96 overflow-y-auto">';
                                data.tasks.forEach(task => {
                                    const statusColors = {
                                        'выполнена': 'bg-green-100 text-green-800',
                                        'в работе': 'bg-blue-100 text-blue-800',
                                        'просрочена': 'bg-red-100 text-red-800',
                                        'не назначена': 'bg-gray-100 text-gray-800'
                                    };

                                    tasksHtml += `
                                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h5 class="font-semibold text-gray-900">${task.name}</h5>
                                                <span class="px-2 py-1 ${statusColors[task.status] || 'bg-gray-100'} rounded-full text-xs">
                                                    ${task.status}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 mb-2">
                                                ${task.description ? task.description.substring(0, 100) + '...' : 'Описание отсутствует'}
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Создана: ${new Date(task.created_at).toLocaleDateString('ru-RU')}</span>
                                                ${task.deadline ? `<span>Дедлайн: ${new Date(task.deadline).toLocaleDateString('ru-RU')}</span>` : ''}
                                            </div>
                                        </div>
                                    `;
                                });
                                tasksHtml += '</div>';
                            }

                            tasksList.innerHTML = tasksHtml;
                        } else {
                            throw new Error(data.error || 'Unknown error');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading tasks:', error);
                        tasksList.innerHTML = `
                            <div class="text-center py-4">
                                <p class="text-red-600">Ошибка загрузки задач</p>
                                <p class="text-gray-600 text-sm mt-1">${error.message}</p>
                            </div>
                        `;
                    });
            }

            function getCompletionRateColor(rate) {
                if (rate >= 80) return 'text-green-600';
                if (rate >= 60) return 'text-yellow-600';
                if (rate >= 40) return 'text-orange-600';
                return 'text-red-600';
            }

            // Фильтрация таблицы
            const applyFiltersBtn = document.getElementById('applyFilters');
            const resetFiltersBtn = document.getElementById('resetFilters');

            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener('click', function() {
                    applyFilters();
                });
            }

            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', function() {
                    resetFilters();
                });
            }

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const department = document.getElementById('departmentFilter').value;
                const role = document.getElementById('roleFilter').value;
                const status = document.getElementById('statusFilter').value;
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;
                const completionMin = document.getElementById('completionMin').value;
                const completionMax = document.getElementById('completionMax').value;

                let url = new URL(window.location.href);

                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');

                if (department) url.searchParams.set('department', department);
                else url.searchParams.delete('department');

                if (role) url.searchParams.set('role', role);
                else url.searchParams.delete('role');

                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');

                if (dateFrom) url.searchParams.set('date_from', dateFrom);
                else url.searchParams.delete('date_from');

                if (dateTo) url.searchParams.set('date_to', dateTo);
                else url.searchParams.delete('date_to');

                if (completionMin) url.searchParams.set('completion_min', completionMin);
                else url.searchParams.delete('completion_min');

                if (completionMax) url.searchParams.set('completion_max', completionMax);
                else url.searchParams.delete('completion_max');

                // Сбрасываем пагинацию при применении фильтров
                url.searchParams.delete('page');

                window.location.href = url.toString();
            }

            function resetFilters() {
                // Сбрасываем все поля фильтров
                document.getElementById('searchInput').value = '';
                document.getElementById('departmentFilter').value = '';
                document.getElementById('roleFilter').value = '';
                document.getElementById('statusFilter').value = '';
                document.getElementById('dateFrom').value = '';
                document.getElementById('dateTo').value = '';
                document.getElementById('completionMin').value = '';
                document.getElementById('completionMax').value = '';

                // Переходим на страницу без параметров
                window.location.href = window.location.pathname;
            }

            // Автопоиск при вводе (с задержкой)
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        applyFilters();
                    }, 800);
                });
            }
        });

        // Обработка модального окна приглашения
        const inviteModal = document.getElementById('inviteModal');
        const closeInviteModal = document.getElementById('closeInviteModal');
        const cancelInvite = document.getElementById('cancelInvite');
        const inviteForm = document.getElementById('inviteForm');
        const userSearch = document.getElementById('userSearch');
        const searchResults = document.getElementById('searchResults');
        const selectedUsersElement = document.getElementById('selectedUsers');
        const submitInvite = document.getElementById('submitInvite');

        // Функция выбора пользователя
        function selectUser(email, name) {
            if (selectedUsersData.has(email)) {
                return;
            }

            selectedUsersData.set(email, {
                email: email,
                name: name
            });

            updateSelectedUsers();
            userSearch.value = '';
            hideSearchResults();
            updateSubmitButton();
        }

        // Функция удаления выбранного пользователя
        function removeSelectedUser(email) {
            selectedUsersData.delete(email);
            updateSelectedUsers();
            updateSubmitButton();
        }

        // Функция обновления списка выбранных пользователей
        function updateSelectedUsers() {
            if (selectedUsersData.size === 0) {
                selectedUsersElement.innerHTML = '<div class="text-gray-500 text-sm italic">Пользователи не выбраны</div>';
                return;
            }

            let html = '<div class="text-sm font-medium text-gray-700 mb-2">Выбранные пользователи:</div>';

            selectedUsersData.forEach((user) => {
                const displayName = user.name || user.email.split('@')[0];
                const safeEmail = escapeHtml(user.email);
                const safeName = escapeHtml(displayName);

                html += `
                    <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-blue-900 truncate">${safeName}</div>
                            <div class="text-sm text-blue-600 truncate">${safeEmail}</div>
                        </div>
                        <button type="button" onclick="removeSelectedUser('${safeEmail.replace(/'/g, "\\'")}')"
                                class="flex-shrink-0 text-red-500 hover:text-red-700 ml-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            });

            selectedUsersElement.innerHTML = html;
        }

        // Функция обновления состояния кнопки отправки
        function updateSubmitButton() {
            if (submitInvite) {
                submitInvite.disabled = selectedUsersData.size === 0;
            }
        }

        // Функция для показа результатов поиска
        function displaySearchResults(users) {
            if (users.length === 0) {
                searchResults.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-search mb-2"></i>
                        <p>Пользователи не найдены</p>
                    </div>
                `;
                searchResults.classList.remove('hidden');
                return;
            }

            let html = '';

            users.forEach(user => {
                const isDisabled = user.status !== 'can_invite';
                const statusText = user.status === 'already_member' ? 'Уже в организации' :
                    user.status === 'already_invited' ? 'Уже приглашен' : '';

                const safeEmail = escapeHtml(user.email);
                const safeName = escapeHtml(user.name);

                html += `
                    <div class="p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 ${isDisabled ? 'opacity-60' : 'cursor-pointer'}"
                         onclick="${isDisabled ? '' : `selectUser('${safeEmail.replace(/'/g, "\\'")}', '${safeName.replace(/'/g, "\\'")}')`}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium text-gray-900">${safeName}</div>
                                <div class="text-sm text-gray-600">${safeEmail}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                ${statusText ? `<span class="text-xs px-2 py-1 rounded-full ${user.status === 'already_member' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${statusText}</span>` : ''}
                                ${!isDisabled ? `<i class="fas fa-plus text-green-500"></i>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            searchResults.innerHTML = html;
            searchResults.classList.remove('hidden');
        }

        // Функция скрытия результатов поиска
        function hideSearchResults() {
            searchResults.classList.add('hidden');
        }

        // Функция поиска пользователей
        function searchUsers(searchTerm) {
            const csrfToken = getCsrfToken();

            const url = new URL('/team/invitations/search', window.location.origin);
            url.searchParams.append('search', searchTerm);

            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        displaySearchResults(data.users);
                    } else {
                        hideSearchResults();
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    hideSearchResults();
                });
        }

        // Проверяем существование элементов перед добавлением обработчиков
        if (inviteModal && closeInviteModal && cancelInvite && inviteForm && userSearch) {
            // Открытие модального окна приглашения
            const newUserBtn = document.getElementById('newUserBtn');
            if (newUserBtn) {
                newUserBtn.addEventListener('click', function() {
                    inviteModal.classList.remove('hidden');
                    userSearch.focus();
                    resetForm();
                });
            }

            // Закрытие модального окна
            closeInviteModal.addEventListener('click', function() {
                inviteModal.classList.add('hidden');
                resetForm();
            });

            cancelInvite.addEventListener('click', function() {
                inviteModal.classList.add('hidden');
                resetForm();
            });

            // Поиск пользователей
            userSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (searchTerm.length < 2) {
                    hideSearchResults();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    searchUsers(searchTerm);
                }, 300);
            });

            // Скрытие результатов при клике вне области
            document.addEventListener('click', function(e) {
                if (!searchResults.contains(e.target) && e.target !== userSearch) {
                    hideSearchResults();
                }
            });

            // Обработка формы приглашения
            inviteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (selectedUsersData.size === 0) {
                    showNotification('error', 'Выберите хотя бы одного пользователя');
                    return;
                }

                const formData = new FormData();
                const emailsArray = Array.from(selectedUsersData.keys());
                formData.append('emails', emailsArray.join(','));

                const roleId = document.getElementById('inviteRole').value;
                const departmentId = document.getElementById('inviteDepartment').value;

                if (roleId) formData.append('role_id', roleId);
                if (departmentId) formData.append('department_id', departmentId);

                // Добавляем CSRF токен
                const csrfToken = getCsrfToken();
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }

                const originalText = submitInvite.innerHTML;

                // Показываем индикатор загрузки
                submitInvite.innerHTML = `
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    <span>Отправка...</span>
                `;
                submitInvite.disabled = true;

                fetch('/team/invite', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`HTTP ${response.status}: ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);

                        if (data.success) {
                            showNotification('success', data.message || 'Приглашения отправлены!');

                            if (data.warning) {
                                showNotification('warning', data.warning);
                            }
                            if (data.info) {
                                showNotification('info', data.info);
                            }

                            setTimeout(() => {
                                inviteModal.classList.add('hidden');
                                resetForm();
                            }, 1000);
                        } else {
                            showNotification('error', data.error || 'Произошла ошибка при отправке приглашений');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        try {
                            const errorData = JSON.parse(error.message.split(':')[1]);
                            if (errorData.message) {
                                showNotification('error', errorData.message);
                                return;
                            }
                        } catch(e) {
                            showNotification('error', 'Произошла ошибка при отправке приглашений');
                        }
                    })
                    .finally(() => {
                        submitInvite.innerHTML = originalText;
                        submitInvite.disabled = selectedUsersData.size === 0;
                    });
            });
        }

        // Функция сброса формы
        function resetForm() {
            selectedUsersData.clear();
            userSearch.value = '';
            if (selectedUsersElement) {
                selectedUsersElement.innerHTML = '';
            }
            if (searchResults) {
                searchResults.classList.add('hidden');
            }
            if (document.getElementById('inviteRole')) {
                document.getElementById('inviteRole').value = '';
            }
            if (document.getElementById('inviteDepartment')) {
                document.getElementById('inviteDepartment').value = '';
            }
            updateSubmitButton();
        }

        // Безопасное получение CSRF токена
        function getCsrfToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                return metaTag.getAttribute('content');
            }

            const csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput) {
                return csrfInput.value;
            }

            const allInputs = document.querySelectorAll('input[name="_token"]');
            if (allInputs.length > 0) {
                return allInputs[0].value;
            }

            const forms = document.querySelectorAll('form');
            for (let form of forms) {
                const input = form.querySelector('input[name="_token"]');
                if (input) {
                    return input.value;
                }
            }

            console.warn('CSRF token not found');
            return '';
        }

        // Функция для показа уведомлений
        function showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' :
                        type === 'warning' ? 'bg-yellow-500 text-white' :
                            'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-${
                type === 'success' ? 'check-circle' :
                    type === 'error' ? 'exclamation-circle' :
                        type === 'warning' ? 'exclamation-triangle' :
                            'info-circle'
            }"></i>
                    <span>${escapeHtml(message)}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }
    </script>


    <style>
        /* Анимации для плавного скрытия/показа */
        #filtersSection {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .min-w-0 {
            min-width: 0;
        }

        /* Стили для модальных окон на мобильных */
        @media (max-width: 768px) {
            #userModal > div,
            #inviteModal > div {
                width: 100%;
                max-height: 90vh;
            }

            .overflow-y-auto {
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
@endpush
