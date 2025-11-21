<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>МенеджерПлюс - Современная система управления задачами</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>
<body class="bg-white font-sans">
<!-- Навигация -->
<nav class="bg-white border-b border-gray-200 py-4 px-6 flex justify-between items-center">
    <a href="{{route('welcome')}}">
        <div class="flex items-center space-x-2">
            <h1 class="text-2xl font-bold text-gray-800">Менеджер<span class="text-primary">Плюс</span></h1>
        </div>
    </a>

    <div class="hidden md:flex space-x-8">
        <a href="{{route('welcome')}}" class="nav-link active-nav text-gray-700 hover:text-primary transition-colors" data-page="welcome">Главная</a>
        @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))
            <a href="{{route('departments.index')}}" class="nav-link text-gray-700 hover:text-primary transition-colors" data-page="boards">Отделы</a>
        @endif
        <a href="{{route('team.index')}}" class="nav-link text-gray-700 hover:text-primary transition-colors" data-page="team">Команда</a>
        <a href="{{route('photobank')}}" class="nav-link text-gray-700 hover:text-primary transition-colors" data-page="team">Фотобанк</a>
        <a href="{{route('mail.index')}}" class="nav-link text-gray-700 hover:text-primary transition-colors" data-page="mail">Почта</a>
    </div>

    @auth()
        <div class="flex items-center space-x-4">
            <!-- Кнопка чата -->

            <div class="flex items-center space-x-2 cursor-pointer" id="userMenuBtn" onclick="userProfileModal()">
                <div class="avatar-container">
                    <div class="avatar bg-gradient-to-r from-primary to-secondary">
                        {{mb_substr(auth()->user()->name, 0,1)}}
                    </div>
                </div>
                @if(auth())
                    <span class="hidden md:block font-medium text-gray-700">{{auth()->user()->name}}</span>
                @endif
                <i class="fas fa-chevron-down text-gray-500"></i>
            </div>
        </div>
    @endauth
</nav>

<div class="flex">
    <!-- Боковая панель -->
    <div class="sidebar w-64 bg-white border-r border-gray-200 py-6 px-4">
        <div class="mb-8">
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    <div>
                        <p class="font-medium " style="color: #16a34a; font-weight: 600 ">{{auth()->user()->company->name}}</p>
                        <p class="text-xs text-gray-800">Участников: <span style="color: #16a34a; font-weight: 8">{{auth()->user()->company->users()->count()}}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Мои задачи</h2>
                <button class="text-primary hover:text-secondary">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <span class="text-gray-700">В работе</span>
                    </div>
                    <span class="bg-primary text-white text-xs rounded-full px-2 py-1">5</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                        <span class="text-gray-700">На проверке</span>
                    </div>
                    <span class="bg-yellow-500 text-white text-xs rounded-full px-2 py-1">3</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-gray-700">Завершённые</span>
                    </div>
                    <span class="bg-green-500 text-white text-xs rounded-full px-2 py-1">12</span>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Отделы</h2>
                <button onclick="openDepartmentModal()" class="text-primary hover:text-secondary">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            @if(isset($departments) && $departments->count() > 0)
                <div class="space-y-2">
                    @foreach($departments as $department)
                        <div class="group flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 cursor-pointer workspace-item"
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

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Категории</h2>
                <button onclick="openCategoryModal()" class="text-primary hover:text-secondary">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Категории -->
            @if(isset($categories) && $categories->count() > 0)
                <div class="space-y-2">
                    @foreach($categories as $category)
                        <div
                            class="group flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 cursor-pointer board-item"
                            data-board="development">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded" style="background-color: {{ $category->color }}"></div>
                                <span class="text-gray-700">{{ $category->name }}</span>
                            </div>
                            @if(in_array(auth()->user()->role->name, ['Руководитель', 'Менеджер']))
                                <button onclick="openEditCategoryModal({{ $category->id }})"
                                        class="text-gray-400 hover:text-primary opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">Нет доступных категорий</p>
            @endif
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Пользователи онлайн</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <!-- Пример пользователей онлайн -->
                <div class="avatar-container">
                    <div class="avatar bg-blue-500">
                        БШ
                    </div>
                    <div class="online-indicator"></div>
                </div>
                <div class="avatar-container">
                    <div class="avatar bg-purple-500">
                        АИ
                    </div>
                    <div class="online-indicator"></div>
                </div>
                <div class="avatar-container">
                    <div class="avatar bg-red-500">
                        МК
                    </div>
                    <div class="online-indicator"></div>
                </div>
                <div class="avatar-container">
                    <div class="avatar bg-yellow-500">
                        ДП
                    </div>
                    <div class="online-indicator"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="flex-1 p-6 bg-gray-50">
        <!-- Главная страница -->
        <div id="home" class="page active-page">
            @yield('content')
        </div>

        <div class="chat-button" style="position: fixed; bottom: 10px; right: 20px;">
            <button class="bg-primary text-white p-2 rounded-full hover:bg-secondary transition-colors" style="width: 70px;">
                <i class="fas fa-comment-dots"></i>
            </button>
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

                // Очищаем и заполняем подзадачи
                const subtasksContainer = document.getElementById('subtasksContainer');
                subtasksContainer.innerHTML = '';

                if (task.subtasks && task.subtasks.length > 0) {
                    task.subtasks.forEach(subtask => {
                        addSubtaskWithValue(subtask.name);
                    });
                } else {
                    addSubtask(); // Добавляем пустое поле
                }

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
        const subtasksContainer = document.getElementById('subtasksContainer');
        subtasksContainer.innerHTML = '<div class="flex space-x-2 mb-2"><input type="text" name="subtasks[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Название подзадачи"><button type="button" onclick="removeSubtask(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"><i class="fas fa-times"></i></button></div>';

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

    // Функция для добавления подзадачи с значением
    function addSubtaskWithValue(value = '') {
        const container = document.getElementById('subtasksContainer');
        const div = document.createElement('div');
        div.className = 'flex space-x-2 mb-2';
        div.innerHTML = `
            <input type="text" name="subtasks[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Название подзадачи" value="${value}">
            <button type="button" onclick="removeSubtask(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
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

    // Остальные функции оставляем без изменений...
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
