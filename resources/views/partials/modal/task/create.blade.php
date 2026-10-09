<div id="taskModal" class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-50 backdrop-blur-sm max-[500px]:p-4">
    <div class="bg-white modal-content rounded-[24px] w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
            scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh] transition-all duration-300">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Новая задача</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Заполните информацию о задаче</p>
                </div>
                <button onclick="closeTaskModal()" class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="taskForm" enctype="multipart/form-data" class="px-8 py-6 space-y-7 max-[500px]:px-5 max-[500px]:py-4">
            @csrf
            <input type="hidden" name="selected_files" id="selectedFiles" value="[]">

            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                  <x-form.input
                        name="name"
                        label="Название задачи"
                        placeholder="Введите название задачи"
                        :required="true"
                    />

                <x-form.input-select
                    name="priority"
                    label="Приоритет"
                    :required="true"
                    :options="[
                        'низкий'       => 'Низкий',
                        'средний'      => 'Средний',
                        'высокий'      => 'Высокий',
                        'критический'  => 'Критический',
                    ]"
                />
            </div>

            <!-- Описание -->
            <x-form.textarea
                name="description"
                label="Описание"
                placeholder="Добавьте подробное описание..."
                :rows="3"
            />
            <!-- Отдел и категория -->
           <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <x-form.input-select
                    name="department_id"
                    label="Отдел"
                    placeholder="Выберите отдел"
                    :required="true"
                    :options="collect($departments ?? [])->pluck('name', 'id')->all()"
                />

                <x-form.input-select
                    name="category_id"
                    label="Категория"
                    placeholder="Выберите категорию"
                    :options="collect($categories ?? [])->pluck('name', 'id')->all()"
                />
            </div>

            <!-- Исполнитель и сроки -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <x-form.input-select
                        name="user_id"
                        id="editTaskUser"
                        label="Исполнитель"
                        placeholder="Не назначено"
                        :options="collect($assignableUsers ?? [])->pluck('name', 'id')->all()"
                        :selected="$task->user_id ?? null"
                    />
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Дедлайн
                    </label>
                    <div class="relative group">
                        <input type="datetime-local" name="deadline"
                               class="w-full min-w-0 min-h-[42px] box-border appearance-none px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl color-scheme-light focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 cursor-pointer text-sm hover:border-slate-300">
                    </div>
                </div>
            </div>

            <!-- Оценка времени и статус -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Планируемые часы
                    </label>
                    <div class="relative group">
                        <input type="number" name="estimated_hours" min="0" step="0.5"
                               class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-medium text-[12px] pointer-events-none">часов</span>
                    </div>
                </div>

                 @php
                    $availableStatuses = array_filter(\App\Models\Task::getStatuses(), function($status) {
                        return $status !== 'в работе';
                    });
                    $statusOptions = array_combine($availableStatuses, $availableStatuses);
                @endphp

                <x-form.input-select
                    name="status"
                    label="Статус"
                    :required="true"
                    selected="назначена"
                    :options="$statusOptions"
                />
            </div>

            <!-- Вкладки для файлов (оставлено без изменений по вашему запросу) -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-100">
                    <nav class="flex space-x-6" aria-label="Tabs">
                        <button type="button"
                                onclick="switchFileTab('storage')"
                                id="storageTab"
                                class="py-2.5 px-1 border-b-2 outline-none font-medium text-[13px] focus:outline-none tab-button active transition-all duration-200 max-[500px]:text-[12px]"
                                data-tab="storage">
                            <i class="fas fa-database mr-1.5 opacity-70"></i>Из хранилища
                        </button>
                        <button type="button"
                                onclick="switchFileTab('upload')"
                                id="uploadTab"
                                class="py-2.5 px-1 border-b-2 outline-none font-medium text-[13px] focus:outline-none tab-button transition-all duration-200 max-[500px]:text-[12px]"
                                data-tab="upload">
                            <i class="fas fa-cloud-upload-alt mr-1.5 opacity-70"></i>Новая загрузка
                        </button>
                    </nav>
                </div>

                <!-- Контейнер для файлов из хранилища -->
                <div id="storageTabContent" class="tab-content active">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <div>
                            <h4 class="text-[13px] font-medium text-slate-700">Выберите файлы из хранилища</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Файлы будут прикреплены к задаче</p>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" onclick="openFileManager()"
                                    class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-[12px] font-medium text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                <i class="fas fa-folder-open mr-1.5 opacity-70"></i>Открыть хранилище
                            </button>
                            <button type="button" onclick="clearCreateSelectedFiles()"
                                    class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-[12px] font-medium text-slate-600 bg-white hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition-all duration-200">
                                <i class="fas fa-times mr-1.5 opacity-70"></i>Очистить
                            </button>
                        </div>
                    </div>

                    <!-- Выбранные файлы -->
                    <div id="selectedFilesContainer" class="space-y-3 min-h-[100px]">
                        <div class="text-center py-8 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <i class="fas fa-folder-open text-3xl text-slate-300 mb-3"></i>
                            <p class="text-[13px] text-slate-500 font-medium">Файлы не выбраны</p>
                            <p class="text-[11px] text-slate-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                        </div>
                    </div>

                    <!-- Счетчик файлов -->
                    <div id="fileCounter" class="hidden text-[12px] text-slate-500 mt-3">
                        <i class="fas fa-paperclip mr-1"></i>
                        <span id="fileCount">0</span> файлов выбрано
                    </div>
                </div>

                <!-- Контейнер для загрузки новых файлов -->
                <div id="uploadTabContent" class="tab-content hidden">
                    <div class="mb-4">
                        <h4 class="text-[13px] font-medium text-slate-700">Загрузите новые файлы</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Файлы будут сохранены в хранилище и прикреплены к задаче</p>
                    </div>

                    <div class="file-upload-area border border-dashed border-slate-300 rounded-xl p-8 text-center transition-all duration-300 bg-slate-50/50 hover:bg-slate-50 cursor-pointer group"
                         onclick="document.getElementById('uploadNewFilesInput').click()">
                        <input type="file" name="new_files[]" multiple class="hidden" id="uploadNewFilesInput">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-100 group-hover:scale-110 group-hover:border-emerald-100 transition-all duration-300">
                                <i class="fas fa-cloud-upload-alt text-xl text-emerald-500"></i>
                            </div>
                            <p class="text-[13px] font-medium text-slate-700 mb-1">Нажмите или перетащите файлы сюда</p>
                            <p class="text-[11px] text-slate-400">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Максимальный размер: 10MB</p>
                        </div>
                    </div>

                    <!-- Список новых файлов -->
                    <div id="uploadFilesList" class="space-y-3 mt-4 hidden">
                        <h5 class="text-[13px] font-medium text-slate-700">Выбранные файлы:</h5>
                        <div id="uploadFilesContainer" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse max-[500px]:space-x-0 max-[500px]:gap-3">
                <x-ui.button
                    variant="secondary"
                    onclick="closeTaskModal()"
                >
                    Отмена
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    variant="primary"
                    icon="fas fa-plus"
                >
                    Создать задачу
                </x-ui.button>
            </div>
        </form>
    </div>
