const menuItem = (action, icon, iconColor, label, textColor = 'text-gray-700') => `
    <button onclick="${action}" class="w-full text-left px-4 py-2 text-sm ${textColor} hover:bg-gray-100 flex items-center">
        <i class="fas ${icon} mr-2 ${iconColor}"></i> ${label}
    </button>`;

export function buildTaskMenu(taskId, status, canManage) {
    if (status === 'выполнена') return '';
    const manage = canManage
        ? menuItem(`openEditModal(${taskId})`, 'fa-edit', 'text-blue-500', 'Редактировать') +
          menuItem(`archiveTask(${taskId})`, 'fa-archive', 'text-yellow-500', 'В архив')
        : '';

    const subtask = menuItem(`openCreateSubtaskModal(${taskId})`, 'fa-list', 'text-green-500', 'Подзадача');
    const start   = menuItem(`startTask(${taskId})`, 'fa-play', 'text-green-500', 'Начать');
    const review  = menuItem(`sendForReview(${taskId})`, 'fa-check-circle', 'text-green-500', 'Отправить на проверку');
    const reject  = menuItem(`showRejectModal(${taskId})`, 'fa-times-circle', '', 'Отказаться', 'text-red-600');
    const edit = menuItem(`openEditModal(${taskId})`, 'fa-edit', 'text-blue-500', 'Редактировать');
    const archive = menuItem(`archiveTask(${taskId})`, 'fa-archive', 'text-yellow-500', 'В архив');
    let actions;
    switch (status) {
        case 'назначена':
            actions = edit+archive+subtask + start + reject;
            break;
        case 'в работе':
        case 'просрочена':
            actions =review + reject;
            break;
        case 'на проверке':
            actions = reject;
            break;
        default:
            actions = reject;
    }

    return `<div class="py-1">${manage}${actions}</div>`;
}
