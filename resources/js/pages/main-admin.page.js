import * as fileHelpers from './../helpers/helpers.js';
import '../components/task-create-files';
import {closeTaskViewModal}from '../functions/close-task-view-modal';
import {setViewMode} from '../functions/main-admin-page/setViewMode'
import {toggleFiltersDropdown} from '../functions/main-admin-page/toggleFiltersDropdown'
import { toggleDropdown,toggleTaskMenu } from '../functions/main-admin-page/toggleTaskMenuDropdown.js';
import {updateViewModeBeforeSubmit,resetWithCurrentMode} from '../components/main-admin-page/main-page-filters'
window.closeTaskViewModal=closeTaskViewModal;
console.log('main-admin.page.js')

// filters
window.mainPageFilters = window.mainPageFilters || {};
window.mainPageFilters.updateViewModeBeforeSubmit = updateViewModeBeforeSubmit;
window.mainPageFilters.resetWithCurrentMode = resetWithCurrentMode;
window.setViewMode=setViewMode;
window.toggleFiltersDropdown=toggleFiltersDropdown;
window.toggleTaskMenu=toggleTaskMenu;
// task
window.toggleDropdown=toggleDropdown;

// helpers
Object.assign(window, fileHelpers);

      document.addEventListener('click', function(event) {
            if (!event.target.closest('.task-menu') && !event.target.closest('[onclick*="toggleTaskMenu"]')) {
                document.querySelectorAll('.task-menu').forEach(menu => menu.classList.add('hidden'));
            }
        });
