import { addTaskToColumn } from "./add-task-to-column";

(function () {
    'use strict';
console.log('init create-personal task')

    function isPersonalTaskModal() {
        const modal = document.getElementById('taskModal');
        if (!modal || modal.classList.contains('hidden')) return false;

        const titleElement = modal.querySelector('h3');
        return titleElement && titleElement.textContent.trim() === 'Новая личная задача';
    }

    function getCsrfToken(form) {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || form?.querySelector('input[name="_token"]')?.value
            || '';
    }

    // function syncSelectedFiles(formData) {
    //     const state = window.taskCreateState;

    //     if (state && Array.isArray(state.selected) && state.selected.length > 0) {
    //         const selectedIds = state.selected.map(file => file.id);
    //         formData.set('selected_files', JSON.stringify(selectedIds));
    //         console.log('📌 [PersonalTask] Прикрепленные ID файлов:', selectedIds);
    //     } else {
    //         formData.set('selected_files', JSON.stringify([]));
    //         console.log('ℹ️ [PersonalTask] Файлы не выбраны');
    //     }
    // }
function syncSelectedFiles(formData) {
    formData.delete('selected_files');
    (window.taskCreateState?.selected ?? [])
        .forEach(f => formData.append('selected_file_ids[]', f.id));
}
    function resetFileState() {
        if (window.taskCreateState) {
            window.taskCreateState.selected = [];
            window.taskCreateState.temp = [];
        }
        if (typeof updateCreateSelectedFilesDisplay === 'function') {
            updateCreateSelectedFilesDisplay();
        }
        if (typeof updateCreateSelectedCount === 'function') {
            updateCreateSelectedCount();
        }
    }

    async function handlePersonalTaskSubmit(form) {
        const formData = new FormData(form);

        formData.set('is_personal', '1');

        syncSelectedFiles(formData);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.innerHTML || 'Сохранить';

        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Создание...';
            submitBtn.disabled = true;
        }

        try {
            const response = await fetch('/tasks/personal/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(form),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showNotification(data.message || 'Личная задача успешно создана!', 'success');

                resetFileState();
                form.reset();
                if (typeof closeTaskModal === 'function') {
                    closeTaskModal();
                }
                if (data.task) {
                    if (!data.task.author && window.currentUser) {
                        data.task.author = window.currentUser;
                    }

                    const status = data.task.status;
                    const statusMap = {
                        'назначена': 'new',
                        'новая': 'new',
                        'в работе': 'in-progress',
                        'в процессе': 'in-progress',
                        'на проверке': 'review',
                        'проверка': 'review',
                        'завершена': 'done',
                        'выполнена': 'done'
                    };

                    const targetStatusKey = statusMap[status.toLowerCase()] || 'new';

                    const columnContainer = document.querySelector(`.task-container[data-status="${targetStatusKey}"]`)
                        || document.querySelector(`[data-status="${targetStatusKey}"]`);

                    if (columnContainer && typeof addTaskToColumn === 'function') {
                        const canManage = true;
                        addTaskToColumn(columnContainer, data.task, canManage);
                        console.log(`✅ Карточка задачи #${data.task.id} добавлена в колонку "${targetStatusKey}"`);
                    } else {
                        console.warn(`⚠️ Контейнер для статуса "${targetStatusKey}" не найден. Выполняем перезагрузку.`);
                        location.reload();
                    }
                } else {
                    location.reload();
                    console.log(data)
                }
            }else {
                const errorMsg = data.message || 'Ошибка при создании личной задачи';
                if (typeof showNotification === 'function') {
                    showNotification(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        } catch (error) {
            console.error('❌ [PersonalTask] Ошибка отправки:', error);
            if (typeof showNotification === 'function') {
                showNotification('Сетевая ошибка при создании задачи', 'error');
            }
        } finally {
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    }

    function initPersonalTaskForm() {
        const form = document.getElementById('taskForm');
        if (!form) return;

        form.addEventListener('submit', function (event) {
            if (isPersonalTaskModal()) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();

                handlePersonalTaskSubmit(form);
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPersonalTaskForm);
    } else {
        initPersonalTaskForm();
    }
})();
