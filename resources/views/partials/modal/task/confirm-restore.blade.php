 <div id="confirmRestoreModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
     <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmRestoreModalContent">
         <div class="p-6">
             <div class="flex items-center justify-center mb-4">
                 <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                     <i class="fas fa-trash-restore text-3xl text-green-600"></i>
                 </div>
             </div>

             <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Восстановление задачи</h3>
             <p class="text-gray-600 text-center mb-6">
                 Задача будет восстановлена и появится на канбан-доске. Продолжить?
             </p>

             <div class="flex space-x-3">
                 <button onclick="closeConfirmRestoreModal()"
                     class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                     Отмена
                 </button>
                 <button onclick="confirmRestore()"
                     class="flex-1 px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition font-medium">
                     <i class="fas fa-check mr-2"></i>Восстановить
                 </button>
             </div>
         </div>
     </div>
 </div>
 @push('scripts')
 <script>
     let pendingRestoreTaskId = null;

     function restoreTask(taskId) {
         pendingRestoreTaskId = taskId;
         const modal = document.getElementById('confirmRestoreModal');
         const modalContent = document.getElementById('confirmRestoreModalContent');

         if (modal) {
             modal.classList.remove('hidden');
             setTimeout(() => {
                 modalContent.classList.remove('scale-95', 'opacity-0');
                 modalContent.classList.add('scale-100', 'opacity-100');
             }, 10);
         }
     }

     function closeConfirmRestoreModal() {
         const modal = document.getElementById('confirmRestoreModal');
         const modalContent = document.getElementById('confirmRestoreModalContent');

         if (modal) {
             modalContent.classList.remove('scale-100', 'opacity-100');
             modalContent.classList.add('scale-95', 'opacity-0');

             setTimeout(() => {
                 modal.classList.add('hidden');
                 pendingRestoreTaskId = null;
             }, 300);
         }
     }

     async function confirmRestore() {
         if (!pendingRestoreTaskId) return;

         const confirmBtn = document.querySelector('#confirmRestoreModal .bg-green-500');
         const originalText = confirmBtn.innerHTML;
         confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Восстановление...';
         confirmBtn.disabled = true;

         try {
             const response = await fetch(`/tasks/${pendingRestoreTaskId}/restore`, {
                 method: 'PATCH',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                     'Accept': 'application/json'
                 }
             });

             const data = await response.json();

             if (data.success) {
                 showNotification(data.message, 'success');
                 closeConfirmRestoreModal();
                 setTimeout(() => location.reload(), 1500);
             } else {
                 showNotification(data.message, 'error');
                 closeConfirmRestoreModal();
             }
         } catch (error) {
             console.error('Ошибка:', error);
             showNotification('Ошибка при восстановлении задачи', 'error');
             closeConfirmRestoreModal();
         } finally {
             confirmBtn.innerHTML = originalText;
             confirmBtn.disabled = false;
         }
     }
 </script>
 @endpush
