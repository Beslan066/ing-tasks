<div id="editTaskModal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-md">
    <div
        class="bg-white modal-content rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl">
        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm border-b border-gray-100">
            <div class="flex justify-between items-center p-6">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                        Редактирование задачи
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Измените информацию о задаче</p>
                </div>
                <button onclick="closeEditModal()"
                        class="text-gray-400 hover:text-gray-600 transition-all duration-200 p-2 rounded-xl hover:bg-gray-100 hover:scale-110">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="editTaskForm" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="selected_file_ids" id="editSelectedFiles" value="[]">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Название задачи -->
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-tag text-green-500 mr-2 text-xs"></i>Название задачи *
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tasks text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="text" name="name" id="editTaskName"
                               class="w-full pl-10 pr-4 py-3 border-2 outline-none border-gray-200 rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400 hover:border-gray-300"
                               placeholder="Введите название задачи" required>
                    </div>
                </div>

                <!-- Описание -->
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-align-left text-green-500 mr-2 text-xs"></i>Описание
                    </label>
                    <textarea name="description" id="editTaskDescription" rows="4"
                              class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 resize-none bg-white placeholder-gray-400"
                              placeholder="Подробное описание задачи..."></textarea>
                </div>

                <!-- Отдел -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-building text-green-500 mr-2 text-xs"></i>Отдел *
                    </label>
                    <div class="relative group">
                        <select name="department_id" id="editTaskDepartment"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl outline-none focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                required>
                            <option value="">Выберите отдел</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Категория -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-folder text-green-500 mr-2 text-xs"></i>Категория
                    </label>
                    <div class="relative group">
                        <select name="category_id" id="editTaskCategory"
                                class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                            <option value="">Без категории</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Исполнитель -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-user-check text-green-500 mr-2 text-xs"></i>Исполнитель
                    </label>
                    <div class="relative group">
                        <select name="user_id" id="editTaskUser"
                                class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300">
                            <option value="">Не назначен</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Приоритет -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-flag text-green-500 mr-2 text-xs"></i>Приоритет *
                    </label>
                    <div class="relative group">
                        <select name="priority" id="editTaskPriority"
                                class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                required>
                            <option value="низкий">Низкий</option>
                            <option value="средний">Средний</option>
                            <option value="высокий">Высокий</option>
                            <option value="критический">Критический</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Статус -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-chart-line text-green-500 mr-2 text-xs"></i>Статус *
                    </label>
                    <div class="relative group">
                        <select name="status" id="editTaskStatus"
                                class="w-full px-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white appearance-none cursor-pointer hover:border-gray-300"
                                required>
                            <option value="не назначена">Не назначена</option>
                            <option value="назначена">Назначена</option>
                            <option value="в работе">В работе</option>
                            <option value="на проверке">На проверке</option>
                            <option value="выполнена">Выполнена</option>
                            <option value="просрочена">Просрочена</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Дедлайн -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-calendar-alt text-green-500 mr-2 text-xs"></i>Дедлайн
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="datetime-local" name="deadline" id="editTaskDeadline"
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white cursor-pointer hover:border-gray-300">
                    </div>
                </div>

                <!-- Планируемое время -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-hourglass-half text-green-500 mr-2 text-xs"></i>Планируемое время (часы)
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-stopwatch text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="number" name="estimated_hours" id="editTaskEstimatedHours" step="0.5" min="0"
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400"
                               placeholder="0.0">
                        <span
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm">часов</span>
                    </div>
                </div>

                <!-- Фактическое время -->
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>Фактическое время (часы)
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-chart-simple text-gray-400 group-focus-within:text-green-500 transition-colors text-sm"></i>
                        </div>
                        <input type="number" name="actual_hours" id="editTaskActualHours" step="0.5" min="0"
                               class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 outline-none rounded-xl focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white placeholder-gray-400"
                               placeholder="0.0">
                        <span
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm">часов</span>
                    </div>
                </div>

                <!-- Вкладки для файлов (как в создании) -->
                <div class="md:col-span-2 space-y-4">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-6" aria-label="Tabs">
                            <button type="button"
                                    onclick="switchEditFileTab('storage')"
                                    id="editStorageTab"
                                    class="py-2 px-1 border-b-2 outline-none font-medium text-sm focus:outline-none tab-button active transition-all duration-200"
                                    data-tab="storage">
                                <i class="fas fa-database mr-2"></i>Из хранилища
                            </button>
                            <button type="button"
                                    onclick="switchEditFileTab('upload')"
                                    id="editUploadTab"
                                    class="py-2 px-1 border-b-2 outline-none font-medium text-sm focus:outline-none tab-button transition-all duration-200"
                                    data-tab="upload">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>Новая загрузка
                            </button>
                        </nav>
                    </div>

                    <!-- Контейнер для файлов из хранилища -->
                    <div id="editStorageTabContent" class="tab-content active">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700">Выберите файлы из хранилища</h4>
                                <p class="text-xs text-gray-500 mt-1">Файлы будут прикреплены к задаче</p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" onclick="openEditFileManager()"
                                        class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                    <i class="fas fa-folder-open mr-2"></i>Открыть хранилище
                                </button>
                                <button type="button" onclick="clearEditSelectedFiles()"
                                        class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-red-50 hover:border-red-300 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                    <i class="fas fa-times mr-2"></i>Очистить
                                </button>
                            </div>
                        </div>

                        <!-- Выбранные файлы -->
                        <div id="editSelectedFilesContainer" class="space-y-3 min-h-[100px]">
                            <div
                                class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-500">Файлы не выбраны</p>
                                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                            </div>
                        </div>

                        <!-- Счетчик файлов -->
                        <div id="editFileCounter" class="hidden text-sm text-gray-600 mt-3">
                            <i class="fas fa-paperclip mr-1"></i>
                            <span id="editFileCount">0</span> файлов выбрано
                        </div>
                    </div>

                    <!-- Контейнер для загрузки новых файлов -->
                    <div id="editUploadTabContent" class="tab-content hidden">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700">Загрузите новые файлы</h4>
                            <p class="text-xs text-gray-500 mt-1">Файлы будут сохранены в хранилище и прикреплены к
                                задаче</p>
                        </div>

                        <div
                            class="file-upload-area border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-all duration-300 bg-gradient-to-br from-gray-50 to-white hover:from-green-50 hover:to-white cursor-pointer group"
                            onclick="document.getElementById('editUploadNewFilesInput').click()">
                            <input type="file" name="new_files[]" multiple class="hidden"
                                   id="editUploadNewFilesInput">
                            <div class="flex flex-col items-center justify-center">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-white"></i>
                                </div>
                                <p class="text-base font-medium text-gray-700 mb-2">Нажмите или перетащите файлы
                                    сюда</p>
                                <p class="text-sm text-gray-500">Поддерживаются: PDF, DOC, DOCX, XLS, XLSX, JPG,
                                    PNG, GIF, ZIP</p>
                                <p class="text-xs text-gray-400 mt-1">Максимальный размер: 10MB на файл</p>
                            </div>
                        </div>

                        <!-- Список новых файлов -->
                        <div id="editUploadFilesList" class="space-y-3 mt-4 hidden">
                            <h5 class="text-sm font-semibold text-gray-700">Выбранные файлы:</h5>
                            <div id="editUploadFilesContainer" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <!-- История отказов -->
                <div class="md:col-span-2 space-y-3">
                    <label class="block text-gray-700 text-sm font-semibold mb-1">
                        <i class="fas fa-history text-green-500 mr-2 text-xs"></i>История отказов от задачи
                        <span id="editRejectionsCount"
                              class="bg-gradient-to-r from-red-400 to-red-500 text-white text-xs px-2 py-1 rounded-full ml-2 shadow-sm">0</span>
                    </label>
                    <div id="editRejectionsList"
                         class="space-y-3 max-h-60 overflow-y-auto border-2 border-gray-200 rounded-xl p-4 bg-gray-50 custom-scrollbar">
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">Отказов нет</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end space-x-3 pt-6 mt-4 border-t border-gray-200 max-[500px]:flex-col-reverse max-[500px]:space-x-0 max-[500px]:gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-300 font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Отмена
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
