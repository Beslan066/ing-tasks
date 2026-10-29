 export function updateVisibleCount() {
    const visible = document.querySelectorAll('.task-row:not([style*="display: none"])').length;
    const countElement = document.getElementById('visibleCount');

    if (countElement) {
        countElement.textContent = visible;
    }
}
