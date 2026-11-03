/**
 * Универсальное открытие модального окна просмотра задачи
 * @param {string|number} taskId - ID задачи
 * @param {Object} options - Настройки кастомизации
 * @param {string} options.urlPrefix - Префикс для URL в браузере (например, '/team' или '')
 * @param {boolean} options.lockScroll - Блокировать ли прокрутку body (по умолчанию true)
 * @param {boolean} options.strictErrors - Падать ли в catch при HTTP-ошибках (по умолчанию true)
 * @param {Function} options.onSuccess - Коллбэк при успешной загрузке (например, () => setDefaultTab())
 */
export async function openTaskViewModal(taskId, options = {}) {
    console.log('openTaskViewModal.js')
    // Настройки по умолчанию
    const defaults = {
        urlPrefix: '/team',
        lockScroll: true,
        strictErrors: true,
        onSuccess: typeof setDefaultTab === 'function' ? setDefaultTab : null
    };
    const config = { ...defaults, ...options };

    const modal = document.getElementById('taskViewModal');
    const content = document.getElementById('taskModalContent');

    if (!modal || !content) {
        console.error('Модальное окно не найдено');
        return;
    }

    // Сохраняем ID задачи и текущий URL для возврата
    window.currentTaskId = taskId;
    window.taskId = taskId;
    window.returnUrl = window.location.pathname;

    // Формируем URL с учетом префикса
    const cleanPrefix = config.urlPrefix ? `/${config.urlPrefix.replace(/^\/+|\/+$/g, '')}` : '';
    const newUrl = `${cleanPrefix}/tasks/${taskId}`;

    window.history.pushState({
        taskId: taskId,
        modalOpen: true,
        returnUrl: window.returnUrl
    }, '', newUrl);

    // Показываем загрузчик
    content.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
            <p class="text-gray-500 mt-2">Загрузка задачи...</p>
        </div>
    `;

    // Активируем модальное окно
    modal.style.backdropFilter = 'blur(10px)';
    modal.classList.remove('hidden');

    try {
        // Запрос всегда идет на базовый роут (как в ваших файлах)
        const response = await fetch(`/tasks/${taskId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        // Строгий режим обработки ошибок
        if (!response.ok && config.strictErrors) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const html = await response.text();
        content.innerHTML = html;

        // Если все ок и передан коллбэк (например, setDefaultTab) — вызываем его
        if (response.ok && typeof config.onSuccess === 'function') {
            config.onSuccess();
        } else if (!response.ok) {
            console.warn(`HTTP status: ${response.status} - отображен текст ответа сервера`);
        }

    } catch (error) {
        console.error('Ошибка при загрузке задачи:', error);

        // Динамически подставляем вызов с теми же опциями в кнопку "Повторить"
        const escapedOptions = JSON.stringify(options).replace(/"/g, '&quot;');

        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                <p class="text-gray-500 mt-2">Не удалось загрузить задачу</p>
                <p class="text-sm text-gray-400 mt-1">${error.message}</p>
                <button onclick="openTaskViewModal(${taskId}, ${escapedOptions})"
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-sync-alt mr-2"></i>Повторить
                </button>
            </div>
        `;
    } finally {
        // Опциональное управление скроллом страницы
        if (config.lockScroll) {
            document.body.classList.add('overflow-y-hidden');
        }
    }
}
