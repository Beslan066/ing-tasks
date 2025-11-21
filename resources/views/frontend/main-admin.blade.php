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
            <!-- Сортировка и информация -->
            <div class="flex justify-between items-center mb-6">
                <div class="text-gray-500">
                    Показано {{ $tasks->count() }} из {{ $tasks->total() }} задач
                </div>
                <div class="flex items-center space-x-4">
                    <select id="sortSelect" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="created_at_desc">Новые сначала</option>
                        <option value="created_at_asc">Старые сначала</option>
                        <option value="deadline_asc">Ближайший дедлайн</option>
                        <option value="deadline_desc">Дальний дедлайн</option>
                        <option value="priority_desc">Высокий приоритет</option>
                        <option value="name_asc">По названию (А-Я)</option>
                    </select>
                </div>
            </div>

            <!-- Таблица задач -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Задача</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Исполнитель</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Отдел</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Приоритет</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дедлайн</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr class="hover:bg-gray-50 transition
                    @if($task->trashed()) bg-red-50 border-l-4 border-red-400 @endif">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                {{ $task->name }}
                                                @if($task->trashed())
                                                    <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                            <i class="fas fa-trash mr-1"></i>Удалена
                                        </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                                            <div class="flex space-x-2 mt-1">
                                                @if($task->category)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $task->category->name }}
                                        </span>
                                                @endif
                                                @if($task->rejections_count > 0)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                          title="Количество отказов: {{ $task->rejections_count }}">
                                            <i class="fas fa-user-slash mr-1"></i>
                                            {{ $task->rejections_count }}
                                        </span>
                                                @endif
                                                @if($task->trashed() && $task->deletedBy)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                          title="Удалил: {{ $task->deletedBy->name }}">
                                            <i class="fas fa-user-times mr-1"></i>
                                            Удалил: {{ $task->deletedBy->name }}
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->trashed())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
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
                                        @if($task->isOverdue())
                                            <span class="ml-1 text-xs text-red-600">⚠️ Просрочена</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($task->user)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ substr($task->user->name, 0, 2) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $task->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $task->user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Не назначен</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $task->department->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $priorityColors = [
                                            'низкий' => 'bg-gray-100 text-gray-800',
                                            'средний' => 'bg-blue-100 text-blue-800',
                                            'высокий' => 'bg-orange-100 text-orange-800',
                                            'критический' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    @if(!$task->trashed())
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $task->priority }}
                            </span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($task->deadline && !$task->trashed())
                                        <div class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $task->deadline->format('d.m.Y H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $task->getTimeRemaining() }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($task->trashed())
                                        <span class="text-gray-400">Действия недоступны</span>
                                    @else
                                        <div class="flex space-x-2">
                                            <button onclick="openEditModal({{ $task->id }})"
                                                    class="text-blue-600 hover:text-blue-900"><i class="fa-solid fa-file-pen" style="color: #854d0e;"></i></button>

                                            @if($task->status === 'на проверке')
                                                <button onclick="returnToWork({{ $task->id }})"
                                                        class="text-orange-600 hover:text-orange-900">Вернуть</button>
                                            @endif

                                            @if($task->author_id === Auth::id())
                                                <button onclick="openDeleteModal({{ $task->id }})"
                                                        class="text-red-600 hover:text-red-900"><i class="fa-solid fa-trash" style="color: #dc2626;"></i></button>
                                            @else
                                                <button class="text-gray-400 cursor-not-allowed" title="Можно удалять только свои задачи">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Задачи не найдены
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($tasks->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования задачи -->
    <div id="editTaskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Редактирование задачи</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editTaskForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Название задачи -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Название задачи *</label>
                        <input type="text" name="name" id="editTaskName"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <!-- Описание -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Описание</label>
                        <textarea name="description" id="editTaskDescription" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- Отдел -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Отдел *</label>
                        <select name="department_id" id="editTaskDepartment"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Выберите отдел</option>
                        </select>
                    </div>

                    <!-- Категория -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Категория</label>
                        <select name="category_id" id="editTaskCategory"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Без категории</option>
                        </select>
                    </div>

                    <!-- Исполнитель -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Исполнитель</label>
                        <select name="user_id" id="editTaskUser"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Не назначен</option>
                        </select>
                    </div>

                    <!-- Приоритет -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Приоритет *</label>
                        <select name="priority" id="editTaskPriority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="низкий">Низкий</option>
                            <option value="средний">Средний</option>
                            <option value="высокий">Высокий</option>
                            <option value="критический">Критический</option>
                        </select>
                    </div>

                    <!-- Статус -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Статус *</label>
                        <select name="status" id="editTaskStatus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="не назначена">Не назначена</option>
                            <option value="назначена">Назначена</option>
                            <option value="в работе">В работе</option>
                            <option value="на проверке">На проверке</option>
                            <option value="выполнена">Выполнена</option>
                            <option value="просрочена">Просрочена</option>
                        </select>
                    </div>

                    <!-- Дедлайн -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Дедлайн</label>
                        <input type="datetime-local" name="deadline" id="editTaskDeadline"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Планируемое время -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Планируемое время (часы)</label>
                        <input type="number" name="estimated_hours" id="editTaskEstimatedHours" step="0.5" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Фактическое время -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Фактическое время (часы)</label>
                        <input type="number" name="actual_hours" id="editTaskActualHours" step="0.5" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- 🔥 НОВЫЙ БЛОК: История отказов -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            История отказов от задачи
                            <span id="rejectionsCount" class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full ml-2">0</span>
                        </label>

                        <div id="rejectionsList" class="space-y-3 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4">
                            <!-- Отказы будут загружаться здесь -->
                            <p class="text-gray-500 text-center py-4">Отказов нет</p>
                        </div>
                    </div>

                    <!-- 🔥 БЛОК: Управление файлами -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Файлы задачи</label>

                        <!-- Список существующих файлов -->
                        <div id="existingFiles" class="mb-4 space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <!-- Файлы будут загружаться сюда -->
                        </div>

                        <!-- Добавление новых файлов -->
                        <div class="border border-dashed border-gray-300 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Добавить новые файлы</label>
                            <input type="file" id="newFilesInput" multiple
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt">
                            <div id="newFilesList" class="mt-2 space-y-1">
                                <!-- Список выбранных файлов будет здесь -->
                            </div>
                            <button type="button" onclick="addNewFiles()"
                                    class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                                <i class="fas fa-plus mr-1"></i> Добавить файлы
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex space-x-4 mt-6 pt-4 border-t border-gray-200">
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Сохранить изменения</span>
                    </button>
                    <button type="button" onclick="closeEditModal()"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно возврата на доработку -->
    <div id="returnToWorkModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Возврат задачи на доработку</h3>
            <p class="text-gray-600 mb-4">Укажите комментарий для исполнителя:</p>
            <textarea id="returnComment" placeholder="Комментарий..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 h-24 resize-none"></textarea>
            <div class="flex space-x-3">
                <button onclick="confirmReturnToWork()" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                    Вернуть на доработку
                </button>
                <button onclick="closeReturnModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    Отмена
                </button>
            </div>
        </div>
    </div>

    <!-- Модальное окно удаления задачи -->
    <div id="deleteTaskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Удаление задачи</h3>
            <p class="text-gray-600 mb-4">Вы уверены, что хотите удалить эту задачу? Это действие нельзя отменить.</p>
            <div class="flex space-x-3">
                <button onclick="confirmDeleteTask()" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                    Да, удалить
                </button>
                <button onclick="closeDeleteModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400">
                    Отмена
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentTaskId = null;

        // ==================== ОБЩИЕ ФУНКЦИИ ====================

        // Переключение фильтров
        document.getElementById('filterToggle').addEventListener('click', function() {
            const panel = document.getElementById('filtersPanel');
            panel.classList.toggle('hidden');
        });

        // Сортировка
        document.getElementById('sortSelect').addEventListener('change', function() {
            const value = this.value;
            let sort, order;

            switch(value) {
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

        // Установка правильного значения в селекторе сортировки при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sort = urlParams.get('sort') || 'created_at';
            const order = urlParams.get('order') || 'desc';

            let selectedValue;
            switch(sort + '_' + order) {
                case 'created_at_desc':
                    selectedValue = 'created_at_desc';
                    break;
                case 'created_at_asc':
                    selectedValue = 'created_at_asc';
                    break;
                case 'deadline_asc':
                    selectedValue = 'deadline_asc';
                    break;
                case 'deadline_desc':
                    selectedValue = 'deadline_desc';
                    break;
                case 'priority_desc':
                    selectedValue = 'priority_desc';
                    break;
                case 'name_asc':
                    selectedValue = 'name_asc';
                    break;
                default:
                    selectedValue = 'created_at_desc';
            }

            document.getElementById('sortSelect').value = selectedValue;
        });

        // ==================== ФУНКЦИИ ДЛЯ АДМИНОВ ====================

        // Открыть модальное окно редактирования
        async function openEditModal(taskId) {
            currentTaskId = taskId;

            try {
                const response = await fetch(`/tasks/${taskId}/get`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Заполняем форму данными задачи
                    const task = data.task;
                    document.getElementById('editTaskName').value = task.name;
                    document.getElementById('editTaskDescription').value = task.description || '';

                    // Заполняем отделы
                    const departmentSelect = document.getElementById('editTaskDepartment');
                    departmentSelect.innerHTML = '<option value="">Выберите отдел</option>';
                    data.departments.forEach(dept => {
                        const option = new Option(dept.name, dept.id, dept.id == task.department_id, dept.id == task.department_id);
                        departmentSelect.add(option);
                    });

                    // Заполняем категории
                    const categorySelect = document.getElementById('editTaskCategory');
                    categorySelect.innerHTML = '<option value="">Без категории</option>';
                    data.categories.forEach(cat => {
                        const option = new Option(cat.name, cat.id, cat.id == task.category_id, cat.id == task.category_id);
                        categorySelect.add(option);
                    });

                    // Заполняем пользователей
                    const userSelect = document.getElementById('editTaskUser');
                    userSelect.innerHTML = '<option value="">Не назначен</option>';
                    data.users.forEach(user => {
                        const option = new Option(`${user.name} (${user.email})`, user.id, user.id == task.user_id, user.id == task.user_id);
                        userSelect.add(option);
                    });

                    document.getElementById('editTaskPriority').value = task.priority;
                    document.getElementById('editTaskStatus').value = task.status;
                    document.getElementById('editTaskDeadline').value = task.deadline ? task.deadline.slice(0, 16) : '';
                    document.getElementById('editTaskEstimatedHours').value = task.estimated_hours || '';
                    document.getElementById('editTaskActualHours').value = task.actual_hours || '';

                    // 🔥 ОТОБРАЖАЕМ СУЩЕСТВУЮЩИЕ ФАЙЛЫ
                    displayExistingFiles(task.files);

                    // 🔥 ОТОБРАЖАЕМ ИСТОРИЮ ОТКАЗОВ
                    displayRejections(task.rejections);

                    document.getElementById('editTaskModal').classList.remove('hidden');
                } else {
                    alert(data.message || 'Ошибка при загрузке данных задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при загрузке данных задачи');
            }
        }

        // Закрыть модальное окно редактирования
        function closeEditModal() {
            document.getElementById('editTaskModal').classList.add('hidden');
            // Очищаем списки
            document.getElementById('existingFiles').innerHTML = '';
            document.getElementById('newFilesList').innerHTML = '';
            document.getElementById('rejectionsList').innerHTML = '';
            document.getElementById('newFilesInput').value = '';
            currentTaskId = null;
        }

        // Сохранить изменения задачи
        document.getElementById('editTaskForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/tasks/${currentTaskId}/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Задача успешно обновлена!');
                    closeEditModal();
                    location.reload();
                } else {
                    alert(result.message || 'Ошибка при обновлении задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при обновлении задачи');
            }
        });

        // Открыть модальное окно возврата на доработку
        function returnToWork(taskId) {
            currentTaskId = taskId;
            document.getElementById('returnToWorkModal').classList.remove('hidden');
        }

        // Закрыть модальное окно возврата
        function closeReturnModal() {
            document.getElementById('returnToWorkModal').classList.add('hidden');
            document.getElementById('returnComment').value = '';
            currentTaskId = null;
        }

        // Подтвердить возврат на доработку
        async function confirmReturnToWork() {
            const comment = document.getElementById('returnComment').value.trim();

            try {
                const response = await fetch(`/tasks/${currentTaskId}/return-to-work`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ comment })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Задача возвращена на доработку!');
                    closeReturnModal();
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при возврате задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при возврате задачи');
            }
        }

        // Обновленная функция для модального окна удаления
        function openDeleteModal(taskId) {
            currentTaskId = taskId;
            document.getElementById('deleteTaskModal').classList.remove('hidden');
        }

        // Закрыть модальное окно удаления
        function closeDeleteModal() {
            document.getElementById('deleteTaskModal').classList.add('hidden');
            currentTaskId = null;
        }

        // Подтвердить удаление задачи
        async function confirmDeleteTask() {
            if (!confirm('Вы уверены, что хотите удалить эту задачу?')) {
                return;
            }

            try {
                const response = await fetch(`/tasks/${currentTaskId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Задача успешно удалена!');
                    closeDeleteModal();
                    location.reload();
                } else {
                    alert(data.message || 'Ошибка при удалении задачи');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при удалении задачи');
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ РАБОТЫ С ФАЙЛАМИ ====================

        // Отобразить существующие файлы
        function displayExistingFiles(files) {
            const filesContainer = document.getElementById('existingFiles');
            filesContainer.innerHTML = '';

            if (files && files.length > 0) {
                files.forEach(file => {
                    const fileElement = document.createElement('div');
                    fileElement.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg mb-2';
                    fileElement.innerHTML = `
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
                    <button type="button" onclick="deleteFile(${file.id})"
                            class="text-red-600 hover:text-red-800 p-1 rounded transition">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                    filesContainer.appendChild(fileElement);
                });
            } else {
                filesContainer.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">Файлы отсутствуют</p>';
            }
        }

        // Удалить файл
        async function deleteFile(fileId) {
            if (!confirm('Вы уверены, что хотите удалить этот файл?')) {
                return;
            }

            try {
                const response = await fetch(`/files/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Файл успешно удален');
                    // Перезагружаем данные задачи чтобы обновить список файлов
                    openEditModal(currentTaskId);
                } else {
                    alert(data.message || 'Ошибка при удалении файла');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при удалении файла');
            }
        }

        // Показать выбранные файлы
        function displaySelectedFiles() {
            const fileInput = document.getElementById('newFilesInput');
            const filesList = document.getElementById('newFilesList');
            filesList.innerHTML = '';

            if (fileInput.files.length > 0) {
                Array.from(fileInput.files).forEach((file, index) => {
                    const fileElement = document.createElement('div');
                    fileElement.className = 'flex items-center justify-between bg-blue-50 p-2 rounded mb-1';
                    fileElement.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-file text-blue-500"></i>
                        <span class="text-blue-700 text-sm">${file.name}</span>
                        <span class="text-xs text-blue-600">(${Math.round(file.size / 1024)} KB)</span>
                    </div>
                    <button type="button" onclick="removeSelectedFile(${index})"
                            class="text-red-500 hover:text-red-700 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                    filesList.appendChild(fileElement);
                });
            }
        }

        // Удалить выбранный файл из списка
        function removeSelectedFile(index) {
            const fileInput = document.getElementById('newFilesInput');
            const files = Array.from(fileInput.files);
            files.splice(index, 1);

            // Создаем новый FileList
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;

            displaySelectedFiles();
        }

        // Добавить новые файлы к задаче
        async function addNewFiles() {
            const fileInput = document.getElementById('newFilesInput');

            if (!fileInput.files.length) {
                alert('Пожалуйста, выберите файлы для добавления');
                return;
            }

            const formData = new FormData();
            Array.from(fileInput.files).forEach(file => {
                formData.append('files[]', file);
            });

            try {
                const response = await fetch(`/tasks/${currentTaskId}/add-files`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('Файлы успешно добавлены!');
                    // Очищаем input
                    fileInput.value = '';
                    document.getElementById('newFilesList').innerHTML = '';
                    // Перезагружаем данные задачи
                    openEditModal(currentTaskId);
                } else {
                    alert(data.message || 'Ошибка при добавлении файлов');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('Ошибка при добавлении файлов');
            }
        }

        // ==================== ФУНКЦИИ ДЛЯ ОТОБРАЖЕНИЯ ОТКАЗОВ ====================

        // Отобразить историю отказов
        function displayRejections(rejections) {
            const rejectionsContainer = document.getElementById('rejectionsList');
            const rejectionsCount = document.getElementById('rejectionsCount');

            if (rejections && rejections.length > 0) {
                rejectionsCount.textContent = rejections.length;

                rejectionsContainer.innerHTML = rejections.map(rejection => `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center text-red-600">
                                    <i class="fas fa-user-slash text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-red-800">${rejection.user?.name || 'Пользователь'}</div>
                                    <div class="text-xs text-red-600">${formatDateTime(rejection.created_at)}</div>
                                </div>
                            </div>
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                Отказ
                            </span>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm text-red-700 bg-red-100 p-3 rounded-lg">${rejection.reason || 'Причина не указана'}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                rejectionsCount.textContent = '0';
                rejectionsContainer.innerHTML = '<p class="text-gray-500 text-center py-4">Отказов нет</p>';
            }
        }

        // Форматирование даты
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

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Обработчик для отображения выбранных файлов
            const fileInput = document.getElementById('newFilesInput');
            if (fileInput) {
                fileInput.addEventListener('change', displaySelectedFiles);
            }
        });
    </script>
@endsection
