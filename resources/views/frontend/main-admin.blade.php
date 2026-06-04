@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
        $company = auth()->check() ? auth()->user()->company : null;
    @endphp

        <!-- Страница статистики компании -->
    <div id="company-stats">

        <!-- ИНФОРМАЦИЯ О КОМПАНИИ И КНОПКА УЛУЧШЕНИЯ -->
        @if($company)
            <div class="mb-6 md:mb-8">
                @if($backgroundEnabled && $backgroundImage)
                    <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-building-columns text-3xl text-white"></i>
                                    <h2 class="text-2xl md:text-3xl font-bold text-white">{{ $company->name }}</h2>
                                    @if($company->verified)
                                        <i class="fas fa-check-circle text-blue-400 text-xl" title="Верифицирована"></i>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-3 text-sm text-white/80">
                                    @if($company->phone)
                                        <span><i class="fas fa-phone mr-1"></i> {{ $company->phone }}</span>
                                    @endif
                                    <span><i class="fas fa-users mr-1"></i> Сотрудников: {{ $company->getActiveUsersCount() }}</span>
                                    <span><i class="fas fa-tasks mr-1"></i> Всего задач: {{ $company->getTasksCount() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/20">
                            <div class="flex flex-wrap justify-between items-center gap-3">
                                <div>
                                    <span class="text-sm text-white/70">Тарифный план:</span>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold
                                        @if($company->license_type === 'premium') bg-gradient-to-r from-purple-500 to-pink-500 text-white
                                        @elseif($company->license_type === 'optimal') bg-blue-500 text-white
                                        @else bg-gray-600 text-white @endif">
                                        {{ $company->getLicenseTypeName() }}
                                    </span>
                                </div>
                                <div class="text-sm text-white/70">
                                    <i class="fas fa-database mr-1"></i>
                                    Хранилище:
                                    @php
                                        $usedBytes = $company->getStorageStats()['used'] ?? 0;
                                        $limitBytes = $company->getStorageLimit();
                                        $usedGB = round($usedBytes / 1073741824, 2);
                                        $limitGB = $company->license_type === 'premium' ? 1024 : 2;
                                    @endphp
                                    @if($usedGB < 1)
                                        {{ round($usedBytes / 1048576, 2) }} МБ / {{ $limitGB }} ГБ
                                    @else
                                        {{ number_format($usedGB, 2) }} ГБ / {{ $limitGB }} ГБ
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 border border-gray-100">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-building text-3xl text-green-600"></i>
                                    <h2 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $company->name }}</h2>
                                    @if($company->verified)
                                        <i class="fas fa-check-circle text-blue-500 text-xl" title="Верифицирована"></i>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                    @if($company->phone)
                                        <span><i
                                                class="fas fa-phone mr-1 text-green-500"></i> {{ $company->phone }}</span>
                                    @endif
                                    <span><i class="fas fa-users mr-1 text-green-500"></i> Сотрудников: {{ $company->getActiveUsersCount() }}</span>
                                    <span><i class="fas fa-tasks mr-1 text-green-500"></i> Всего задач: {{ $company->getTasksCount() }}</span>
                                </div>
                            </div>
                            <div>
                                @if($company->license_type !== 'premium')
                                    <button onclick="openUpgradeModal()"
                                            class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-2 px-4 md:py-3 md:px-6 rounded-lg shadow-lg transition duration-300 transform hover:scale-105 flex items-center gap-2 text-sm md:text-base">
                                        <i class="fas fa-crown"></i>
                                        <span>Улучшить подписку</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                @else
                                    <span
                                        class="bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-2 px-4 md:py-3 md:px-6 rounded-lg shadow-lg inline-flex items-center gap-2 text-sm md:text-base">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Премиум</span>
                                        <i class="fas fa-star"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap justify-between items-center gap-3">
                                <div>
                                    <span class="text-sm text-gray-600">Тарифный план:</span>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold
                                        @if($company->license_type === 'premium') bg-gradient-to-r from-purple-500 to-pink-500 text-white
                                        @elseif($company->license_type === 'optimal') bg-blue-500 text-white
                                        @else bg-gray-500 text-white @endif">
                                        {{ $company->getLicenseTypeName() }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-database mr-1 text-green-500"></i>
                                    Хранилище:
                                    @php
                                        $usedBytes = $company->getStorageStats()['used'] ?? 0;
                                        $limitBytes = $company->getStorageLimit();
                                        $usedGB = round($usedBytes / 1073741824, 2);
                                        $limitGB = $company->license_type === 'premium' ? 1024 : 2;
                                    @endphp
                                    @if($usedGB < 1)
                                        {{ round($usedBytes / 1048576, 2) }} МБ / {{ $limitGB }} ГБ
                                    @else
                                        {{ number_format($usedGB, 2) }} ГБ / {{ $limitGB }} ГБ
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Модальное окно улучшения до Премиум -->
        <div id="upgradeModal"
             class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 backdrop-blur-md">
            <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md mx-4">
                <div class="text-center mb-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-crown text-3xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Улучшение до Премиум</h3>
                    <p class="text-gray-600 text-sm">
                        Вы уверены, что хотите перейти на тариф <strong class="text-yellow-600">Премиум</strong>?
                    </p>
                </div>

                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-gem text-purple-600"></i>
                        <span class="font-semibold text-gray-800">Преимущества Премиум:</span>
                    </div>
                    <ul class="text-sm text-gray-700 space-y-1 ml-6">
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> 1 ТБ хранилища</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Файлы до 1 ГБ</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Приоритетная поддержка</li>
                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Расширенная аналитика</li>
                    </ul>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('company.upgrade-license') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="new_license_type" value="premium">
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-crown"></i>
                            <span>Да, улучшить</span>
                        </button>
                    </form>
                    <button onclick="closeUpgradeModal()"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Отмена
                    </button>
                </div>
            </div>
        </div>

        <!-- Заголовок и кнопка -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
            <div>
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Задачи компании</h2>
                    <p class="text-white text-sm">Обзор производительности и задач</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Задачи компании</h2>
                    <p class="text-gray-700 text-sm">Обзор производительности и задач</p>
                @endif
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                @if($backgroundEnabled && $backgroundImage)
                    <button id="filterToggle"
                            class="flex-1 md:flex-none bg-transparent/20 border-none text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 transition text-sm md:text-base">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform"></i>
                    </button>
                @else
                    <button id="filterToggle"
                            class="flex-1 md:flex-none bg-white border border-gray-300 text-gray-700 px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition text-sm md:text-base">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform"></i>
                    </button>
                @endif
                <button id="newTaskBtn"
                        class="flex-1 md:flex-none bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:from-green-700 hover:to-green-600 transition text-sm md:text-base">
                    <i class="fas fa-plus"></i>
                    <span>Новая задача</span>
                </button>
            </div>
        </div>


        <!-- Фильтры и поиск -->
        @if($backgroundEnabled && $backgroundImage)
            <div id="filtersPanel"
                 class="backdrop-blur-md bg-transparent/20 rounded-lg border-gray-200 hidden mb-[20px]">
                <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <form method="GET" action="{{ route('tasks.admin') }}"
                          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
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
                                @foreach($filterData['users'] as $user)
                                    <option class="text-gray-800"
                                            value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
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
                        <div
                            class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                            <button type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm md:text-base">
                                Применить фильтры
                            </button>
                            <a href="{{ route('tasks.admin') }}"
                               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center text-sm md:text-base">
                                Сбросить
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div id="filtersPanel" class="bg-white rounded-lg border-gray-200 hidden mb-[20px]">
                <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <form method="GET" action="{{ route('tasks.admin') }}"
                          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
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
                                <option value="" class="appearance-none">Все статусы</option>
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
                                @foreach($filterData['users'] as $user)
                                    <option
                                        value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
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
                                    <option
                                        value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
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
                                    <option
                                        value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
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
                                    <option
                                        value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Кнопки фильтра -->
                        <div
                            class="sm:col-span-2 lg:col-span-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                            <button type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm md:text-base">
                                Применить фильтры
                            </button>
                            <a href="{{ route('tasks.admin') }}"
                               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center text-sm md:text-base">
                                Сбросить
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Статистика в виде карточек -->
        @if($backgroundEnabled && $backgroundImage)
            <div
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 min-[1370px]:grid-cols-4 min-[1600px]:grid-cols-6 gap-3 md:gap-6 mb-6 md:mb-8">
                <!-- Всего задач -->
                <div
                    class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <a href="{{route('allTeamTasks')}}">
                                <h3 class="font-bold text-sm md:text-lg text-white">Всего задач</h3>
                            </a>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tasks text-blue-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['total'] }}</div>
                        <div class="text-white text-md underline">
                            <a href="{{route('allTeamTasks')}}">Все задачи</a>
                        </div>
                    </div>
                </div>

                <!-- Назначены -->
                <div
                    class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-white">Назначены</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-check text-purple-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['assigned'] }}</div>
                </div>

                <!-- В работе -->
                <div
                    class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-white">В работе</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cogs text-white text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['in_progress'] }}</div>
                </div>

                <!-- На проверке -->
                <div
                    class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-white">На проверке</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-search text-yellow-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['review'] }}</div>
                </div>

                <!-- Выполнено -->
                <div
                    class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-white">Выполнено</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['completed'] }}</div>
                </div>

                <!-- Просрочено -->
                <div
                    class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-white">Просрочено</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['overdue'] }}</div>
                </div>
            </div>
        @else
            <div
                class="grid grid-cols-1 min-[550px]:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 min-[1370px]:grid-cols-4 min-[1600px]:grid-cols-6 gap-3 md:gap-6 mb-6 md:mb-8">
                <!-- Всего задач -->
                <div
                    class=" bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-gray-800">Всего задач</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tasks text-blue-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-3xl font-bold" style="color: #16a34a;">{{ $stats['total'] }}</div>
                </div>

                <!-- Назначены -->
                <div
                    class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-gray-800">Назначены</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-check text-purple-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-3xl font-bold text-purple-600">{{ $stats['assigned'] }}</div>
                </div>

                <!-- В работе -->
                <div
                    class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-gray-800">В работе</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cogs text-orange-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-3xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
                </div>

                <!-- На проверке -->
                <div
                    class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-gray-800">На проверке</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-search text-yellow-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-3xl font-bold text-yellow-600">{{ $stats['review'] }}</div>
                </div>

                <!-- Выполнено -->
                <div
                    class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-gray-800">Выполнено</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                </div>

                <!-- Просрочено -->
                <div
                    class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
                    <div
                        class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                        <div>
                            <h3 class="font-bold text-sm md:text-lg text-gray-800">Просрочено</h3>
                        </div>
                        <div
                            class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600 text-sm md:text-xl"></i>
                        </div>
                    </div>
                    <div class="text-xl md:text-3xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                </div>
            </div>
        @endif

        <!-- Таблица задач -->
        @if($backgroundEnabled && $backgroundImage)
            <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                    <div class="text-gray-500 text-sm md:text-base">
                        Показано {{ $tasks->count() }} из {{ $tasks->total() }} задач
                    </div>
                    <div class="w-full sm:w-auto">
                        <select id="sortSelect"
                                class="w-full  sm:w-48 border-none rounded-lg px-3 py-2 text-white focus:outline-none backdrop-blur-md bg-transparent/20">
                            <option class="text-gray-800" value="created_at_desc">Новые сначала</option>
                            <option class="text-gray-800" value="created_at_asc">Старые сначала</option>
                            <option class="text-gray-800" value="deadline_asc">Ближайший дедлайн</option>
                            <option class="text-gray-800" value="deadline_desc">Дальний дедлайн</option>
                            <option class="text-gray-800" value="priority_desc">Высокий приоритет</option>
                            <option class="text-gray-800" value="name_asc">По названию (А-Я)</option>
                        </select>
                    </div>
                </div>

                <!-- Адаптивная таблица -->
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <!-- Десктопный вид таблицы -->
                            <table class="min-w-full  hidden md:table">
                                <thead class="bg-transparent/20">
                                <tr class="border-none">
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Задача
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Статус
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Исполнитель
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Отдел
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Приоритет
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Автор
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Дедлайн
                                    </th>
                                    <th
                                        class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Действия
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-transparent/10">
                                @forelse($tasks as $task)
                                    <tr
                                        class="hover:bg-gray-50 transition text-white  hover:text-gray-900 @if($task->trashed()) bg-red-50 border-l-4 border-red-400 @endif">
                                        <td class="px-3 py-4 cursor-pointer   hover:text-gray-900"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                            <div class="flex items-start ">
                                                <div class="ml-2 hover:text-gray-900">
                                                    <div class="text-sm font-medium flex items-center flex-wrap gap-1">
                                                        <span class="truncate max-w-[250px]">{{ $task->name }}</span>
                                                        @if($task->trashed())
                                                            <span
                                                                class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full whitespace-nowrap">
                                                                    <i class="fas fa-trash mr-1"></i>Удалена
                                                                </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @if($task->category)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[{{$task->category->color}}] text-white">
                                                                    {{ $task->category->name }}
                                                                </span>
                                                        @endif
                                                        @if($task->rejections_count > 0)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                                title="Количество отказов: {{ $task->rejections_count }}">
                                                                    <i class="fas fa-user-slash mr-1"></i>
                                                                    {{ $task->rejections_count }}
                                                                </span>
                                                        @endif
                                                        @if($task->trashed() && $task->deletedBy)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                                title="Удалил: {{ $task->deletedBy->name }}">
                                                                    <i class="fas fa-user-times mr-1"></i>
                                                                    Удалил: {{ $task->deletedBy->name }}
                                                                </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 cursor-pointer whitespace-nowrap"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                            @if($task->trashed())
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Удалена
                                                    </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $task->status === 'в работе' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $task->status === 'не назначена' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $task->status === 'просрочена' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $task->status === 'на проверке' ? 'bg-orange-100 text-orange-800' : '' }}">

                                                        {{ $task->status }}
                                                    </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 cursor-pointer"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                            @if($task->user)
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                        {{ substr($task->user->name, 0, 2) }}
                                                    </div>
                                                    <div class="ml-2">
                                                        <div class="text-sm font-medium truncate max-w-[100px]">
                                                            {{ $task->user->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 truncate max-w-[100px]">
                                                            {{ $task->user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">Не назначен</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 cursor-pointer whitespace-nowrap text-sm text-gray-500"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                            {{ $task->department->name ?? ($task->is_personal ? 'Личная задача' : 'Без отдела') }}
                                        </td>
                                        <td class="px-3 py-4 cursor-pointer whitespace-nowrap"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
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
                                                <div
                                                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}">
                                                    <div class="flex items-end gap-[3px] h-5">
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"></div>
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"></div>
                                                    </div>
                                                    <span
                                                        class="text-xs font-medium {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 cursor-pointer whitespace-nowrap"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                            @if($task->author)
                                                <div
                                                    class="text-sm font-medium truncate max-w-[100px]">{{ $task->author->name }}
                                                </div>
                                            @else
                                                <span class="text-sm">Нет автора</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 cursor-pointer"
                                            onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                            @if($task->deadline && !$task->trashed())
                                                <div
                                                    class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }} text-sm">
                                                    {{ $task->deadline->format('d.m.Y H:i') }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $task->getTimeRemaining() }}
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap">
                                            @if($task->trashed())
                                                <span class="text-gray-400 text-sm">Действия недоступны</span>
                                            @else
                                                <div class="flex space-x-2">
                                                    <button onclick="openEditModal({{ $task->id }})"
                                                            class="text-yellow-700 hover:text-yellow-900 p-1"
                                                            title="Редактировать">
                                                        <i class="fa-solid fa-file-pen"></i>
                                                    </button>
                                                    @if($task->status === 'на проверке')
                                                        <button onclick="returnToWork({{ $task->id }})"
                                                                class="text-orange-600 hover:text-orange-900 p-1 text-sm"
                                                                title="Вернуть на доработку">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                    @if($task->author_id === Auth::id())
                                                        <button onclick="openDeleteModal({{ $task->id }})"
                                                                class="text-red-600 hover:text-red-900 p-1"
                                                                title="Удалить">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button class="text-gray-400 cursor-not-allowed p-1"
                                                                title="Можно удалять только свои задачи">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Задачи не найдены
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>

                            <!-- Мобильный вид таблицы (карточки) -->
                            <div class="md:hidden space-y-3">
                                @forelse($tasks as $task)
                                    <div
                                        class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition @if($task->trashed()) border-l-4 border-l-red-400 bg-red-50 @endif"
                                        onclick="if(!event.target.closest('.action-buttons-mobile')) openTaskViewModal({{ $task->id }})">
                                        <!-- Заголовок карточки -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                                    <h3 class="font-semibold text-gray-900 truncate">{{ $task->name }}</h3>
                                                    @if($task->trashed())
                                                        <span
                                                            class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                                            <i class="fas fa-trash mr-1"></i>Удалена
                                                        </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="text-sm text-gray-600 mb-2 line-clamp-2 max-[500px]:!hidden">
                                                    {{ $task->description }}
                                                </div>
                                            </div>
                                            <div class="flex space-x-1">
                                                @if(!$task->trashed())
                                                    <button onclick="openEditModal({{ $task->id }})"
                                                            class="text-yellow-700 hover:text-yellow-900 p-1"
                                                            title="Редактировать">
                                                        <i class="fa-solid fa-file-pen"></i>
                                                    </button>
                                                    @if($task->author_id === Auth::id())
                                                        <button onclick="openDeleteModal({{ $task->id }})"
                                                                class="text-red-600 hover:text-red-900 p-1"
                                                                title="Удалить">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Информация в карточке -->
                                        <div class="space-y-2">
                                            <!-- Статус -->
                                            <div class="flex items-center gap-6 max-[500px]:hidden">
                                                <span class="text-sm text-gray-600">Статус:</span>
                                                @if($task->trashed())
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Удалена
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $task->status === 'выполнена' ? 'bg-green-100 text-green-800' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $task->status === 'в работе' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $task->status === 'не назначена' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $task->status === 'просрочена' ? 'bg-red-100 text-red-800' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $task->status === 'на проверке' ? 'bg-orange-100 text-orange-800' : '' }}">
                                                        {{ $task->status }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Исполнитель -->
                                            <div class="flex items-center gap-2 max-[500px]:hidden">
                                                <span class="text-sm text-gray-600">Исполнитель:</span>
                                                @if($task->user)
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium mr-2">
                                                            {{ substr($task->user->name, 0, 2) }}
                                                        </div>
                                                        <span class="text-sm font-medium">{{ $task->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Не назначен</span>
                                                @endif
                                            </div>

                                            <!-- Отдел и Приоритет -->
                                            <div class="grid grid-cols-2 gap-2 max-[500px]:grid-cols-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-gray-600">Отдел:</span>
                                                    <span class="text-sm">{{ $task->department->name ?? '—' }}</span>
                                                </div>
                                                @php
                                                    $priorityStyles = [
                                                        'низкий' => [
                                                            'level' => 1,
                                                            'color' => 'gray',
                                                            'bg' => 'bg-gray-50',
                                                            'border' => 'border-gray-200',
                                                            'filled' => 'bg-gray-500',
                                                            'empty' => 'bg-gray-200',
                                                            'text' => 'text-gray-700'
                                                        ],
                                                        'средний' => [
                                                            'level' => 2,
                                                            'color' => 'blue',
                                                            'bg' => 'bg-blue-50',
                                                            'border' => 'border-blue-200',
                                                            'filled' => 'bg-blue-500',
                                                            'empty' => 'bg-blue-100',
                                                            'text' => 'text-blue-700'
                                                        ],
                                                        'высокий' => [
                                                            'level' => 3,
                                                            'color' => 'orange',
                                                            'bg' => 'bg-orange-50',
                                                            'border' => 'border-orange-200',
                                                            'filled' => 'bg-orange-500',
                                                            'empty' => 'bg-orange-100',
                                                            'text' => 'text-orange-700'
                                                        ],
                                                        'критический' => [
                                                            'level' => 4,
                                                            'color' => 'red',
                                                            'bg' => 'bg-red-50',
                                                            'border' => 'border-red-200',
                                                            'filled' => 'bg-red-500',
                                                            'empty' => 'bg-red-100',
                                                            'text' => 'text-red-700'
                                                        ],
                                                    ];
                                                    $style = $priorityStyles[$task->priority] ?? $priorityStyles['средний'];
                                                @endphp

                                                <div
                                                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md {{ $style['bg'] }} border {{ $style['border'] }}">
                                                    <div class="flex items-end gap-[3px] h-5">
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $style['level'] >= 1 ? $style['filled'] : $style['empty'] }} h-2"></div>
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $style['level'] >= 2 ? $style['filled'] : $style['empty'] }} h-3"></div>
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $style['level'] >= 3 ? $style['filled'] : $style['empty'] }} h-4"></div>
                                                        <div
                                                            class="w-1.5 rounded-sm {{ $style['level'] >= 4 ? $style['filled'] : $style['empty'] }} h-5"></div>
                                                    </div>
                                                    <span
                                                        class="text-xs font-medium {{ $style['text'] }}">{{ ucfirst($task->priority) }}</span>
                                                </div>
                                            </div>

                                            <!-- Автор и Дедлайн -->
                                            <div class="grid grid-cols-2 gap-2 max-[500px]:grid-cols-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-gray-600">Автор:</span>
                                                    <span
                                                        class="text-sm truncate">{{ $task->author->name ?? '—' }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-gray-600">Дедлайн:</span>
                                                    @if($task->deadline && !$task->trashed())
                                                        <div
                                                            class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                                            <div
                                                                class="text-sm">{{ $task->deadline->format('d.m.Y') }}</div>
                                                            <div
                                                                class="text-xs text-gray-400">{{ $task->getTimeRemaining() }}</div>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-sm">—</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Дополнительная информация -->
                                            <div class="flex flex-wrap gap-1 pt-2 border-t max-[500px]:!hidden">
                                                @if($task->category)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $task->category->name }}
                                                    </span>
                                                @endif
                                                @if($task->rejections_count > 0)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                        title="Количество отказов: {{ $task->rejections_count }}">
                                                        <i class="fas fa-user-slash mr-1"></i>
                                                        {{ $task->rejections_count }}
                                                    </span>
                                                @endif
                                                @if($task->trashed() && $task->deletedBy)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                        title="Удалил: {{ $task->deletedBy->name }}">
                                                        <i class="fas fa-user-times mr-1"></i>
                                                        Удалил: {{ $task->deletedBy->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Кнопка "Вернуть" для мобильных -->
                                            @if(!$task->trashed() && $task->status === 'на проверке')
                                                <div class="pt-2 border-t">
                                                    <button onclick="returnToWork({{ $task->id }})"
                                                            class="w-full bg-orange-100 text-orange-800 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-200 transition flex items-center justify-center space-x-2">
                                                        <i class="fas fa-redo"></i>
                                                        <span>Вернуть на доработку</span>
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

                <!-- Пагинация -->
                @if($tasks->hasPages())
                    <div class="mt-4 md:mt-6">
                        {{ $tasks->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                    <div class="text-gray-500 text-sm md:text-base">
                        Показано {{ $tasks->count() }} из {{ $tasks->total() }} задач
                    </div>
                    <div class="w-full sm:w-auto">
                        <select id="sortSelect"
                                class="w-full sm:w-48 border-none rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-600 text-sm md:text-base">
                            <option value="created_at_desc">Новые сначала</option>
                            <option value="created_at_asc">Старые сначала</option>
                            <option value="deadline_asc">Ближайший дедлайн</option>
                            <option value="deadline_desc">Дальний дедлайн</option>
                            <option value="priority_desc">Высокий приоритет</option>
                            <option value="name_asc">По названию (А-Я)</option>
                        </select>
                    </div>
                </div>

                <!-- Адаптивная таблица -->
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">

                            <!-- Десктопный вид таблицы -->
                            <div class="hidden md:block">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Задача
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Статус
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Исполнитель
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Отдел
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Приоритет
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Автор
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Дедлайн
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Действия
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($tasks as $task)
                                        <tr class="hover:bg-gray-50 transition @if($task->trashed()) bg-red-50 border-l-4 border-red-400 @endif">
                                            <td class="px-3 py-4">
                                                <div class="flex items-start cursor-pointer">
                                                    <div class="ml-2"
                                                         onclick="if(!event.target.closest('.action-buttons')) openTaskViewModal({{ $task->id }})">
                                                        <div
                                                            class="text-sm font-medium text-gray-900 flex items-center flex-wrap gap-1">
                                                            <span
                                                                class="truncate max-w-[150px]">{{ $task->name }}</span>
                                                            @if($task->trashed())
                                                                <span
                                                                    class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full whitespace-nowrap">
                                                        <i class="fas fa-trash mr-1"></i>Удалена
                                                    </span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-500 truncate max-w-[200px] mt-1">
                                                            {{ $task->description }}
                                                        </div>
                                                        <div class="flex flex-wrap gap-1 mt-2">
                                                            @if($task->category)
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[{{$task->category->color}}] text-white">
                                                        {{ $task->category->name }}
                                                    </span>
                                                            @endif
                                                            @if($task->rejections_count > 0)
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                                    title="Количество отказов: {{ $task->rejections_count }}">
                                                        <i class="fas fa-user-slash mr-1"></i>
                                                        {{ $task->rejections_count }}
                                                    </span>
                                                            @endif
                                                            @if($task->trashed() && $task->deletedBy)
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                                    title="Удалил: {{ $task->deletedBy->name }}">
                                                        <i class="fas fa-user-times mr-1"></i>
                                                        Удалил: {{ $task->deletedBy->name }}
                                                    </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                @if($task->trashed())
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Удалена
                                        </span>
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
                                                        <div
                                                            class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                            {{ substr($task->user->name, 0, 2) }}
                                                        </div>
                                                        <div class="ml-2">
                                                            <div
                                                                class="text-sm font-medium text-gray-900 truncate max-w-[100px]">
                                                                {{ $task->user->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 truncate max-w-[100px]">
                                                                {{ $task->user->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Не назначен</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $task->department->name ?? ($task->is_personal ? 'Личная задача' : 'Без отдела') }}
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                @php
                                                    $priorityColors = [
                                                        'низкий' => 'bg-gray-100 text-gray-800',
                                                        'средний' => 'bg-blue-100 text-blue-800',
                                                        'высокий' => 'bg-orange-100 text-orange-800',
                                                        'критический' => 'bg-red-100 text-red-800'
                                                    ];
                                                @endphp
                                                @if(!$task->trashed())
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $task->priority }}
                                        </span>
                                                @else
                                                    <span class="text-sm text-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                @if($task->author)
                                                    <div
                                                        class="text-sm font-medium text-gray-900 truncate max-w-[100px]">
                                                        {{ $task->author->name }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Нет автора</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4">
                                                @if($task->deadline && !$task->trashed())
                                                    <div
                                                        class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }} text-sm">
                                                        {{ $task->deadline->format('d.m.Y H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $task->getTimeRemaining() }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-sm">—</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                @if($task->trashed())
                                                    <span class="text-gray-400 text-sm">Действия недоступны</span>
                                                @else
                                                    <div class="flex space-x-2">
                                                        <button onclick="openEditModal({{ $task->id }})"
                                                                class="text-yellow-700 hover:text-yellow-900 p-1"
                                                                title="Редактировать">
                                                            <i class="fa-solid fa-file-pen"></i>
                                                        </button>
                                                        @if($task->status === 'на проверке')
                                                            <button onclick="returnToWork({{ $task->id }})"
                                                                    class="text-orange-600 hover:text-orange-900 p-1 text-sm"
                                                                    title="Вернуть на доработку">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        @endif
                                                        @if($task->author_id === Auth::id())
                                                            <button onclick="openDeleteModal({{ $task->id }})"
                                                                    class="text-red-600 hover:text-red-900 p-1"
                                                                    title="Удалить">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        @else
                                                            <button class="text-gray-400 cursor-not-allowed p-1"
                                                                    title="Можно удалять только свои задачи">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Задачи не найдены
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Мобильный вид таблицы (карточки) -->
                            <div class="md:hidden space-y-3 p-4">
                                @forelse($tasks as $task)
                                    <div
                                        class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm @if($task->trashed()) border-l-4 border-l-red-400 bg-red-50 @endif">
                                        <!-- Заголовок карточки -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center flex-wrap gap-2 mb-1">
                                                    <h3 class="font-semibold text-gray-900 truncate">{{ $task->name }}</h3>
                                                    @if($task->trashed())
                                                        <span
                                                            class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                            <i class="fas fa-trash mr-1"></i>Удалена
                                        </span>
                                                    @endif
                                                </div>
                                                <div
                                                    class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $task->description }}</div>
                                            </div>
                                            <div class="flex space-x-1">
                                                @if(!$task->trashed())
                                                    <button onclick="openEditModal({{ $task->id }})"
                                                            class="text-yellow-700 hover:text-yellow-900 p-1"
                                                            title="Редактировать">
                                                        <i class="fa-solid fa-file-pen"></i>
                                                    </button>
                                                    @if($task->author_id === Auth::id())
                                                        <button onclick="openDeleteModal({{ $task->id }})"
                                                                class="text-red-600 hover:text-red-900 p-1"
                                                                title="Удалить">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Информация в карточке -->
                                        <div class="space-y-2">
                                            <!-- Статус -->
                                            <div class="flex items-center">
                                                <span class="text-sm text-gray-600 w-20">Статус:</span>
                                                @if($task->trashed())
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Удалена
                                    </span>
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

                                            <!-- Исполнитель -->
                                            <div class="flex items-center max-[500px]:gap-1">
                                                <span
                                                    class="text-sm text-gray-600 w-20 max-[500px]:w-auto">Исполнитель:</span>
                                                @if($task->user)
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-medium mr-2">
                                                            {{ substr($task->user->name, 0, 2) }}
                                                        </div>
                                                        <span class="text-sm font-medium">{{ $task->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Не назначен</span>
                                                @endif
                                            </div>

                                            <!-- Отдел и Приоритет -->
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-center">
                                                    <span class="text-sm text-gray-600 w-16">Отдел:</span>
                                                    <span
                                                        class="text-sm">{{ $task->department->name ?? ($task->is_personal ? 'Личная задача' : '—') }}</span>
                                                </div>
                                                <div class="flex items-center max-[500px]:gap-1">
                                                    <span class="text-sm text-gray-600 w-16 max-[500px]:w-auto">Приоритет:</span>
                                                    @if(!$task->trashed())
                                                        @php
                                                            $priorityColors = [
                                                                'низкий' => 'bg-gray-100 text-gray-800',
                                                                'средний' => 'bg-blue-100 text-blue-800',
                                                                'высокий' => 'bg-orange-100 text-orange-800',
                                                                'критический' => 'bg-red-100 text-red-800'
                                                            ];
                                                        @endphp
                                                        <span
                                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $task->priority }}
                                        </span>
                                                    @else
                                                        <span class="text-sm text-gray-400">—</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Автор и Дедлайн -->
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-center">
                                                    <span class="text-sm text-gray-600 w-16">Автор:</span>
                                                    <span
                                                        class="text-sm truncate">{{ $task->author->name ?? '—' }}</span>
                                                </div>
                                                <div class="flex items-center max-[500px]:gap-2">
                                                    <span
                                                        class="text-sm text-gray-600 w-16 max-[500px]:w-auto">Дедлайн:</span>
                                                    @if($task->deadline && !$task->trashed())
                                                        <div
                                                            class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                                            <div
                                                                class="text-sm">{{ $task->deadline->format('d.m.Y') }}</div>
                                                            <div
                                                                class="text-xs text-gray-400">{{ $task->getTimeRemaining() }}</div>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-sm">—</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Дополнительная информация -->
                                            <div class="flex flex-wrap gap-1 pt-2 border-t">
                                                @if($task->category)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[{{$task->category->color}}] text-white">
                                        {{ $task->category->name }}
                                    </span>
                                                @endif
                                                @if($task->rejections_count > 0)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                        title="Количество отказов: {{ $task->rejections_count }}">
                                        <i class="fas fa-user-slash mr-1"></i>
                                        {{ $task->rejections_count }}
                                    </span>
                                                @endif
                                                @if($task->trashed() && $task->deletedBy)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                        title="Удалил: {{ $task->deletedBy->name }}">
                                        <i class="fas fa-user-times mr-1"></i>
                                        Удалил: {{ $task->deletedBy->name }}
                                    </span>
                                                @endif
                                            </div>

                                            <!-- Кнопка "Вернуть" для мобильных -->
                                            @if(!$task->trashed() && $task->status === 'на проверке')
                                                <div class="pt-2 border-t">
                                                    <button onclick="returnToWork({{ $task->id }})"
                                                            class="w-full bg-orange-100 text-orange-800 px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-200 transition flex items-center justify-center space-x-2">
                                                        <i class="fas fa-redo"></i>
                                                        <span>Вернуть на доработку</span>
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

                <!-- Пагинация -->
                @if($tasks->hasPages())
                    <div class="mt-4 md:mt-6">
                        {{ $tasks->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Модальное окно редактирования задачи -->
    <div id="editTaskModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-md">
        <div
            class="bg-white modal-content rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
            <!-- Заголовок -->
            <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-100">
                <div class="flex justify-between items-center p-6">
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                            Редактирование задачи
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Измените информацию о задаче</p>
                    </div>
                    <button onclick="closeEditModal()"
                            class="text-gray-400 hover:text-gray-600 transition-all duration-200 p-2 rounded-xl hover:bg-gray-100 hover:scale-110">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Форма -->
            <form id="editTaskForm" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="selected_file_ids" id="editSelectedFiles" value="[]">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Название задачи -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-tag text-green-500 mr-2 text-xs"></i>Название задачи *
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tasks text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="text" name="name" id="editTaskName"
                                   class="w-full pl-10 pr-4 py-3 border-2 outline-none border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400 hover:border-gray-300"
                                   placeholder="Введите название задачи" required>
                        </div>
                    </div>

                    <!-- Описание -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-align-left text-green-500 mr-2 text-xs"></i>Описание
                        </label>
                        <textarea name="description" id="editTaskDescription" rows="4"
                                  class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 resize-none bg-white placeholder-gray-400"
                                  placeholder="Подробное описание задачи..."></textarea>
                    </div>

                    <!-- Отдел -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-building text-green-500 mr-2 text-xs"></i>Отдел *
                        </label>
                        <div class="relative group">
                            <select name="department_id" id="editTaskDepartment"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl outline-none focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                    required>
                                <option value="">Выберите отдел</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Категория -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-folder text-green-500 mr-2 text-xs"></i>Категория
                        </label>
                        <div class="relative group">
                            <select name="category_id" id="editTaskCategory"
                                    class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                                <option value="">Без категории</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Исполнитель -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-user-check text-green-500 mr-2 text-xs"></i>Исполнитель
                        </label>
                        <div class="relative group">
                            <select name="user_id" id="editTaskUser"
                                    class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                                <option value="">Не назначен</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Приоритет -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-flag text-green-500 mr-2 text-xs"></i>Приоритет *
                        </label>
                        <div class="relative group">
                            <select name="priority" id="editTaskPriority"
                                    class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                    required>
                                <option value="низкий">Низкий</option>
                                <option value="средний">Средний</option>
                                <option value="высокий">Высокий</option>
                                <option value="критический">Критический</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Статус -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-chart-line text-green-500 mr-2 text-xs"></i>Статус *
                        </label>
                        <div class="relative group">
                            <select name="status" id="editTaskStatus"
                                    class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                    required>
                                <option value="не назначена">Не назначена</option>
                                <option value="назначена">Назначена</option>
                                <option value="в работе">В работе</option>
                                <option value="на проверке">На проверке</option>
                                <option value="выполнена">Выполнена</option>
                                <option value="просрочена">Просрочена</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Дедлайн -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-calendar-alt text-green-500 mr-2 text-xs"></i>Дедлайн
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="datetime-local" name="deadline" id="editTaskDeadline"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white cursor-pointer hover:border-gray-300">
                        </div>
                    </div>

                    <!-- Планируемое время -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-hourglass-half text-green-500 mr-2 text-xs"></i>Планируемое время (часы)
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-stopwatch text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="number" name="estimated_hours" id="editTaskEstimatedHours" step="0.5" min="0"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400"
                                   placeholder="0.0">
                            <span
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm">часов</span>
                        </div>
                    </div>

                    <!-- Фактическое время -->
                    <div class="space-y-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>Фактическое время (часы)
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-chart-simple text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                            </div>
                            <input type="number" name="actual_hours" id="editTaskActualHours" step="0.5" min="0"
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400"
                                   placeholder="0.0">
                            <span
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm">часов</span>
                        </div>
                    </div>

                    <!-- Вкладки для файлов (как в создании) -->
                    <div class="md:col-span-2 space-y-4">
                        <div class="border-b border-gray-200">
                            <nav class="flex space-x-6" aria-label="Tabs">
                                <button type="button"
                                        onclick="switchEditFileTab('storage')"
                                        id="editStorageTab"
                                        class="py-2 px-1 border-b-2 outline-none font-medium text-sm focus:outline-none tab-button active transition-all duration-200"
                                        data-tab="storage">
                                    <i class="fas fa-database mr-2"></i>Из хранилища
                                </button>
                                <button type="button"
                                        onclick="switchEditFileTab('upload')"
                                        id="editUploadTab"
                                        class="py-2 px-1 border-b-2 outline-none font-medium text-sm focus:outline-none tab-button transition-all duration-200"
                                        data-tab="upload">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Новая загрузка
                                </button>
                            </nav>
                        </div>

                        <!-- Контейнер для файлов из хранилища -->
                        <div id="editStorageTabContent" class="tab-content active">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700">Выберите файлы из хранилища</h4>
                                    <p class="text-xs text-gray-500 mt-1">Файлы будут прикреплены к задаче</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" onclick="openEditFileManager()"
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
                                <div
                                    class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
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
                                <p class="text-xs text-gray-500 mt-1">Файлы будут сохранены в хранилище и прикреплены к
                                    задаче</p>
                            </div>

                            <div
                                class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 bg-gradient-to-br from-gray-50 to-white hover:from-green-50 hover:to-white cursor-pointer group"
                                onclick="document.getElementById('editUploadNewFilesInput').click()">
                                <input type="file" name="new_files[]" multiple class="hidden"
                                       id="editUploadNewFilesInput">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                                    </div>
                                    <p class="text-base font-medium text-gray-700 mb-2">Нажмите или перетащите файлы
                                        сюда</p>
                                    <p class="text-sm text-gray-500">Поддерживаются: PDF, DOC, DOCX, XLS, XLSX, JPG,
                                        PNG, GIF, ZIP</p>
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

                    <!-- История отказов -->
                    <div class="md:col-span-2 space-y-3">
                        <label class="block text-gray-700 text-sm font-semibold mb-1">
                            <i class="fas fa-history text-green-500 mr-2 text-xs"></i>История отказов от задачи
                            <span id="editRejectionsCount"
                                  class="bg-gradient-to-r from-red-400 to-red-500 text-white text-xs px-2 py-1 rounded-full ml-2 shadow-sm">0</span>
                        </label>
                        <div id="editRejectionsList"
                             class="space-y-3 max-h-60 overflow-y-auto border-2 border-gray-200 rounded-xl p-4 bg-gray-50 custom-scrollbar">
                            <div class="text-center py-8">
                                <i class="fas fa-check-circle text-3xl text-gray-300 mb-2"></i>
                                <p class="text-gray-500">Отказов нет</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex justify-end space-x-3 pt-6 mt-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()"
                            class="px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-300 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
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


    <!-- Модальное окно возврата на доработку -->
    <div id="returnToWorkModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 backdrop-blur-md">
        <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-3">Возврат задачи на доработку</h3>
            <p class="text-gray-600 mb-3">Укажите комментарий для исполнителя:</p>
            <textarea id="returnComment" placeholder="Комментарий..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 h-24 resize-none text-sm md:text-base"></textarea>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <button onclick="confirmReturnToWork()"
                        class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 text-sm md:text-base">
                    Вернуть на доработку
                </button>
                <button onclick="closeReturnModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 text-sm md:text-base">
                    Отмена
                </button>
            </div>
        </div>
    </div>

    <!-- Модальное окно удаления задачи -->
    <div id="deleteTaskModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 backdrop-blur-md">
        <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-3">Удаление задачи</h3>
            <p class="text-gray-600 mb-4">Вы уверены, что хотите удалить эту задачу? Это действие нельзя отменить.</p>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <button onclick="confirmDeleteTask()"
                        class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 text-sm md:text-base">
                    Да, удалить
                </button>
                <button onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 text-sm md:text-base">
                    Отмена
                </button>
            </div>
        </div>
    </div>

    @include('partials.modal.task.show')

    <!-- Модальное окно файлового менеджера для редактирования -->
    <div id="fileManagerModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
        <div class="bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
            <!-- Заголовок -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                        Файловое хранилище</h3>
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

            <!-- Панель поиска и фильтров -->
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

            <!-- Контент файлового менеджера -->
            <div class="flex-1 overflow-hidden">
                <div class="h-full flex">
                    <!-- Список файлов -->
                    <div class="flex-1 overflow-y-auto p-4" id="fileManagerContent">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            <div class="col-span-full text-center py-12">
                                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600">Загрузка файлов...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Предпросмотр файла -->
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

            <!-- Футер с кнопками -->
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

    <script>
        let currentTaskId = null;
        let editSelectedFiles = [];
        let editAllFiles = [];
        let editTempSelectedFiles = [];
        let currentDeleteTaskId = null;
        let currentReturnTaskId = null;

        // Функции для модального окна улучшения
        function openUpgradeModal() {
            const modal = document.getElementById('upgradeModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeUpgradeModal() {
            const modal = document.getElementById('upgradeModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Переключение фильтров
        document.getElementById('filterToggle')?.addEventListener('click', function () {
            document.getElementById('filtersPanel').classList.toggle('hidden');
        });

        // Сортировка
        document.getElementById('sortSelect')?.addEventListener('change', function () {
            const value = this.value;
            let sort, order;
            switch (value) {
                case 'created_at_desc':
                    sort = 'created_at';
                    order = 'desc';
                    break;
                case 'created_at_asc':
                    sort = 'created_at';
                    order = 'asc';
                    break;
                case 'deadline_asc':
                    sort = 'deadline';
                    order = 'asc';
                    break;
                case 'deadline_desc':
                    sort = 'deadline';
                    order = 'desc';
                    break;
                case 'priority_desc':
                    sort = 'priority';
                    order = 'desc';
                    break;
                case 'name_asc':
                    sort = 'name';
                    order = 'asc';
                    break;
                default:
                    sort = 'created_at';
                    order = 'desc';
            }
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sort);
            url.searchParams.set('order', order);
            window.location.href = url.toString();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const sort = urlParams.get('sort') || 'created_at';
            const order = urlParams.get('order') || 'desc';
            let selectedValue = 'created_at_desc';
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) sortSelect.value = selectedValue;
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

                    // Заполняем отдел
                    const departmentSelect = document.getElementById('editTaskDepartment');
                    if (departmentSelect) {
                        departmentSelect.innerHTML = '<option value="">Выберите отдел</option>';
                        @foreach($filterData['departments'] as $department)
                            departmentSelect.innerHTML += `<option value="{{ $department->id }}">{{ $department->name }}</option>`;
                        @endforeach
                            departmentSelect.value = task.department_id || '';
                    }

                    // Заполняем категорию
                    const categorySelect = document.getElementById('editTaskCategory');
                    if (categorySelect) {
                        categorySelect.innerHTML = '<option value="">Без категории</option>';
                        @foreach($filterData['categories'] as $category)
                            categorySelect.innerHTML += `<option value="{{ $category->id }}">{{ $category->name }}</option>`;
                        @endforeach
                            categorySelect.value = task.category_id || '';
                    }

                    // Заполняем исполнителя
                    const userSelect = document.getElementById('editTaskUser');
                    if (userSelect) {
                        userSelect.innerHTML = '<option value="">Не назначен</option>';
                        @foreach($filterData['users'] as $user)
                            userSelect.innerHTML += `<option value="{{ $user->id }}">{{ $user->name }}</option>`;
                        @endforeach
                            userSelect.value = task.user_id || '';
                    }

                    document.getElementById('editTaskPriority').value = task.priority || 'средний';
                    document.getElementById('editTaskStatus').value = task.status;
                    document.getElementById('editTaskDeadline').value = task.deadline ? task.deadline.slice(0, 16) : '';
                    document.getElementById('editTaskEstimatedHours').value = task.estimated_hours || '';
                    document.getElementById('editTaskActualHours').value = task.actual_hours || '';

                    // Загружаем файлы задачи
                    if (task.files && task.files.length > 0) {
                        editSelectedFiles = task.files;
                        console.log('Загружены файлы задачи:', editSelectedFiles.map(f => f.id));
                    } else {
                        editSelectedFiles = [];
                    }
                    updateEditSelectedFilesDisplay();

                    // Загружаем историю отказов
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
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            const activeContent = document.getElementById('edit' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'TabContent');
            if (activeContent) activeContent.classList.remove('hidden');
        }

        // ==================== ФАЙЛОВЫЙ МЕНЕДЖЕР ДЛЯ РЕДАКТИРОВАНИЯ ====================

        async function openEditFileManager() {
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                // Копируем текущие файлы задачи во временный массив
                editTempSelectedFiles = [...editSelectedFiles];
                console.log('Открыт менеджер, editTempSelectedFiles:', editTempSelectedFiles.map(f => f.id));
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
                console.log('Загружено файлов из хранилища:', editAllFiles.length);
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
            console.log('toggleEditFileSelection called for fileId:', fileId);

            const file = editAllFiles.find(f => f.id === fileId);
            if (!file) {
                console.error('File not found:', fileId);
                return;
            }

            const index = editTempSelectedFiles.findIndex(f => f.id === fileId);

            if (index === -1) {
                editTempSelectedFiles.push(file);
                console.log('File added, now total:', editTempSelectedFiles.length);
            } else {
                editTempSelectedFiles.splice(index, 1);
                console.log('File removed, now total:', editTempSelectedFiles.length);
            }

            // Обновляем отображение карточек
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
            console.log('updateEditFileManagerUI: count =', count);

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

        // ЕДИНАЯ ФУНКЦИЯ ПОДТВЕРЖДЕНИЯ ВЫБОРА ФАЙЛОВ (исправлено дублирование)
        window.confirmEditFileSelectionForEdit = function () {
            console.log('=== confirmEditFileSelectionForEdit вызвана ===');
            console.log('Текущие выбранные файлы (editTempSelectedFiles):', editTempSelectedFiles.map(f => f.id));

            // Сохраняем выбранные файлы в основной массив
            editSelectedFiles = [...editTempSelectedFiles];

            console.log('Сохранено файлов в editSelectedFiles:', editSelectedFiles.length);
            console.log('ID сохраненных файлов:', editSelectedFiles.map(f => f.id));

            // Обновляем отображение в модалке редактирования
            updateEditSelectedFilesDisplay();

            // Закрываем файловый менеджер
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
                if (searchTerm) {
                    filtered = filtered.filter(f => f.name.toLowerCase().includes(searchTerm));
                }
                if (typeFilter && typeFilter.value) {
                    const type = typeFilter.value;
                    if (type === 'image') {
                        filtered = filtered.filter(f => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(f.extension?.toLowerCase()));
                    } else if (type === 'document') {
                        filtered = filtered.filter(f => ['pdf', 'doc', 'docx', 'txt', 'rtf'].includes(f.extension?.toLowerCase()));
                    } else if (type === 'video') {
                        filtered = filtered.filter(f => ['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(f.extension?.toLowerCase()));
                    } else if (type === 'audio') {
                        filtered = filtered.filter(f => ['mp3', 'wav', 'ogg', 'flac', 'm4a'].includes(f.extension?.toLowerCase()));
                    } else if (type === 'archive') {
                        filtered = filtered.filter(f => ['zip', 'rar', '7z', 'tar', 'gz'].includes(f.extension?.toLowerCase()));
                    }
                }
                if (sortBy && sortBy.value) {
                    const sort = sortBy.value;
                    if (sort === 'newest') {
                        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    } else if (sort === 'oldest') {
                        filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    } else if (sort === 'name_asc') {
                        filtered.sort((a, b) => a.name.localeCompare(b.name));
                    } else if (sort === 'name_desc') {
                        filtered.sort((a, b) => b.name.localeCompare(a.name));
                    } else if (sort === 'size_asc') {
                        filtered.sort((a, b) => (a.size || 0) - (b.size || 0));
                    } else if (sort === 'size_desc') {
                        filtered.sort((a, b) => (b.size || 0) - (a.size || 0));
                    }
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

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU');
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

                // ВАЖНО: передаем ID выбранных файлов
                const selectedFileIds = editSelectedFiles.map(f => f.id);
                console.log('Отправляемые ID файлов:', selectedFileIds);

                // Очищаем старые значения и добавляем новые
                formData.delete('selected_files');
                formData.append('selected_files', JSON.stringify(selectedFileIds));

                const newFiles = document.getElementById('editUploadNewFilesInput');
                if (newFiles?.files.length) {
                    for (let i = 0; i < newFiles.files.length; i++) {
                        formData.append('new_files[]', newFiles.files[i]);
                    }
                    console.log('Новых файлов для загрузки:', newFiles.files.length);
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
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
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
            return {bg: 'bg-gray-100'};
        }

        function formatFileSize(bytes) {
            if (!bytes) return '0 B';
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
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
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
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
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 404) {
                    throw new Error('Маршрут не найден. Проверьте URL.');
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message || 'Задача успешно удалена', 'success');
                    closeDeleteModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
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

        function openTaskViewModal(taskId) {
            // Показываем загрузчик
            const content = document.getElementById('taskModalContent');
            if (content) {
                content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                <p class="text-gray-500 mt-2">Загрузка задачи...</p>
            </div>
        `;
            }

            // Загружаем всю вьюху задачи с комментариями
            fetch(`/tasks/${taskId}/view`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка загрузки задачи');
                    }
                    return response.text();
                })
                .then(html => {
                    const content = document.getElementById('taskModalContent');
                    const modal = document.getElementById('taskViewModal');

                    window.currentTaskId = taskId;
                    window.taskId = taskId;

                    if (content) content.innerHTML = html;
                    if (modal) modal.classList.remove('hidden');

                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    if (content) {
                        content.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                        <p class="text-gray-500 mt-2">Не удалось загрузить задачу</p>
                        <button onclick="openTaskViewModal(${taskId})"
                                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            <i class="fas fa-sync-alt mr-2"></i>Повторить
                        </button>
                    </div>
                `;
                    }
                });
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

        // ==================== ФУНКЦИИ ДЛЯ КОММЕНТАРИЕВ ====================

        function submitComment(taskId) {
            const commentText = document.getElementById('commentInput')?.value.trim();
            if (!commentText) { showNotification('Напишите комментарий', 'warning'); return; }

            fetch(`/tasks/${taskId}/comments`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' },
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
            // Берем taskId из window.currentTaskId или из data-атрибута
            const taskId = window.currentTaskId || window.taskId;

            console.log('submitReply called - taskId:', taskId, 'commentId:', commentId);

            if (!replyText) {
                if (typeof showNotification === 'function') showNotification('Напишите ответ', 'warning');
                return;
            }

            if (!taskId) {
                if (typeof showNotification === 'function') showNotification('Ошибка: ID задачи не определен', 'error');
                console.error('taskId is undefined!');
                return;
            }

            fetch(`/tasks/${taskId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    comment: replyText,
                    parent_id: commentId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
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

            console.log('deleteComment - taskId:', taskId, 'commentId:', commentId);

            if (!taskId) {
                if (typeof showNotification === 'function') showNotification('Ошибка: ID задачи не определен', 'error');
                return;
            }

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

            console.log('saveEdit - taskId:', taskId, 'commentId:', commentId);

            if (!newText) {
                if (typeof showNotification === 'function') showNotification('Комментарий не может быть пустым', 'warning');
                return;
            }

            if (!taskId) {
                if (typeof showNotification === 'function') showNotification('Ошибка: ID задачи не определен', 'error');
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
    </script>

    <style>
        /* Дополнительные стили для адаптивности */
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

        @media (max-width: 768px) {
            .card-hover:hover {
                transform: none;
            }
        }

        /* Стили для элементов истории отказов */
        .rejection-item {
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
            border-left: 4px solid #ef4444;
            transition: all 0.2s ease;
        }

        .rejection-item:hover {
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
        }

        /* Стили для файлов */
        .existing-file-item {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .existing-file-item:hover {
            border-color: #10b981;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
        }

        .new-file-item {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            border: 1px solid #dcfce7;
            border-radius: 0.75rem;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Улучшенный скроллбар для истории отказов */
        #rejectionsList.custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        #rejectionsList.custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #rejectionsList.custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 10px;
        }

        /* Анимация для модального окна */
        .modal-content {
            animation: fadeIn 0.3s ease-out;
        }

        /* Стили для статусов в выпадающем списке */
        select option {
            padding: 10px;
        }

        /* Улучшенный вид для input file */
        input[type="file"]::file-selector-button {
            transition: all 0.2s ease;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: #d1fae5 !important;
        }

        /* public/css/task-comments.css */

        .comment-thread {
            transition: all 0.2s ease;
        }

        .comment-thread:hover {
            background-color: transparent;
        }

        .comment-text a {
            word-break: break-all;
        }

        .replies-container {
            transition: all 0.3s ease;
        }

        /* Анимация для новых комментариев */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-thread {
            animation: fadeInUp 0.3s ease;
        }

        /* Стили для формы комментария */
        #commentInput:focus,
        #replyInput:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Адаптивность для мобильных устройств */
        @media (max-width: 768px) {
            .comment-thread {
                margin-left: 0 !important;
            }

            .reply-form-container {
                margin-left: 0 !important;
            }
        }
    </style>
@endsection
