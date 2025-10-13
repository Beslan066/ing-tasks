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
    </style>
</head>
<body class="bg-gray-50 font-sans">
<!-- Навигация -->
<nav class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <a href="{{route('home')}}">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                <i class="fas fa-tasks text-white"></i>
            </div>
            <span class="text-xl font-bold text-dark">TaskFlow</span>
        </div>
    </a>

    <div class="hidden md:flex space-x-6">
        <a href="{{route('home')}}" class="nav-link active-nav" data-page="home">Главная</a>
        <a href="{{route('departments.index')}}" class="nav-link" data-page="boards">Отделы</a>
        <a href="{{route('teams.index')}}" class="nav-link" data-page="team">Команда</a>
        <a href="{{route('tasks.index')}}" class="nav-link" data-page="tasks">Задачи</a>
        <a href="{{route('photobank')}}" class="nav-link" data-page="team">Фотобанк</a>
    </div>

    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2 cursor-pointer" id="userMenuBtn">
            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white font-semibold">
                ИИ
            </div>
            @if(auth())
                <span class="hidden md:block font-medium">{{auth()->user()->name}}</span>
            @endif
            <i class="fas fa-chevron-down text-gray-500"></i>
        </div>
    </div>
</nav>

<div class="flex">
    <!-- Боковая панель -->
    <div class="sidebar w-64 bg-white h-screen shadow-lg py-6 px-4">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-500">Отделы</h2>
                <button onclick="openDepartmentModal()" class="text-blue-500 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
            @if(isset($departments))
                @foreach($departments as $department)
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 cursor-pointer workspace-item" data-workspace="alpha">
                            <span>{{$department->name}}</span>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-500">Категории</h2>
                <button onclick="openCategoryModal()" class="text-blue-500 hover:text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
            @if(isset($categories))
                @foreach($categories as $category)
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 cursor-pointer board-item" data-board="development">
                            <div class="w-4 h-4 bg-blue-500 rounded"></div>
                            <span>{{$category->name}}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-500 mb-4">КОМАНДА</h2>
            <div class="space-y-2">
                <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 cursor-pointer user-item" data-user="anna">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>Анна Петрова</span>
                </div>
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
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новая задача</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="taskForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название задачи</label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Введите название задачи">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Описание</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" rows="3" placeholder="Опишите задачу"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Исполнитель</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option>Иван Иванов</option>
                    <option>Анна Петрова</option>
                    <option>Сергей Смирнов</option>
                    <option>Мария Козлова</option>
                    <option>Дмитрий Волков</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Метка</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    <option>Frontend</option>
                    <option>Backend</option>
                    <option>Дизайн</option>
                    <option>Тестирование</option>
                    <option>Документация</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelTask" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать задачу</button>
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
                    <p class="text-sm text-gray-600">Задача "Доработка главной страницы" должна быть завершена завтра</p>
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
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Профиль пользователя</h3>
            <button id="closeUserModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="text-center mb-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-primary to-secondary mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold">
                ИИ
            </div>
            <h3 class="font-bold text-lg">Иван Иванов</h3>
            <p class="text-gray-500">Team Lead / Fullstack Developer</p>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium">ivan@example.com</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Телефон:</span>
                <span class="font-medium">+7 (999) 123-45-67</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Отдел:</span>
                <span class="font-medium">Разработка</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Роль:</span>
                <span class="font-medium">Администратор</span>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button id="editProfile" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Редактировать</button>
            <button id="logout" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Выйти</button>
        </div>
    </div>
</div>


<!-- Модальное окно для новой категории -->
<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новая категория</h3>
            <button onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="categoryForm">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название категории</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Введите название категории" required>
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
                        <label for="color-yellow" class="w-8 h-8 bg-yellow-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#8B5CF6" class="hidden" id="color-purple">
                        <label for="color-purple" class="w-8 h-8 bg-purple-500 rounded-full cursor-pointer block"></label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать категорию</button>
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
                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Введите название отдела" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Компания</label>
                <select name="company_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">

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
                <button type="button" onclick="closeDepartmentModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать отдел</button>
            </div>
        </form>
    </div>
</div>



