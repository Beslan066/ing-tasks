<div id="taskModal" class="fixed inset-0 modal-overlay flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white modal-content rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Заголовок -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-white to-gray-50">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Новая задача</h3>
                <p class="text-sm text-gray-500 mt-1">Заполните информацию о задаче</p>
            </div>
            <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Форма -->
        <form id="taskForm" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Название задачи *</label>
                    <input type="text" name="name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white placeholder-gray-400"
                           placeholder="Введите название задачи" required>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Приоритет *</label>
                    <select name="priority"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white appearance-none cursor-pointer">
                        <option value="низкий" class="priority-option">🟢 Низкий</option>
                        <option value="средний" selected class="priority-option">🟡 Средний</option>
                        <option value="высокий" class="priority-option">🔴 Высокий</option>
                        <option value="критический" class="priority-option">🟣 Критический</option>
                    </select>
                </div>
            </div>

            <!-- Описание -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold">Описание</label>
                <textarea name="description"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none bg-white placeholder-gray-400"
                          rows="4" placeholder="Подробное описание задачи..."></textarea>
            </div>

            <!-- Отдел и категория -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Отдел *</label>
                    <select name="department_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white appearance-none cursor-pointer"
                            required>
                        <option value="" class="text-gray-400">Выберите отдел</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Категория</label>
                    <select name="category_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white appearance-none cursor-pointer">
                        <option value="" class="text-gray-400">Выберите категорию</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Исполнитель и сроки -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Исполнитель</label>
                    <select name="user_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white appearance-none cursor-pointer">
                        <option value="" class="text-gray-400">Не назначено</option>
                        @foreach($assignableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Дедлайн</label>
                    <input type="datetime-local" name="deadline"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white cursor-pointer">
                </div>
            </div>

            <!-- Оценка времени и статус -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Планируемые часы</label>
                    <div class="relative">
                        <input type="number" name="estimated_hours" min="0" step="0.5"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white placeholder-gray-400 pr-12"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">часов</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Статус *</label>
                    <select name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all bg-white appearance-none cursor-pointer"
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

            <!-- Файлы -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold">Прикрепленные файлы</label>
                <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-6 text-center transition-all bg-gray-50 hover:bg-gray-100 cursor-pointer"
                     id="fileUploadArea" onclick="document.getElementById('fileInput').click()">
                    <input type="file" name="files[]" multiple class="hidden" id="fileInput">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-cloud-upload-alt text-lg text-primary"></i>
                        </div>
                        <p class="text-sm text-gray-600 mb-2 font-medium">Нажмите для выбора файлов</p>
                        <p class="text-xs text-gray-500">Поддерживаются: PDF, JPG, PNG, DOC • Макс. 10MB</p>
                    </div>
                    <div id="fileList" class="mt-4 text-left space-y-2 hidden"></div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeTaskModal()"
                        class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Отмена
                </button>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary font-medium transition-colors flex items-center shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Создать задачу
                </button>
            </div>
        </form>
    </div>
</div>
