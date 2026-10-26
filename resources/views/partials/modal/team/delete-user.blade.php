<!-- Модальное окно подтверждения удаления пользователя -->
<div id="deleteConfirmModal"
     role="alertdialog" aria-modal="true"
     aria-labelledby="deleteConfirmTitle" aria-describedby="deleteConfirmDesc"
     onclick="if (event.target === this) document.getElementById('cancelDeleteBtn').click()"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-50 backdrop-blur-sm p-4">

    <div class="modal-content relative bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%]">

        <button type="button" id="closeDeleteModal" aria-label="Закрыть"
                class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
            <i class="fas fa-times text-sm"></i>
        </button>

        <div class="px-8 pt-8 pb-6 text-center max-[500px]:px-5 max-[500px]:pt-6">
            <div class="mx-auto w-14 h-14 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center mb-4">
                <i class="fas fa-user-minus text-lg text-rose-500"></i>
            </div>

            <h3 id="deleteConfirmTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Удалить пользователя?</h3>
            <p id="deleteConfirmDesc" class="text-[13px] text-slate-500 mt-2">
                Вы действительно хотите удалить пользователя
                <span id="deleteUserName" class="font-semibold text-slate-700"></span>?
            </p>
        </div>

        <!-- Кнопки действий -->
        <div class="flex justify-end items-center gap-3 px-8 py-5 border-t border-slate-100 max-[500px]:px-5 max-[500px]:justify-center max-[500px]:flex-col-reverse">
            <x-ui.button type="button" id="cancelDeleteBtn" variant="secondary">
                Отмена
            </x-ui.button>

            <x-ui.button type="button" id="confirmDeleteBtn" variant="danger" icon="fas fa-trash-alt">
                Удалить
            </x-ui.button>
        </div>
    </div>
</div>
