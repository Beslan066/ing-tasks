export function initFilePreview() {
    const modal = document.getElementById('file-preview-modal');
    if (!modal) return;

    const img = document.getElementById('file-preview-img');
    const nameEl = document.getElementById('file-preview-name');
    const closeBtn = document.getElementById('file-preview-close');

    const open = (url, name) => {
        img.src = url;
        img.alt = name;
        nameEl.textContent = name;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        img.src = '';
        document.body.classList.remove('overflow-hidden');
    };
    document.addEventListener('click', (e) => {
        const row = e.target.closest('[data-preview="true"]');
        if (!row) return;
        if (e.target.closest('a, button, form')) return;

        open(row.dataset.imageUrl, row.dataset.imageName);
    });

    closeBtn.addEventListener('click', close);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}
