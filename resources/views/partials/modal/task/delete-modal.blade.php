<div id="deleteTaskModal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 backdrop-blur-md">
    <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-3">Удаление задачи</h3>
        <p class="text-gray-600 mb-4">Вы уверены, что хотите удалить эту задачу? Это действие нельзя отменить.</p>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <button onclick="confirmDeleteTask()"
                    class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 text-sm md:text-base">
                Да, удалить
            </button>
            <button onclick="closeDeleteModal()"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 text-sm md:text-base">
                Отмена
            </button>
        </div>
    </div>
</div>
