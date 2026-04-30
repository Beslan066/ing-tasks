<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white modal-content rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-100">
            <div class="flex justify-between items-center p-6">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Новая задача</h3>
                    <p class="text-sm text-gray-500 mt-1">Заполните информацию о задаче</p>
                </div>
                <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-200 p-2 rounded-xl hover:bg-gray-100 hover:scale-110">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="taskForm" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="selected_files" id="selectedFiles" value="[]">

            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-tag text-green-500 mr-2 text-xs"></i>Название задачи *
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tasks text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="text" name="name"
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400 hover:border-gray-300"
                               placeholder="Введите название задачи" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-flag text-green-500 mr-2 text-xs"></i>Приоритет *
                    </label>
                    <div class="relative group">
                        <select name="priority"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                            <option value="низкий" class="priority-option">Низкий</option>
                            <option value="средний" selected class="priority-option">Средний</option>
                            <option value="высокий" class="priority-option">Высокий</option>
                            <option value="критический" class="priority-option">Критический</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Описание -->
            <div class="space-y-2">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    <i class="fas fa-align-left text-green-500 mr-2 text-xs"></i>Описание
                </label>
                <div class="relative group">
                    <textarea name="description"
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 resize-none bg-white placeholder-gray-400"
                              rows="4" placeholder="Подробное описание задачи..."></textarea>
                </div>
            </div>

            <!-- Отдел и категория -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-building text-green-500 mr-2 text-xs"></i>Отдел *
                    </label>
                    <div class="relative group">
                        <select name="department_id"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                            <option value="" class="text-gray-400">Выберите отдел</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-folder text-green-500 mr-2 text-xs"></i>Категория
                    </label>
                    <div class="relative group">
                        <select name="category_id"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                            <option value="" class="text-gray-400">Выберите категорию</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Исполнитель и сроки -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-user-check text-green-500 mr-2 text-xs"></i>Исполнитель
                    </label>
                    <div class="relative group">
                        <select name="user_id"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                            <option value="" class="text-gray-400">Не назначено</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-calendar-alt text-green-500 mr-2 text-xs"></i>Дедлайн
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="datetime-local" name="deadline"
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white cursor-pointer hover:border-gray-300">
                    </div>
                </div>
            </div>

            <!-- Оценка времени и статус -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-hourglass-half text-green-500 mr-2 text-xs"></i>Планируемые часы
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-stopwatch text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="number" name="estimated_hours" min="0" step="0.5"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm">часов</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-chart-line text-green-500 mr-2 text-xs"></i>Статус *
                    </label>
                    <div class="relative group">
                        <select name="status"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                required>
                            @php
                                $availableStatuses = array_filter(\App\Models\Task::getStatuses(), function($status) {
                                    return $status !== 'в работе';
                                });
                            @endphp
                            @foreach($availableStatuses as $status)
                                <option value="{{ $status }}" {{ $status == 'назначена' ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладки для файлов -->
            <div class="space-y-4">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-6" aria-label="Tabs">
                        <button type="button"
                                onclick="switchFileTab('storage')"
                                id="storageTab"
                                class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button active transition-all duration-200"
                                data-tab="storage">
                            <i class="fas fa-database mr-2"></i>Из хранилища
                        </button>
                        <button type="button"
                                onclick="switchFileTab('upload')"
                                id="uploadTab"
                                class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button transition-all duration-200"
                                data-tab="upload">
                            <i class="fas fa-cloud-upload-alt mr-2"></i>Новая загрузка
                        </button>
                    </nav>
                </div>

                <!-- Контейнер для файлов из хранилища -->
                <div id="storageTabContent" class="tab-content active">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Выберите файлы из хранилища</h4>
                            <p class="text-xs text-gray-500 mt-1">Файлы будут прикреплены к задаче</p>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="openFileManager()"
                                    class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                <i class="fas fa-folder-open mr-2"></i>Открыть хранилище
                            </button>
                            <button type="button" onclick="clearSelectedFiles()"
                                    class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-red-50 hover:border-red-300 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                <i class="fas fa-times mr-2"></i>Очистить
                            </button>
                        </div>
                    </div>

                    <!-- Выбранные файлы -->
                    <div id="selectedFilesContainer" class="space-y-3 min-h-[100px]">
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm text-gray-500">Файлы не выбраны</p>
                            <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                        </div>
                    </div>

                    <!-- Счетчик файлов -->
                    <div id="fileCounter" class="hidden text-sm text-gray-600 mt-3">
                        <i class="fas fa-paperclip mr-1"></i>
                        <span id="fileCount">0</span> файлов выбрано
                    </div>
                </div>

                <!-- Контейнер для загрузки новых файлов -->
                <div id="uploadTabContent" class="tab-content hidden">
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-700">Загрузите новые файлы</h4>
                        <p class="text-xs text-gray-500 mt-1">Файлы будут сохранены в хранилище и прикреплены к задаче</p>
                    </div>

                    <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 bg-gradient-to-br from-gray-50 to-white hover:from-green-50 hover:to-white cursor-pointer group"
                         onclick="document.getElementById('uploadNewFilesInput').click()">
                        <input type="file" name="new_files[]" multiple class="hidden" id="uploadNewFilesInput">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                            </div>
                            <p class="text-base font-medium text-gray-700 mb-2">Нажмите или перетащите файлы сюда</p>
                            <p class="text-sm text-gray-500">Поддерживаются: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP</p>
                            <p class="text-xs text-gray-400 mt-1">Максимальный размер: 10MB на файл</p>
                        </div>
                    </div>

                    <!-- Список новых файлов -->
                    <div id="uploadFilesList" class="space-y-3 mt-4 hidden">
                        <h5 class="text-sm font-semibold text-gray-700">Выбранные файлы:</h5>
                        <div id="uploadFilesContainer" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeTaskModal()"
                        class="px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-300 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Отмена
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>Создать задачу
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно файлового менеджера -->
<div id="fileManagerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
    <div class="bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
        <!-- Заголовок -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white">
            <div>
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Файловое хранилище</h3>
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
                        <!-- Файлы будут загружаться здесь -->
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600">Загрузка файлов...</p>
                        </div>
                    </div>
                </div>

                <!-- Предпросмотр файла (правая панель) -->
                <div id="fileManagerPreviewPanel" class="hidden w-96 border-l border-gray-200 bg-gray-50 p-4 overflow-y-auto">
                    <div class="sticky top-0 bg-gray-50 pb-4">
                        <button onclick="closeFilePreview()"
                                class="mb-4 text-gray-400 hover:text-gray-600 flex items-center transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i> Назад
                        </button>
                        <div id="filePreviewContent" class="space-y-4">
                            <!-- Контент предпросмотра -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Футер с кнопками -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600" id="fileManagerStorageInfo">
                    <i class="fas fa-hdd mr-1"></i>
                    <span id="fileManagerStorageUsed">0</span> из <span id="fileManagerStorageTotal">0</span> использовано
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeFileManager()"
                            class="px-5 py-2.5 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-300 font-medium transition-all duration-200">
                        Отмена
                    </button>
                    <button type="button" onclick="confirmFileSelection()"
                            class="px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-check mr-2"></i>Выбрать (<span id="confirmCount">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Стили для вкладок */
        .tab-button {
            border-color: transparent;
            color: #6b7280;
        }

        .tab-button:hover {
            color: #374151;
            border-color: #d1d5db;
        }

        .tab-button.active {
            border-color: #10b981;
            color: #10b981;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .file-item-enter {
            animation: slideIn 0.3s ease-out;
        }

        /* Стили для файлового менеджера */
        .file-card {
            transition: all 0.2s ease;
        }

        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .file-card.selected {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        /* Drag and drop стили */
        .file-upload-area.drag-over {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            transform: scale(0.98);
        }

        /* Кастомный скроллбар */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #059669, #047857);
        }

        /* Улучшенные стили для полей ввода */
        input, select, textarea {
            transition: all 0.2s ease;
        }

        input:focus, select:focus, textarea:focus {
            transform: translateY(-1px);
        }

        /* Стили для файлов в контейнере */
        .selected-file-item {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .selected-file-item:hover {
            border-color: #10b981;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
            transform: translateX(4px);
        }

        /* Анимация для модального окна */
        .modal-content {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
@endpush
