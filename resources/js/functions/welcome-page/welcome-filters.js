// Переменные для фильтров
let activeFilters = {
    priority: [],
    deadline: [],
    hasFiles: []
};

const priorityMap = {
    'critical': 'критический',
    'high': 'высокий',
    'medium': 'средний',
    'low': 'низкий'
};
// ==================== ФУНКЦИИ ФИЛЬТРАЦИИ ====================
export function toggleFiltersDropdown() {
    console.log('welcome-filters.js toggleFiltersDropdown')
    const dropdown = document.getElementById('filtersDropdown');
    const chevron = document.getElementById('filtersChevron');

    if (!dropdown || !chevron) return;
    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden');
        dropdown.classList.remove('fade-out-x');
        dropdown.classList.add('fade-in-x');
        chevron.style.transform = 'rotate(180deg)';
    } else {
        dropdown.classList.remove('fade-in-x');
        dropdown.classList.add('fade-out-x');

        chevron.style.transform = 'rotate(0deg)';

        setTimeout(() => {
            if (dropdown.classList.contains('fade-out-x')) {
                dropdown.classList.add('hidden');
            }
        }, 200);
    }
}

export function toggleFilterSection(sectionId) {
    console.log('welcome-filters.js toggleFilterSection')
    const section = document.getElementById(sectionId);
    const icon = document.getElementById(sectionId + 'Icon');
    if (section && icon) {
        section.classList.toggle('hidden');
        icon.style.transform = section.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }
}

function collectFiltersFromCheckboxes() {
    console.log('welcome-filters.js collectFiltersFromCheckboxes')
    activeFilters = { priority: [], deadline: [], hasFiles: [] };
    document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => {
        const type = checkbox.dataset.filterType;
        const value = checkbox.value;
        if (activeFilters[type]) activeFilters[type].push(value);
    });
    updateFiltersCounter();
}

function updateFiltersCounter() {
    console.log('welcome-filters.js updateFiltersCounter')
    const totalFilters = activeFilters.priority.length + activeFilters.deadline.length + activeFilters.hasFiles.length;
    const counterBadge = document.getElementById('activeFiltersCount');
    if (counterBadge) {
        if (totalFilters > 0) {
            counterBadge.textContent = totalFilters;
            counterBadge.classList.remove('hidden');
        } else {
            counterBadge.classList.add('hidden');
        }
    }
}

function updateActiveFiltersDisplay() {
    console.log('welcome-filters.js updateActiveFiltersDisplay')
    const container = document.getElementById('activeFiltersContainer');
    if (!container) return;
    container.innerHTML = '';
    activeFilters.priority.forEach(priority => {
        const label = priority === 'critical' ? '🚨 Критический' : (priority === 'high' ? '‼️ Высокий' : (priority === 'medium' ? 'Средний' : 'Низкий'));
        addFilterChip(container, 'priority', priority, label);
    });
    activeFilters.deadline.forEach(deadline => {
        const label = deadline === 'overdue' ? '⚠️ Просроченные' : (deadline === 'today' ? '📅 Сегодня' : (deadline === 'tomorrow' ? 'Завтра' : 'На этой неделе'));
        addFilterChip(container, 'deadline', deadline, label);
    });
    activeFilters.hasFiles.forEach(hasFile => {
        const label = hasFile === 'true' ? '📎 Есть файлы' : 'Нет файлов';
        addFilterChip(container, 'has-files', hasFile, label);
    });
}

function addFilterChip(container, type, value, label) {
    console.log('welcome-filters.js addFilterChip')
    const chip = document.createElement('div');
    chip.className = 'inline-flex items-center bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full';
    chip.innerHTML = `<span>${label}</span><button onclick="removeFilter('${type}', '${value}')" class="ml-2 text-gray-500 hover:text-gray-700"><i class="fas fa-times-circle text-xs"></i></button>`;
    container.appendChild(chip);
}

export function removeFilter(type, value) {
    console.log('welcome-filters.js removeFilter')
    const index = activeFilters[type].indexOf(value);
    if (index !== -1) activeFilters[type].splice(index, 1);
    const checkbox = document.querySelector(`.filter-checkbox[data-filter-type="${type}"][value="${value}"]`);
    if (checkbox) checkbox.checked = false;
    updateActiveFiltersDisplay();
    updateFiltersCounter();
    applyFilters();
}

