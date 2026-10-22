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
