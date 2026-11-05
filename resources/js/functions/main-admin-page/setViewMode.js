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

    const hasBgImage = listBtn.classList.contains('backdrop-blur-sm') || kanbanBtn.classList.contains('backdrop-blur-sm');


    const activeClasses = ['bg-emerald-500', 'text-white', 'shadow-sm', 'hover:bg-emerald-600'];

    const inactiveClasses = hasBgImage
        ? ['bg-white/80', 'backdrop-blur-sm', 'text-gray-800', 'hover:bg-white']
        : ['bg-white', 'text-gray-700', 'hover:bg-gray-50'];

    if (mode === 'list') {
        listContainer.classList.remove('hidden');
        kanbanContainer.classList.add('hidden');
        statisticCard.classList.remove('hidden');

        listBtn.classList.remove(...inactiveClasses);
        listBtn.classList.add(...activeClasses);
        kanbanBtn.classList.remove(...activeClasses);
        kanbanBtn.classList.add(...inactiveClasses);
    } else {
        listContainer.classList.add('hidden');
        kanbanContainer.classList.remove('hidden');
        statisticCard.classList.add('hidden');
        kanbanBtn.classList.remove(...inactiveClasses);
        kanbanBtn.classList.add(...activeClasses);
        listBtn.classList.remove(...activeClasses);
        listBtn.classList.add(...inactiveClasses);
    }

    fetch('/set-view-mode', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.getCsrfToken(),
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
                const url = new URL(window.location.href);
                url.searchParams.set('view_mode', mode);
                window.history.pushState({}, '', url);


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
