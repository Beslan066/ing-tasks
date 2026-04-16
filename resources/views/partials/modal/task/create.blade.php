<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white modal-content rounded-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar">
        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center p-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Новая задача</h3>
                    <p class="text-sm text-gray-500 mt-1">Заполните информацию о задаче</p>
                </div>
                <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="taskForm" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="selected_files" id="selectedFiles" value="[]">

            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Название задачи *</label>
                    <input type="text" name="name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white placeholder-gray-400"
                           placeholder="Введите название задачи" required>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Приоритет *</label>
                    <select name="priority"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white appearance-none cursor-pointer">
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
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all resize-none bg-white placeholder-gray-400"
                          rows="4" placeholder="Подробное описание задачи..."></textarea>
            </div>

            <!-- Отдел и категория -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Отдел *</label>
                    <select name="department_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white appearance-none cursor-pointer"
                            >
                        <option value="" class="text-gray-400">Выберите отдел</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Категория</label>
                    <select name="category_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white appearance-none cursor-pointer">
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white appearance-none cursor-pointer">
                        <option value="" class="text-gray-400">Не назначено</option>
                        @foreach($assignableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Дедлайн</label>
                    <input type="datetime-local" name="deadline"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white cursor-pointer">
                </div>
            </div>

            <!-- Оценка времени и статус -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Планируемые часы</label>
                    <div class="relative">
                        <input type="number" name="estimated_hours" min="0" step="0.5"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white placeholder-gray-400 pr-12"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">часов</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold">Статус *</label>
                    <select name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent transition-all bg-white appearance-none cursor-pointer"
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
                </div>
            </div>

            <!-- Вкладки для файлов -->
            <div class="space-y-4">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-6" aria-label="Tabs">
                        <button type="button"
                                onclick="switchFileTab('storage')"
                                id="storageTab"
                                class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button active"
                                data-tab="storage">
                            <i class="fas fa-database mr-2"></i>Из хранилища
                        </button>
                        <button type="button"
                                onclick="switchFileTab('upload')"
                                id="uploadTab"
                                class="py-2 px-1 border-b-2 font-medium text-sm focus:outline-none tab-button"
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
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-folder-open mr-2"></i>Открыть хранилище
                            </button>
                            <button type="button" onclick="clearSelectedFiles()"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-times mr-2"></i>Очистить
                            </button>
                        </div>
                    </div>

                    <!-- Выбранные файлы -->
                    <div id="selectedFilesContainer" class="space-y-3 min-h-[100px]">
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                            <i class="fas fa-folder-open text-3xl text-gray-300 mb-3"></i>
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

                    <div class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all bg-gray-50 hover:bg-gray-100 cursor-pointer"
                         onclick="document.getElementById('uploadNewFilesInput').click()">
                        <input type="file" name="new_files[]" multiple class="hidden" id="uploadNewFilesInput">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-cloud-upload-alt text-2xl text-green-600"></i>
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
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-gray-500">
                    Отмена
                </button>
                <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium transition-colors focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-green-500 flex items-center shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Создать задачу
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Модальное окно файлового менеджера -->
<div id="fileManagerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
    <div class="bg-white rounded-xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Заголовок -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Файловое хранилище</h3>
                <p class="text-sm text-gray-500 mt-1">Выберите файлы для прикрепления к задаче</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-600">
                    Выбрано: <span id="selectedCount" class="font-semibold">0</span>
                </span>
                <button onclick="closeFileManager()"
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Панель поиска и фильтров -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               id="fileManagerSearch"
                               placeholder="Поиск по названию файла..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent bg-white">
                    </div>
                </div>
                <div class="flex space-x-2">
                    <select id="fileManagerTypeFilter"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 bg-white text-sm">
                        <option value="">Все типы</option>
                        <option value="image">Изображения</option>
                        <option value="document">Документы</option>
                        <option value="video">Видео</option>
                        <option value="audio">Аудио</option>
                        <option value="archive">Архивы</option>
                    </select>
                    <select id="fileManagerSortBy"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-green-500 bg-white text-sm">
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
                                class="mb-4 text-gray-400 hover:text-gray-600 flex items-center">
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
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Отмена
                    </button>
                    <button type="button" onclick="confirmFileSelection()"
                            class="px-5 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium transition-colors">
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
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .file-item-enter {
            animation: fadeIn 0.3s ease-out;
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
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        /* Drag and drop стили */
        .file-upload-area.drag-over {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        /* Кастомный скроллбар */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
@endpush
