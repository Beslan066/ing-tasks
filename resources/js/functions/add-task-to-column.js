export function addTaskToColumn(columnContainer, task, canManage) {
    if (!columnContainer || !task) return;

    const taskCard = document.createElement('div');
    taskCard.className = `task-card bg-white p-4 rounded-lg shadow cursor-move min-h-[100px] flex flex-col justify-between ${task.status === "просрочена" ? "border-l-4 border-red-500" : ""}`;
    taskCard.setAttribute('draggable', 'true');
    taskCard.dataset.task = task.id;
    taskCard.dataset.priority = task.priority || 'medium';
    taskCard.dataset.deadline = task.deadline || '';
    taskCard.dataset.hasFiles = task.files_count > 0 ? 'true' : 'false';
    taskCard.dataset.taskName = (task.name || '').toLowerCase();
    taskCard.dataset.authorId = task.author_id;

    const prioritySignals = {
        'низкий': {level: 1, bg: 'bg-green-50', border: 'border-green-200', filled: 'bg-green-500', empty: 'bg-green-200', text: 'text-green-700'},
        'средний': {level: 2, bg: 'bg-blue-50', border: 'border-blue-200', filled: 'bg-blue-500', empty: 'bg-blue-100', text: 'text-blue-700'},
        'высокий': {level: 3, bg: 'bg-orange-50', border: 'border-orange-200', filled: 'bg-orange-500', empty: 'bg-orange-100', text: 'text-orange-700'},
        'критический': {level: 4, bg: 'bg-red-50', border: 'border-red-200', filled: 'bg-red-500', empty: 'bg-red-100', text: 'text-red-700'},
    };
    const signal = prioritySignals[task.priority] || prioritySignals['средний'];

    taskCard.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <a href="/team/tasks/${task.id}" onclick="openTaskViewModal(${task.id}); return false;">
                <h4 class="font-medium cursor-pointer hover:text-blue-600 flex-1">
                    ${task.name}
                </h4>
            </a>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <button onclick="toggleTaskMenu(event, ${task.id})" class="text-gray-500 hover:text-gray-700 p-1" title="Действия">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div id="taskMenu-${task.id}" class="task-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50">
                        <div class="py-1">
                            ${canManage ? `
                                <button onclick="openEditModal(${task.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                                </button>
                                <button onclick="archiveTask(${task.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                    <i class="fas fa-archive mr-2 text-yellow-500"></i> В архив
                                </button>
                            ` : ''}

                            <button onclick="openCreateSubtaskModal(${task.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-list mr-2 text-green-500"></i>Подзадача
                            </button>
                            <button onclick="startTask(${task.id})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-play mr-2 text-green-500"></i> Начать
                            </button>
                            <button onclick="showRejectModal(${task.id})" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-times-circle mr-2"></i> Отказаться
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        ${task.deadline ? `
            <div class="mb-3 max-[500px]:hidden">
                <div class="flex items-center text-sm ${new Date(task.deadline) < new Date() ? 'text-red-600 font-semibold' : 'text-gray-500'}">
                    <i class="fas fa-clock mr-2"></i>
                    ${new Date(task.deadline).toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '')}${new Date(task.deadline) < new Date() ? '<span class="ml-1">(Просрочено)</span>' : ''}
                </div>
            </div>
        ` : ''}

        <div class="flex justify-between items-center max-[1300px]:items-end">
            <div class="flex space-x-1 max-[1300px]:flex-col max-[1300px]:gap-1 max-[500px]:flex-wrap max-[500px]:space-x-0 max-[500px]:flex-row">
                <span style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);" class="text-xs px-2 py-1 rounded text-white">
                    ${task.department ? task.department.name : (task.is_personal ? 'Личная' : 'Без отдела')}
                </span>

                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md ${signal.bg} border ${signal.border}">
                    <div class="flex items-end gap-[3px] h-5">
                        <div class="w-1.5 rounded-sm ${signal.level >= 1 ? signal.filled : signal.empty} h-2"></div>
                        <div class="w-1.5 rounded-sm ${signal.level >= 2 ? signal.filled : signal.empty} h-3"></div>
                        <div class="w-1.5 rounded-sm ${signal.level >= 3 ? signal.filled : signal.empty} h-4"></div>
                        <div class="w-1.5 rounded-sm ${signal.level >= 4 ? signal.filled : signal.empty} h-5"></div>
                    </div>
                    <span class="text-xs font-medium ${signal.text}">
                        ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
                    </span>
                </div>
            </div>

            <div class="flex space-x-1">
                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs"
                     title="${task.author ? task.author.name : 'Автор'}">
                    ${task.author ? task.author.name.substring(0, 2) : '??'}
                </div>
            </div>
        </div>
    `;

    if (typeof attachDragEvents === 'function') {
        attachDragEvents(taskCard);
    } else {
        taskCard.addEventListener('dragstart', (e) => {
            e.dataTransfer.setData('text/plain', task.id);
            e.dataTransfer.effectAllowed = 'move';
            taskCard.classList.add('opacity-50', 'dragging');

            if ('draggedTaskCard' in window) {
                window.draggedTaskCard = taskCard;
            }
        });

        taskCard.addEventListener('dragend', () => {
            taskCard.classList.remove('opacity-50', 'dragging');
            if ('draggedTaskCard' in window) {
                window.draggedTaskCard = null;
            }
        });
    }

    columnContainer.prepend(taskCard);
    if (typeof initDragAndDrop === 'function') {
    initDragAndDrop();
    console.log('dnd inited in column')
}
}
