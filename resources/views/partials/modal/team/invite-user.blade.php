<div id="inviteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-4 md:p-6">
            <div class="flex justify-between items-center mb-4 md:mb-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-900">Пригласить сотрудников</h3>
                <button type="button" id="closeInviteModal" class="text-gray-500 hover:text-gray-700 p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="inviteForm">
                @csrf
                <div class="space-y-4 md:space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Поиск пользователей
                        </label>
                        <div class="relative">
                            <input type="text" id="userSearch" placeholder="Введите имя или email пользователя..."
                                   class="w-full px-3 py-2 md:px-4 md:py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 text-sm md:text-base"
                                   autocomplete="off">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <div id="searchResults" class="hidden mt-2 border border-gray-200 rounded-lg bg-white shadow-lg max-h-48 md:max-h-60 overflow-y-auto"></div>
                        <div id="selectedUsers" class="mt-3 space-y-2"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Роль</label>
                            <select id="inviteRole" name="role_id"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 text-sm md:text-base bg-white">
                                <option value="">Выберите роль</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Отдел</label>
                            <select id="inviteDepartment" name="department_id"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 text-sm md:text-base bg-white">
                                <option value="">Выберите отдел</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 mt-4 md:mt-6 pt-4 border-t border-gray-200">
                    <button type="button" id="cancelInvite"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium text-sm md:text-base">
                        Отмена
                    </button>
                    <button type="submit" id="submitInvite"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-base"
                            disabled>
                        <i class="fas fa-paper-plane"></i>
                        <span>Отправить приглашения</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>`
