import { getCsrfToken } from "./../helpers/getCsrfToken";

window.taskEditState = window.taskEditState || {
    all: [],
    selected: [],
    temp: [],
};
const state = window.taskEditState;

function applyCardState(checkbox, selected) {
    console.log("task-edit-files.js applyCardState");
    checkbox.checked = selected;
    const card = checkbox.closest(".file-card");
    if (!card) return;
    card.classList.toggle("border-green-500", selected);
    card.classList.toggle("shadow-md", selected);
    card.classList.toggle("border-gray-200", !selected);
}

export function updateEditSelectedFilesDisplay() {
    console.log('task-edit-files.js updateEditSelectedFilesDisplay')
    const container = document.getElementById("editSelectedFilesContainer");
    const fileCounter = document.getElementById("editFileCounter");
    const fileCount = document.getElementById("editFileCount");

    if (!container) return;

    if (state.selected.length === 0) {
        container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Файлы не выбраны</p>
                <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
            </div>`;
        if (fileCounter) fileCounter.classList.add("hidden");
        return;
    }

    let html = "";
    state.selected.forEach((file) => {
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
                <button type="button" onclick="removeEditSelectedFile(${file.id})" class="text-red-500 hover:text-red-700 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>`;
    });
    container.innerHTML = html;
    if (fileCount) fileCount.textContent = state.selected.length;
    if (fileCounter) fileCounter.classList.remove("hidden");
}

export function removeEditSelectedFile(fileId) {
     console.log('task-edit-files.js removeEditSelectedFile')
    state.selected = state.selected.filter(
        (f) => Number(f.id) !== Number(fileId),
    );
    updateEditSelectedFilesDisplay();
    showNotification("Файл удален", "info");
}

export function clearTaskEditSelectedFiles() {
         console.log('task-edit-files.js clearTaskEditSelectedFiles')
    if (state.selected.length === 0) return;
    if (confirm(`Удалить все выбранные файлы (${state.selected.length})?`)) {
        state.selected = [];
        updateEditSelectedFilesDisplay();
    }
}

export async function openTaskStorageManager() {
    console.log('task-edit-files.js openTaskStorageManager')
    const modal = document.getElementById("fileManagerModal");
    if (!modal) return;

    state.temp = [...state.selected];
    modal.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
    await loadTaskEditFiles();
}

export function closeTaskStorageManager() {
    console.log('task-edit-files.js closeTaskStorageManager')
    const modal = document.getElementById("fileManagerModal");
    if (modal) modal.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
}

export async function loadTaskEditFiles() {
    console.log('task-edit-files.js loadTaskEditFiles')
    const contentDiv = document.getElementById("fileManagerContent");
    if (!contentDiv) return;
    contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Загрузка...</div>`;

    try {
        const response = await fetch("/tasks/file-storage/get-files", {
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        state.all = await response.json();
        renderTaskEditFiles(state.all);

        const searchInput = document.getElementById("fileManagerSearch");
        if (searchInput) {
            searchInput.value = "";
            searchInput.removeEventListener("input", handleTaskEditFileSearch);
            searchInput.addEventListener("input", handleTaskEditFileSearch);
        }
    } catch (error) {
        console.error("Ошибка загрузки файлов хранилища:", error);
        contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600">Ошибка загрузки</div>`;
    }
}

export function handleTaskEditFileSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const filtered = state.all.filter((file) =>
        file.name.toLowerCase().includes(searchTerm),
    );
    renderTaskEditFiles(filtered);
}

export function renderTaskEditFiles(files) {
    const contentDiv = document.getElementById("fileManagerContent");
    if (!contentDiv) return;

    if (!files || files.length === 0) {
        contentDiv.innerHTML = `<div class="col-span-full text-center py-12">Нет файлов</div>`;
        updateTaskEditSelectedCount();
        return;
    }

    let html =
        '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
    files.forEach((file) => {
        const isSelected = state.temp.some(
            (f) => Number(f.id) === Number(file.id),
        );
        const fileIcon = getFileIcon(file.extension);
        const fileType = getFileTypeClass(file.extension);
        html += `
            <div class="file-card bg-white border ${isSelected ? "border-green-500 shadow-md" : "border-gray-200"} rounded-lg p-3">
                <div class="flex justify-end mb-2">
                    <input type="checkbox"
                           value="${file.id}"
                           class="task-edit-file-checkbox w-5 h-5 rounded border-gray-300 cursor-pointer"
                           ${isSelected ? "checked" : ""}>
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
    html += "</div>";
    contentDiv.innerHTML = html;

    contentDiv
        .querySelectorAll(".task-edit-file-checkbox")
        .forEach((checkbox) => {
            checkbox.addEventListener("change", handleTaskEditCheckboxChange);
        });

    updateTaskEditSelectedCount();
}

export function toggleTaskEditFileSelection(fileId) {
     console.log('task-edit-files.js toggleTaskEditFileSelection')
    fileId = Number(fileId);

    const file = state.all.find((f) => Number(f.id) === fileId);
    if (!file) return;

    const index = state.temp.findIndex((f) => Number(f.id) === fileId);
    const nowSelected = index === -1;
    if (nowSelected) state.temp.push(file);
    else state.temp.splice(index, 1);

    const checkbox = document.querySelector(
        `#fileManagerContent .task-edit-file-checkbox[value="${fileId}"]`,
    );
    if (checkbox) applyCardState(checkbox, nowSelected);

    updateTaskEditSelectedCount();
}

export function handleTaskEditCheckboxChange(e) {
    console.log('task-edit-files.js handleTaskEditCheckboxChange')
    e.stopPropagation();
    toggleTaskEditFileSelection(e.target.value);
}

export function updateTaskEditSelectedCount() {
        console.log('task-edit-files.js updateTaskEditSelectedCount')
    const n = state.temp.length;
    const selectedCountSpan = document.getElementById("selectedCount");
    const confirmCountSpan = document.getElementById("confirmCount");
    const confirmBtn = document.getElementById("confirmFileSelectionBtn");

    if (selectedCountSpan) selectedCountSpan.textContent = n;
    if (confirmCountSpan) confirmCountSpan.textContent = n;

    if (confirmBtn) {
        confirmBtn.disabled = n === 0;
        confirmBtn.classList.toggle("opacity-50", n === 0);
        confirmBtn.classList.toggle("cursor-not-allowed", n === 0);
    }
}
export function confirmEditFileSelectionForEdit() {
     console.log('task-edit-files.js confirmEditFileSelectionForEdit')
    state.selected = [...state.temp];
    updateEditSelectedFilesDisplay();
    closeTaskStorageManager();
    showNotification(`Выбрано ${state.selected.length} файлов`, "success");
}

export {
    openTaskStorageManager as openTaskEditFileManager,
    removeEditSelectedFile as removeTaskEditSelectedFile,
    updateEditSelectedFilesDisplay as updateTaskEditSelectedFilesDisplay,
};

Object.assign(window, {
    openTaskStorageManager,
    closeTaskStorageManager,
    loadTaskEditFiles,
    renderTaskEditFiles,
    toggleTaskEditFileSelection,
    handleTaskEditCheckboxChange,
    confirmEditFileSelectionForEdit,
    updateEditSelectedFilesDisplay,
    removeEditSelectedFile,
    clearTaskEditSelectedFiles,
    clearEditSelectedFiles: clearTaskEditSelectedFiles,
    // старые имена
    openTaskEditFileManager: openTaskStorageManager,
    removeTaskEditSelectedFile: removeEditSelectedFile,
    updateTaskEditSelectedFilesDisplay: updateEditSelectedFilesDisplay,
});
