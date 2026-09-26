import './bootstrap';

import Alpine from 'alpinejs';
import { initImagePreview } from './image-preview';

window.Alpine = Alpine;

Alpine.start();
document.addEventListener('DOMContentLoaded', () => {
    initImagePreview();
    console.log('initImagePreview')
});
