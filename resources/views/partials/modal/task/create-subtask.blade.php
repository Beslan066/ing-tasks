<div id="createSubtaskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[70] backdrop-blur-md max-[500px]:p-4">
    <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto max-[500px]:max-h-[80vh]">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i>Новая подзадача
            </h3>
            <button onclick="closeCreateSubtaskModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="createSubtaskForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="subtask_parent_id" name="parent_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Название <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="subtask_name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea name="description" id="subtask_description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label>
                <select name="priority" id="subtask_priority"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="низкий">Низкий</option>
                    <option value="средний" selected>Средний</option>
                    <option value="высокий">Высокий</option>
                    <option value="критический">Критический</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Исполнитель</label>
                <select name="user_id" id="subtask_user_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">Не назначен</option>
                    @foreach($filterData['users'] ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Дедлайн</label>
                <input type="datetime-local" name="deadline" id="subtask_deadline"
                       class="w-full min-w-0 min-h-[52px] box-border appearance-none border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Планируемые часы</label>
                <input type="number" name="estimated_hours" id="subtask_estimated_hours" step="0.5" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeCreateSubtaskModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Отмена
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Добавить
                </button>
            </div>
        </form>
    </div>
</div>
