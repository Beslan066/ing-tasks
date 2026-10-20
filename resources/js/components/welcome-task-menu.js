// ==================== ФУНКЦИИ ДЛЯ МЕНЮ С ТРЕМЯ ТОЧКАМИ ====================
export function toggleTaskMenu(event, taskId) {
    event.stopPropagation();
    document.querySelectorAll('.task-menu').forEach(menu => {
        if (menu.id !== `taskMenu-${taskId}`) {
            menu.classList.add('hidden');
        }
    });
    const menu = document.getElementById(`taskMenu-${taskId}`);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}
