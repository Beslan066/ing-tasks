import * as fileHelpers from './../helpers/helpers.js';
import '../components/task-create-files';
import {closeTaskViewModal}from '../functions/close-task-view-modal';
import {setViewMode} from '../functions/main-admin-page/setViewMode'
import {toggleFiltersDropdown} from '../functions/main-admin-page/toggleFiltersDropdown'
import {updateViewModeBeforeSubmit,resetWithCurrentMode} from '../components/main-admin-page/main-page-filters'
window.closeTaskViewModal=closeTaskViewModal;
console.log('main-admin.page.js')

// filters
window.mainPageFilters = window.mainPageFilters || {};
window.mainPageFilters.updateViewModeBeforeSubmit = updateViewModeBeforeSubmit;
window.mainPageFilters.resetWithCurrentMode = resetWithCurrentMode;
window.setViewMode=setViewMode;
window.toggleFiltersDropdown=toggleFiltersDropdown;


// helpers
Object.assign(window, fileHelpers);
