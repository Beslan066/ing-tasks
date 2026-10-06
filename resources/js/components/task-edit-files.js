console.log('task-edit-files.js')
window.taskEditState = window.taskEditState || {
    all: [],
    selected: [],
    temp: [],
};
const state = window.taskEditState;

  function renderTaskEditFiles(files) {
    console.log('renderTaskEditFiles');
            const contentDiv = document.getElementById('fileManagerContent');
            if (!contentDiv) return;
            if (!files || files.length === 0) {
                contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Нет файлов</div>`;
                return;
            }

            let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
            files.forEach(file => {
                const isSelected = state.temp.some(f => Number(f.id) === Number(file.id));
                const fileIcon = getFileIcon(file.extension);
                const fileType = getFileTypeClass(file.extension);
                html += `
                    <div class="file-card v bg-white border ${isSelected ? 'border-green-500 shadow-md' : 'border-gray-200'} rounded-lg p-3">
                        <div class="flex justify-end mb-2">
                            <input type="checkbox"
                                   value="${file.id}"
                                   class="task-edit-file-checkbox w-5 h-5 rounded border-gray-300 cursor-pointer"
                                   ${isSelected ? 'checked' : ''}>
                        </div>
                        <div class="text-center cursor-pointer" onclick="toggleTaskEditFileSelection(${file.id})">
                            <div class="w-16 h-16 ${fileType.bg} rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl">${fileIcon}</span>
                            </div>
                            <p class="text-sm font-medium truncate">${escapeHtml(file.name)}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            <p class="text-xs text-gray-400 mt-1">${formatDate(file.created_at)}</p>
                        </div>
                        <div class="flex justify-center space-x-2 mt-2 pt-2 border-t border-gray-100">
                            <button type="button" onclick="event.stopPropagation(); downloadTaskFile(${file.id})"
                                    class="text-gray-400 hover:text-green-600 p-1" title="Скачать">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;

            document.querySelectorAll('#fileManagerContent .task-edit-file-checkbox').forEach(checkbox => {
                checkbox.removeEventListener('change', handleTaskEditCheckboxChange);
                checkbox.addEventListener('change', handleTaskEditCheckboxChange);
            });

            updateTaskEditSelectedCount();
        }

function openTaskStorageManager() {
    console.log('openTaskStorageManager');
    state.temp = [...state.selected];
    document.getElementById('fileManagerModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    loadTaskEditFiles();
}

function closeTaskStorageManager() {
    console.log('closeTaskStorageManager');
    document.getElementById('fileManagerModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}
function toggleTaskEditFileSelection(fileId) {
    fileId = Number(fileId);
    console.log('toggleTaskEditFileSelection, temp:', state.temp.map(f => f.id));

    const file = state.all.find(f => Number(f.id) === fileId);
    if (!file) return;

    const index = state.temp.findIndex(f => Number(f.id) === fileId);
    if (index === -1) state.temp.push(file);
    else state.temp.splice(index, 1);

    renderTaskEditFiles(state.all);
}
function updateEditSelectedFilesDisplay() {
    console.log('updateEditSelectedFilesDisplay');
    const container = document.getElementById('editSelectedFilesContainer');
    const fileCounter = document.getElementById('editFileCounter');
    const fileCount = document.getElementById('editFileCount');

    if (!container) return;

    if (state.selected.length === 0) {
        container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Файлы не выбраныs</p>
                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
            </div>`;
        if (fileCounter) fileCounter.classList.add('hidden');
    } else {
        let html = '';
        state.selected.forEach(file => {
            const fileIcon = getFileIcon(file.extension);
            const fileType = getFileTypeClass(file.extension);
            html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3 flex-1">
                        <div class="w-10 h-10 ${fileType.bg} rounded flex items-center justify-center">
                            <span class="text-lg">${fileIcon}</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</p>
                            <span class="text-xs text-gray-500">${formatFileSize(file.size)}</span>
                        </div>
                    </div>
                    <button onclick="removeEditSelectedFile(${file.id})" class="text-red-500 hover:text-red-700 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
        });
        container.innerHTML = html;
        if (fileCount) fileCount.textContent = state.selected.length;
        if (fileCounter) fileCounter.classList.remove('hidden');
    }
}
function handleTaskEditCheckboxChange(e) {
    console.log('handleTaskEditCheckboxChange');
    toggleTaskEditFileSelection(Number(e.target.value));
}
function updateTaskEditSelectedCount() {
    console.log('updateTaskEditSelectedCount');
    const n = state.temp.length;
    const a = document.getElementById('selectedCount');
    const b = document.getElementById('confirmCount');
    if (a) a.textContent = n;
    if (b) b.textContent = n;
}
function confirmEditFileSelectionForEdit() {
    console.log('confirmEditFileSelectionForEdit');
    state.selected = [...state.temp];
    updateEditSelectedFilesDisplay();
    closeTaskStorageManager();
    showNotification(`Выбрано ${state.selected.length} файлов`, 'success');
}
  function removeEditSelectedFile(fileId) {
       state.selected = state.selected.filter(f => Number(f.id) !== Number(fileId));
       updateEditSelectedFilesDisplay();
       showNotification('Файл удален', 'info');
   }
window.openTaskStorageManager = openTaskStorageManager;
window.closeTaskStorageManager = closeTaskStorageManager;
window.toggleTaskEditFileSelection = toggleTaskEditFileSelection;
window.confirmEditFileSelectionForEdit = confirmEditFileSelectionForEdit;
window.renderTaskEditFiles=renderTaskEditFiles;
window.updateEditSelectedFilesDisplay=updateEditSelectedFilesDisplay;
window.removeEditSelectedFile=removeEditSelectedFile;
window.handleTaskEditCheckboxChange = handleTaskEditCheckboxChange;
