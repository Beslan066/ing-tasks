<div id="editTaskModal" class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-50 backdrop-blur-sm max-[500px]:p-4">
    <div class="bg-white modal-content rounded-[24px] w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
            scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh] transition-all duration-300">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Редактирование задачи</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Измените информацию о задаче</p>
                </div>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="editTaskForm" enctype="multipart/form-data" class="px-8 py-6 space-y-7 max-[500px]:px-5 max-[500px]:py-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="task_id" id="editTaskId">
            <input type="hidden" name="selected_files" id="editSelectedFiles" value="[]">

            <!-- Название -->
            <x-input
                name="name"
                id="editTaskName"
                label="Название задачи"
                placeholder="Введите название задачи"
                :required="true"
                :value="$task->name ?? null"
            />
            <!-- Приоритет и статус -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <x-input-select
                        name="priority"
                        id="editTaskPriority"
                        label="Приоритет"
                        required
                        :options="[
                            'низкий'       => 'Низкий',
                            'средний'      => 'Средний',
                            'высокий'      => 'Высокий',
                            'критический'  => 'Критический',
                        ]"
                        :selected="$task->priority ?? null"
                    />
                <x-input-select
                        name="status"
                        id="editTaskStatus"
                        label="Статус"
                        required
                        :options="[
                            'назначена'       => 'Назначена',
                            'в работе'      => 'В работе',
                            'на проверке'     => 'На проверке',
                            'выполнена'  => 'Выполнена',
                            'просрочена'  => 'Просрочена'
                        ]"
                        :selected="$task->status ?? null"
                    />
            </div>

            <!-- Описание -->
            <div class="space-y-1.5">
                <label class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                    Описание
                </label>
                <div class="relative group">
                    <textarea name="description" id="editTaskDescription" rows="3"
                              class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 resize-none text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
                              placeholder="Добавьте подробное описание..."></textarea>
                </div>
            </div>

            <!-- Отдел и категория -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
               <x-input-select
                    name="department_id"
                    id="editTaskDepartment"
                    label="Отдел"
                    placeholder="Выберите отдел"
                    :required="true"
                    :options="collect($departments ?? [])->pluck('name', 'id')->all()"
                />
                <x-input-select
                    name="category_id"
                    id="editTaskCategory"
                    label="Категория"
                    placeholder="Без категории"
                    :options="collect($categories ?? [])->pluck('name', 'id')->all()"
                />
            </div>

            <!-- Исполнитель и дедлайн -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                 <x-input-select
                    name="user_id"
                    id="editTaskUser"
                    label="Исполнитель"
                    placeholder="Не назначен"
                    :options="collect($assignableUsers ?? [])->pluck('name', 'id')->all()"
                />
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Дедлайн
                    </label>
                    <div class="relative group">
                        <input type="datetime-local" name="deadline" id="editTaskDeadline"
                               class="w-full min-w-0 min-h-[42px] box-border appearance-none px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl [color-scheme:light] focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 cursor-pointer text-sm hover:border-slate-300">
                    </div>
                </div>
            </div>

            <!-- Оценка времени -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Планируемые часы
                    </label>
                    <div class="relative group">
                        <input type="number" name="estimated_hours" id="editTaskEstimatedHours" min="0" step="0.5"
                               class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-medium text-[12px] pointer-events-none">часов</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Фактические часы
                    </label>
                    <div class="relative group">
                        <input type="number" name="actual_hours" id="editTaskActualHours" min="0" step="0.5"
                               class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-medium text-[12px] pointer-events-none">часов</span>
                    </div>
                </div>
            </div>

            <!-- Вкладки для файлов -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-100">
                    <nav class="flex space-x-6" aria-label="Tabs">
                        <button type="button"
                                onclick="switchEditFileTab('storage')"
                                id="editStorageTab"
                                class="py-2.5 px-1 border-b-2 outline-none font-medium text-[13px] focus:outline-none tab-button active transition-all duration-200 max-[500px]:text-[12px]"
                                data-tab="storage">
                            <i class="fas fa-database mr-1.5 opacity-70"></i>Из хранилища
                        </button>
                        <button type="button"
                                onclick="switchEditFileTab('upload')"
                                id="editUploadTab"
                                class="py-2.5 px-1 border-b-2 outline-none font-medium text-[13px] focus:outline-none tab-button transition-all duration-200 max-[500px]:text-[12px]"
                                data-tab="upload">
                            <i class="fas fa-cloud-upload-alt mr-1.5 opacity-70"></i>Новая загрузка
                        </button>
                    </nav>
                </div>

                <!-- Контейнер для файлов из хранилища -->
                <div id="editStorageTabContent" class="tab-content active">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <div>
                            <h4 class="text-[13px] font-medium text-slate-700">Выберите файлы из хранилища</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Файлы будут прикреплены к задаче</p>
                        </div>
                        <div class="flex space-x-2 max-[500px]:w-full">
                            <button type="button" onclick="openTaskEditFileManager()"
                                    class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-[12px] font-medium text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                <i class="fas fa-folder-open mr-1.5 opacity-70"></i>Открыть хранилище
                            </button>
                            <button type="button" onclick="clearEditSelectedFiles()"
                                    class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-[12px] font-medium text-slate-600 bg-white hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition-all duration-200 max-[500px]:grow max-[500px]:justify-center">
                                <i class="fas fa-times mr-1.5 opacity-70"></i>Очистить
                            </button>
                        </div>
                    </div>

                    <!-- Выбранные файлы -->
                    <div id="editSelectedFilesContainer" class="space-y-3 min-h-[100px]">
                        <div class="text-center py-8 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <i class="fas fa-folder-open text-3xl text-slate-300 mb-3"></i>
                            <p class="text-[13px] text-slate-500 font-medium">Файлы не выбраны</p>
                            <p class="text-[11px] text-slate-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                        </div>
                    </div>

                    <!-- Счетчик файлов -->
                    <div id="editFileCounter" class="hidden text-[12px] text-slate-500 mt-3">
                        <i class="fas fa-paperclip mr-1"></i>
                        <span id="editFileCount">0</span> файлов выбрано
                    </div>
                </div>

                <!-- Контейнер для загрузки новых файлов -->
                <div id="editUploadTabContent" class="tab-content hidden">
                    <div class="mb-4">
                        <h4 class="text-[13px] font-medium text-slate-700">Загрузите новые файлы</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Файлы будут сохранены в хранилище и прикреплены к задаче</p>
                    </div>

                    <div class="file-upload-area border border-dashed border-slate-300 rounded-xl p-8 text-center transition-all duration-300 bg-slate-50/50 hover:bg-slate-50 cursor-pointer group"
                         onclick="document.getElementById('editUploadNewFilesInput').click()">
                        <input type="file" name="new_files[]" multiple class="hidden" id="editUploadNewFilesInput">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-100 group-hover:scale-110 group-hover:border-emerald-100 transition-all duration-300">
                                <i class="fas fa-cloud-upload-alt text-xl text-emerald-500"></i>
                            </div>
                            <p class="text-[13px] font-medium text-slate-700 mb-1">Нажмите или перетащите файлы сюда</p>
                            <p class="text-[11px] text-slate-400">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Максимальный размер: 10MB на файл</p>
                        </div>
                    </div>

                    <!-- Список новых файлов -->
                    <div id="editUploadFilesList" class="space-y-3 mt-4 hidden">
                        <h5 class="text-[13px] font-medium text-slate-700">Выбранные файлы:</h5>
                        <div id="editUploadFilesContainer" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse max-[500px]:space-x-0 max-[500px]:gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-800 hover:bg-slate-50 font-medium text-[13px] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-200 max-[500px]:w-full">
                    Отмена
                </button>
                <button type="submit" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-medium text-[13px] transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 flex items-center shadow-sm hover:shadow-md max-[500px]:w-full max-[500px]:justify-center">
                    <i class="fas fa-save mr-1.5 text-[11px]"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
