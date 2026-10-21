import './bootstrap';

import Alpine from 'alpinejs';
import { initFilePreview } from './components/file-preview';
import {addTaskToColumn} from './functions/add-task-to-column'
import { showNotification } from './functions/show-notification';
window.Alpine = Alpine;
window.addTaskToCol=addTaskToColumn;
window.showNotification=showNotification;
window.csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
Alpine.start();
document.addEventListener('DOMContentLoaded', () => {
    initFilePreview();

    console.log('initFilePreview')
});
 console.log('toke:',window.csrfToken())
