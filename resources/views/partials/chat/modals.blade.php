{{-- New Chat Modal --}}
<div x-show="showNewChatModal"
     x-cloak
     x-transition.opacity.duration.200ms
     x-effect="document.body.classList.toggle('overflow-hidden', showNewChatModal)"
     @keydown.escape.window="showNewChatModal = false"
     @click.self="showNewChatModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm max-[500px]:p-4">

    <div x-show="showNewChatModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-5 scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-5 scale-[0.98]"
         role="dialog"
         aria-modal="true"
         aria-labelledby="newChatModalTitle"
         class="bg-white dark:bg-gray-800 rounded-[24px] w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:max-h-[85vh]">

        {{-- Заголовок --}}
        <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-slate-100 dark:border-gray-700">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 id="newChatModalTitle"
                        class="text-[22px] font-semibold text-slate-800 dark:text-gray-100 tracking-tight max-[500px]:text-xl">
                        Новый чат
                    </h3>
                    <p class="text-[13px] text-slate-500 dark:text-gray-400 mt-0.5 max-[500px]:text-[12px]">
                        Выберите сотрудника для личной переписки
                    </p>
                </div>
                <button type="button"
                        @click="showNewChatModal = false"
                        :disabled="loading"
                        aria-label="Закрыть"
                        class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Содержимое --}}
        <div class="px-8 py-6 space-y-4 max-[500px]:px-5 max-[500px]:py-4">

            {{-- Поиск --}}
            <div class="relative">
                <label for="newChatSearchInput" class="sr-only">Поиск сотрудников</label>
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="text"
                       id="newChatSearchInput"
                       x-model="colleagueSearch"
                       placeholder="Поиск сотрудников..."
                       :disabled="loading"
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300 disabled:opacity-60 disabled:cursor-not-allowed">
            </div>

            {{-- Список сотрудников --}}
            <div class="max-h-80 overflow-auto space-y-1 custom-scrollbar border border-slate-200 dark:border-gray-600 rounded-xl p-2">
                <template x-if="filteredColleagues.length === 0">
                    <div class="text-center py-8 border border-dashed border-slate-200 dark:border-gray-600 rounded-lg bg-slate-50/50 dark:bg-gray-700/30">
                        <i class="fas fa-user-slash text-3xl text-slate-300 mb-3"></i>
                        <p class="text-[13px] text-slate-500 font-medium">Нет доступных сотрудников</p>
                    </div>
                </template>

                <template x-for="colleague in filteredColleagues" :key="colleague.id">
                    <button type="button"
                            @click="startPrivateChat(colleague)"
                            :disabled="loading"
                            class="group w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-lg border border-transparent hover:bg-slate-50 dark:hover:bg-gray-700 hover:border-slate-200 dark:hover:border-gray-600 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 disabled:opacity-50 disabled:pointer-events-none">

                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-200">
                                <template x-if="colleague.avatar">
                                    <img :src="colleague.avatar" alt="" class="h-full w-full object-cover">
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

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-[13px] text-slate-700 dark:text-gray-100 truncate" x-text="colleague.name"></p>
                            <p class="text-[12px] text-slate-500 dark:text-gray-400 truncate" x-text="colleague.department || 'Без отдела'"></p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-[12px]"
                                  :class="colleague.is_online ? 'text-emerald-600' : 'text-slate-500 dark:text-gray-400'">
                                <i class="fas fa-circle text-[6px] mr-1 align-middle"></i>
                                <span x-text="colleague.is_online ? 'В сети' : getLastActivityText(colleague.last_activity)"></span>
                            </span>
                            <i class="fas fa-chevron-right text-xs text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Индикатор загрузки --}}
        <div x-show="loading"
             x-transition.opacity
             class="sticky bottom-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-t border-slate-100 dark:border-gray-700
                    flex justify-center items-center gap-2 px-8 py-4 max-[500px]:px-5">
            <i class="fas fa-spinner fa-spin text-emerald-500"></i>
            <span class="text-[13px] text-slate-500 dark:text-gray-400">Создание чата...</span>
        </div>
    </div>
</div>

