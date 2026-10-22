import { getCsrfToken } from "../../helpers/getCsrfToken";
let currentTaskId = null;
 let taskSelectedFiles = [];
export function openPersonalTaskModal() {
    const modal = document.getElementById("taskModal");
    const form = document.getElementById("taskForm");

    if (!modal || !form) return;

    // Отключаем HTML5 валидацию формы
    form.setAttribute("novalidate", "novalidate");

    // Удаляем старые скрытые поля если есть
    const oldHiddenDept = form.querySelector(
        'input[name="department_id"][type="hidden"]',
    );
    if (oldHiddenDept) oldHiddenDept.remove();

    const oldIsPersonal = form.querySelector('input[name="is_personal"]');
    if (oldIsPersonal) oldIsPersonal.remove();
    form.querySelectorAll('[name="department_id"]').forEach((el) => {
        el.value = "";
        el.disabled = true; // disabled-поля не попадают в FormDataы
    });
    form.querySelectorAll("#department_id-field").forEach((el) => {
        el.classList.add("hidden");
    });
    //  form.querySelectorAll('#editTaskUser-field').forEach(el=> {
    //     el.classList.add('hidden')
    // })
    // // Добавляем скрытое поле department_id с отделом пользователя
    // !закоментировал так как у личных задач не должно быть отдела, по умолчанию это поле null
    // @if (isset($user) && $user->department_id)
    // const hiddenDeptInput = document.createElement("input");
    // hiddenDeptInput.type = "hidden";
    // hiddenDeptInput.name = "department_id";
    // hiddenDeptInput.value = "{{ $user->department_id }}";
    // form.appendChild(hiddenDeptInput);
    // @endif

    // Добавляем скрытое поле is_personal
    const isPersonalInput = document.createElement("input");
    isPersonalInput.type = "hidden";
    isPersonalInput.name = "is_personal";
    isPersonalInput.value = "1";
    form.appendChild(isPersonalInput);

    const titleElement = modal.querySelector("h3");
    const descElement = modal.querySelector("p");
    if (titleElement) titleElement.textContent = "Новая личная задача";
    if (descElement) descElement.textContent = "Создайте задачу для себя";

    // Скрываем ненужные поля
    const executorField = document
        .querySelector('select[name="user_id"]')
        ?.closest(".space-y-2");
    const departmentField = document
        .querySelector('select[name="department_id"]:not([type="hidden"])')
        ?.closest(".space-y-2");
    const statusField = document
        .querySelector('select[name="status"]')
        ?.closest(".space-y-2");

    if (executorField) executorField.style.display = "none";
    if (departmentField) departmentField.style.display = "none";
    if (statusField) statusField.style.display = "none";

    modal.classList.remove("hidden");
    document.body.classList.add("overflow-y-hidden");
}
export function updateTaskSelectedFilesDisplay() {
            const container = document.getElementById('selectedFilesContainer');
            const fileCounter = document.getElementById('fileCounter');
            const fileCount = document.getElementById('fileCount');

            if (!container) return;

            if (taskSelectedFiles.length === 0) {
                container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                    <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Файлы не выбраны</p>
                    <p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p>
                </div>`;
                if (fileCounter) fileCounter.classList.add('hidden');
            } else {
                let html = '';
                taskSelectedFiles.forEach(file => {
                    const fileIcon = getFileIcon(file.extension);
                    const fileType = getFileTypeClass(file.extension);
                    html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 ${fileType.bg} rounded flex items-center justify-center">
                                <span class="text-lg">${fileIcon}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(file.name)}</p>
                                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            </div>
                        </div>
                        <button onclick="removeTaskSelectedFile(${file.id})" class="text-red-500 hover:text-red-700 p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;
                });
                container.innerHTML = html;
                if (fileCount) fileCount.textContent = taskSelectedFiles.length;
                if (fileCounter) fileCounter.classList.remove('hidden');
            }
        }
export function closeTaskModal() {
    const modal = document.getElementById("taskModal");
    const form = document.getElementById("taskForm");

    if (!modal) return;

    // Восстанавливаем валидацию формы
    form.removeAttribute("novalidate");

    // Восстанавливаем видимость полей
    const executorField = document
        .querySelector('select[name="user_id"]')
        ?.closest(".space-y-2");
    const departmentField = document
        .querySelector('select[name="department_id"]:not([type="hidden"])')
        ?.closest(".space-y-2");
    const statusField = document
        .querySelector('select[name="status"]')
        ?.closest(".space-y-2");

    if (executorField) executorField.style.display = "block";
    if (departmentField) departmentField.style.display = "block";
    if (statusField) statusField.style.display = "block";

    // Удаляем скрытые поля
    const hiddenDept = form.querySelector(
        'input[name="department_id"][type="hidden"]',
    );
    if (hiddenDept) hiddenDept.remove();

    const hiddenIsPersonal = form.querySelector('input[name="is_personal"]');
    if (hiddenIsPersonal) hiddenIsPersonal.remove();

    const titleElement = modal.querySelector("h3");
    const descElement = modal.querySelector("p");
    if (titleElement) titleElement.textContent = "Новая задача";
    if (descElement) descElement.textContent = "Заполните информацию о задаче";

    if (form) {
        form.reset();
    }

    taskSelectedFiles = [];
    updateTaskSelectedFilesDisplay();

    modal.classList.add("hidden");
    document.body.classList.remove("overflow-y-hidden");
}

export async function startTask(taskId) {
    try {
        const response = await fetch(`/tasks/${taskId}/status`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ status: "в работе" }),
        });
        const data = await response.json();
        if (data.success) {
            showNotification("Задача переведена в работу!", "success");
            location.reload();
        } else {
            showNotification(
                data.message || "Ошибка при обновлении статуса",
                "error",
            );
        }
    } catch (error) {
        console.error("Ошибка:", error);
        showNotification("Ошибка при обновлении статуса", "error");
    }
}

export async function sendForReview(taskId) {
    currentTaskId = taskId;
    const timeModal = document.getElementById("timeModal");
    if (timeModal) timeModal.classList.remove("hidden");
}

export async function submitForReview() {
    const actualHours = document.getElementById("actualHours")?.value;
    if (!actualHours || actualHours <= 0) {
        showNotification(
            "Пожалуйста, укажите корректное время работы",
            "warning",
        );
        return;
    }

    try {
        const response = await fetch(`/tasks/${currentTaskId}/status`, {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({
                status: "на проверке",
                actual_hours: actualHours,
            }),
        });
        const data = await response.json();
        if (data.success) {
            showNotification("Задача отправлена на проверку!", "success");
            closeTimeModal();
            location.reload();
        } else {
            showNotification(
                data.message || "Ошибка при отправке на проверку",
                "error",
            );
        }
    } catch (error) {
        console.error("Ошибка:", error);
        showNotification("Ошибка при отправке на проверку", "error");
    }
}

export function showRejectModal(taskId) {
    currentTaskId = taskId;
    const rejectModal = document.getElementById("rejectModal");
    if (rejectModal) rejectModal.classList.remove("hidden");
}

export async function submitRejection() {
    const reason = document.getElementById("rejectReason")?.value.trim();
    if (!reason) {
        showNotification("Пожалуйста, укажите причину отказа", "error");
        return;
    }

    try {
        const response = await fetch(`/tasks/${currentTaskId}/reject`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            body: JSON.stringify({ reason }),
        });
        const data = await response.json();
        if (data.success) {
            showNotification("Вы отказались от задачи", "warning");
            closeRejectModal();
            location.reload();
        } else {
            showNotification(
                data.message || "Ошибка при отказе от задачи",
                "error",
            );
        }
    } catch (error) {
        console.error("Ошибка:", error);
        showNotification("Ошибка при отказе от задачи", "error");
    }
}

export function closeTimeModal() {
    const timeModal = document.getElementById("timeModal");
    const actualHours = document.getElementById("actualHours");
    if (timeModal) timeModal.classList.add("hidden");
    if (actualHours) actualHours.value = "";
    currentTaskId = null;
}

export function closeRejectModal() {
    const rejectModal = document.getElementById("rejectModal");
    const rejectReason = document.getElementById("rejectReason");
    if (rejectModal) rejectModal.classList.add("hidden");
    if (rejectReason) rejectReason.value = "";
    currentTaskId = null;
}
