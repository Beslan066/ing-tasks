<!-- resources/views/partials/modal/category/delete.blade.php -->
<div id="deleteCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-6">
            <!-- Заголовок -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Подтверждение удаления</h3>
                <button type="button" onclick="closeDeleteCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Сообщение -->
            <p class="text-gray-600 mb-6">
                Вы уверены, что хотите удалить категорию
                <span id="deleteCategoryName" class="font-semibold"></span>?
                Это действие нельзя отменить.
            </p>

            <!-- Форма -->
            <form id="deleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="category_id" id="delete_category_id">

                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteCategoryModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center">
                        <i class="fas fa-trash mr-2"></i>
                        Удалить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
