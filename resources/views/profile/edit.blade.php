@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Заголовок и статистика -->
        <div class="bg-white shadow">
            <div class="mx-auto px-4">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Панель управления задачами</h1>
                        <p class="text-gray-500 mt-1">Обзор всех задач компании</p>
                    </div>
                    <div class="flex space-x-4">
                        <button id="newTaskBtn"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-700 transition">
                            <i class="fas fa-plus"></i>
                            <span>Новая задача</span>
                        </button>
                        <button id="filterToggle" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition">
                            <i class="fas fa-filter"></i>
                            <span>Фильтры</span>
                        </button>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4 pb-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                        <div class="text-gray-500 text-sm">Всего задач</div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-yellow-700">{{ $stats['assigned'] }}</div>
                        <div class="text-yellow-600 text-sm">Назначены</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-700">{{ $stats['in_progress'] }}</div>
                        <div class="text-blue-600 text-sm">В работе</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-orange-700">{{ $stats['review'] }}</div>
                        <div class="text-orange-600 text-sm">На проверке</div>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-red-700">{{ $stats['overdue'] }}</div>
                        <div class="text-red-600 text-sm">Просрочено</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-700">{{ $stats['completed'] }}</div>
                        <div class="text-green-600 text-sm">Выполнено</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Фильтры и поиск -->
        <div id="filtersPanel" class="bg-white border-b border-gray-200 hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <form method="GET" action="{{ route('tasks.admin') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Поиск -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Название или описание...">
                    </div>

                    <!-- Статус -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Все исполнители</option>
                            @foreach($filterData['users'] as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Отдел -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                        <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Все категории</option>
                            @foreach($filterData['categories'] as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Кнопки фильтра -->
                    <div class="md:col-span-4 flex space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Применить фильтры
                        </button>
                        <a href="{{ route('tasks.admin') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Основной контент -->
        <div class="mx-auto py-8">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-2">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-2">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-2">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
