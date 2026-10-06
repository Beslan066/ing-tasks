 export function closeTaskViewModal(url='/home') {
    console.log('close-task-view-modal.js')
            const modal = document.getElementById('taskViewModal');
            const content = document.getElementById('taskModalContent');

            if (modal) {
                modal.classList.add('hidden');
                modal.style.backdropFilter = '';
            }
            if (content) {
                content.innerHTML = `<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i><p class="text-gray-500 mt-2">Загрузка задачи...</p></div>`;
            }
            window.history.pushState({}, '', url);
        }
