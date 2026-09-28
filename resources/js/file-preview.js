const MAX_TEXT_BYTES = 500 * 1024;

export function initFilePreview() {
    const modal = document.getElementById('file-view-modal');
    const img = document.getElementById('file-view-img');
    const pre = document.getElementById('text-view-content');
    const nameEl = document.getElementById('file-view-name');
    const closeBtn = document.getElementById('file-view-close');
if (!modal || !img || !pre || !nameEl) {
    console.warn('file-preview: не найдены элементы модалки');
    return;
}
    let controller = null;

    const show = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        controller?.abort();
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        img.src = '';
        img.classList.add('hidden');
        pre.textContent = '';
        pre.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    const openImage = (url, name) => {
        img.src = url;
        img.alt = name;
        img.classList.remove('hidden');
        nameEl.textContent = name;
        show();
    };

    const openText = async (url, name) => {
        controller?.abort();
        controller = new AbortController();

        pre.textContent = 'Загрузка...';
        pre.classList.remove('hidden');
        nameEl.textContent = name;
        show();

        try {
            const res = await fetch(url, { signal: controller.signal });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const blob = await res.blob();
            let text = await blob.slice(0, MAX_TEXT_BYTES).text();
            if (blob.size > MAX_TEXT_BYTES) {
                text += '\n\n... файл обрезан, скачайте его целиком для полного просмотра';
            }
            pre.textContent = text;
        } catch (err) {
            if (err.name === 'AbortError') return;
            pre.textContent = 'Не удалось загрузить файл';
        }
    };

    document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-preview="true"]');
    if (!trigger) return;
    if (e.target.closest('a, button, form')) return;

    const { previewType, previewUrl, previewName } = trigger.dataset;

    if (previewType === 'image') openImage(previewUrl, previewName);
    else if (previewType === 'text') openText(previewUrl, previewName);
    else if (previewType === 'pdf') window.open(previewUrl, '_blank', 'noopener');
});

    closeBtn?.addEventListener('click', close);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}
