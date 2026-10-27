 <div id="confirmArchiveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm max-[500px]:p-6">
     <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmArchiveModalContent">
         <div class="p-6">
             <div class="flex items-center justify-center mb-4">
                 <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                     <i class="fas fa-archive text-3xl text-yellow-600"></i>
                 </div>
             </div>

             <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Архивация задачи</h3>
             <p class="text-gray-600 text-center mb-6" id="archiveTaskMessage">
                 Вы уверены, что хотите отправить эту задачу в архив?
             </p>

             <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                 <div class="flex">
                     <div class="flex-shrink-0">
                         <i class="fas fa-info-circle text-yellow-400"></i>
                     </div>
                     <div class="ml-3">
                         <p class="text-sm text-yellow-700">
                             Архивированные задачи можно будет восстановить на странице "Все задачи"
                         </p>
                     </div>
                 </div>
             </div>

             <div class="flex space-x-3">
                 <button onclick="closeConfirmArchiveModal()"
                     class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                     Отмена
                 </button>
                 <button onclick="confirmArchive()"
                     class="flex-1 px-4 py-2.5 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition font-medium">
                     <i class="fas fa-archive mr-2"></i>Архивировать
                 </button>
             </div>
         </div>
     </div>
 </div>
 @push('scripts')
 <script>
     let pendingArchiveTaskId = null;
     // ==================== АРХИВАЦИЯ С МОДАЛЬНЫМ ОКНОМ ====================
    function archiveTask(taskId) {
        console.log('confirm-archive.blade.php archiveTask')
         pendingArchiveTaskId = taskId;
         const modal = document.getElementById('confirmArchiveModal');
         const modalContent = document.getElementById('confirmArchiveModalContent');

         if (modal) {
             modal.classList.remove('hidden');
             setTimeout(() => {
                 modalContent.classList.remove('scale-95', 'opacity-0');
                 modalContent.classList.add('scale-100', 'opacity-100');
             }, 10);
         }
     }
    async function confirmArchive() {
         console.log('confirm-archive.blade.php confirmArchive')
         if (!pendingArchiveTaskId) return;

         // Показываем индикатор загрузки на кнопке
         const confirmBtn = document.querySelector('#confirmArchiveModal .bg-yellow-500');
         if (!confirmBtn) return;

         const originalText = confirmBtn.innerHTML;
         confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Архивация...';
         confirmBtn.disabled = true;

         try {
             const response = await fetch(`/tasks/${pendingArchiveTaskId}/archive`, {
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
                 closeConfirmArchiveModal();
                 setTimeout(() => location.reload(), 1500);
             } else {
                 showNotification(data.message, 'error');
                 closeConfirmArchiveModal();
             }
         } catch (error) {
             console.error('Ошибка:', error);
             showNotification('Ошибка при архивации задачи', 'error');
             closeConfirmArchiveModal();
         } finally {
             confirmBtn.innerHTML = originalText;
             confirmBtn.disabled = false;
         }
     }
function archiveTask(taskId) {
                pendingArchiveTaskId = taskId;
                const modal = document.getElementById("confirmArchiveModal");
                const modalContent = document.getElementById("confirmArchiveModalContent");

                if (modal) {
                    modal.classList.remove("hidden");
                    setTimeout(() => {
                        modalContent.classList.remove("scale-95", "opacity-0");
                        modalContent.classList.add("scale-100", "opacity-100");
                    }, 10);
                }
            }
     function closeConfirmArchiveModal() {
         console.log('confirm-archive.blade.php closeConfirmArchiveModal')
         const modal = document.getElementById('confirmArchiveModal');
         const modalContent = document.getElementById('confirmArchiveModalContent');

         if (modal) {
             modalContent.classList.remove('scale-100', 'opacity-100');
             modalContent.classList.add('scale-95', 'opacity-0');

             setTimeout(() => {
                 modal.classList.add('hidden');
                 pendingArchiveTaskId = null;
             }, 300);
         }
     }
 </script>
 @endpush
