<div id="editCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Редактировать категорию</h3>
            <button onclick="closeEditCategoryModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editCategoryForm" action="{{ route('category.update') }}" method="POST">
            @csrf
            @method('patch')
            <input type="hidden" name="category_id" id="edit_category_id">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название категории</label>
                <input type="text" name="name" id="edit_category_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название категории" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Цвет</label>
                <div class="flex space-x-2">
                    <div class="color-option">
                        <input type="radio" name="color" value="#3B82F6" class="hidden" id="edit-color-blue">
                        <label for="edit-color-blue"
                               class="w-8 h-8 bg-blue-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#EF4444" class="hidden" id="edit-color-red">
                        <label for="edit-color-red"
                               class="w-8 h-8 bg-red-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#10B981" class="hidden" id="edit-color-green">
                        <label for="edit-color-green"
                               class="w-8 h-8 bg-green-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#F59E0B" class="hidden" id="edit-color-yellow">
                        <label for="edit-color-yellow"
                               class="w-8 h-8 bg-yellow-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#8B5CF6" class="hidden" id="edit-color-purple">
                        <label for="edit-color-purple"
                               class="w-8 h-8 bg-purple-500 rounded-full cursor-pointer block"></label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditCategoryModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Сохранить
                    изменения
                </button>
            </div>
        </form>
    </div>
</div>