<script>
    // Глобальные переменные
    let currentModalType = '';

    // Функции для открытия модальных окон
    function openCategoryModal() {
        currentModalType = 'category';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function openDepartmentModal() {
        currentModalType = 'department';
        loadCompanies();
        document.getElementById('departmentModal').classList.remove('hidden');
    }

    // Функции для закрытия модальных окон
    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryForm').reset();
    }

    function closeDepartmentModal() {
        document.getElementById('departmentModal').classList.add('hidden');
        document.getElementById('departmentForm').reset();
    }

    // Загрузка компаний для выпадающего списка
    async function loadCompanies() {
        try {
            const response = await fetch('/companies/create');
            const data = await response.json();

            if (data.success) {
                const select = document.querySelector('select[name="company_id"]');
                select.innerHTML = '<option value="">Выберите компанию</option>';

                data.companies.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = company.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Ошибка при загрузке компаний:', error);
        }
    }

    // Обработка формы отдела
    document.getElementById('departmentForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Показываем индикатор загрузки
        submitButton.textContent = 'Создание...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/departments/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const data = await response.json();

            if (data.success) {
                // Закрываем модальное окно
                closeDepartmentModal();

                // Показываем уведомление об успехе
                showNotification('Отдел успешно создан!', 'success');

                // Обновляем список отделов на странице
                addDepartmentToList(data.department);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при создании отдела', 'error');
        } finally {
            // Восстанавливаем кнопку
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });

    // Функция для добавления отдела в список
    function addDepartmentToList(department) {
        const departmentsContainer = document.querySelector('.workspace-item').closest('.space-y-2');

        const newDepartment = document.createElement('div');
        newDepartment.className = 'flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 cursor-pointer workspace-item';
        newDepartment.setAttribute('data-workspace', department.id);

        newDepartment.innerHTML = `
        <span>${department.name}</span>
    `;

        departmentsContainer.appendChild(newDepartment);
    }


    // Обработка формы категории
    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;

        // Показываем индикатор загрузки
        submitButton.textContent = 'Создание...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/category/create', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const data = await response.json();

            if (data.success) {
                // Закрываем модальное окно
                closeCategoryModal();

                // Показываем уведомление об успехе
                showNotification('Категория успешно создана!', 'success');

                // Обновляем список категорий на странице
                addCategoryToList(data.category);
            } else {
                showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Произошла ошибка при создании категории', 'error');
        } finally {
            // Восстанавливаем кнопку
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    });
    // Функция для добавления категории в список
    function addCategoryToList(category) {
        const categoriesContainer = document.querySelector('.board-item').closest('.space-y-2');

        const newCategory = document.createElement('div');
        newCategory.className = 'flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 cursor-pointer board-item';
        newCategory.setAttribute('data-board', category.id);

        newCategory.innerHTML = `
        <div class="w-4 h-4 rounded" style="background-color: ${category.color}"></div>
        <span>${category.name}</span>
    `;

        categoriesContainer.appendChild(newCategory);
    }



    // Функция для показа уведомлений
    function showNotification(message, type = 'info') {
        // Создаем элемент уведомления
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                    'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Автоматически удаляем уведомление через 5 секунд
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Обработчики для кнопок "+"
    document.addEventListener('DOMContentLoaded', function() {
        // Обработчик для кнопки добавления категории
        const categoryAddButton = document.querySelector('button[onclick="openCategoryModal()"]');
        if (categoryAddButton) {
            categoryAddButton.addEventListener('click', openCategoryModal);
        }

        // Обработчик для кнопки добавления отдела
        const departmentAddButton = document.querySelector('button[onclick="openDepartmentModal()"]');
        if (departmentAddButton) {
            departmentAddButton.addEventListener('click', openDepartmentModal);
        }

        // Закрытие модальных окон при клике вне их
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed')) {
                if (currentModalType === 'category') {
                    closeCategoryModal();
                } else if (currentModalType === 'department') {
                    closeDepartmentModal();
                }
            }
        });
    });
</script>

<script>

    // Модальные окна
    document.getElementById('newTaskBtn').addEventListener('click', function() {
        document.getElementById('taskModal').classList.remove('hidden');
    });


    // Закрытие модальных окон
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('taskModal').classList.add('hidden');
    });

    document.getElementById('cancelTask').addEventListener('click', function() {
        document.getElementById('taskModal').classList.add('hidden');
    });


    // Обработка формы создания задачи
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Задача успешно создана!');
        document.getElementById('taskModal').classList.add('hidden');
        // Здесь будет код для добавления задачи на доску
    });

    // Перетаскивание задач
    let draggedTask = null;

    document.querySelectorAll('.task-card').forEach(task => {
        task.addEventListener('dragstart', function() {
            draggedTask = this;
            setTimeout(() => {
                this.style.opacity = '0.5';
            }, 0);
        });

        task.addEventListener('dragend', function() {
            setTimeout(() => {
                this.style.opacity = '1';
                draggedTask = null;
            }, 0);
        });
    });

    document.querySelectorAll('.board-column').forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.backgroundColor = '#e5e7eb';
        });

        column.addEventListener('dragleave', function() {
            this.style.backgroundColor = '#f3f4f6';
        });

        column.addEventListener('drop', function(e) {
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
    document.addEventListener('DOMContentLoaded', function() {
        updateTaskCounters();

        // Добавляем обработчики для элементов боковой панели
        document.querySelectorAll('.workspace-item, .board-item, .user-item').forEach(item => {
            item.addEventListener('click', function() {
                const type = this.classList.contains('workspace-item') ? 'workspace' :
                    this.classList.contains('board-item') ? 'board' : 'user';
                const id = this.getAttribute(`data-${type}`);

                // В реальном приложении здесь будет загрузка данных
                console.log(`Выбран ${type}: ${id}`);
            });
        });
    });
</script>

</body>
</html>
