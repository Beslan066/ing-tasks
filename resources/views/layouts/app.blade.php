<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>МенеджерПлюс - Современная система управления задачами</title>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#16a34a',
                        secondary: '#15803d',
                        accent: '#10B981',
                        dark: '#1F2937',
                        light: '#F9FAFB'
                    }
                }
            }
        }
    </script>


    <style>
        body {
            font-family: "Inter", sans-serif !important;
            font-optical-sizing: auto;
            font-style: normal;
        }


        .fon {
            /*background: url("https://4kwallpapers.com/images/walls/thumbs_3t/14811.jpg") no-repeat center;*/
            background-size: cover; /* покрывает всю область */
            position: relative;
            /*background: -webkit-linear-gradient(45deg, rgb(86, 181, 184), rgb(22, 163, 74));*/
            /*background: -moz-linear-gradient(45deg, rgb(86, 181, 184),   rgb(22, 163, 74));*/
            /*background: linear-gradient(45deg, rgb(86, 181, 184),  rgb(22, 163, 74));*/

        }

        .priority-option {
            position: relative;
        }

        .priority-option::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            background-image: var(--priority-icon);
            background-size: contain;
        }

        .priority-option[value="низкий"] {
            --priority-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2314ab28' viewBox='0 0 24 24'%3E%3Cpath d='M3 15h2v5H3zm4-3h2v8H7zm4-3h2v11h-2z'%3E%3C/path%3E%3C/svg%3E");
        }

        .priority-option[value="средний"] {
            --priority-icon: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23ffb300' viewBox='0 0 24 24'%3E%3Cpath d='M7 12h2v6H7zm4-3h2v9h-2zm4-3h2v12h-2z'%3E%3C/path%3E%3C/svg%3E");
        }


    </style>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>
<body class="bg-white font-sans">

