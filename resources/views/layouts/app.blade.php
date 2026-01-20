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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d'
                        },
                        brown: {
                            "50": "#fdf7f2",
                            "100": "#f9eee5",
                            "200": "#f2d9c5",
                            "300": "#e7bd9a",
                            "400": "#da9c6a",
                            "500": "#c47c45",
                            "600": "#a66238",
                            "700": "#854e2e",
                            "800": "#6a3e25",
                            "900": "#3d2416",
                        },
                        sidebar: {
                            bg: '#1a1f2e',
                            text: '#94a3b8',
                            active: '#ffffff',
                            hover: '#2d3447'
                        }
                    },
                    animation: {
                        'slide-in': 'slideIn 0.3s ease-out',
                        'pulse-glow': 'pulseGlow 2s infinite'
                    },
                    keyframes: {
                        slideIn: {
                            '0%': { transform: 'translateX(-20px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.7' }
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: "Inter", sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar:hover {
            box-shadow: 0 0 50px rgba(34, 197, 94, 0.1);
        }

        .nav-item {
            position: relative;
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(180deg, #22c55e, #16a34a);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-item:hover::before {
            transform: scaleY(1);
        }

        .nav-item.active::before {
            transform: scaleY(1);
        }

        .nav-item.active {
            background: rgba(34, 197, 94, 0.1);
        }

        .avatar-container {
            position: relative;
            transition: transform 0.2s ease;
        }

        .avatar-container:hover {
            transform: translateY(-2px);
        }

        .online-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 50%;
            border: 2px solid #1a1f2e;
            animation: pulseGlow 2s infinite;
        }

        .progress-bar {
            background: linear-gradient(90deg, #22c55e, #16a34a);
            border-radius: 9999px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 9999px;
            font-weight: 600;
        }

        .category-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .category-item:hover .category-dot {
            transform: scale(1.3);
        }

        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #4b5563 transparent;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 2px;
        }

        .dropdown-enter {
            animation: dropdownEnter 0.2s ease-out;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 10px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        @keyframes dropdownEnter {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset;
            -webkit-text-fill-color: #111827;
            transition: background-color 5000s ease-in-out 0s;
        }

        .dark input:-webkit-autofill,
        .dark input:-webkit-autofill:hover,
        .dark input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #374151 inset;
            -webkit-text-fill-color: white;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

<div class="flex min-h-screen">
    <!-- Боковая панель -->
    <div class="sidebar w-64 py-6 px-4 hidden sm:flex flex-col relative">
        <!-- Логотип -->
        <div class="mb-8">
            <a href="{{route('welcome')}}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg group-hover:shadow-primary-500/20 transition-all duration-300">
                    <i class="fas fa-tasks text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">Менеджер<span class="text-primary-500">Плюс</span></h1>
                    <p class="text-xs text-sidebar-text mt-1">Управление задачами</p>
                </div>
            </a>
        </div>

        <!-- Навигация -->
        <div class="space-y-2 flex-1 scrollbar-thin">
            <!-- Главное меню -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-sidebar-text uppercase tracking-wider mb-4 px-2">ГЛАВНОЕ</h3>

                <div class="space-y-1">
                    <a href="{{route('welcome')}}" class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover active">
                        <div class="w-8 h-8 rounded-lg bg-primary-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-check text-primary-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Мои задачи</span>
                        <div class="ml-auto">
                            <span class="badge">5</span>
                        </div>
                    </a>

                    <a href="{{route('tasks.admin')}}" class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-landmark text-purple-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Моя компания</span>
                    </a>
                    <a href="{{route('departments.index')}}" class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover">
                        <div class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-building text-orange-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Отделы</span>
                    </a>

                    <a href="{{route('team.index')}}" class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-users text-blue-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Пользователи</span>
                    </a>

                    <a href="{{route('chat')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover">
                        <div class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-comments text-pink-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Мессенджер</span>
                    </a>

                    <a href="{{route('chat')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover">
                        <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-envelope text-yellow-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Почта</span>
                    </a>

                    <a href="{{route('chat')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-sidebar-hover">
                        <div class="w-8 h-8 rounded-lg bg-brown-500/10 flex items-center justify-center mr-3">
                            <i class="fas fa-hard-drive text-brown-500 text-sm"></i>
                        </div>
                        <span class="font-medium">Хранилище</span>
                    </a>
                </div>
            </div>

            <!-- Теги -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4 px-2">
                    <h3 class="text-xs font-semibold text-sidebar-text uppercase tracking-wider">ТЕГИ</h3>
                    <button onclick="openCategoryModal()"
                            class="w-6 h-6 rounded-full bg-sidebar-hover flex items-center justify-center text-sidebar-text hover:text-white hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>

                <div class="space-y-1">
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <div class="group flex items-center justify-between px-4 py-2.5 text-sidebar-text hover:text-white hover:bg-sidebar-hover rounded-lg cursor-pointer transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="category-dot" style="background-color: {{ $category->color }}"></div>
                                    <span class="font-medium">{{ $category->name }}</span>
                                </div>
                                @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))
                                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="openEditCategoryModal({{ $category->id }})"
                                                class="w-6 h-6 rounded hover:bg-white/10 flex items-center justify-center"
                                                title="Редактировать">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button onclick="openDeleteCategoryModal({{ $category->id }}, {{ json_encode($category->name) }})"
                                                class="w-6 h-6 rounded hover:bg-white/10 flex items-center justify-center"
                                                title="Удалить">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="px-4 py-3 text-center">
                            <p class="text-sm text-sidebar-text">Нет доступных тегов</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Онлайн пользователи -->
            <div class="mb-4">
                <h3 class="text-xs font-semibold text-sidebar-text uppercase tracking-wider mb-3 px-2">В СЕТИ</h3>

                @if(isset($onlineUsersCount) && $onlineUsersCount > 0)
                    <div class="flex items-center mb-3">
                        <div class="flex -space-x-2 mr-3">
                            @if(isset($onlineUsers) && $onlineUsers->count() > 0)
                                @foreach($onlineUsers->take(3) as $user)
                                    <div class="avatar-container">
                                        <div class="w-8 h-8 rounded-full {{ $user['color'] ?? 'bg-gradient-to-br from-blue-500 to-purple-600' }}
                                                    flex items-center justify-center text-white text-xs font-bold shadow-lg"
                                             title="{{ $user['name'] ?? 'Пользователь' }}">
                                            {{ $user['initials'] ?? '??' }}
                                        </div>
                                        <div class="online-indicator"></div>
                                    </div>
                                @endforeach
                                @php
                                    $moreOnline = $onlineUsersCount - min(3, $onlineUsers->count());
                                @endphp
                                @if($moreOnline > 0)
                                    <div class="w-8 h-8 rounded-full bg-sidebar-hover flex items-center justify-center text-sidebar-text text-xs font-bold shadow-lg"
                                         title="Еще {{ $moreOnline }} онлайн">
                                        +{{ $moreOnline }}
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div>
                            <div class="text-white font-medium text-sm">{{ $onlineUsersCount }} онлайн</div>
                            <div class="text-sidebar-text text-xs">Активные сейчас</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-2">
                        <i class="fas fa-users text-sidebar-text text-lg mb-1"></i>
                        <p class="text-xs text-sidebar-text">Сейчас никого нет в сети</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Нижняя часть -->
        <div class="mt-auto space-y-4 pt-4 border-t border-white/10">


            <!-- Файловое хранилище -->
{{--            <div class="bg-gradient-to-r from-sidebar-hover to-transparent p-4 rounded-xl border border-white/5">--}}
{{--                <div class="flex items-center justify-between mb-3">--}}
{{--                    <div class="flex items-center">--}}
{{--                        <i class="fas fa-hard-drive text-primary-500 mr-2"></i>--}}
{{--                        <h6 class="font-medium text-white">Хранилище</h6>--}}
{{--                    </div>--}}
{{--                    <span class="text-xs text-sidebar-text">15%</span>--}}
{{--                </div>--}}
{{--                <div class="w-full bg-white/10 rounded-full h-1.5 mb-2 overflow-hidden">--}}
{{--                    <div class="progress-bar h-full rounded-full" style="width: 15%"></div>--}}
{{--                </div>--}}
{{--                <div class="text-xs text-sidebar-text">12.47 GB из 50 GB</div>--}}
{{--            </div>--}}

            <!-- Профиль пользователя -->
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-sidebar-hover transition-colors cursor-pointer"
                 onclick="userProfileModal()">
                <div class="flex items-center">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold shadow-lg">
                        <img class="rounded" src="{{auth()->user()->getAvatarUrlAttribute()}}" alt="">
                    </div>
                    <div class="ml-3">
                        <div class="text-white font-medium text-sm">{{ auth()->user()->name }} {{ auth()->user()->surname }}</div>
                        @if(auth()->user()->role)
                            <div class="text-sidebar-text text-xs">{{ auth()->user()->role->name }}</div>
                        @endif
                    </div>
                </div>
                <i class="fas fa-chevron-right text-sidebar-text text-sm"></i>
            </div>
        </div>

        <!-- Индикатор активности -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-primary-500 to-transparent opacity-20"></div>
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


    // Добавим интерактивности для сайдбара
    document.addEventListener('DOMContentLoaded', function() {
        // Добавляем анимацию при наведении на элементы
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Индикатор активного элемента
        const currentPath = window.location.pathname;
        navItems.forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            }
        });
    });

    // Добавим плавное появление элементов
    setTimeout(() => {
        const elements = document.querySelectorAll('.sidebar > *');
        elements.forEach((el, index) => {
            el.style.animation = `slide-in 0.3s ease-out ${index * 0.1}s both`;
        });
    }, 100);

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
