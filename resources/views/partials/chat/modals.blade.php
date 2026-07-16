{{-- New Chat Modal --}}
<div x-show="showNewChatModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
     @click.self="showNewChatModal = false"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="bg-white rounded-2xl w-full max-w-md p-6 dark:bg-gray-800 shadow-xl transform transition-all"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="scale-95 opacity-0 translate-y-4"
         x-transition:enter-end="scale-100 opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="scale-100 opacity-100 translate-y-0"
         x-transition:leave-end="scale-95 opacity-0 translate-y-4">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Новый чат</h3>
            <button @click="showNewChatModal = false"
                    class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full transition-colors"
                    :disabled="loading">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-4">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       x-model="colleagueSearch"
                       placeholder="Поиск сотрудников..."
                       class="w-full rounded-lg border-2 border-gray-300 px-4 py-2 pl-10 dark:bg-gray-700 dark:border-gray-600 outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all"
                       :disabled="loading">
            </div>
        </div>

        <div class="max-h-96 overflow-auto space-y-2 custom-scrollbar">
            <template x-if="filteredColleagues.length === 0">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-user-slash text-4xl mb-2 opacity-50"></i>
                    <p>Нет доступных сотрудников</p>
                </div>
            </template>

            <template x-for="colleague in filteredColleagues" :key="colleague.id">
                <div @click="startPrivateChat(colleague)"
                     class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 group"
                     :class="{'opacity-50 pointer-events-none': loading}">

                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200">
                            <template x-if="colleague.avatar">
                                <img :src="colleague.avatar" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!colleague.avatar">
                                <div class="h-full w-full flex items-center justify-center text-sm font-medium text-white"
                                     :class="'bg-' + (colleague.avatar_color || 'blue-500')">
                                    <span x-text="colleague.initials"></span>
                                </div>
                            </template>
                        </div>
                        <span class="online-indicator w-2.5 h-2.5"
                              :class="colleague.is_online ? 'online' : 'offline'"></span>
                    </div>

                    <div class="flex-1">
                        <p class="font-medium" x-text="colleague.name"></p>
                        <p class="text-sm text-gray-500" x-text="colleague.department || 'Без отдела'"></p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs"
                              :class="colleague.is_online ? 'text-green-500' : 'text-gray-400'">
                            <i class="fas fa-circle text-[6px] mr-1"></i>
                            <span x-text="colleague.is_online ? 'В сети' : getLastActivityText(colleague.last_activity)"></span>
                        </span>
                        <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-500 transition-colors"></i>
                    </div>
                </div>
            </template>
        </div>

        <!-- Индикатор загрузки внизу модалки -->
        <div x-show="loading" class="flex justify-center items-center mt-4">
            <i class="fas fa-spinner fa-spin text-green-500 mr-2"></i>
            <span class="text-sm text-gray-500">Создание чата...</span>
        </div>
    </div>
</div>

