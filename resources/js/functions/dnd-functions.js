/**
 * Kanban Drag & Drop Module with Swiper support
 */

import { showNotification } from "./show-notification";
import { getCsrfToken } from "./../helpers/getCsrfToken";
import { buildTaskMenu } from "../components/build-task-menu";

let draggedItem = null;
let swiperSlideTimeout = null;

// Карта сопоставления data-status -> статус в Laravel
const STATUS_MAP = {
    'new': 'назначена',
    'in-progress': 'в работе',
    'review': 'на проверке',
    'done': 'выполнена'
};

function applyDoneStyle(card) {
    card.style.removeProperty('opacity');
    if (card.getAttribute('style') === '') {
        card.removeAttribute('style');
    }
}

function finishDrag(card = draggedItem) {
    if (card) {
        applyDoneStyle(card);
    }
    draggedItem = null;

    if (window.mySwiper && typeof window.mySwiper.attachEvents === 'function') {
        window.mySwiper.attachEvents();
    }
}

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

        applyDoneStyle(card);
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
    finishDrag(this);
}

function dragOver(e) {
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
    const column = this.closest('.board-column');
    if (column) {
        column.classList.remove('drag-over-active');
    }

    if (swiperSlideTimeout) {
        clearTimeout(swiperSlideTimeout);
        swiperSlideTimeout = null;
    }
}
function refreshCardMenu(card, status) {
    console.log('refreshCardMenu',card)
    card.dataset.status = status;
    const menu = card.querySelector('.task-menu');
    if (!menu) return;
    menu.innerHTML = buildTaskMenu(card.dataset.task, status, true);
    menu.classList.add('hidden');
}
async function drop(e) {
    e.preventDefault();

    if (swiperSlideTimeout) {
        clearTimeout(swiperSlideTimeout);
        swiperSlideTimeout = null;
    }

    const column = this.closest('.board-column');
    column?.classList.remove('drag-over-active');
    if (!draggedItem || !column) return;

    const card = draggedItem;
    const newStatus = column.dataset.status;
    const newStatusValue = STATUS_MAP[newStatus];
    const prevColumn = card.closest('.board-column');
    const prevStatus = prevColumn?.dataset.status;

    if (!newStatusValue || prevStatus === newStatus) {
        finishDrag(card);
        return;
    }

    const prevParent = card.parentElement;
    const prevNext = card.nextElementSibling;

    (column.querySelector('.task-container') || column).prepend(card);
    finishDrag(card);
    refreshCardMenu(card, newStatusValue);

    const ok = await updateTaskStatus(card.dataset.task, newStatusValue);
    if (!ok) {
        prevParent.insertBefore(card, prevNext);
        refreshCardMenu(card, STATUS_MAP[prevStatus]);
    }
}

window.initDragAndDrop = initDragAndDrop;

document.addEventListener('DOMContentLoaded', () => {
    initDragAndDrop();
});
async function updateTaskStatus(taskId, status) {
    try {
        const response = await fetch(`/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ status })
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            showNotification(data.message || 'Ошибка при перемещении задачи', 'error');
            return false;
        }
        showNotification('Статус задачи обновлён', 'success');
        return true;
    } catch (e) {
        console.error(e);
        showNotification('Ошибка при перемещении задачи', 'error');
        return false;
    }
}
window.updateTaskStatus = updateTaskStatus;
