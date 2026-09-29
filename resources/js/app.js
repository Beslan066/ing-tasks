import './bootstrap';

import Alpine from 'alpinejs';
import { initFilePreview } from './file-preview';

window.Alpine = Alpine;

Alpine.start();
document.addEventListener('DOMContentLoaded', () => {
    initFilePreview();
    console.log('initFilePreview')
});
