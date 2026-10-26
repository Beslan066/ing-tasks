<div id="inviteModal"
     role="dialog" aria-modal="true" aria-labelledby="inviteModalTitle"
     onclick="if (event.target === this) document.getElementById('closeInviteModal').click()"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-50 backdrop-blur-sm p-4">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h3 id="inviteModalTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Пригласить сотрудников</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Найдите пользователей и выберите роль и отдел</p>
                </div>
                <button type="button" id="closeInviteModal" aria-label="Закрыть"
                        class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="inviteForm" class="px-8 py-6 space-y-7 max-[500px]:px-5 max-[500px]:py-4">
            @csrf

            <!-- Поиск пользователей -->
            <div class="space-y-1.5">
                <label for="userSearch" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
                    Поиск пользователей
                </label>
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none group-focus-within:text-emerald-500 transition-colors duration-200"></i>
                    <input type="text" id="userSearch" autocomplete="off"
                           placeholder="Введите имя или email пользователя..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300">
                </div>

                <div id="searchResults"
                     class="hidden mt-2 border border-slate-200 rounded-xl bg-white shadow-lg shadow-slate-900/5 max-h-48 md:max-h-60 overflow-y-auto custom-scrollbar"></div>
                <div id="selectedUsers" class="pt-2 space-y-2"></div>
            </div>

            <!-- Роль и отдел -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <x-form.input-select
                    name="role_id"
                    id="inviteRole"
                    label="Роль"
                    placeholder="Выберите роль"
                    :options="collect($roles ?? [])->pluck('name', 'id')->all()"
                />

                <x-form.input-select
                    name="department_id"
                    id="inviteDepartment"
                    label="Отдел"
                    placeholder="Выберите отдел"
                    :options="collect($departments ?? [])->pluck('name', 'id')->all()"
                />
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse">
                <x-ui.button type="button" id="cancelInvite" variant="secondary">
                    Отмена
                </x-ui.button>

                <x-ui.button type="submit" id="submitInvite" variant="primary" icon="fas fa-paper-plane" disabled>
                    Отправить приглашения
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
