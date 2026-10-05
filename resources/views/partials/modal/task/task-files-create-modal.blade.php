<div id="createFileManagerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[60]">
    <div class="bg-white rounded-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-white">
            <div>
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Файловdddое хранилище</h3>
                <p class="text-sm text-gray-500 mt-1">Выберите файлы для прикрепления к задаче</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-600 bg-green-50 px-3 py-1 rounded-full">
                    Выбрано: <span id="createSelectedCount" class="font-semibold text-green-600">0</span>
                </span>
                <button onclick="closeCreateFileManager()" class="text-gray-400 hover:text-gray-600 p-2 rounded-xl">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="createFileManagerSearch" placeholder="Поиск по названию файла..." class="w-full pl-10 pr-4 py-2 border-2 border-gray-200 rounded-xl bg-white">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-hidden">
            <div class="h-full flex">
                <div class="flex-1 overflow-y-auto p-4" id="createFileManagerContent">
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
                    <button type="button" onclick="closeCreateFileManager()" class="px-5 py-2.5 border-2 border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Отмена</button>
                    <button type="button" onclick="confirmCreateFileSelection()"
                            class="px-5 py-2.5 text-white rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
                        <i class="fas fa-check mr-2"></i>Выбрать (<span id="createConfirmCount">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function renderTaskFiles(files) {
        console.log('task-files-create-modal.blade.php renderTaskFiles')
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;

            if (!files || files.length === 0) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Нет файлов</div>`;
                return;
            }

            let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
            files.forEach(file => {
                const isSelected = taskSelectedFiles.some(f => f.id === file.id);
                const fileIcon = getFileIcon(file.extension);
                const fileType = getFileTypeClass(file.extension);
                html += `
                      <div class="file-card bg-white border-2 ${isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-lg p-3 transition-all duration-200 hover:shadow-md cursor-pointer"
                 onclick="toggleEditFileSelection(${file.id})">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-5 h-5 rounded ${isSelected ? 'bg-green-500' : 'border-2 border-gray-300'} flex items-center justify-center">
                        ${isSelected ? '<i class="fas fa-check text-white text-xs"></i>' : ''}
                    </div>
                    <button type="button"
                            onclick="event.stopPropagation(); downloadEditFile(${file.id})"
                            class="text-gray-400 hover:text-green-600 p-1 transition-colors"
                            title="Скачать">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 ${fileType.bg} rounded-lg flex items-center justify-center mx-auto mb-2">
                        <img src="/storage/${file.path}" alt="${escapeHtml(file.name)}" class="w-full h-full object-cover">
                    </div>
                    <p class="text-sm font-medium text-gray-800 truncate" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</p>
                    <p class="text-xs text-gray-500 mt-1">${formatFileSize(file.size)}</p>
                    <p class="text-xs text-gray-400">${formatDate(file.created_at)}</p>
                </div>
            </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;
            updateTaskSelectedCount();
        }
</script>
@endpush
