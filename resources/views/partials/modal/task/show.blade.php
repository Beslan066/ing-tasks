<!-- Модальное окно просмотра задачи -->
<div id="taskViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 backdrop-blur-md">
    <div class="relative flex w-[85%] h-[90vh]">
        <!-- Боковая панель с кнопками -->
        <div class="absolute right-0 top-20 -translate-y-1/2 -mr-12 flex flex-col gap-3">
        <button onclick="copyTaskLink()"
                    class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 hover:scale-110"
                    title="Копировать ссылку">
                <i class="fas fa-link"></i>
            </button>
            <button onclick="printTask()"
                    class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-all duration-200 hover:scale-110"
                    title="Печать">
                <i class="fas fa-print"></i>
            </button>
            <button onclick="closeTaskViewModal()"
                    class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200 hover:scale-110"
                    title="Закрыть">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Контент модального окна -->
        <div class="bg-[#eef2f4] rounded-lg shadow-xl w-full h-full overflow-y-auto">
            <div id="taskModalContent" class="pl-6 pt-2 pb-6 h-full">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                    <p class="text-gray-500 mt-2">Загрузка задачи...</p>
                </div>
            </div>
        </div>
    </div>
</div>
