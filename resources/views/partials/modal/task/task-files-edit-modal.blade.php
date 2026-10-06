<div id="fileManagerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
        <div class="bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
            <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Файловое хранилище TaskFileEdit</h3>
                    <p class="text-sm text-gray-500 mt-1">Выберите файлы для прикрепления к задаче</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-600 bg-green-50 px-3 py-1 rounded-full">
                        Выбрано: <span id="selectedCount" class="font-semibold text-green-600">0</span>
                    </span>
                    <button onclick="closeTaskStorageManager()" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="fileManagerSearch" placeholder="Поиск по названию файла..."
                                   class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-hidden">
                <div class="h-full flex">
                    <div class="flex-1 overflow-y-auto p-4" id="fileManagerContent">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div class="col-span-full text-center py-12">Загрузка...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-white">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">Файловое хранилище</div>
                    <div class="flex space-x-3">
                        <button onclick="closeTaskStorageManager()"
                                class="px-5 py-2.5 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Отмена</button>
                        <button type="button" id="confirmFileSelectionBtn" onclick="confirmEditFileSelectionForEdit()"
                            class="px-5 py-2.5 text-white rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
                        <i class="fas fa-check mr-2"></i>Выбрать (<span id="confirmCount">0</span>)
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
