<div id="confirmForceDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmForceDeleteModalContent">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
            </div>

            <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Удаление задачи</h3>
            <p class="text-gray-600 text-center mb-4">
                Вы действительно хотите удалить эту задачу <span class="font-bold text-red-600">НАВСЕГДА</span>?
            </p>

            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Это действие невозможно отменить. Задача и все связанные с ней данные будут удалены безвозвратно.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <button onclick="closeConfirmForceDeleteModal()"
                    class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                    Отмена
                </button>
                <button onclick="confirmForceDelete()"
                    class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-medium">
                    <i class="fas fa-trash-alt mr-2"></i>Удалить навсегда
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    let pendingForceDeleteTaskId = null;

    // ==================== ПОЛНОЕ УДАЛЕНИЕ С МОДАЛЬНЫМ ОКНОМ ====================
    function forceDeleteTask(taskId) {
        pendingForceDeleteTaskId = taskId;
        const modal = document.getElementById('confirmForceDeleteModal');
        const modalContent = document.getElementById('confirmForceDeleteModalContent');

        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    function closeConfirmForceDeleteModal() {
        const modal = document.getElementById('confirmForceDeleteModal');
        const modalContent = document.getElementById('confirmForceDeleteModalContent');

        if (modal) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                pendingForceDeleteTaskId = null;
            }, 300);
        }
    }

    async function confirmForceDelete() {
        if (!pendingForceDeleteTaskId) return;

        const confirmBtn = document.querySelector('#confirmForceDeleteModal .bg-red-500');
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Удаление...';
        confirmBtn.disabled = true;

        try {
            const response = await fetch(`/tasks/${pendingForceDeleteTaskId}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showNotification(data.message, 'success');
                closeConfirmForceDeleteModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
                closeConfirmForceDeleteModal();
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при удалении задачи', 'error');
            closeConfirmForceDeleteModal();
        } finally {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    }
</script>
@endpush
