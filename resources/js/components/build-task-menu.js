const ITEMS = {
    edit:    { fn: 'openEditModal',          icon: 'fa-edit',         color: 'text-blue-500',   label: 'Редактировать' },
    archive: { fn: 'archiveTask',            icon: 'fa-archive',      color: 'text-yellow-500', label: 'В архив' },
    subtask: { fn: 'openCreateSubtaskModal', icon: 'fa-list',         color: 'text-green-500',  label: 'Подзадача' },
    start:   { fn: 'startTask',              icon: 'fa-play',         color: 'text-green-500',  label: 'Начать' },
    review:  { fn: 'sendForReview',          icon: 'fa-check-circle', color: 'text-green-500',  label: 'Отправить на проверку' },
    approve: { fn: 'approveTask',            icon: 'fa-check-circle', color: 'text-blue-500',   label: 'Завершить' },
    revise:  { fn: 'returnTaskToWork',       icon: 'fa-redo',         color: 'text-orange-500', label: 'На доработку' },
    reject:  { fn: 'showRejectModal',        icon: 'fa-times-circle', color: '',                label: 'Отказаться', text: 'text-red-600' },
};

const MENUS = {
    personal: {
        manage: ['edit', 'archive'],
        byStatus: {
            'назначена':   ['subtask', 'start', 'reject'],
            'в работе':    ['review', 'reject'],
            'просрочена':  ['review', 'reject'],
            'на проверке': ['approve', 'reject'],
        },
        fallback: ['reject'],
    },
    team: {
        manage: ['edit', 'archive'],
        byStatus: {
            'назначена':   ['subtask'],
            'в работе':    ['review', 'reject'],
            'просрочена':  ['review', 'reject'],
            'на проверке': ['approve', 'revise', 'reject'],
        },
        fallback: ['reject'],
    },
};

const menuItem = (action, icon, iconColor, label, textColor = 'text-gray-700') => `
    <button onclick="${action}" class="w-full text-left px-4 py-2 text-sm ${textColor} hover:bg-gray-100 flex items-center">
        <i class="fas ${icon} mr-2 ${iconColor}"></i> ${label}
    </button>`;

const renderItem = (key, taskId) => {
    const { fn, icon, color, label, text } = ITEMS[key];
    return menuItem(`${fn}(${taskId})`, icon, color, label, text);
};

export function buildTaskMenu(taskId, status, canManage, isPersonal = false) {
    if (status === 'выполнена') return '';

    const menu = MENUS[isPersonal ? 'personal' : 'team'];
    const keys = [
        ...(canManage ? menu.manage : []),
        ...(menu.byStatus[status] ?? menu.fallback),
    ];

    return `<div class="py-1">${keys.map(key => renderItem(key, taskId)).join('')}</div>`;
}
