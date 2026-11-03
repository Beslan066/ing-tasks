import {closeTaskViewModal}from '../functions/close-task-view-modal';
import { updateVisibleCount } from '../functions/updateVisibleCount';
import './../functions/all-team-tasks/handle-edit-task-form';
import '../components/task-edit-files';
import { openTaskViewModal } from '../functions/openTaskViewModal';
window.closeTaskViewModal=closeTaskViewModal;
window.updateEditSelectedFilesDisplay=updateEditSelectedFilesDisplay;
window.openTaskViewModal=openTaskViewModal;
console.log('all-team-tasks.page.js')
   document.addEventListener('DOMContentLoaded', function () {
            updateVisibleCount();
        });