{{-- New Group Modal --}}
<div x-show="showNewGroupModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
     @click.self="showNewGroupModal = false">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 dark:bg-gray-800 shadow-xl transform transition-all max-h-[90vh] overflow-auto"
         x-transition:enter="transition transform duration-300"
         x-transition:enter-start="scale-95 opacity-0"
         x-transition:enter-end="scale-100 opacity-100"
         x-transition:leave="transition transform duration-300"
         x-transition:leave-start="scale-100 opacity-100"
         x-transition:leave-end="scale-95 opacity-0">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Создать группу</h3>
            <button @click="showNewGroupModal = false"
                    class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Название группы *</label>
                <input type="text"
                       x-model="newGroup.name"
                       placeholder="Введите название..."
                       class="w-full rounded-lg border-2 border-gray-300 px-4 py-2 dark:bg-gray-700 dark:border-gray-600 outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Описание (необязательно)</label>
                <textarea x-model="newGroup.description"
                          rows="2"
                          placeholder="Краткое описание группы..."
                          class="w-full rounded-lg border-2 border-gray-300 px-4 py-2 dark:bg-gray-700 dark:border-gray-600 outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Участники (минимум 2)</label>
                <div class="relative mb-2">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text"
                           x-model="groupSearch"
                           placeholder="Поиск сотрудников..."
                           class="w-full rounded-lg border-2 border-gray-300 px-4 py-2 pl-10 dark:bg-gray-700 dark:border-gray-600 outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100">
                </div>

                <div class="max-h-48 overflow-auto space-y-1 custom-scrollbar border rounded-lg p-2">
                    <template x-if="filteredGroupColleagues.length === 0">
                        <div class="text-center py-4 text-gray-500 text-sm">
                            <i class="fas fa-users-slash mb-1"></i>
                            <p>Нет доступных сотрудников</p>
                        </div>
                    </template>

                    <template x-for="colleague in filteredGroupColleagues" :key="colleague.id">
                        <label class="flex items-center gap-3 p-2 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <input type="checkbox"
                                   :value="colleague.id"
                                   x-model="newGroup.selectedUsers"
                                   class="rounded border-gray-300 text-green-500 border-2 outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100">
                            <div class="flex-1">
                                <p class="font-medium text-sm" x-text="colleague.name"></p>
                                <p class="text-xs text-gray-500" x-text="colleague.department || 'Без отдела'"></p>
                            </div>
                            <span class="online-indicator w-2.5 h-2.5"
                                  :class="colleague.is_online ? 'online' : 'offline'"></span>
                        </label>
                    </template>
                </div>

                <div class="mt-2 text-sm text-gray-500 flex items-center gap-2">
                    <span>Выбрано: <strong x-text="newGroup.selectedUsers.length"></strong></span>
                    <span class="text-xs" x-show="newGroup.selectedUsers.length < 2">(нужно минимум 2)</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button @click="showNewGroupModal = false"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                Отмена
            </button>
            <button @click="createGroupChat"
                    :disabled="!newGroup.name || newGroup.selectedUsers.length < 2"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                Создать
            </button>
        </div>
    </div>
</div>

{{-- Add Users Modal --}}
<div x-show="showAddUsersModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
     @click.self="showAddUsersModal = false">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 dark:bg-gray-800 shadow-xl transform transition-all"
         x-transition:enter="transition transform duration-300"
         x-transition:enter-start="scale-95 opacity-0"
         x-transition:enter-end="scale-100 opacity-100"
         x-transition:leave="transition transform duration-300"
         x-transition:leave-start="scale-100 opacity-100"
         x-transition:leave-end="scale-95 opacity-0">

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Добавить участников</h3>
            <button @click="showAddUsersModal = false"
                    class="text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-4">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                       x-model="addUsersSearch"
                       placeholder="Поиск сотрудников..."
                       class="w-full rounded-lg border-2 border-gray-300 px-4 py-2 pl-10 dark:bg-gray-700 dark:border-gray-600 outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100">
            </div>
        </div>

        <div class="max-h-96 overflow-auto space-y-1 custom-scrollbar">
            <template x-for="colleague in filteredAddUsers" :key="colleague.id">
                <template x-if="activeChat && activeChat.users && !activeChat.users.some(u => u.id === colleague.id)">
                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <input type="checkbox"
                               :value="colleague.id"
                               x-model="selectedUsersToAdd"
                               class="rounded border-2 border-gray-300 text-green-500 focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none">
                        <div class="flex-1">
                            <p class="font-medium" x-text="colleague.name"></p>
                            <p class="text-sm text-gray-500" x-text="colleague.department || 'Без отдела'"></p>
                        </div>
                        <span class="online-indicator w-2.5 h-2.5"
                              :class="colleague.is_online ? 'online' : 'offline'"></span>
                    </label>
                </template>
            </template>

            <template x-if="activeChat && activeChat.users && filteredAddUsers.filter(u => !activeChat.users.some(au => au.id === u.id)).length === 0">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl mb-2 opacity-50"></i>
                    <p>Все сотрудники уже в чате</p>
                </div>
            </template>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button @click="showAddUsersModal = false"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                Отмена
            </button>
            <button @click="addUsersToChat"
                    :disabled="selectedUsersToAdd.length === 0"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                Добавить (<span x-text="selectedUsersToAdd.length"></span>)
            </button>
        </div>
    </div>
</div>
