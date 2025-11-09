<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow - Современный таск-менеджер</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#10B981',
                        dark: '#1F2937',
                        light: '#F9FAFB'
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .sidebar {
            transition: all 0.3s ease;
        }

        .task-card {
            transition: all 0.2s ease;
        }

        .task-card:hover {
            transform: translateY(-2px);
        }

        .board-column {
            min-height: 500px;
        }

        .page {
            display: none;
        }

        .active-page {
            display: block;
        }

        .active-nav {
            color: #3B82F6;
            font-weight: 600;
        }

        .before\:bg-\[url\(\'https\:\/\/preline\.co\/assets\/svg\/examples\/polygon-bg-element\.svg\'\)\]::before {
            background-image: unset !important;
        }

        .sidebar {
            min-height: 100vh !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
<!-- Навигация -->
<nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="{{route('welcome')}}">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                <i class="fas fa-tasks text-white"></i>
            </div>
            <span class="text-xl font-bold text-dark">TaskFlow</span>
        </div>
    </a>

    <div class="hidden md:flex space-x-6">
        <a href="{{route('welcome')}}" class="nav-link active-nav" data-page="welcome">Главная</a>
        <a href="{{route('departments.index')}}" class="nav-link" data-page="boards">Отделы</a>
        <a href="{{route('team.index')}}" class="nav-link" data-page="team">Команда</a>
        <a href="{{route('photobank')}}" class="nav-link" data-page="team">Фотобанк</a>
    </div>

    @auth()
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 cursor-pointer" id="userMenuBtn" onclick="userProfileModal()">
                <div
                    class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white font-semibold">
                    {{mb_substr(auth()->user()->name, 0,1)}}
                </div>
                @if(auth())
                    <span class="hidden md:block font-medium">{{auth()->user()->name}}</span>
                @endif
                <i class="fas fa-chevron-down text-gray-500"></i>
            </div>
        </div>

    @endauth
</nav>

<div class="flex">
    <!-- Боковая панель -->
    <div class="sidebar w-64 bg-white shadow-lg py-6 px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-500">Отделы</h2>
                <button onclick="openDepartmentModal()" class="text-blue-500 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>
            </div>
            @if(isset($departments) && $departments->count() > 0)
                <div class="space-y-2">
                    @foreach($departments as $department)
                        <div
                            class="group flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 cursor-pointer workspace-item"
                            data-workspace="alpha">
                            <span>{{ $department->name }}</span>
                            <button onclick="openEditDepartmentModal({{ $department->id }})"
                                    class="text-gray-400 hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Нет доступных отделов</p>
            @endif
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-500">Категории</h2>
                <button onclick="openCategoryModal()" class="text-blue-500 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>
            </div>

            <!-- Категории -->
            @if(isset($categories) && $categories->count() > 0)
                <div class="space-y-2">
                    @foreach($categories as $category)
                        <div
                            class="group flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 cursor-pointer board-item"
                            data-board="development">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 rounded" style="background-color: {{ $category->color }}"></div>
                                <span>{{ $category->name }}</span>
                            </div>
                            <button onclick="openEditCategoryModal({{ $category->id }})"
                                    class="text-gray-400 hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Нет доступных категорий</p>
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-500 mb-4">КОМАНДА</h2>
                <button onclick="createUserModal()" class="text-blue-500 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-2">
                @if(isset($team))
                    @foreach($team as $item)
                        <div
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 cursor-pointer user-item"
                            data-user="anna">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>{{$item->name}}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="flex-1 p-6">
        <!-- Главная страница -->
        <div id="home" class="page active-page">
            @yield('content')
        </div>

    </div>
</div>

<!-- Модальное окно для новой задачи -->
<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новая задача</h3>
            <button onclick="closeTaskModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="taskForm" enctype="multipart/form-data">
            @csrf

            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Название задачи *</label>
                    <input type="text" name="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                           placeholder="Введите название задачи" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Приоритет *</label>
                    <select name="priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            required>
                        <option value="низкий">Низкий</option>
                        <option value="средний" selected>Средний</option>
                        <option value="высокий">Высокий</option>
                        <option value="критический">Критический</option>
                    </select>
                </div>
            </div>

            <!-- Описание -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Описание</label>
                <textarea name="description"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                          rows="4" placeholder="Подробное описание задачи..."></textarea>
            </div>

            <!-- Отдел и категория -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Отдел *</label>
                    <select name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                            required>
                        <option value="">Выберите отдел</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Категория</label>
                    <select name="category_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Без категории</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Исполнитель и сроки -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Исполнитель</label>
                    <select name="user_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Не назначено</option>
                        @foreach($assignableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Дедлайн</label>
                    <input type="datetime-local" name="deadline"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <!-- Оценка времени -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Планируемые часы</label>
                    <input type="number" name="estimated_hours" min="0" step="0.5"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                           placeholder="0.0">
                </div>

                <div class="flex items-end">
                    <div class="w-full">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Статус *</label>
                        <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                                required>
                            @php
                                $availableStatuses = array_filter(\App\Models\Task::getStatuses(), function($status) {
                                    return $status !== 'в работе'; // Исключаем "в работе"
                                });
                            @endphp
                            @foreach($availableStatuses as $status)
                                <option value="{{ $status }}" {{ $status == 'назначена' ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Файлы -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Прикрепленные файлы</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                    <input type="file" name="files[]" multiple class="hidden" id="fileInput">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 mb-2">Перетащите файлы сюда или нажмите для выбора</p>
                        <button type="button" onclick="document.getElementById('fileInput').click()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Выбрать файлы
                        </button>
                    </div>
                    <div id="fileList" class="mt-3 text-left"></div>
                </div>
            </div>

            <!-- Подзадачи -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Подзадачи</label>
                <div id="subtasksContainer">
                    <div class="flex space-x-2 mb-2">
                        <input type="text" name="subtasks[]"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="Название подзадачи">
                        <button type="button" onclick="removeSubtask(this)"
                                class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addSubtask()"
                        class="mt-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-plus mr-2"></i>Добавить подзадачу
                </button>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeTaskModal()"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                    <i class="fas fa-plus mr-2"></i>Создать задачу
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно уведомлений -->
<div id="notificationsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Уведомления</h3>
            <button id="closeNotifications" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4 max-h-96 overflow-y-auto">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white mt-1">
                    <i class="fas fa-tasks text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Новая задача назначена</p>
                    <p class="text-sm text-gray-600">Вам назначена задача "Интеграция платежной системы"</p>
                    <p class="text-xs text-gray-500 mt-1">2 часа назад</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white mt-1">
                    <i class="fas fa-check text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Задача завершена</p>
                    <p class="text-sm text-gray-600">Анна Петрова завершила задачу "Рефакторинг модуля авторизации"</p>
                    <p class="text-xs text-gray-500 mt-1">5 часов назад</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center text-white mt-1">
                    <i class="fas fa-exclamation text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Срок задачи истекает</p>
                    <p class="text-sm text-gray-600">Задача "Доработка главной страницы" должна быть завершена
                        завтра</p>
                    <p class="text-xs text-gray-500 mt-1">Вчера</p>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button class="text-primary hover:text-secondary font-medium">Показать все уведомления</button>
        </div>
    </div>
</div>

<!-- Модальное окно профиля пользователя -->
@auth()
    <div id="userProfileModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Профиль пользователя</h3>
                <button id="closeUserModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="text-center mb-6">
                <div
                    class="w-20 h-20 rounded-full bg-gradient-to-r from-primary to-secondary mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold">
                    {{mb_substr(auth()->user()->name, 0,1)}}
                </div>
                <h3 class="font-bold text-lg">{{auth()->user()->name}}</h3>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="font-medium">{{auth()->user()->email}}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Телефон:</span>
                    <span class="font-medium">+7 (999) 123-45-67</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Организация:</span>
                    @if(isset(auth()->user()->company))
                        <span class="font-medium">{{auth()->user()->company->name}}</span>

                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Роль:</span>
                    @if(isset(auth()->user()->role))
                        <span class="font-medium">{{auth()->user()->role->name}}</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{route('profile.edit')}}" id="editProfile"
                   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                    Редактировать
                </a>
                <form action="{{route('logout')}}" method="post">
                    @csrf
                    <button id="logout"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                            type="submit">
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </div>
@endauth


<!-- Модальное окно для новой категории -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новая категория</h3>
            <button onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="categoryForm" action="{{ route('category.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название категории</label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название категории" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Цвет</label>
                <div class="flex space-x-2">
                    <div class="color-option">
                        <input type="radio" name="color" value="#3B82F6" class="hidden" id="color-blue" checked>
                        <label for="color-blue" class="w-8 h-8 bg-blue-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#EF4444" class="hidden" id="color-red">
                        <label for="color-red" class="w-8 h-8 bg-red-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#10B981" class="hidden" id="color-green">
                        <label for="color-green" class="w-8 h-8 bg-green-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#F59E0B" class="hidden" id="color-yellow">
                        <label for="color-yellow"
                               class="w-8 h-8 bg-yellow-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#8B5CF6" class="hidden" id="color-purple">
                        <label for="color-purple"
                               class="w-8 h-8 bg-purple-500 rounded-full cursor-pointer block"></label>
                    </div>
                </div>
            </div>

            <!-- Скрытое поле с company_id -->
            @if(auth()->user() && auth()->user()->company_id)
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
            @endif

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Категория будет создана для компании:
                    <strong>{{ auth()->user()->company->name ?? 'Не указана' }}</strong>
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCategoryModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать
                    категорию
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно редактирования категории -->
<div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Редактировать категорию</h3>
            <button onclick="closeEditCategoryModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editCategoryForm" action="{{ route('category.update') }}" method="POST">
            @csrf
            @method('patch')
            <input type="hidden" name="category_id" id="edit_category_id">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название категории</label>
                <input type="text" name="name" id="edit_category_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название категории" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Цвет</label>
                <div class="flex space-x-2">
                    <div class="color-option">
                        <input type="radio" name="color" value="#3B82F6" class="hidden" id="edit-color-blue">
                        <label for="edit-color-blue"
                               class="w-8 h-8 bg-blue-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#EF4444" class="hidden" id="edit-color-red">
                        <label for="edit-color-red"
                               class="w-8 h-8 bg-red-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#10B981" class="hidden" id="edit-color-green">
                        <label for="edit-color-green"
                               class="w-8 h-8 bg-green-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#F59E0B" class="hidden" id="edit-color-yellow">
                        <label for="edit-color-yellow"
                               class="w-8 h-8 bg-yellow-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#8B5CF6" class="hidden" id="edit-color-purple">
                        <label for="edit-color-purple"
                               class="w-8 h-8 bg-purple-500 rounded-full cursor-pointer block"></label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditCategoryModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Сохранить
                    изменения
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно для нового отдела -->
<div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новый отдел</h3>
            <button onclick="closeDepartmentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="departmentForm" action="{{route('departments.store')}}" method="post">
            @csrf
            @method('post')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название отдела</label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название отдела" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Компания</label>
                <select name="company_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">

                    @if(isset($ownedCompanies))
                        @foreach($ownedCompanies as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Вы будете автоматически назначены руководителем этого отдела
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDepartmentModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать
                    отдел
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно редактирования отдела -->
<div id="editDepartmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Редактировать отдел</h3>
            <button onclick="closeEditDepartmentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editDepartmentForm" action="{{ route('departments.update') }}" method="post">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="department_id" id="edit_department_id">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название отдела</label>
                <input type="text" name="name" id="edit_department_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название отдела" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Компания</label>
                <select name="company_id" id="edit_department_company"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    @if(isset($ownedCompanies))
                        @foreach($ownedCompanies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Вы являетесь руководителем этого отдела
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditDepartmentModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Сохранить
                    изменения
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно для нового пользователя -->
<div id="newUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новый пользователь</h3>
            <button onclick="closeUserModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="userForm" action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Имя *</label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите имя пользователя" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Email *</label>
                <input type="email" name="email"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="email@example.com" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Компания *</label>
                <select name="company_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        required>
                    <option value="">Выберите компанию</option>
                    @foreach($ownedCompanies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Отдел</label>
                <select name="department_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Без отдела</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Роль *</label>
                <select name="role_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                        required>
                    <option value="">Выберите роль</option>
                    <!-- Здесь нужно получить роли из базы -->
                    @if(isset($roles))
                        @foreach($roles as $role)
                            <option value="{{$role->id}}">{{$role->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Пароль *</label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Минимум 8 символов" required minlength="8">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Подтверждение пароля *</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Повторите пароль" required>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeUserModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать
                    пользователя
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    // Глобальные переменные
    let currentModalType = '';

    // ==================== ФУНКЦИИ ДЛЯ МОДАЛЬНЫХ ОКОН ====================

    // Открытие модальных окон
    function openTaskModal() {
        currentModalType = 'task';
        document.getElementById('taskModal').classList.remove('hidden');
    }

    function userProfileModal() {
        currentModalType = 'user';
        document.getElementById('userProfileModal').classList.remove('hidden');
    }

    function createUserModal() {
        currentModalType = 'user';
        document.getElementById('newUserModal').classList.remove('hidden');
    }

    function openCategoryModal() {
        currentModalType = 'category';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function openDepartmentModal() {
        currentModalType = 'department';
        document.getElementById('departmentModal').classList.remove('hidden');
    }

    // Закрытие модальных окон
    function closeTaskModal() {
        document.getElementById('taskModal').classList.add('hidden');
        document.getElementById('taskForm').reset();
        document.getElementById('fileList').innerHTML = '';
        document.getElementById('subtasksContainer').innerHTML = '<div class="flex space-x-2 mb-2"><input type="text" name="subtasks[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Название подзадачи"><button type="button" onclick="removeSubtask(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"><i class="fas fa-times"></i></button></div>';
    }

    function closeUserModal() {
        document.getElementById('createUserModal').classList.add('hidden');
        document.getElementById('userForm').reset();
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryForm').reset();
    }

    function closeDepartmentModal() {
        document.getElementById('departmentModal').classList.add('hidden');
        document.getElementById('departmentForm').reset();
    }

    // ==================== УПРАВЛЕНИЕ ПОДЗАДАЧАМИ ====================

    function addSubtask() {
        const container = document.getElementById('subtasksContainer');
        const div = document.createElement('div');
        div.className = 'flex space-x-2 mb-2';
        div.innerHTML = `
        <input type="text" name="subtasks[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Название подзадачи">
        <button type="button" onclick="removeSubtask(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            <i class="fas fa-times"></i>
        </button>
    `;
        container.appendChild(div);
    }

    function removeSubtask(button) {
        if (document.querySelectorAll('#subtasksContainer > div').length > 1) {
            button.parentElement.remove();
        }
    }

    // ==================== УПРАВЛЕНИЕ ФАЙЛАМИ ====================

    function initFileUpload() {
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');

        fileInput.addEventListener('change', function (e) {
            fileList.innerHTML = '';

            Array.from(e.target.files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded mb-1';
                div.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-file text-gray-400 mr-2"></i>
                    <span class="text-sm truncate max-w-xs">${file.name}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                    <button type="button" onclick="removeFile('${file.name}')" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
                fileList.appendChild(div);
            });
        });
    }

    function removeFile(fileName) {
        const fileInput = document.getElementById('fileInput');
        const files = Array.from(fileInput.files);
        const updatedFiles = files.filter(file => file.name !== fileName);

        // Создаем новый DataTransfer для обновления файлов
        const dataTransfer = new DataTransfer();
        updatedFiles.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;

        // Обновляем отображение
        fileInput.dispatchEvent(new Event('change'));
    }

    // ==================== ОБРАБОТКА ФОРМ ====================

    // Обработка формы задачи
    document.getElementById('taskForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Валидация обязательных полей
        const name = formData.get('name');
        const departmentId = formData.get('department_id');

        if (!name || !departmentId) {
            showNotification('Заполните обязательные поля: название задачи и отдел', 'error');
            return;
        }

        submitButton.textContent = 'Создание...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/tasks/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                // Обработка HTTP ошибок (4xx, 5xx)
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            if (data.success) {
                closeTaskModal();
                showNotification('Задача успешно создана!', 'success');
                // Сброс формы
                this.reset();
                document.getElementById('fileList').innerHTML = '';

                // Обновить список задач на странице
                if (typeof refreshTasks === 'function') {
                    refreshTasks();
                }
            } else {
                showNotification(data.message || 'Ошибка при создании задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification(error.message || 'Произошла ошибка при создании задачи', 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // Обработка формы пользователя
    document.getElementById('userForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Валидация пароля
        const password = formData.get('password');
        const passwordConfirmation = formData.get('password_confirmation');

        if (password !== passwordConfirmation) {
            showNotification('Пароли не совпадают', 'error');
            return;
        }

        if (password.length < 8) {
            showNotification('Пароль должен содержать минимум 8 символов', 'error');
            return;
        }

        submitButton.textContent = 'Создание...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/users/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                closeUserModal();
                showNotification('Пользователь успешно создан!', 'success');
                // Обновить список пользователей на странице
                if (typeof refreshUsers === 'function') {
                    refreshUsers();
                }
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при создании пользователя', 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // Обработка формы категории
    document.getElementById('categoryForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        submitButton.textContent = 'Создание...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/category/create', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                closeCategoryModal();
                showNotification('Категория успешно создана!', 'success');
                // Обновить список категорий на странице
                if (typeof refreshCategories === 'function') {
                    refreshCategories();
                }
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при создании категории', 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    let currentEditingCategoryId = null;

    // Функция для открытия модального окна редактирования
    async function openEditCategoryModal(categoryId) {
        currentEditingCategoryId = categoryId;

        try {
            // Загружаем данные категории
            const response = await fetch(`/category/${categoryId}/edit`);
            const category = await response.json();

            if (category) {
                // Заполняем форму данными
                document.getElementById('edit_category_id').value = category.id;
                document.getElementById('edit_category_name').value = category.name;

                // Устанавливаем выбранный цвет
                const colorInput = document.querySelector(`input[name="color"][value="${category.color}"]`);
                if (colorInput) {
                    colorInput.checked = true;
                }

                // Показываем модальное окно
                document.getElementById('editCategoryModal').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Ошибка при загрузке категории:', error);
            showNotification('Ошибка при загрузке категории', 'error');
        }
    }

    // Функция для закрытия модального окна редактирования
    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
        document.getElementById('editCategoryForm').reset();
        currentEditingCategoryId = null;
    }

    // Обработчик отправки формы редактирования
    document.getElementById('editCategoryForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        submitButton.textContent = 'Сохранение...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/category/update', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'patch'
                }
            });

            const data = await response.json();

            if (data.success) {
                closeEditCategoryModal();
                showNotification('Категория успешно обновлена!', 'success');
                // Обновить список категорий на странице
                if (typeof refreshCategories === 'function') {
                    refreshCategories();
                }
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при обновлении категории', 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // Обработка формы отдела
    document.getElementById('departmentForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        submitButton.textContent = 'Создание...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/departments/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                closeDepartmentModal();
                showNotification('Отдел успешно создан!', 'success');
                // Обновить список отделов на странице
                if (typeof refreshDepartments === 'function') {
                    refreshDepartments();
                }
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при создании отдела', 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // Редактирование отдела
    let currentEditingDepartmentId = null;

    // Функция для открытия модального окна редактирования отдела
    async function openEditDepartmentModal(departmentId) {
        currentEditingDepartmentId = departmentId;

        try {
            // Загружаем данные отдела
            const response = await fetch(`/departments/${departmentId}/edit`);
            const department = await response.json();

            if (department) {
                // Заполняем форму данными
                document.getElementById('edit_department_id').value = department.id;
                document.getElementById('edit_department_name').value = department.name;
                document.getElementById('edit_department_company').value = department.company_id;

                // Показываем модальное окно
                document.getElementById('editDepartmentModal').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Ошибка при загрузке отдела:', error);
            showNotification('Ошибка при загрузке отдела', 'error');
        }
    }

    // Функция для закрытия модального окна редактирования отдела
    function closeEditDepartmentModal() {
        document.getElementById('editDepartmentModal').classList.add('hidden');
        document.getElementById('editDepartmentForm').reset();
        currentEditingDepartmentId = null;
    }

    document.getElementById('editDepartmentForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        submitButton.textContent = 'Сохранение...';
        submitButton.disabled = true;

        try {
            const response = await fetch('{{ route("departments.update") }}', {
                method: 'POST', // Используем POST
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                closeEditDepartmentModal();
                showNotification('Отдел успешно обновлен!', 'success');
                window.location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при обновлении отдела: ' + error.message, 'error');
        } finally {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // ==================== УВЕДОМЛЕНИЯ ====================

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                    type === 'warning' ? 'bg-yellow-500 text-white' :
                        'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
            type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' :
                        'fa-info-circle'
        } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(notification);

        // Анимация появления
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Автоматическое удаление через 5 секунд
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    // ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================

    // Форматирование даты
    function formatDate(dateString) {
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

    // Получение приоритета в виде цвета
    function getPriorityColor(priority) {
        const colors = {
            'низкий': 'bg-gray-100 text-gray-800',
            'средний': 'bg-blue-100 text-blue-800',
            'высокий': 'bg-orange-100 text-orange-800',
            'критический': 'bg-red-100 text-red-800'
        };
        return colors[priority] || colors['средний'];
    }

    // Получение статуса в виде цвета
    function getStatusColor(status) {
        const colors = {
            'не назначена': 'bg-gray-100 text-gray-800',
            'в работе': 'bg-blue-100 text-blue-800',
            'просрочена': 'bg-red-100 text-red-800',
            'на проверке': 'bg-yellow-100 text-yellow-800',
            'выполнена': 'bg-green-100 text-green-800'
        };
        return colors[status] || colors['не назначена'];
    }

    // ==================== DRAG AND DROP ДЛЯ ЗАДАЧ ====================

    let draggedTask = null;

    function initTaskDragAndDrop() {
        document.querySelectorAll('.task-card').forEach(task => {
            task.setAttribute('draggable', 'true');

            task.addEventListener('dragstart', function () {
                draggedTask = this;
                setTimeout(() => {
                    this.style.opacity = '0.5';
                }, 0);
            });

            task.addEventListener('dragend', function () {
                setTimeout(() => {
                    this.style.opacity = '1';
                    draggedTask = null;
                }, 0);
            });
        });

        document.querySelectorAll('.board-column').forEach(column => {
            column.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.style.backgroundColor = '#e5e7eb';
            });

            column.addEventListener('dragleave', function () {
                this.style.backgroundColor = '#f3f4f6';
            });

            column.addEventListener('drop', async function (e) {
                e.preventDefault();
                this.style.backgroundColor = '#f3f4f6';

                if (draggedTask) {
                    const newStatus = this.getAttribute('data-status');
                    const taskId = draggedTask.getAttribute('data-task-id');

                    try {
                        const response = await fetch(`/tasks/${taskId}/status`, {
                            method: 'patch',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            const taskContainer = this.querySelector('.task-container');
                            taskContainer.appendChild(draggedTask);
                            showNotification('Статус задачи обновлен', 'success');
                            updateTaskCounters();
                        } else {
                            showNotification('Ошибка при обновлении статуса', 'error');
                        }
                    } catch (error) {
                        console.error('Ошибка:', error);
                        showNotification('Ошибка при обновлении статуса', 'error');
                    }
                }
            });
        });
    }

    // Обновление счетчиков задач
    function updateTaskCounters() {
        const statuses = ['не назначена', 'в работе', 'на проверке', 'выполнена'];

        statuses.forEach(status => {
            const container = document.querySelector(`.task-container[data-status="${status}"]`);
            const counter = document.querySelector(`.board-column[data-status="${status}"] .task-counter`);

            if (container && counter) {
                const count = container.querySelectorAll('.task-card').length;
                counter.textContent = count;
            }
        });
    }

    // ==================== ИНИЦИАЛИЗАЦИЯ ====================

    document.addEventListener('DOMContentLoaded', function () {
        // Инициализация загрузки файлов
        initFileUpload();

        // Инициализация drag and drop
        initTaskDragAndDrop();

        // Обновление счетчиков
        updateTaskCounters();

        // Обработчики для кнопок создания
        const newTaskBtn = document.getElementById('newTaskBtn');
        if (newTaskBtn) {
            newTaskBtn.addEventListener('click', openTaskModal);
        }

        const newUserBtn = document.getElementById('newUserBtn');
        if (newUserBtn) {
            newUserBtn.addEventListener('click', openUserModal);
        }

        // Закрытие модальных окон при клике вне их
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('fixed')) {
                if (currentModalType === 'task') {
                    closeTaskModal();
                } else if (currentModalType === 'user') {
                    closeUserModal();
                } else if (currentModalType === 'category') {
                    closeCategoryModal();
                } else if (currentModalType === 'department') {
                    closeDepartmentModal();
                }
            }
        });

        // Закрытие модальных окон по ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (currentModalType === 'task') {
                    closeTaskModal();
                } else if (currentModalType === 'user') {
                    closeUserModal();
                } else if (currentModalType === 'category') {
                    closeCategoryModal();
                } else if (currentModalType === 'department') {
                    closeDepartmentModal();
                }
            }
        });

        // Обработчики для элементов боковой панели
        document.querySelectorAll('.workspace-item, .board-item, .user-item').forEach(item => {
            item.addEventListener('click', function () {
                const type = this.classList.contains('workspace-item') ? 'workspace' :
                    this.classList.contains('board-item') ? 'board' : 'user';
                const id = this.getAttribute(`data-${type}`);

                console.log(`Выбран ${type}: ${id}`);
                // Здесь можно добавить загрузку данных для выбранного элемента
            });
        });
    });

    // ==================== ФУНКЦИИ ДЛЯ ОБНОВЛЕНИЯ ДАННЫХ ====================

    // Функция для обновления списка задач (должна быть реализована в конкретных представлениях)
    function refreshTasks() {
        // Эта функция должна быть переопределена в представлениях
        console.log('Обновление списка задач...');
        // location.reload(); // или AJAX запрос для обновления данных
    }

    // Функция для обновления списка пользователей
    function refreshUsers() {
        console.log('Обновление списка пользователей...');
        // location.reload(); // или AJAX запрос
    }

    // Функция для обновления списка категорий
    function refreshCategories() {
        // Простой способ - перезагрузка страницы
        window.location.reload();

        // Или более сложный - AJAX загрузка категорий
        // fetch('/categories/list')
        //     .then(response => response.text())
        //     .then(html => {
        //         document.querySelector('.mb-8').innerHTML = html;
        //     });
    }

    // Функция для обновления списка отделов
    function refreshDepartments() {
        console.log('Обновление списка отделов...');
        // location.reload(); // или AJAX запрос
    }
</script>

<script>

    // Модальные окна
    document.getElementById('newTaskBtn').addEventListener('click', function () {
        document.getElementById('taskModal').classList.remove('hidden');
    });

    document.getElementById('newUserBtn').addEventListener('click', function () {
        document.getElementById('newUserModal').classList.remove('hidden');
    });


    // Закрытие модальных окон
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('taskModal').classList.add('hidden');
    });

    document.getElementById('cancelTask').addEventListener('click', function () {
        document.getElementById('taskModal').classList.add('hidden');
    });


    // Обработка формы создания задачи
    document.getElementById('taskForm').addEventListener('submit', function (e) {
        e.preventDefault();
        alert('Задача успешно создана!');
        document.getElementById('taskModal').classList.add('hidden');
        // Здесь будет код для добавления задачи на доску
    });

    // Перетаскивание задач
    let draggedTask = null;

    document.querySelectorAll('.task-card').forEach(task => {
        task.addEventListener('dragstart', function () {
            draggedTask = this;
            setTimeout(() => {
                this.style.opacity = '0.5';
            }, 0);
        });

        task.addEventListener('dragend', function () {
            setTimeout(() => {
                this.style.opacity = '1';
                draggedTask = null;
            }, 0);
        });
    });

    document.querySelectorAll('.board-column').forEach(column => {
        column.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.backgroundColor = '#e5e7eb';
        });

        column.addEventListener('dragleave', function () {
            this.style.backgroundColor = '#f3f4f6';
        });

        column.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.backgroundColor = '#f3f4f6';

            if (draggedTask) {
                const status = this.getAttribute('data-status');
                const taskContainer = this.querySelector('.task-container');

                // Обновляем статус задачи в данных
                const taskId = draggedTask.getAttribute('data-task');
                const task = appData.tasks.find(t => t.id == taskId);
                if (task) {
                    task.status = status;
                }

                // Перемещаем задачу в новый контейнер
                taskContainer.appendChild(draggedTask);

                // Обновляем счетчики задач
                updateTaskCounters();
            }
        });
    });

    // Обновление счетчиков задач
    function updateTaskCounters() {
        const statuses = ['new', 'in-progress', 'review', 'done'];

        statuses.forEach(status => {
            const container = document.querySelector(`.task-container[data-status="${status}"]`);
            const counter = document.querySelector(`.board-column[data-status="${status}"] .bg-gray-200,
                                                      .board-column[data-status="${status}"] .bg-blue-100,
                                                      .board-column[data-status="${status}"] .bg-yellow-100,
                                                      .board-column[data-status="${status}"] .bg-green-100`);

            if (container && counter) {
                const count = container.querySelectorAll('.task-card').length;
                counter.textContent = count;
            }
        });
    }

    // Инициализация приложения
    document.addEventListener('DOMContentLoaded', function () {
        updateTaskCounters();

        // Добавляем обработчики для элементов боковой панели
        document.querySelectorAll('.workspace-item, .board-item, .user-item').forEach(item => {
            item.addEventListener('click', function () {
                const type = this.classList.contains('workspace-item') ? 'workspace' :
                    this.classList.contains('board-item') ? 'board' : 'user';
                const id = this.getAttribute(`data-${type}`);

                // В реальном приложении здесь будет загрузка данных
                console.log(`Выбран ${type}: ${id}`);
            });
        });
    });
</script>

@stack('scripts')

</body>
</html>
