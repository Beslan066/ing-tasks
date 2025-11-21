<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новая категория</h3>
            <button onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="categoryForm" action="{{ route('category.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название категории</label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название категории" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Цвет</label>
                <div class="flex space-x-2">
                    <div class="color-option">
                        <input type="radio" name="color" value="#3B82F6" class="hidden" id="color-blue" checked>
                        <label for="color-blue" class="w-8 h-8 bg-blue-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#EF4444" class="hidden" id="color-red">
                        <label for="color-red" class="w-8 h-8 bg-red-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#10B981" class="hidden" id="color-green">
                        <label for="color-green" class="w-8 h-8 bg-green-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#F59E0B" class="hidden" id="color-yellow">
                        <label for="color-yellow"
                               class="w-8 h-8 bg-yellow-500 rounded-full cursor-pointer block"></label>
                    </div>
                    <div class="color-option">
                        <input type="radio" name="color" value="#8B5CF6" class="hidden" id="color-purple">
                        <label for="color-purple"
                               class="w-8 h-8 bg-purple-500 rounded-full cursor-pointer block"></label>
                    </div>
                </div>
            </div>

            <!-- Скрытое поле с company_id -->
            @if(auth()->user() && auth()->user()->company_id)
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
            @endif

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Категория будет создана для компании:
                    <strong>{{ auth()->user()->company->name ?? 'Не указана' }}</strong>
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCategoryModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать
                    категорию
                </button>
            </div>
        </form>
    </div>
</div>
