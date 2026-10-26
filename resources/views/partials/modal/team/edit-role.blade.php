<!-- Модальное окно изменения роли пользователя -->
<div id="roleEditModal"
     role="dialog" aria-modal="true" aria-labelledby="roleEditTitle"
     onclick="if (event.target === this) closeRoleEditModal()"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-[60] backdrop-blur-sm p-4">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h3 id="roleEditTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Изменение роли</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Назначьте новую роль пользователю</p>
                </div>
                <button type="button" onclick="closeRoleEditModal()" aria-label="Закрыть"
                        class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-8 py-6 space-y-5 max-[500px]:px-5 max-[500px]:py-4">
            <!-- Пользователь -->
            <div class="flex items-center gap-3">
                <img id="roleEditAvatar" src="" alt="" class="hidden w-11 h-11 rounded-full object-cover border border-slate-100">
                <div id="roleEditInitial"
                     class="w-11 h-11 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-semibold"></div>
                <div class="min-w-0">
                    <p id="roleEditName" class="font-semibold text-slate-800 truncate"></p>
                    <p id="roleEditEmail" class="text-[13px] text-slate-500 truncate"></p>
                </div>
            </div>

            <!-- Текущая роль -->
            <div class="px-4 py-3 bg-slate-50/50 border border-slate-100 rounded-xl">
                <p class="text-[12px] text-slate-500">Текущая роль</p>
                <p id="roleEditCurrent" class="text-sm font-medium text-slate-800 mt-0.5"></p>
            </div>

            <!-- Новая роль (список заполняется из JS) -->
            <div class="space-y-1.5">
                <label for="roleSelect" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
                    Новая роль
                </label>
                <select id="roleSelect" name="role_id"
                        class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 text-sm hover:border-slate-300 cursor-pointer">
                    <option value="">Без роли</option>
                </select>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse">
                <x-ui.button type="button" variant="secondary" onclick="closeRoleEditModal()">
                    Отмена
                </x-ui.button>

                <x-ui.button type="button" id="saveRoleBtn" variant="primary" icon="fas fa-check">
                    Сохранить
                </x-ui.button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            const $ = (id) => document.getElementById(id);

            function onEsc(e) {
                if (e.key === 'Escape') window.closeRoleEditModal();
            }

            function fillUser(user) {
                $('roleEditName').textContent = user.name || '';
                $('roleEditEmail').textContent = user.email || '';
                $('roleEditCurrent').textContent = user.role ? user.role.name : 'Не назначена';

                const img = $('roleEditAvatar');
                const initial = $('roleEditInitial');
                if (user.avatar_url) {
                    img.src = user.avatar_url;
                    img.alt = user.name || '';
                    img.classList.remove('hidden');
                    initial.classList.add('hidden');
                } else {
                    img.removeAttribute('src');
                    img.classList.add('hidden');
                    initial.textContent = (user.name || '?').charAt(0).toUpperCase();
                    initial.classList.remove('hidden');
                }
            }

            function fillRoles(select, roles, currentRoleId) {
                select.replaceChildren(new Option('Без роли', ''));
                roles.forEach(role => select.add(new Option(role.name, role.id)));
                select.value = currentRoleId != null ? String(currentRoleId) : '';
            }

            async function getJson(url) {
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                return res.json();
            }

            window.openRoleEditModal = async function (userId) {
                showNotification('Загрузка данных...', 'info');

                try {
                    const [userData, rolesData] = await Promise.all([
                        getJson(`/team/user/${userId}`),
                        getJson('/team/roles/list'),
                    ]);

                    if (!userData.success) {
                        showNotification('Ошибка при загрузке данных пользователя', 'error');
                        return;
                    }
                    if (!rolesData.success) {
                        showNotification('Ошибка при загрузке списка ролей', 'error');
                        return;
                    }

                    const user = userData.user;
                    fillUser(user);
                    fillRoles($('roleSelect'), rolesData.roles, user.role ? user.role.id : null);

                    $('saveRoleBtn').onclick = () => saveUserRole(user.id);

                    $('roleEditModal').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                    document.addEventListener('keydown', onEsc);
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Ошибка при загрузке данных пользователя', 'error');
                }
            };

            window.closeRoleEditModal = function () {
                $('roleEditModal').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                document.removeEventListener('keydown', onEsc);
            };
        })();
    </script>
@endpush
