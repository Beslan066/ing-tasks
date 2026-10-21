// ==================== ПЕРЕКЛЮЧЕНИЕ РЕЖИМОВ ====================
export function setViewMode(mode) {
    console.log('setViewMode.js')
    const listContainer = document.getElementById('listViewContainer');
    const kanbanContainer = document.getElementById('kanbanViewContainer');
    const statisticCard = document.getElementById('statisticCardContainer');
    const listBtn = document.getElementById('viewModeListBtn');
    const kanbanBtn = document.getElementById('viewModeKanbanBtn');
    const filterForm = document.getElementById('filterForm');
    const filterFormList = document.getElementById('filterFormList');

    if (mode === 'list') {
        listContainer.classList.remove('hidden');
        kanbanContainer.classList.add('hidden');
        statisticCard.classList.remove('hidden');

        listBtn.classList.add('bg-gradient-to-br', 'from-emerald-500', 'to-emerald-600', 'text-white');
        listBtn.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
        kanbanBtn.classList.remove('bg-gradient-to-br', 'from-emerald-500', 'to-emerald-600', 'text-white');
        kanbanBtn.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
    } else {
        listContainer.classList.add('hidden');
        kanbanContainer.classList.remove('hidden');
        statisticCard.classList.add('hidden');
        kanbanBtn.classList.add('bg-gradient-to-br', 'from-emerald-500', 'to-emerald-600', 'text-white');
        kanbanBtn.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
        listBtn.classList.remove('bg-gradient-to-br', 'from-emerald-500', 'to-emerald-600', 'text-white');
        listBtn.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
    }

    // Используем правильный URL /set-view-mode
    fetch('/set-view-mode', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ view_mode: mode })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Обновляем URL в адресной строке
                const url = new URL(window.location.href);
                url.searchParams.set('view_mode', mode);
                window.history.pushState({}, '', url);

                // Обновляем скрытые поля в формах фильтров
                if (filterForm) {
                    let viewModeInput = filterForm.querySelector('input[name="view_mode"]');
                    if (!viewModeInput) {
                        viewModeInput = document.createElement('input');
                        viewModeInput.type = 'hidden';
                        viewModeInput.name = 'view_mode';
                        filterForm.appendChild(viewModeInput);
                    }
                    viewModeInput.value = mode;
                }

                if (filterFormList) {
                    let viewModeInput = filterFormList.querySelector('input[name="view_mode"]');
                    if (!viewModeInput) {
                        viewModeInput = document.createElement('input');
                        viewModeInput.type = 'hidden';
                        viewModeInput.name = 'view_mode';
                        filterFormList.appendChild(viewModeInput);
                    }
                    viewModeInput.value = mode;
                }

                console.log('View mode saved:', mode);
            }
        })
        .catch(error => console.error('Error saving view mode:', error));
}
