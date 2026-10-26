<div id="createFileManagerModal"
     role="dialog" aria-modal="true" aria-labelledby="createFileManagerTitle"
     onclick="if (event.target === this) closeCreateFileManager()"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-[60] backdrop-blur-sm max-[500px]:p-4">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl shadow-slate-900/10
                max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="bg-white border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h3 id="createFileManagerTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Файловое хранилище</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Выберите файлы для прикрепления к задаче</p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="inline-flex items-center gap-1 text-[12px] font-medium text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full">
                        Выбрано:
                        <span id="createSelectedCount" class="font-semibold">0</span>
                    </span>

                    <button type="button" onclick="closeCreateFileManager()" aria-label="Закрыть"
                            class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Поиск -->
        <div class="px-8 py-4 border-b border-slate-100 max-[500px]:px-5">
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none group-focus-within:text-emerald-500 transition-colors duration-200"></i>
                <input type="text" id="createFileManagerSearch"
                       placeholder="Поиск по названию файла..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300">
            </div>
        </div>

        <!-- Список файлов -->
        <div id="createFileManagerContent"
             class="flex-1 overflow-y-auto custom-scrollbar px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="col-span-full text-center py-12 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                    <i class="fas fa-spinner fa-spin text-2xl text-slate-300 mb-3"></i>
                    <p class="text-[13px] text-slate-500 font-medium">Загрузка файлов...</p>
                </div>
            </div>
        </div>

        <!-- Кнопки действий -->
        <div class="px-8 py-5 border-t border-slate-100 bg-white max-[500px]:px-5 max-[500px]:py-4">
            <div class="flex justify-between items-center gap-3 max-[500px]:flex-col-reverse">
                <p class="text-[12px] text-slate-500 max-[500px]:hidden">Нажмите на файл, чтобы выбрать или снять выбор</p>

                <div class="flex items-center gap-3 max-[500px]:w-full max-[500px]:flex-col-reverse">
                    <x-ui.button
                        type="button"
                        variant="secondary"
                        onclick="closeCreateFileManager()"
                    >
                        Отмена
                    </x-ui.button>

                    <x-ui.button
                        type="button"
                        variant="primary"
                        icon="fas fa-check"
                        onclick="confirmCreateFileSelection()"
                    >
                        Выбрать (<span id="createConfirmCount">0</span>)
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