<div class="flex fon">
    <!-- Боковая панель -->
    <div class="sidebar w-64 bg-white border-r border-gray-200 py-6 px-4 bg-transparent  hidden sm:block" style="backdrop-filter: blur(40px);">

        <a href="{{route('welcome')}}">
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-bold text-gray-800">Менеджер<span class="text-primary">Плюс</span></h1>
            </div>
        </a>

        <div class="mb-8 hover:fill-[#16a34a]">
                <div class="hover:bg-green-50 cursor-pointer p-2 rounded-[50px]">
                <a href="{{route('welcome')}}" class="flex align-items-center">

                    <svg width="24" height="24" fill="#374151" viewBox="0 0 24 24" id="injected-svg">
                        <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                        <path
                            d="M9 15.59 4.71 11.3 3.3 12.71l5 5c.2.2.45.29.71.29s.51-.1.71-.29l11-11-1.41-1.41L9.02 15.59Z"/>
                    </svg>
                    <h2 class="text-md font-semibold text-gray-700 ml-2">Мои задачи</h2>
                </a>
            </div>
                @if(isset(auth()->user()->role->name))
                    @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))
                        <div class="hover:bg-green-50 cursor-pointer p-2 rounded-[50px]">
                            <a href="{{route('tasks.admin')}}" class="flex align-items-center">

                                <svg width="24" height="24" fill="#374151" viewBox="0 0 24 24" id="injected-svg" class="">
                                    <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                                    <path
                                        d="m21.41,6.09L12.41,2.09c-.26-.12-.55-.12-.81,0L2.59,6.09c-.36.16-.59.52-.59.91v3c0,.55.45,1,1,1v5c-.55,0-1,.45-1,1v4c0,.55.45,1,1,1h18c.55,0,1-.45,1-1v-4c0-.55-.45-1-1-1v-5c.55,0,1-.45,1-1v-3c0-.4-.23-.75-.59-.91Zm-17.41,1.56l8-3.55,8,3.55v1.35H4v-1.35Zm9,8.35h-2v-5h2v5Zm-7-5h2v5h-2v-5Zm14,9H4v-2h16v2Zm-2-4h-2v-5h2v5Z"/>
                                </svg>
                                <h2 class="text-md font-semibold text-gray-700 hover:text-[#16a34a] ml-2">Моя компания</h2>
                            </a>
                        </div>
                    @endif
                @endif
                <div class="p-2 cursor-pointer">
                    <div class="flex items-center justify-between mb-2 group relative">
                        <div class="flex align-items-center">
                            <svg width="24" height="24" fill="#374151" viewBox="0 0 24 24" id="injected-svg">
                                <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                                <path
                                    d="m21,6h-2v-2c0-1.1-.9-2-2-2H7c-1.1,0-2,.9-2,2v6h-2c-1.1,0-2,.9-2,2v9c0,.55.45,1,1,1h20c.55,0,1-.45,1-1v-13c0-1.1-.9-2-2-2Zm0,14h-7v-5h-4v5H3v-8h3c.55,0,1-.45,1-1v-7h10v3c0,.55.45,1,1,1h3v12Z"/>
                                <path d="M9 10H11V12H9z"/>
                                <path d="M9 6H11V8H9z"/>
                                <path d="M5 14H7V16H5z"/>
                                <path d="M17 14H19V16H17z"/>
                                <path d="M17 10H19V12H17z"/>
                                <path d="M13 6H15V8H13z"/>
                                <path d="M13 10H15V12H13z"/>
                            </svg>
                            <h2 class="text-md font-semibold text-gray-700 ml-2">Отделы</h2>
                        </div>
                        <button
                            onclick="openDepartmentModal()"
                            class="absolute top-1 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-primary hover:text-secondary"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    @if(isset($departments) && $departments->count() > 0)
                        <div class="space-y-2">
                            @foreach($departments as $department)
                                <div
                                    class="group flex items-center justify-between p-2 hover:bg-green-50 cursor-pointer rounded-[50px] workspace-item"
                                    data-workspace="alpha" onclick="openDepartmentMail('{{ $department->email }}')">
                                    <div class="flex items-center">
                                        <span class="text-gray-700">{{ $department->name }}</span>
                                        <div class="email-badge">{{ $department->unread_count ?? 0 }}</div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <button class="text-gray-400 hover:text-blue-500" title="Почта отдела">
                                            <i class="fas fa-envelope text-xs"></i>
                                        </button>
                                        @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))
                                            <button onclick="openEditDepartmentModal({{ $department->id }})"
                                                    class="text-gray-400 hover:text-primary opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Нет доступных отделов</p>
                    @endif
                </div>

                <div class="hover:bg-green-50 cursor-pointer p-2 rounded-[50px]">
                    <a href="{{route('tasks.admin')}}" class="flex align-items-center">

                        <svg width="24" height="24" fill="#374151" viewBox="0 0 24 24" id="injected-svg" class="">
                            <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                            <path
                                d="m21.41,6.09L12.41,2.09c-.26-.12-.55-.12-.81,0L2.59,6.09c-.36.16-.59.52-.59.91v3c0,.55.45,1,1,1v5c-.55,0-1,.45-1,1v4c0,.55.45,1,1,1h18c.55,0,1-.45,1-1v-4c0-.55-.45-1-1-1v-5c.55,0,1-.45,1-1v-3c0-.4-.23-.75-.59-.91Zm-17.41,1.56l8-3.55,8,3.55v1.35H4v-1.35Zm9,8.35h-2v-5h2v5Zm-7-5h2v5h-2v-5Zm14,9H4v-2h16v2Zm-2-4h-2v-5h2v5Z"/>
                        </svg>
                        <h2 class="text-md font-semibold text-gray-700 hover:text-[#16a34a] ml-2">Почта</h2>
                    </a>
                </div>
                <div class="hover:bg-green-50 cursor-pointer p-2 rounded-[50px]">
                    <a href="{{route('tasks.admin')}}" class="flex align-items-center">

                        <svg width="24" height="24" fill="#374151" viewBox="0 0 24 24" id="injected-svg" class="">
                            <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                            <path
                                d="m21.41,6.09L12.41,2.09c-.26-.12-.55-.12-.81,0L2.59,6.09c-.36.16-.59.52-.59.91v3c0,.55.45,1,1,1v5c-.55,0-1,.45-1,1v4c0,.55.45,1,1,1h18c.55,0,1-.45,1-1v-4c0-.55-.45-1-1-1v-5c.55,0,1-.45,1-1v-3c0-.4-.23-.75-.59-.91Zm-17.41,1.56l8-3.55,8,3.55v1.35H4v-1.35Zm9,8.35h-2v-5h2v5Zm-7-5h2v5h-2v-5Zm14,9H4v-2h16v2Zm-2-4h-2v-5h2v5Z"/>
                        </svg>
                        <h2 class="text-md font-semibold text-gray-700 hover:text-[#16a34a] ml-2">Почта</h2>
                    </a>
                </div><div class="hover:bg-green-50 cursor-pointer p-2 rounded-[50px]">
                    <a href="{{route('tasks.admin')}}" class="flex align-items-center">

                        <svg width="24" height="24" fill="#374151" viewBox="0 0 24 24" id="injected-svg" class="">
                            <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                            <path
                                d="m21.41,6.09L12.41,2.09c-.26-.12-.55-.12-.81,0L2.59,6.09c-.36.16-.59.52-.59.91v3c0,.55.45,1,1,1v5c-.55,0-1,.45-1,1v4c0,.55.45,1,1,1h18c.55,0,1-.45,1-1v-4c0-.55-.45-1-1-1v-5c.55,0,1-.45,1-1v-3c0-.4-.23-.75-.59-.91Zm-17.41,1.56l8-3.55,8,3.55v1.35H4v-1.35Zm9,8.35h-2v-5h2v5Zm-7-5h2v5h-2v-5Zm14,9H4v-2h16v2Zm-2-4h-2v-5h2v5Z"/>
                        </svg>
                        <h2 class="text-md font-semibold text-gray-700 hover:text-[#16a34a] ml-2">Мессенджер</h2>
                    </a>
                </div>
        </div>



        <div class="mb-8 p-2  cursor-pointer">
            <div class="flex items-center justify-between mb-4 group relative">
                <div class="flex align-items-center">
                    <svg width="24" height="24" fill="#16a34a" viewBox="0 0 24 24" transform="" id="injected-svg">
                        <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                        <path
                            d="M18 6h-8c-1.1 0-2 .9-2 2v13c0 .36.19.69.5.87s.69.17 1 0l4.49-2.66 4.49 2.66c.16.09.33.14.51.14s.34-.04.5-.13c.31-.18.5-.51.5-.87v-13c0-1.1-.9-2-2-2Zm0 8v5.25l-3.49-2.07a.98.98 0 0 0-1.02 0L10 19.25V8h8z"/>
                        <path d="M16 2H6c-1.1 0-2 .9-2 2v14h2V4h10z"/>
                    </svg>
                    <h2 class="text-md font-semibold text-gray-700 ml-2">Теги</h2>
                </div>
                <button onclick="openCategoryModal()"
                        class="absolute top-1 right-2 opacity-0 group-hover:opacity-100 transition-opacity text-primary hover:text-secondary">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Категории -->
            @if(isset($categories) && $categories->count() > 0)
                <div class="space-y-2">
                    @foreach($categories as $category)
                        <div
                            class="group flex items-center justify-between ml-4 hover:bg-green-50 cursor-pointer rounded-[50px] board-item"
                            data-board="development">
                            <div class="flex items-center space-x-3">
                                <svg width="24" height="24" fill="{{ $category->color }}" viewBox="0 0 24 24"
                                     transform="" id="injected-svg">
                                    <!-- Boxicons v3.0.6 https://boxicons.com | License  https://docs.boxicons.com/free -->
                                    <path
                                        d="m12,2C6.49,2,2,6.49,2,12s4.49,10,10,10c.27,0,.52-.11.71-.29l9-9c.19-.19.29-.44.29-.71,0-5.51-4.49-10-10-10ZM4,12c0-4.41,3.59-8,8-8,4.08,0,7.45,3.07,7.93,7.02-.14,0-.29-.02-.43-.02-4.69,0-8.5,3.81-8.5,8.5,0,.14.01.29.02.43-3.95-.48-7.02-3.85-7.02-7.93Z"/>
                                </svg>

                                <span class="text-gray-700">{{ $category->name }}</span>
                            </div>
                            @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))
                                <div
                                    class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button onclick="openEditCategoryModal({{ $category->id }})"
                                            class="text-gray-400 hover:text-primary p-1"
                                            title="Редактировать категорию">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button
                                        onclick="openDeleteCategoryModal({{ $category->id }}, {{ json_encode($category->name) }})"
                                        class="text-gray-400 hover:text-red-500 p-1"
                                        title="Удалить категорию">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Нет доступных категорий</p>
            @endif
        </div>

        <div class="mb-8">
            @if(isset($onlineUsersCount) && $onlineUsersCount > 0)
                <div class="mb-2">
                    <h3 class="text-md font-semibold text-white">
                        В сети ({{ $onlineUsersCount }})
                    </h3>

                </div>

                <div class="flex flex-wrap gap-2">
                    @if(isset($onlineUsers) && $onlineUsers->count() > 0)
                        @foreach($onlineUsers as $user)
                            <div class="avatar-container group relative">
                                <!-- Аватар с инициалами -->
                                <div class="avatar {{ $user['color'] ?? 'bg-blue-500' }}"
                                     title="{{ $user['name'] ?? 'Пользователь' }} - {{ $user['last_activity_text'] ?? 'Недавно' }}">
                                    {{ $user['initials'] ?? '??' }}
                                </div>

                                <!-- Индикатор онлайн -->
                                <div class="online-indicator"></div>

                                <!-- Всплывающая подсказка при наведении -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2
                                    px-2 py-1 bg-gray-800 text-white text-xs rounded
                                    opacity-0 group-hover:opacity-100 transition-opacity
                                    whitespace-nowrap z-10 pointer-events-none">
                                    {{ $user['name'] ?? 'Пользователь' }}
                                    <div class="text-green-400 text-xs">
                                        <i class="fas fa-circle mr-1"></i>Онлайн
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Если онлайн много, показываем счетчик -->
                        @php
                            $totalCompanyUsers = isset($team) ? $team->count() : 0;
                            $displayedUsers = isset($onlineUsers) ? $onlineUsers->count() : 0;
                            $moreOnline = $onlineUsersCount - $displayedUsers;
                        @endphp

                        @if($moreOnline > 0)
                            <div class="avatar-container">
                                <div class="avatar bg-gray-300 text-gray-700"
                                     title="Еще {{ $moreOnline }} онлайн">
                                    +{{ $moreOnline }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-gray-500 w-full">
                            <i class="fas fa-users text-2xl mb-2 block"></i>
                            <p>Нет данных об онлайн пользователях</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-users text-2xl mb-2 block"></i>
                    <p>Сейчас никого нет в сети</p>
                </div>
            @endif
        </div>

        <div class=" p-2" style="border: 1px solid #16a34a; border-radius: 10px;">
            <div class="" ><h6
                    class="font-heading text-sm/tighter font-bold -tracking-snug text-slate-600 mb-4 flex items-center">
                    <img src="{{asset('img/icons/disk.svg')}}" alt="">
                    <span class="ml-2">Файловое хранилище</span></h6>
                <div class="flex rounded-sm bg-slate-100 dark:bg-slate-900 overflow-hidden h-1.5">
                    <div
                        class="text-xs text-center px-1 text-white bg-green-600 bg-[length:theme(spacing.1.5)_theme(spacing.1.5)]"
                        style="width: 15%;"></div>
                </div>
                <div class="text-xs font-medium text-slate-400 mt-4">12.47 GB из 50 GB занято</div>
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="flex-1 min-h-[calc(100vh-80px)] "> <!-- Убрал p-6 и bg-gray-50 -->
        <!-- Главная страница -->
        <div id="home" class="page active-page p-6"> <!-- Добавил padding внутрь -->
            @yield('content')
        </div>

        <div class="chat-button" style="position: fixed; bottom: 10px; right: 20px;">
            <a href="{{route('chat')}}">
                <button class="bg-primary text-white p-2 rounded-full hover:bg-secondary transition-colors"
                        style="width: 70px;">
                    <i class="fas fa-comment-dots"></i>
                </button>
            </a>

        </div>
    </div>
</div>

<!-- Модальное окно для новой задачи -->
@include('partials.modal.task.create')

<!-- Модальное окно уведомлений -->
@include('partials.modal.notifications-modal')

<!-- Модальное окно профиля пользователя -->
@auth()
    @include('partials.modal.user-profile-modal')
@endauth


<!-- Модальное окно для новой категории -->
@include('partials.modal.category.create-category')

<!-- Модальное окно редактирования категории -->
@include('partials.modal.category.edit')

<!-- Модальное окно для нового отдела -->
@include('partials.modal.department.create')

<!-- Модальное окно редактирования отдела -->
@include('partials.modal.department.edit')

<!-- Модальное окно для нового пользователя -->
@include('partials.modal.user.create')

<!-- Модальное окно подтверждения удаления категории -->
@include('partials.modal.category.delete')


<script>
    // Глобальные переменные
    let currentModalType = '';
    let currentEditingTaskId = null;

    // ==================== ФУНКЦИИ ДЛЯ МОДАЛЬНЫХ ОКОН ====================

    // Открытие модальных окон
    function openTaskModal() {
        currentModalType = 'task';
        document.getElementById('taskModal').classList.remove('hidden');
        resetTaskForm();
    }

    // Функция для открытия редактирования задачи
    async function openEditTaskModal(taskId) {
        currentModalType = 'task';
        currentEditingTaskId = taskId;

        try {
            // Загружаем данные задачи
            const response = await fetch(`/tasks/${taskId}/get`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Заполняем форму данными
                const task = data.task;

                // Установить ID задачи
                let taskIdField = document.getElementById('task_id');
                if (!taskIdField) {
                    taskIdField = document.createElement('input');
                    taskIdField.type = 'hidden';
                    taskIdField.name = 'task_id';
                    taskIdField.id = 'task_id';
                    document.getElementById('taskForm').appendChild(taskIdField);
                }
                taskIdField.value = task.id;

                // Заполняем основные поля
                document.querySelector('input[name="name"]').value = task.name || '';
                document.querySelector('textarea[name="description"]').value = task.description || '';
                document.querySelector('select[name="priority"]').value = task.priority || 'средний';
                document.querySelector('select[name="department_id"]').value = task.department_id || '';
                document.querySelector('select[name="category_id"]').value = task.category_id || '';
                document.querySelector('select[name="user_id"]').value = task.user_id || '';
                document.querySelector('input[name="estimated_hours"]').value = task.estimated_hours || '';

                // Форматируем дату для datetime-local
                if (task.deadline) {
                    const deadlineDate = new Date(task.deadline);
                    const formattedDate = deadlineDate.toISOString().slice(0, 16);
                    document.querySelector('input[name="deadline"]').value = formattedDate;
                } else {
                    document.querySelector('input[name="deadline"]').value = '';
                }

                document.querySelector('select[name="status"]').value = task.status || 'назначена';


                // Обновляем заголовок и кнопку
                document.querySelector('#taskModal h3').textContent = 'Редактировать задачу';
                document.querySelector('#taskModal p').textContent = 'Редактирование информации о задаче';
                document.querySelector('#taskModal button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Сохранить изменения';

                // Показываем модальное окно
                document.getElementById('taskModal').classList.remove('hidden');
            } else {
                showNotification(data.message || 'Ошибка при загрузке задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка при загрузке задачи:', error);
            showNotification('Ошибка при загрузке задачи: ' + error.message, 'error');
        }
    }

    // Функция для сброса формы задачи
    function resetTaskForm() {
        document.getElementById('taskForm').reset();
        document.getElementById('fileList').innerHTML = '';
        // Установить заголовок для создания
        document.querySelector('#taskModal h3').textContent = 'Новая задача';
        document.querySelector('#taskModal p').textContent = 'Заполните информацию о задаче';
        document.querySelector('#taskModal button[type="submit"]').innerHTML = '<i class="fas fa-plus mr-2"></i>Создать задачу';

        // Удалить поле ID если есть
        const taskIdField = document.getElementById('task_id');
        if (taskIdField) {
            taskIdField.remove();
        }
        currentEditingTaskId = null;
    }


    // Закрытие модальных окон
    function closeTaskModal() {
        document.getElementById('taskModal').classList.add('hidden');
        resetTaskForm();
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

    function closeUserModal() {
        document.getElementById('newUserModal').classList.add('hidden');
        document.getElementById('userForm').reset();
    }

    function closeUserProfileModal() {
        document.getElementById('userProfileModal').classList.add('hidden');
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
        const originalText = submitButton.innerHTML;

        // Валидация обязательных полей
        const name = formData.get('name');
        const departmentId = formData.get('department_id');

        if (!name || !departmentId) {
            showNotification('Заполните обязательные поля: название задачи и отдел', 'error');
            return;
        }

        const isEditMode = !!formData.get('task_id');
        submitButton.innerHTML = isEditMode ? '<i class="fas fa-spinner fa-spin mr-2"></i>Сохранение...' : '<i class="fas fa-spinner fa-spin mr-2"></i>Создание...';
        submitButton.disabled = true;

        try {
            const taskId = formData.get('task_id');
            let url, method;

            if (taskId) {
                // Режим редактирования - используем POST с _method
                url = `/tasks/${taskId}/update`;
                method = 'POST';
                formData.append('_method', 'PUT'); // Для Laravel
            } else {
                // Режим создания
                url = '/tasks/store';
                method = 'POST';
            }

            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            });

            // Проверяем, является ли ответ JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Ожидался JSON, но получили: ${text.substring(0, 100)}`);
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            if (data.success) {
                closeTaskModal();
                showNotification(
                    isEditMode ? 'Задача успешно обновлена!' : 'Задача успешно создана!',
                    'success'
                );

                // Обновить список задач на странице
                if (typeof refreshTasks === 'function') {
                    refreshTasks();
                } else {
                    // Если функция не определена, перезагружаем страницу
                    window.location.reload();
                }
            } else {
                showNotification(data.message || 'Ошибка при сохранении задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification(error.message || 'Произошла ошибка при сохранении задачи', 'error');
        } finally {
            submitButton.innerHTML = originalText;
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


    // Модальное окно удаления категории
    let currentDeletingCategoryId = null;

    function openDeleteCategoryModal(categoryId, categoryName) {
        currentDeletingCategoryId = categoryId;

        // Устанавливаем название категории
        document.getElementById('deleteCategoryName').textContent = categoryName;

        // Устанавливаем ID категории в скрытое поле формы
        const categoryIdField = document.getElementById('delete_category_id');
        if (categoryIdField) {
            categoryIdField.value = categoryId;
        }

        // Показываем модальное окно
        document.getElementById('deleteCategoryModal').classList.remove('hidden');
    }

    function closeDeleteCategoryModal() {
        document.getElementById('deleteCategoryModal').classList.add('hidden');
        currentDeletingCategoryId = null;
    }

    // Обработчик отправки формы удаления
    document.getElementById('deleteCategoryForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        // Проверка, что category_id установлен
        const categoryId = document.getElementById('delete_category_id').value;
        if (!categoryId) {
            showNotification('Ошибка: ID категории не указан', 'error');
            return;
        }

        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Удаление...';
        submitButton.disabled = true;

        try {
            // Для отладки: посмотреть, что отправляется
            console.log('Отправляемые данные:', Object.fromEntries(formData));

            const response = await fetch('/category/delete', {
                method: 'POST', // Laravel требует POST для DELETE через метод-переопределение
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            // Для отладки
            console.log('Статус ответа:', response.status);
            console.log('Заголовки:', response.headers);

            const data = await response.json();
            console.log('Ответ сервера:', data);

            if (data.success) {
                closeDeleteCategoryModal();
                showNotification('Категория успешно удалена!', 'success');

                // Обновить список категорий на странице
                if (typeof refreshCategories === 'function') {
                    refreshCategories();
                } else {
                    window.location.reload();
                }
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при удалении категории: ' + error.message, 'error');
        } finally {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });
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


    function updateUserActivity() {
        fetch('/update-activity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log('Активность обновлена:', data);

                // После обновления активности, обновляем список онлайн пользователей
                updateOnlineUsers();
            })
            .catch(error => console.error('Ошибка обновления активности:', error));
    }

    // Функция для обновления списка онлайн пользователей через AJAX
    function updateOnlineUsers() {
        fetch('/get-online-users', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                // Обновляем только блок с онлайн пользователями
                updateOnlineUsersUI(data);
            })
            .catch(error => console.error('Ошибка получения онлайн пользователей:', error));
    }

    // Обновление интерфейса
    function updateOnlineUsersUI(data) {
        // Обновляем аватары онлайн пользователей
        const onlineUsersContainer = document.querySelector('.online-users-avatars');
        if (onlineUsersContainer && data.onlineUsers) {
            onlineUsersContainer.innerHTML = data.onlineUsers.map(user => `
            <div class="avatar-container group relative">
                <div class="avatar ${user.color}"
                     title="${user.name} - ${user.last_activity_text}">
                    ${user.initials}
                </div>
                <div class="online-indicator"></div>
            </div>
        `).join('');
        }

        // Обновляем счетчик
        const counterElement = document.querySelector('.online-users-count');
        if (counterElement && data.onlineUsersCount !== undefined) {
            counterElement.textContent = data.onlineUsersCount;
        }
    }

    // Обновлять активность каждые 30 секунд
    setInterval(updateUserActivity, 30000);

    // Обновлять список онлайн пользователей каждые 10 секунд
    setInterval(updateOnlineUsers, 10000);

    // При загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        updateUserActivity();
        // Запускаем первый раз через 2 секунды
        setTimeout(updateOnlineUsers, 2000);
    });

    // Отправляем запрос при закрытии вкладки
    window.addEventListener('beforeunload', function(event) {
        // Используем navigator.sendBeacon для надежной отправки при закрытии
        const data = new FormData();
        data.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        navigator.sendBeacon('/user-leaving', data);

        // Альтернатива: синхронный AJAX (менее надежно)
        // const xhr = new XMLHttpRequest();
        // xhr.open('POST', '/user-leaving', false);
        // xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        // xhr.send();
    });

    // Также отслеживаем видимость вкладки
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Вкладка стала невидимой (пользователь переключился на другую вкладку)
            fetch('/user-hidden', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ hidden: true })
            });
        } else {
            // Вкладка снова стала видимой
            updateUserActivity();
        }
    });

    // Heartbeat - периодическая проверка активности
    let lastActivity = Date.now();
    const INACTIVITY_TIMEOUT = 30000; // 30 секунд неактивности

    // Отслеживаем любую активность пользователя
    ['mousemove', 'keydown', 'click', 'scroll'].forEach(eventName => {
        document.addEventListener(eventName, function() {
            lastActivity = Date.now();
        });
    });

    // Проверяем неактивность каждые 10 секунд
    setInterval(function() {
        const now = Date.now();
        if (now - lastActivity > INACTIVITY_TIMEOUT) {
            // Пользователь неактивен более 30 секунд
            fetch('/user-inactive', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ inactive: true })
            });
        }
    }, 10000);
</script>


@stack('scripts')

</body>
</html>
