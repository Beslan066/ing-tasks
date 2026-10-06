/**
 * Kanban Drag & Drop Module with Swiper support
 */

let draggedItem = null;
let swiperSlideTimeout = null;

// Карта сопоставления data-status -> статус в Laravel
const STATUS_MAP = {
    'new': 'назначена',
    'in-progress': 'в работе',
    'review': 'на проверке',
    'done': 'выполнена'
};

/**
 * Инициализация / Переинициализация Drag and Drop
 */
export function initDragAndDrop() {
    const taskCards = document.querySelectorAll('.task-card');
    const columns = document.querySelectorAll('.board-column');

    taskCards.forEach(card => {
        card.setAttribute('draggable', 'true');
        card.removeEventListener('dragstart', dragStart);
        card.removeEventListener('dragend', dragEnd);
        card.addEventListener('dragstart', dragStart);
        card.addEventListener('dragend', dragEnd);
    });

    columns.forEach(column => {
        column.removeEventListener('dragover', dragOver);
        column.removeEventListener('dragleave', dragLeave);
        column.removeEventListener('drop', drop);
        column.addEventListener('dragover', dragOver);
        column.addEventListener('dragleave', dragLeave);
        column.addEventListener('drop', drop);
    });
}

function dragStart(e) {
        console.log('welcome-dnd-cols.js')
    draggedItem = this;
    e.dataTransfer.setData('text/plain', this.dataset.task);
    this.style.opacity = '0.5';

    if (window.mySwiper && typeof window.mySwiper.detachEvents === 'function') {
        window.mySwiper.detachEvents();
    }

    if (e.dataTransfer.setDragImage) {
        e.dataTransfer.setDragImage(this, 0, 0);
    }
}

function dragEnd(e) {
    console.log('welcome-dnd-cols.js')
    if (draggedItem) {
        draggedItem.style.opacity = '';
        draggedItem = null;
    }

    if (window.mySwiper && typeof window.mySwiper.attachEvents === 'function') {
        window.mySwiper.attachEvents();
    }
}

function dragOver(e) {
        console.log('welcome-dnd-cols.js')
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';

    const column = this.closest('.board-column');
    if (column) {
        column.classList.add('drag-over-active');

        if (window.mySwiper && typeof window.mySwiper.slideTo === 'function') {
            const allColumns = Array.from(document.querySelectorAll('.board-column'));
            const columnIndex = allColumns.indexOf(column);

            if (columnIndex !== -1 && window.mySwiper.activeIndex !== columnIndex && !swiperSlideTimeout) {
                swiperSlideTimeout = setTimeout(() => {
                    window.mySwiper.slideTo(columnIndex, 300);
                    swiperSlideTimeout = null;
                }, 400);
            }
        }
    }
}

function dragLeave(e) {
        console.log('welcome-dnd-cols.js')
    const column = this.closest('.board-column');
    if (column) {
        column.classList.remove('drag-over-active');
    }

    if (swiperSlideTimeout) {
        clearTimeout(swiperSlideTimeout);
        swiperSlideTimeout = null;
    }
}

function drop(e) {
        console.log('welcome-dnd-cols.js')
    e.preventDefault();

    if (swiperSlideTimeout) {
        clearTimeout(swiperSlideTimeout);
        swiperSlideTimeout = null;
        if (window.mySwiper && typeof window.mySwiper.attachEvents === 'function') {
            window.mySwiper.attachEvents();
        }
    }

    const column = this.closest('.board-column');
    if (column) {
        column.classList.remove('drag-over-active');
    }

    if (!draggedItem || !column) return;

    const newStatus = column.dataset.status;
    const currentColumn = draggedItem.closest('.task-container');
    const currentStatus = currentColumn ? currentColumn.dataset.status : null;

    if (currentStatus === newStatus) {
        draggedItem.style.opacity = '1';
        draggedItem = null;
        return;
    }

    // 1. Физическое перемещение карточки в DOM
    const targetContainer = column.querySelector('.task-container') || column;
    targetContainer.prepend(draggedItem);

    // 2. Отправка обновленного статуса на сервер
    const taskId = draggedItem.dataset.task;
    const newStatusValue = STATUS_MAP[newStatus];

    if (newStatusValue && typeof window.updateTaskStatus === 'function') {
        window.updateTaskStatus(taskId, newStatusValue);
    } else if (newStatusValue && typeof updateTaskStatus === 'function') {
        updateTaskStatus(taskId, newStatusValue);
    }
}

// Делаем функцию доступной глобально на случай классического подключения через <script>
window.initDragAndDrop = initDragAndDrop;

// Автоинициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    initDragAndDrop();
});
