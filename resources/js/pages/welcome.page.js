import * as fileHelpers from './../helpers/helpers.js';
import * as welcomeFilters from './../functions/welcome-filters.js'
import '../components/task-edit-files';
import '../components/task-create-files';
import '../components/welcome-dnd-cols'

import { toggleTaskMenu } from './../components/welcome-task-menu.js'
import '../functions/welcome-task-edit-form';
import '../functions/create-personal-task';
import { closeTaskViewModal } from '../functions/close-task-view-modal';

window.closeTaskViewModal = closeTaskViewModal;

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
