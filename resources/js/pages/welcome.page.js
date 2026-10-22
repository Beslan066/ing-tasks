import * as fileHelpers from './../helpers/helpers.js';
import * as welcomeFilters from './../functions/welcome-page/welcome-filters.js'
import '../components/task-edit-files';
import '../components/task-create-files';
import '../components/welcome-page/welcome-dnd-cols.js'

import { toggleTaskMenu } from '../components/welcome-page/welcome-task-menu.js'
import '../functions/welcome-page/welcome-task-edit-form';
import '../functions/welcome-page/create-personal-task';
import { closeTaskViewModal } from '../functions/close-task-view-modal';
import {closeEditModal} from './../functions/welcome-page/close-edit-modal.js'

import {
    openPersonalTaskModal,
    startTask,
    showRejectModal,
    sendForReview,
    closeTaskModal,
    submitForReview,
    closeRejectModal,
    closeTimeModal,
    submitRejection
} from './../functions/welcome-page/personal-task.functions.js'


window.closeTaskViewModal = closeTaskViewModal;
window.closeEditModal=closeEditModal;
window.openPersonalTaskModal=openPersonalTaskModal;
window.startTask=startTask;
window.sendForReview=sendForReview;
window.showRejectModal=showRejectModal;
window.submitForReview=submitForReview;
window.closeTaskModal=closeTaskModal;
window.closeRejectModal=closeRejectModal;
window.closeTimeModal=closeTimeModal;
window.submitRejection=submitRejection;

Object.assign(window, fileHelpers);
Object.assign(window, welcomeFilters)

window.toggleTaskMenu = toggleTaskMenu;

document.addEventListener('click', function (event) {
    if (!event.target.closest('.task-menu') && !event.target.closest('[onclick*="toggleTaskMenu"]')) {
        document.querySelectorAll('.task-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});
