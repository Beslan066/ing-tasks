import '../components/task-edit-files';
import {closeTaskViewModal}from '../functions/close-task-view-modal';
import { updateVisibleCount } from '../functions/updateVisibleCount';
window.closeTaskViewModal=closeTaskViewModal;
window.updateVisibleCount=updateVisibleCount;
console.log('all-tasks.page.js')
   document.addEventListener('DOMContentLoaded', function () {
            updateVisibleCount();
        });
