import { addTaskToColumn } from "./add-task-to-column";

/**
 * Модуль создания личных задач (Personal Task Creator)
 */
(function () {
    'use strict';
console.log('init create-personal task')
    /**
     * Проверяет, открыто ли модальное окно именно для "Личной задачи"
     */
    function isPersonalTaskModal() {
        const modal = document.getElementById('taskModal');
        if (!modal || modal.classList.contains('hidden')) return false;

        const titleElement = modal.querySelector('h3');
        return titleElement && titleElement.textContent.trim() === 'Новая личная задача';
    }

    /**
     * Возвращает валидный CSRF-токен из мета-тега или формы
     */
    function getCsrfToken(form) {
        return document.querySelector('meta[name="csrf-token"]')?.content
            || form?.querySelector('input[name="_token"]')?.value
            || '';
    }

    /**
     * Синхронизирует выбранные файлы из состояния window.taskCreateState в FormData
     */
    function syncSelectedFiles(formData) {
        const state = window.taskCreateState;

        if (state && Array.isArray(state.selected) && state.selected.length > 0) {
            const selectedIds = state.selected.map(file => file.id);
            formData.set('selected_files', JSON.stringify(selectedIds));
            console.log('📌 [PersonalTask] Прикрепленные ID файлов:', selectedIds);
        } else {
            // Если файлы не выбраны, очищаем поле
            formData.set('selected_files', JSON.stringify([]));
            console.log('ℹ️ [PersonalTask] Файлы не выбраны');
        }
    }

    /**
     * Очищает состояние файлов после успешного создания задачи
     */
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

    /**
     * Обработчик AJAX-отправки формы
     */
    async function handlePersonalTaskSubmit(form) {
        const formData = new FormData(form);

        // 1. Принудительно передаем флаг личной задачи
        formData.set('is_personal', '1');

        // 2. Синхронизируем прикрепленные файлы
        syncSelectedFiles(formData);

        // 3. Управление состоянием кнопки отправки
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

            // if (response.ok && data.success) {
            //     if (typeof showNotification === 'function') {
            //         showNotification(data.message || 'Личная задача успешно создана!!!!', 'success');
            //     }

            //     // Очищаем прикрепленные файлы
            //     resetFileState();

            //     // Сбрасываем саму форму
            //     form.reset();

            //     // Закрываем модальное окно
            //     if (typeof closeTaskModal === 'function') {
            //         closeTaskModal();
            //     }

            //     // Если у вас есть функция обновления списка задач без перезагрузки:
            //     if (typeof reloadTaskList === 'function') {
            //         reloadTaskList();
            //     } else if (data.redirect_url) {
            //         window.location.href = data.redirect_url;
            //     }else {
            //         location.reload();
            //     }
            // } else {
            //     const errorMsg = data.message || 'Ошибка при создании личной задачи';
            //     if (typeof showNotification === 'function') {
            //         showNotification(errorMsg, 'error');
            //     } else {
            //         alert(errorMsg);
            //     }
            // }
            if (response.ok && data.success) {
                showNotification(data.message || 'Личная задача успешно создана!', 'success');

                // 1. Очищаем состояние и закрываем модалку
                resetFileState();
                form.reset();
                if (typeof closeTaskModal === 'function') {
                    closeTaskModal();
                }

                // 2. Вставка карточки без перезагрузки
                if (data.task) {
                    // Если автор не подгружен с сервера, подставляем имя текущего пользователя (если есть глобальная переменная window.currentUser)
                    if (!data.task.author && window.currentUser) {
                        data.task.author = window.currentUser;
                    }

                    // Ищем контейнер колонки по статусу задачи (например, status: "назначена")
                    // Проверяем по data-status или по ID колонки
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

                    // 2. Получаем нужный ключ data-status (по умолчанию 'new')
                    const targetStatusKey = statusMap[status.toLowerCase()] || 'new';

                    // 3. Ищем контейнер с классом .task-container
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

    /**
     * Инициализация событий при загрузке DOM
     */
    function initPersonalTaskForm() {
        const form = document.getElementById('taskForm');
        if (!form) return;

        // Перехватываем событие submit
        form.addEventListener('submit', function (event) {
            if (isPersonalTaskModal()) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();

                handlePersonalTaskSubmit(form);
            }
        }, true); // UseCapture = true, чтобы перехватить до других скриптов
    }

    // Запускаем инициализацию
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPersonalTaskForm);
    } else {
        initPersonalTaskForm();
    }
})();
