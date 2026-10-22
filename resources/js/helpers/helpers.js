export function getFileIcon(extension) {
    const ext = (extension || '').toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(ext)) return '🖼️';
    if (['pdf'].includes(ext)) return '📄';
    if (['doc', 'docx'].includes(ext)) return '📝';
    if (['xls', 'xlsx', 'csv'].includes(ext)) return '📊';
    if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) return '📦';
    if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) return '🎵';
    if (['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(ext)) return '🎬';
    return '📎';
}

export function getFileTypeClass(extension) {
    const ext = (extension || '').toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(ext)) return { bg: 'bg-blue-100' };
    if (['pdf'].includes(ext)) return { bg: 'bg-red-100' };
    if (['doc', 'docx'].includes(ext)) return { bg: 'bg-blue-100' };
    if (['xls', 'xlsx', 'csv'].includes(ext)) return { bg: 'bg-green-100' };
    return { bg: 'bg-gray-100' };
}

export function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + ' ' + sizes[i];
}

export function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU');
}

export function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
