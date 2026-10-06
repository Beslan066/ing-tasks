window.taskCreateState = window.taskCreateState || {
    all: [],
    selected: [],
    temp: [],
};

const state = window.taskCreateState;

function renderTaskCreateFiles(files) {
    console.log('renderTaskCreateFiles');
    const contentDiv = document.getElementById('createFileManagerContent');
        console.log('renderTaskCreateFiles2');
    if (!contentDiv) return;
        console.log('renderTaskCreateFile3');

    if (!files || files.length === 0) {
        contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-gray-400">Нет файлов</div>`;
        if (typeof updateCreateTaskSelectedCount === 'function') updateCreateTaskSelectedCount();
        return;
    }
 console.log('renderTaskCreateFile4');
    let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
    files.forEach(file => {
        const isSelected = state.temp.some(f => Number(f.id) === Number(file.id));

        const fileIcon = typeof getFileIcon === 'function' ? getFileIcon(file.extension) : '📄';
        const fileType = typeof getFileTypeClass === 'function' ? getFileTypeClass(file.extension) : { bg: 'bg-gray-100' };
        const safeName = typeof escapeHtml === 'function' ? escapeHtml(file.name) : file.name;
        const safeSize = typeof formatFileSize === 'function' ? formatFileSize(file.size) : file.size;
        const safeDate = typeof formatDate === 'function' ? formatDate(file.created_at) : (file.created_at || '');

        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes((file.extension || '').toLowerCase());
        const previewHtml = isImage && file.path
            ? `<img src="/storage/${file.path}" alt="${safeName}" class="w-full h-full object-cover rounded-lg">`
            : `<span class="text-2xl">${fileIcon}</span>`;

        html += `
            <div class="file-card bg-white border-2 ${isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-lg p-3 transition-all duration-200 hover:shadow-md cursor-pointer"
                 onclick="toggleCreateFileSelection(${file.id})">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-5 h-5 rounded ${isSelected ? 'bg-green-500' : 'border-2 border-gray-300'} flex items-center justify-center">
                        ${isSelected ? '<i class="fas fa-check text-white text-xs"></i>' : ''}
                    </div>
                    <button type="button"
                            onclick="event.stopPropagation(); downloadCreateFile(${file.id})"
                            class="text-gray-400 hover:text-green-600 p-1 transition-colors"
                            title="Скачать">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 ${fileType.bg || 'bg-gray-100'} rounded-lg flex items-center justify-center mx-auto mb-2 overflow-hidden">
                        ${previewHtml}
                    </div>
                    <p class="text-sm font-medium text-gray-800 truncate" title="${safeName}">${safeName}</p>
                    <p class="text-xs text-gray-500 mt-1">${safeSize}</p>
                    <p class="text-xs text-gray-400">${safeDate}</p>
                </div>
            </div>`;
    });
    html += '</div>';
    contentDiv.innerHTML = html;

    if (typeof updateCreateTaskSelectedCount === 'function') {
        updateCreateTaskSelectedCount();
    }
}

