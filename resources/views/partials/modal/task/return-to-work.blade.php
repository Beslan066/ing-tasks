<div id="returnToWorkModal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 backdrop-blur-md">
    <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-3">Возврат задачи на доработку</h3>
        <p class="text-gray-600 mb-3">Укажите комментарий для исполнителя:</p>
        <textarea id="returnComment" placeholder="Комментарий..."
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 h-24 resize-none text-sm md:text-base"></textarea>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <button onclick="confirmReturnToWork()"
                    class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 text-sm md:text-base">
                Вернуть на доработку
            </button>
            <button onclick="closeReturnModal()"
                    class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-400 text-sm md:text-base">
                Отмена
            </button>
        </div>
    </div>
</div>
