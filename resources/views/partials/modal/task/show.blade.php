<!-- Модальное окно просмотра задачи -->
<div id="taskViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 backdrop-blur-md">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl min-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Информация о задаче
            </h3>
            <button onclick="closeTaskViewModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="taskModalContent" class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                <p class="text-gray-500 mt-2">Загрузка...</p>
            </div>
        </div>
    </div>
</div>