</div>

@include('partials.modal.task.task-files-create-modal')


@push('styles')
    <style>
        /* Стили для вкладок */
        .tab-button {
            border-color: transparent;
            color: #64748b; /* slate-500 */
        }

        .tab-button:hover {
            color: #334155; /* slate-700 */
            border-color: #cbd5e1; /* slate-300 */
        }

        .tab-button.active {
            border-color: #10b981; /* emerald-500 */
            color: #10b981;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .file-item-enter {
            animation: slideIn 0.2s ease-out;
        }

        /* Стили для файлового менеджера */
        .file-card {
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border-color: #cbd5e1;
        }

        .file-card.selected {
            border-color: #10b981;
            background: #f0fdf4; /* emerald-50 */
            box-shadow: 0 0 0 1px #10b981;
        }

        /* Drag and drop стили */
        .file-upload-area.drag-over {
            border-color: #10b981;
            background: #f0fdf4;
            transform: scale(0.99);
        }

        /* Кастомный скроллбар */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* slate-300 */
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* slate-400 */
        }

        /* Стили для файлов в контейнере */
        .selected-file-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .selected-file-item:hover {
            border-color: #10b981;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.05);
            transform: translateX(2px);
        }

        /* Анимация для модального окна */
        .modal-content {
            animation: modalSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        if (!window.__testInitialized) {
            window.__testInitialized = true;

            // --- Управление модальным окном задачи ---
            window.openTaskModal = function() {
                const modal = document.getElementById('taskModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            };

            window.closeTaskModal = function() {
                const modal = document.getElementById('taskModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            };

            // --- Управление файловым менеджером ---
            async function openCreateFileManager() {
                const modal = document.getElementById('createFileManagerModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                    await loadCreateFiles();
                }
            }

            function closeCreateFileManager() {
                const modal = document.getElementById('createFileManagerModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            }

            function switchCreateFileTab(tabName) {
                document.querySelectorAll('#taskModal .tab-button').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.tab === tabName);
                });

                document.querySelectorAll('#taskModal .tab-content').forEach(content => {
                    const isCurrent = content.id === `${tabName}TabContent`;
                    if (content) {
                        content.classList.toggle('active', isCurrent);
                        content.classList.toggle('hidden', !isCurrent);
                    }
                });
            }

            async function downloadCreateFile(fileId) {
                window.open(`/file-storage/download/${fileId}`, '_blank');
            }

            // Переопределяем функции для создания
            window.openFileManager = openCreateFileManager;
            window.switchFileTab = switchCreateFileTab;
            window.closeFileManager = closeCreateFileManager;
        }
    </script>
@endpush
