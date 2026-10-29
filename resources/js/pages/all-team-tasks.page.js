import {closeTaskViewModal}from '../functions/close-task-view-modal';
import { updateVisibleCount } from '../functions/updateVisibleCount';
import './../functions/all-team-tasks/handle-edit-task-form';
import '../components/task-edit-files';
window.closeTaskViewModal=closeTaskViewModal;
window.updateEditSelectedFilesDisplay=updateEditSelectedFilesDisplay;
console.log('all-team-tasks.page.js')
   document.addEventListener('DOMContentLoaded', function () {
            updateVisibleCount();
        });