async function loadCreateFiles() {
    const contentDiv = document.getElementById('createFileManagerContent');
    if (!contentDiv) return;
    contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Загрузка...</div>`;

    try {
        const response = await fetch('/tasks/file-storage/get-files', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });
        if (!response.ok) throw new Error('Ошибка сети');

        const data = await response.json();
        window.taskCreateState.all = data.files || data || [];

        renderTaskCreateFiles(window.taskCreateState.all);
    } catch (error) {
        console.error('loadCreateFilesError', error);
        if (contentDiv) {
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">Ошибка загрузки файлов</div>`;
        }
    }
}
function toggleCreateFileSelection(fileId) {
    const state = window.taskCreateState;
    if (!state) return;

    // 1. Ищем файл
    const file = state.all.find(f => Number(f.id) === Number(fileId));
    if (!file) return;

    // 2. Обновляем временный массив выбора (temp)
    const index = state.temp.findIndex(f => Number(f.id) === Number(fileId));
    if (index === -1) {
        state.temp.push(file);
    } else {
        state.temp.splice(index, 1);
    }

    // 3. Обновляем счетчик СРАЗУ после изменения состояния
    updateCreateSelectedCount();

    // 4. Перерисовываем сетку файлов (или точечно переключаем стили)
    if (typeof renderTaskCreateFiles === 'function') {
        renderTaskCreateFiles(state.all);
    }
}
function updateCreateSelectedCount() {
    const state = window.taskCreateState;
    if (!state) return;

    // Берем количество из временного выбора (temp)
    const count = (state.temp || []).length;

    // Обновляем оба спана счетчика
    const selectedCountSpan = document.getElementById('createSelectedCount');
    const confirmCountSpan = document.getElementById('createConfirmCount');

    if (selectedCountSpan) selectedCountSpan.textContent = count;
    if (confirmCountSpan) confirmCountSpan.textContent = count;
}
function removeCreateSelectedFile(fileId) {
    const state = window.taskCreateState;
    if (!state) return;
    state.selected = state.selected.filter(f => Number(f.id) !== Number(fileId));
    state.temp = state.temp.filter(f => Number(f.id) !== Number(fileId));

    if (typeof updateCreateSelectedFilesDisplay === 'function') {
        updateCreateSelectedFilesDisplay();
    }
    if (typeof updateCreateSelectedCount === 'function') {
        updateCreateSelectedCount();
    }
    if (typeof renderTaskCreateFiles === 'function' && state.all) {
        renderTaskCreateFiles(state.all);
    }
}
function clearCreateSelectedFiles() {
    const state = window.taskCreateState;
    if (!state) return;

    const count = state.selected.length || state.temp.length;
    if (count === 0) return;

    if (confirm(`Удалить все выбранные файлы (${count})?`)) {

        state.selected = [];
        state.temp = [];

        if (typeof updateCreateSelectedFilesDisplay === 'function') {
            updateCreateSelectedFilesDisplay();
        }
        if (typeof updateCreateSelectedCount === 'function') {
            updateCreateSelectedCount();
        }

        if (typeof renderTaskCreateFiles === 'function' && state.all) {
            renderTaskCreateFiles(state.all);
        }
    }
}
function confirmCreateFileSelection() {
    const state = window.taskCreateState;
    if (!state) return;

    console.log('=== confirmCreateFileSelection вызвана ===');
    console.log('state.temp.length:', state.temp.length);

    if (!state.temp || state.temp.length === 0) {
        alert('Пожалуйста, выберите хотя бы один файл');
        return;
    }

    // Сохраняем временный выбор как основной
    state.selected = [...state.temp];

    // Записываем массив ID или объектов в hidden input для отправки формы
    const selectedFilesInput = document.getElementById('selectedFiles');
    if (selectedFilesInput) {
        selectedFilesInput.value = JSON.stringify(state.selected.map(f => f.id));
    }

    if (typeof updateCreateSelectedFilesDisplay === 'function') {
        updateCreateSelectedFilesDisplay();
    }
    if (typeof switchCreateFileTab === 'function') {
        switchCreateFileTab('storage');
    }
    if (typeof closeCreateFileManager === 'function') {
        closeCreateFileManager();
    }
}

function updateCreateSelectedFilesDisplay() {
    const state = window.taskCreateState;
    if (!state) return;

    const container = document.getElementById('selectedFilesContainer');
    const fileCounter = document.getElementById('fileCounter');
    const fileCount = document.getElementById('fileCount');

    if (!container) return;

    const files = state.selected || [];

    if (files.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                <i class="fas fa-folder-open text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Файлы не выбраны</p>
                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
            </div>`;
        if (fileCounter) fileCounter.classList.add('hidden');
    } else {
        let html = '<div class="space-y-2">';
        files.forEach(file => {
            const fileIcon = typeof getFileIcon === 'function' ? getFileIcon(file.extension) : '📄';
            const fileType = typeof getFileTypeClass === 'function' ? getFileTypeClass(file.extension) : { bg: 'bg-gray-100' };
            const safeName = typeof escapeHtml === 'function' ? escapeHtml(file.name) : file.name;
            const safeSize = typeof formatFileSize === 'function' ? formatFileSize(file.size) : file.size;

            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-10 h-10 ${fileType.bg || 'bg-gray-100'} rounded flex-shrink-0 flex items-center justify-center">
                            <span class="text-lg">${fileIcon}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" title="${safeName}">${safeName}</p>
                            <p class="text-xs text-gray-500">${safeSize}</p>
                        </div>
                    </div>
                    <button type="button"
                            onclick="removeCreateSelectedFile(${file.id})"
                            class="text-red-500 hover:text-red-700 p-1 transition-colors flex-shrink-0 ml-2"
                            title="Удалить">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
        });
        html += '</div>';

        container.innerHTML = html;
        if (fileCount) fileCount.textContent = files.length;
        if (fileCounter) fileCounter.classList.remove('hidden');
    }
}

window.removeCreateSelectedFile = removeCreateSelectedFile;
window.clearCreateSelectedFiles = clearCreateSelectedFiles;
window.updateCreateSelectedCount = updateCreateSelectedCount;
window.toggleCreateFileSelection = toggleCreateFileSelection;
window.renderTaskCreateFiles = renderTaskCreateFiles;
window.loadCreateFiles = loadCreateFiles;
window.confirmCreateFileSelection = confirmCreateFileSelection;
window.updateCreateSelectedFilesDisplay = updateCreateSelectedFilesDisplay;