export function clearAllFilters() {
    activeFilters = { priority: [], deadline: [], hasFiles: [] };
    document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => checkbox.checked = false);
    const searchInput = document.getElementById('taskSearchInput');
    if (searchInput) searchInput.value = '';
    updateActiveFiltersDisplay();
    updateFiltersCounter();
    applyFilters();
    const dropdown = document.getElementById('filtersDropdown');
    if (dropdown && !dropdown.classList.contains('hidden')) {
        dropdown.classList.add('hidden');
        const chevron = document.getElementById('filtersChevron');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }
}

export function applyFiltersAndClose() {
    collectFiltersFromCheckboxes();
    updateActiveFiltersDisplay();
    applyFilters();
    toggleFiltersDropdown();
}

export function applyFilters() {
    const taskCards = document.querySelectorAll('.task-card');
    const searchTerm = document.getElementById('taskSearchInput')?.value.toLowerCase().trim() || '';

    taskCards.forEach(card => {
        let show = true;

        if (activeFilters.priority.length > 0) {
            const priorityValue = card.dataset.priority;
            let matchesPriority = false;
            for (const filter of activeFilters.priority) {
                if (priorityValue === priorityMap[filter]) { matchesPriority = true; break; }
            }
            if (!matchesPriority) show = false;
        }

        if (show && activeFilters.deadline.length > 0) {
            const deadlineDateStr = card.dataset.deadline;
            let matchesDeadline = false;
            for (const filter of activeFilters.deadline) {
                if (filter === 'overdue' && deadlineDateStr) {
                    const deadlineDate = new Date(deadlineDateStr);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (deadlineDate < today) { matchesDeadline = true; break; }
                } else if (filter === 'today' && deadlineDateStr && isToday(new Date(deadlineDateStr))) {
                    matchesDeadline = true; break;
                } else if (filter === 'tomorrow' && deadlineDateStr && isTomorrow(new Date(deadlineDateStr))) {
                    matchesDeadline = true; break;
                } else if (filter === 'week' && deadlineDateStr && isThisWeek(new Date(deadlineDateStr))) {
                    matchesDeadline = true; break;
                }
            }
            if (!matchesDeadline) show = false;
        }

        if (show && activeFilters.hasFiles.length > 0) {
            const hasFilesValue = card.dataset.hasFiles;
            let matchesFiles = false;
            for (const filter of activeFilters.hasFiles) {
                if (hasFilesValue === filter) { matchesFiles = true; break; }
            }
            if (!matchesFiles) show = false;
        }

        if (show && searchTerm) {
            const taskName = card.dataset.taskName || '';
            if (!taskName.includes(searchTerm)) show = false;
        }

        card.style.display = show ? '' : 'none';
    });

    updateColumnCounters();
}

function isToday(date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);
    return date.getTime() === today.getTime();
}

function isTomorrow(date) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);
    return date.getTime() === tomorrow.getTime();
}

function isThisWeek(date) {
    const today = new Date();
    const currentDay = today.getDay();
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - currentDay + (currentDay === 0 ? -6 : 1));
    startOfWeek.setHours(0, 0, 0, 0);
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);
    endOfWeek.setHours(23, 59, 59, 999);
    return date >= startOfWeek && date <= endOfWeek;
}

function updateColumnCounters() {
    const columns = document.querySelectorAll('.board-column');
    columns.forEach(column => {
        const taskContainer = column.querySelector('.task-container');
        if (taskContainer) {
            const visibleTasks = taskContainer.querySelectorAll('.task-card:not([style*="display: none"])').length;
            const counterSpan = column.querySelector('.stat-count');
            if (counterSpan) counterSpan.textContent = visibleTasks;
        }
    });
}


// async function updateTaskStatus(taskId, newStatus) {
//     try {
//         const response = await fetch(`/tasks/${taskId}/status`, {
//             method: 'PATCH',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
//             },
//             body: JSON.stringify({ status: newStatus })
//         });
//         const data = await response.json();
//         if (data.success) {
//             // location.reload();
//             console.log('changed task status ')
//             showNotification('Статус задачи обновлен', 'success');
//         } else {
//             showNotification(data.message || 'Ошибка при перемещении задачи', 'error');
//         }
//     } catch (error) {
//         console.error('Ошибка:', error);
//         showNotification('Ошибка при перемещении задачи', 'error');
//     }
// }

// toggleFiltersDropdown,toggleFilterSection,clearAllFilters,applyFiltersAndClose
