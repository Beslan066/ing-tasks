<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4" style="backdrop-filter: blur(10px)">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="p-4 md:p-6">
            <div class="flex justify-between items-center mb-4 md:mb-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-900">Детали пользователя</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700 p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <div id="modalContent">
                <div class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-gray-600">Загрузка...</span>
                </div>
            </div>
        </div>
    </div>
</div>
