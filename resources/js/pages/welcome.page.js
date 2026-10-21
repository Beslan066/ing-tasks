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
window.closeTaskViewModal = closeTaskViewModal;
window.closeEditModal=closeEditModal;
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
