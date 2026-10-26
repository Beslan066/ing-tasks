// ==================== ФУНКЦИИ ДЛЯ МЕНЮ С ТРЕМЯ ТОЧКАМИ ====================
 export function toggleDropdown(event, taskId) {
                event.stopPropagation();

                // Закрыть все открытые меню
                document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                    const id = dropdown.id.replace('dropdown-', '');
                    if (parseInt(id) !== taskId) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Открыть/закрыть текущее меню
                const dropdown = document.getElementById(`dropdown-${taskId}`);
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
            }
export function toggleTaskMenu(event, taskId) {
            event.stopPropagation();
            document.querySelectorAll('.task-menu').forEach(menu => {
                if (menu.id !== `taskMenu-${taskId}`) menu.classList.add('hidden');
            });
            const menu = document.getElementById(`taskMenu-${taskId}`);
            if (menu) menu.classList.toggle('hidden');
        }
