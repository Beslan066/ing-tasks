const burgerBtn = document.getElementById('burger-btn');
const sidebarMenu = document.getElementById('sidebar-menu');
const overlay = document.getElementById('sidebar-overlay');

if (burgerBtn && sidebarMenu && overlay) {
    function closeSidebar() {
        sidebarMenu.classList.remove('active');
        burgerBtn.classList.remove('active');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    burgerBtn.addEventListener('click', function () {
        sidebarMenu.classList.toggle('active');
        burgerBtn.classList.toggle('active');
        if (sidebarMenu.classList.contains('active')) {
            document.body.classList.add('overflow-hidden');
            overlay.classList.remove('hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
            overlay.classList.add('hidden');
        }
    });

    overlay.addEventListener('click', closeSidebar);
}

const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
const mainContainer = document.querySelector('.main-container');
const navItems = document.querySelectorAll('.nav-item');

const isCollapsed = localStorage.getItem('sidebar-mode-collapsed') === 'true';
if (isCollapsed && mainContainer) {
    mainContainer.classList.add('sidebar-mode-collapsed');
}

if (sidebarToggleBtn && mainContainer) {
    sidebarToggleBtn.addEventListener('click', function () {
        if (window.innerWidth > 638) {
            mainContainer.classList.toggle('sidebar-mode-collapsed');
            const currentlyCollapsed = mainContainer.classList.contains('sidebar-mode-collapsed');
            localStorage.setItem('sidebar-mode-collapsed', currentlyCollapsed);
        }
    });
}

const currentPath = window.location.pathname;
navItems.forEach(item => {
    if (item.getAttribute('href') === currentPath) {
        item.classList.add('active');
    }
});
