import * as fileHelpers from './../helpers/helpers.js';
import '../components/task-create-files.js';
import '../components/task-edit-files.js';
import '../functions/dnd-functions.js';
import {closeTaskViewModal}from '../functions/close-task-view-modal';
import {setViewMode} from '../functions/main-admin-page/setViewMode'
import {toggleFiltersDropdown} from '../functions/main-admin-page/toggleFiltersDropdown'
import { toggleTaskMenu } from '../functions/main-admin-page/toggleTaskMenuDropdown.js';
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
// window.toggleDropdown=toggleDropdown;

// helpers
Object.assign(window, fileHelpers);

      document.addEventListener('click', function(event) {
            if (!event.target.closest('.task-menu') && !event.target.closest('[onclick*="toggleTaskMenu"]')) {
                document.querySelectorAll('.task-menu').forEach(menu => menu.classList.add('hidden'));
            }
        });


// !код для menuDropdown
        (function () {
    let openMenu = null;
    let openBtn  = null;
    let home     = null;

    function closeMenu() {
        if (!openMenu) return;
        openMenu.classList.add('hidden');
        openMenu.removeAttribute('style');
        home.appendChild(openMenu);
        openMenu = openBtn = home = null;
    }

    window.toggleDropdown = function (event) {
        event.stopPropagation();
        const btn = event.currentTarget;

        if (openBtn === btn) { closeMenu(); return; }
        closeMenu();

        const menu = btn.parentElement.querySelector('[data-dropdown]');
        if (!menu) return;

        home = btn.parentElement;
        openMenu = menu;
        openBtn = btn;

        document.body.appendChild(menu);
        menu.classList.remove('hidden');
        const r = btn.getBoundingClientRect();
        const w = menu.offsetWidth;
        const h = menu.offsetHeight;
        const openUp = r.bottom + h + 8 > window.innerHeight && r.top - h - 8 > 0;

        Object.assign(menu.style, {
            position: 'fixed',
            margin: '0',
            right: 'auto',
            left: Math.max(8, Math.min(r.right - w, window.innerWidth - w - 8)) + 'px',
            top: (openUp ? r.top - h - 4 : r.bottom + 4) + 'px',
            zIndex: 9999,
        });

        menu.onclick = closeMenu;
    };

    document.addEventListener('click', e => {
        if (openMenu && !openMenu.contains(e.target)) closeMenu();
    });
    window.addEventListener('scroll', closeMenu, true);
    window.addEventListener('resize', closeMenu);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMenu(); });
})();
