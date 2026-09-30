import './bootstrap';

import Alpine from 'alpinejs';
import { initFilePreview } from './components/file-preview';
import {addTaskToColumn} from './functions/add-task-to-column'
window.Alpine = Alpine;
window.addTaskToCol=addTaskToColumn;
Alpine.start();
document.addEventListener('DOMContentLoaded', () => {
    initFilePreview();

    console.log('initFilePreview')
});
