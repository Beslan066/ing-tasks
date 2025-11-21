@extends('layouts.app')

@section('content')
    <!-- Заголовок и статистика -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-dark">Мои задачи</h1>
            <p class="text-gray-500">{{ $user->company->name }} • {{ $stats['in_progress'] }} активных задач</p>
        </div>
        <div class="flex space-x-4">
            <button
                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition">
                <i class="fas fa-filter"></i>
                <span>Фильтр</span>
            </button>
        </div>
    </div>

    <!-- Доска с задачами -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Колонка "Новые" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="new">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">Новые</h3>
                <span class="bg-gray-200 text-gray-700 text-xs font-medium px-2 py-1 rounded">{{ $stats['new'] }}</span>
            </div>

            <div class="space-y-4 task-container" data-status="new">
                @foreach($tasksByStatus['new'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="{{ $task->id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600" onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs" title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}</p>
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $task->department->name }}</span>
                                @if($task->priority === 'высокий')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">‼️ Высокий</span>
                                @elseif($task->priority === 'критический')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">🚨 Критический</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($task->deadline && $task->deadline->isPast())
                                    <span class="text-xs text-red-600">⚠️ Просрочена</span>
                                @endif
                                <button onclick="startTask({{ $task->id }})">
                                    <i class="fa-solid fa-check" style="color: #166534;"></i>
                                </button>
                                <button onclick="showRejectModal({{ $task->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    <i class="fa-solid fa-rectangle-xmark" style="color: #dc2626;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "В работе" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="in-progress">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">В работе</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">{{ $stats['in_progress'] }}</span>
            </div>

            <div class="space-y-4 task-container" data-status="in-progress">
                @foreach($tasksByStatus['in_progress'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="{{ $task->id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600" onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs" title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}</p>

                        @if($task->deadline)
                            <div class="mb-3">
                                <div class="flex items-center text-sm {{ $task->deadline->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $task->deadline->format('d.m.Y H:i') }}
                                    @if($task->deadline->isPast())
                                        <span class="ml-1">(Просрочено)</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1">
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ $task->department->name }}</span>
                                @if($task->category)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $task->category->name }}</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="sendForReview({{ $task->id }})"
                                        class=" text-white px-3 py-1 rounded text-sm ">
                                    <i class="fa-solid fa-check" style="color: #166534;"></i>
                                </button>
                                <button onclick="showRejectModal({{ $task->id }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fa-solid fa-rectangle-xmark" style="color: #dc2626;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "На проверке" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="review">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">На проверке</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded">{{ $stats['review'] }}</span>
            </div>

            <div class="space-y-4 task-container" data-status="review">
                @foreach($tasksByStatus['review'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow cursor-move" draggable="true" data-task="{{ $task->id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600" onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center text-white text-xs" title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}</p>

                        @if($task->actual_hours)
                            <div class="mb-3">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-hourglass-end mr-2"></i>
                                    Фактическое время: {{ $task->actual_hours }}ч
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <div class="flex space-x-1">
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">{{ $task->department->name }}</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Ожидает проверки
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Колонка "Завершено" -->
        <div class="bg-gray-100 rounded-lg p-4 board-column" data-status="done">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">Завершено</h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">{{ $stats['done'] }}</span>
            </div>

            <div class="space-y-4 task-container" data-status="done">
                @foreach($tasksByStatus['done'] as $task)
                    <div class="task-card bg-white p-4 rounded-lg shadow opacity-70 cursor-move" draggable="true" data-task="{{ $task->id }}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium cursor-pointer hover:text-blue-600" onclick="openTaskViewModal({{ $task->id }})">
                                {{ $task->name }}
                            </h4>
                            <div class="flex space-x-1">
                                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center text-white text-xs" title="{{ $task->author->name }}">
                                    {{ substr($task->author->name, 0, 2) }}
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm mb-3">{{ Str::limit($task->description, 80) ?: 'Описание отсутствует' }}</p>

                        @if($task->actual_hours)
                            <div class="mb-3">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-hourglass-end mr-2"></i>
                                    Затрачено времени: {{ $task->actual_hours }}ч
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $task->department->name }}</span>
                            <span class="text-xs text-gray-500">Завершено</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра задачи -->
    <div id="taskViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Просмотр задачи</h3>
                <button onclick="closeTaskModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="taskModalContent">
                <!-- Контент будет загружаться здесь -->
            </div>
        </div>
    </div>

    <!-- Модальное окно для отказа от задачи -->
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
                <button onclick="closeRejectModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    Отмена
                </button>
            </div>
        </div>
    </div>

    <!-- Модальное окно для указания времени -->
    <div id="timeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Отправка на проверку</h3>
            <p class="text-gray-600 mb-4">Укажите фактическое время работы над задачей:</p>
            <input type="number" id="actualHours" step="0.5" min="0" placeholder="Часы"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
            <div class="flex space-x-3">
                <button onclick="submitForReview()" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                    Отправить на проверку
                </button>
                <button onclick="closeTimeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    Отмена
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentTaskId = null;

        // Открыть модальное окно просмотра задачи
        async function openTaskViewModal(taskId) {
            try {
                const response = await fetch(`/tasks/${taskId}/view`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const task = data.task;
                    const modalContent = document.getElementById('taskModalContent');

                    modalContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Основная информация -->
                    <div class="md:col-span-2">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">${task.name}</h4>
                        <p class="text-gray-600 mb-4">${task.description || 'Описание отсутствует'}</p>
                    </div>

                    <!-- Детали задачи -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(task.status)}">
                                ${task.status_icon || ''} ${task.status}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getPriorityColor(task.priority)}">
                                ${task.priority}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                            <p class="text-gray-900">${task.department.name}</p>
                        </div>

                        ${task.category ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                            <p class="text-gray-900">${task.category.name}</p>
                        </div>
                        ` : ''}

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Автор</label>
                            <p class="text-gray-900">${task.author.name}</p>
                        </div>
                    </div>

                    <!-- Временные параметры -->
                    <div class="space-y-4">
                        ${task.deadline ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Дедлайн</label>
                            <p class="text-gray-900 ${new Date(task.deadline) < new Date() ? 'text-red-600 font-semibold' : ''}">
                                ${formatDateTime(task.deadline)}
                                ${new Date(task.deadline) < new Date() ? '(Просрочено)' : ''}
                            </p>
                        </div>
                        ` : ''}

                        ${task.estimated_hours ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Планируемое время</label>
                            <p class="text-gray-900">${task.estimated_hours} часов</p>
                        </div>
                        ` : ''}

                        ${task.actual_hours ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Фактическое время</label>
                            <p class="text-gray-900">${task.actual_hours} часов</p>
                        </div>
                        ` : ''}

                        ${task.completed_at ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Завершено</label>
                            <p class="text-gray-900">${formatDateTime(task.completed_at)}</p>
                        </div>
                        ` : ''}

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Создана</label>
                            <p class="text-gray-900">${formatDateTime(task.created_at)}</p>
                        </div>
                    </div>

                    <!-- Файлы -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Прикрепленные файлы</label>
                        ${task.files && task.files.length > 0 ? `
                            <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                ${task.files.map(file => `
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-paperclip text-gray-500"></i>
                                            <div>
                                                <a href="/storage/${file.file_path}" target="_blank"
                                                   class="text-blue-600 hover:text-blue-800 font-medium block">
                                                    ${file.name}
                                                </a>
                                                <span class="text-xs text-gray-500">
                                                    ${Math.round(file.file_size / 1024)} KB •
                                                    ${formatDateTime(file.created_at)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `
                            <p class="text-gray-500 text-center py-4">Файлы отсутствуют</p>
                        `}
                    </div>
                </div>

${task.rejections && task.rejections.length > 0 ? `
<div class="md:col-span-2">
    <label class="block text-sm font-medium text-gray-700 mb-3">История отказов</label>
    <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
        ${task.rejections.map(rejection => `
            <div class="bg-red-50 border border-red-200 rounded p-3">
                <div class="flex justify-between items-start mb-1">
                    <span class="text-sm font-medium text-red-800">${rejection.user?.name || 'Пользователь'}</span>
                    <span class="text-xs text-red-600">${formatDateTime(rejection.created_at)}</span>
                </div>
                <p class="text-sm text-red-700">${rejection.reason}</p>
            </div>
        `).join('')}
    </div>
</div>
` : ''}

                <!-- Кнопки действий -->
                <div class="flex space-x-3 mt-6 pt-4 border-t border-gray-200">
                    ${task.status === 'назначена' ? `
                        <button onclick="startTask(${task.id})"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i>Начать работу
                        </button>
                    ` : ''}

                    ${task.status === 'в работе' ? `
                        <button onclick="sendForReview(${task.id})"
                                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-check-circle mr-2"></i>Отправить на проверку
                        </button>
                    ` : ''}

                    ${task.status !== 'выполнена' ? `
                        <button onclick="showRejectModal(${task.id})"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>Отказаться от задачи
                        </button>
                    ` : ''}

                    <button onclick="closeTaskViewModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        Закрыть
                    </button>
                </div>
            `;

                    document.getElementById('taskViewModal').classList.remove('hidden');
                } else {
                    alert(data.message || 'Ошибка при загрузке данных задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при загрузке данных задачи');
            }
        }

        // Закрыть модальное окно просмотра задачи
        function closeTaskViewModal() {
            document.getElementById('taskViewModal').classList.add('hidden');
            document.getElementById('taskModalContent').innerHTML = '';
        }

        // Закрыть модальное окно просмотра задачи
        function closeTaskModal() {
            document.getElementById('taskViewModal').classList.add('hidden');
            document.getElementById('taskModalContent').innerHTML = '';
        }

        // Вспомогательные функции
        function getStatusColor(status) {
            const colors = {
                'не назначена': 'bg-gray-100 text-gray-800',
                'назначена': 'bg-blue-100 text-blue-800',
                'в работе': 'bg-purple-100 text-purple-800',
                'на проверке': 'bg-yellow-100 text-yellow-800',
                'выполнена': 'bg-green-100 text-green-800',
                'просрочена': 'bg-red-100 text-red-800'
            };
            return colors[status] || colors['не назначена'];
        }

        function getPriorityColor(priority) {
            const colors = {
                'низкий': 'bg-gray-100 text-gray-800',
                'средний': 'bg-blue-100 text-blue-800',
                'высокий': 'bg-orange-100 text-orange-800',
                'критический': 'bg-red-100 text-red-800'
            };
            return colors[priority] || colors['средний'];
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

        // Остальные функции остаются без изменений
        async function takeAvailableTask(taskId) {
            if (!confirm('Взять эту задачу в работу?')) return;

            try {
                const response = await fetch(`/tasks/${taskId}/take`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Задача успешно взята в работу!');
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при взятии задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при взятии задачи');
            }
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
                    closeTaskModal();
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при обновлении статуса');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при обновлении статуса');
            }
        }

        async function sendForReview(taskId) {
            currentTaskId = taskId;
            closeTaskModal();
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
            closeTaskModal();
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

        // Закрытие модального окна при клике вне его
        document.addEventListener('click', function(e) {
            if (e.target.id === 'taskViewModal') {
                closeTaskModal();
            }
        });

        // Закрытие модального окна по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTaskModal();
            }
        });
    </script>

    <style>
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
    </style>
@endsection