{{-- New Group Modal --}}
<div x-show="showNewGroupModal"
     x-cloak
     x-transition.opacity.duration.200ms
     x-effect="document.body.classList.toggle('overflow-hidden', showNewGroupModal)"
     @keydown.escape.window="showNewGroupModal = false"
     @click.self="showNewGroupModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm max-[500px]:p-4">

    <div x-show="showNewGroupModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-5 scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-5 scale-[0.98]"
         role="dialog"
         aria-modal="true"
         aria-labelledby="newGroupModalTitle"
         class="bg-white dark:bg-gray-800 rounded-[24px] w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:max-h-[85vh]">

        {{-- Заголовок --}}
        <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-slate-100 dark:border-gray-700">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 id="newGroupModalTitle"
                        class="text-[22px] font-semibold text-slate-800 dark:text-gray-100 tracking-tight max-[500px]:text-xl">
                        Создать группу
                    </h3>
                    <p class="text-[13px] text-slate-500 dark:text-gray-400 mt-0.5 max-[500px]:text-[12px]">
                        Добавьте название и выберите участников
                    </p>
                </div>
                <button type="button"
                        @click="showNewGroupModal = false"
                        aria-label="Закрыть"
                        class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Содержимое --}}
        <div class="px-8 py-6 space-y-6 max-[500px]:px-5 max-[500px]:py-4">

            {{-- Название --}}
            <div class="space-y-1.5">
                <label for="newGroupName"
                       class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                    Название группы <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="newGroupName"
                       x-model="newGroup.name"
                       placeholder="Введите название..."
                       class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300">
            </div>

            {{-- Описание --}}
            <div class="space-y-1.5">
                <label for="newGroupDescription"
                       class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                    Описание
                    <span class="text-slate-400 font-normal">(необязательно)</span>
                </label>
                <textarea id="newGroupDescription"
                          x-model="newGroup.description"
                          rows="2"
                          placeholder="Краткое описание группы..."
                          class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300 resize-none"></textarea>
            </div>

            {{-- Участники --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <label for="newGroupSearch"
                           class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                        Участники <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[12px] text-slate-500 dark:text-gray-400">минимум 2</span>
                </div>

                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input type="text"
                           id="newGroupSearch"
                           x-model="groupSearch"
                           placeholder="Поиск сотрудников..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300">
                </div>

                <div class="max-h-52 overflow-auto space-y-1 custom-scrollbar border border-slate-200 dark:border-gray-600 rounded-xl p-2">
                    <template x-if="filteredGroupColleagues.length === 0">
                        <div class="text-center py-8 border border-dashed border-slate-200 dark:border-gray-600 rounded-lg bg-slate-50/50 dark:bg-gray-700/30">
                            <i class="fas fa-users-slash text-2xl text-slate-300 mb-2"></i>
                            <p class="text-[13px] text-slate-500 font-medium">Нет доступных сотрудников</p>
                        </div>
                    </template>

                    <template x-for="colleague in filteredGroupColleagues" :key="colleague.id">
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer border transition-all duration-200"
                               :class="newGroup.selectedUsers.some(id => id == colleague.id)
                                    ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/30'
                                    : 'border-transparent hover:bg-slate-50 dark:hover:bg-gray-700'">
                            <input type="checkbox"
                                   :value="colleague.id"
                                   x-model="newGroup.selectedUsers"
                                   class="w-4 h-4 rounded border-slate-300 text-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:ring-offset-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-[13px] text-slate-700 dark:text-gray-100 truncate" x-text="colleague.name"></p>
                                <p class="text-[12px] text-slate-500 dark:text-gray-400 truncate" x-text="colleague.department || 'Без отдела'"></p>
                            </div>
                            <span class="online-indicator w-2.5 h-2.5"
                                  :class="colleague.is_online ? 'online' : 'offline'"></span>
                        </label>
                    </template>
                </div>

                <div class="text-[12px] text-slate-500 dark:text-gray-400 flex items-center gap-1.5">
                    <i class="fas fa-user-check opacity-70"></i>
                    <span>Выбрано: <strong class="text-slate-700 dark:text-gray-200" x-text="newGroup.selectedUsers.length"></strong></span>
                    <span class="text-amber-600" x-show="newGroup.selectedUsers.length < 2">· нужно ещё выбрать</span>
                </div>
            </div>
        </div>

        {{-- Кнопки действий --}}
        <div class="sticky bottom-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-t border-slate-100 dark:border-gray-700
                    flex justify-end items-center gap-3 px-8 py-4
                    max-[500px]:px-5 max-[500px]:flex-col-reverse max-[500px]:justify-center">
            <x-ui.button
                type="button"
                variant="secondary"
                @click="showNewGroupModal = false"
            >
                Отмена
            </x-ui.button>

            <x-ui.button
                type="button"
                variant="primary"
                icon="fas fa-users"
                @click="createGroupChat"
                x-bind:disabled="!newGroup.name || newGroup.selectedUsers.length < 2"
            >
                Создать группу
            </x-ui.button>
        </div>
    </div>
</div>

{{-- Add Users Modal --}}
<div x-show="showAddUsersModal"
     x-cloak
     x-transition.opacity.duration.200ms
     x-effect="document.body.classList.toggle('overflow-hidden', showAddUsersModal)"
     @keydown.escape.window="showAddUsersModal = false"
     @click.self="showAddUsersModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm max-[500px]:p-4">

    <div x-show="showAddUsersModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-5 scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-5 scale-[0.98]"
         role="dialog"
         aria-modal="true"
         aria-labelledby="addUsersModalTitle"
         class="bg-white dark:bg-gray-800 rounded-[24px] w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:max-h-[85vh]">

        {{-- Заголовок --}}
        <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-slate-100 dark:border-gray-700">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 id="addUsersModalTitle"
                        class="text-[22px] font-semibold text-slate-800 dark:text-gray-100 tracking-tight max-[500px]:text-xl">
                        Добавить участников
                    </h3>
                    <p class="text-[13px] text-slate-500 dark:text-gray-400 mt-0.5 max-[500px]:text-[12px]">
                        Выберите сотрудников для добавления в чат
                    </p>
                </div>
                <button type="button"
                        @click="showAddUsersModal = false"
                        aria-label="Закрыть"
                        class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Содержимое --}}
        <div class="px-8 py-6 space-y-4 max-[500px]:px-5 max-[500px]:py-4">

            {{-- Поиск --}}
            <div class="relative">
                <label for="addUsersSearchInput" class="sr-only">Поиск сотрудников</label>
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input type="text"
                       id="addUsersSearchInput"
                       x-model="addUsersSearch"
                       placeholder="Поиск сотрудников..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300">
            </div>

            {{-- Список сотрудников --}}
            <div class="max-h-72 overflow-auto space-y-1 custom-scrollbar border border-slate-200 dark:border-gray-600 rounded-xl p-2">
                <template x-for="colleague in filteredAddUsers" :key="colleague.id">
                    <template x-if="activeChat && activeChat.users && !activeChat.users.some(u => u.id === colleague.id)">
                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer border transition-all duration-200"
                               :class="selectedUsersToAdd.some(id => id == colleague.id)
                                    ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/30'
                                    : 'border-transparent hover:bg-slate-50 dark:hover:bg-gray-700'">
                            <input type="checkbox"
                                   :value="colleague.id"
                                   x-model="selectedUsersToAdd"
                                   class="w-4 h-4 rounded border-slate-300 text-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:ring-offset-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-[13px] text-slate-700 dark:text-gray-100 truncate" x-text="colleague.name"></p>
                                <p class="text-[12px] text-slate-500 dark:text-gray-400 truncate" x-text="colleague.department || 'Без отдела'"></p>
                            </div>
                            <span class="online-indicator w-2.5 h-2.5"
                                  :class="colleague.is_online ? 'online' : 'offline'"></span>
                        </label>
                    </template>
                </template>

                <template x-if="activeChat && activeChat.users && filteredAddUsers.filter(u => !activeChat.users.some(au => au.id === u.id)).length === 0">
                    <div class="text-center py-8 border border-dashed border-slate-200 dark:border-gray-600 rounded-lg bg-slate-50/50 dark:bg-gray-700/30">
                        <i class="fas fa-check-circle text-3xl text-slate-300 mb-3"></i>
                        <p class="text-[13px] text-slate-500 font-medium">Все сотрудники уже в чате</p>
                    </div>
                </template>
            </div>

            {{-- Счётчик --}}
            <div class="text-[12px] text-slate-500 dark:text-gray-400 flex items-center gap-1.5">
                <i class="fas fa-user-check opacity-70"></i>
                <span>Выбрано: <strong class="text-slate-700 dark:text-gray-200" x-text="selectedUsersToAdd.length"></strong></span>
            </div>
        </div>

        {{-- Кнопки действий --}}
        <div class="sticky bottom-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-t border-slate-100 dark:border-gray-700
                    flex justify-end items-center gap-3 px-8 py-4
                    max-[500px]:px-5 max-[500px]:flex-col-reverse max-[500px]:justify-center">
            <x-ui.button
                type="button"
                variant="secondary"
                @click="showAddUsersModal = false"
            >
                Отмена
            </x-ui.button>

            <x-ui.button
                type="button"
                variant="primary"
                icon="fas fa-user-plus"
                @click="addUsersToChat"
                x-bind:disabled="selectedUsersToAdd.length === 0"
            >
                Добавить (<span x-text="selectedUsersToAdd.length"></span>)
            </x-ui.button>
        </div>
    </div>
</div>
