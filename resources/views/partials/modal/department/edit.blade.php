<div id="editDepartmentModal"
     role="dialog" aria-modal="true" aria-labelledby="editDepartmentTitle"
     onclick="if (event.target === this) closeEditDepartmentModal()"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-50 backdrop-blur-sm max-[500px]:p-4">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h3 id="editDepartmentTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Редактировать отдел</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Измените название или компанию отдела</p>
                </div>
                <button type="button" onclick="closeEditDepartmentModal()" aria-label="Закрыть"
                        class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="editDepartmentForm" action="{{ route('departments.update') }}" method="post"
              class="px-8 py-6 space-y-5 max-[500px]:px-5 max-[500px]:py-4">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="department_id" id="edit_department_id">

            <x-form.input
                name="name"
                id="edit_department_name"
                label="Название отдела"
                placeholder="Введите название отдела"
                :required="true"
            />

            <x-form.input-select
                name="company_id"
                id="edit_department_company"
                label="Компания"
                :options="collect($ownedCompanies ?? [])->pluck('name', 'id')->all()"
            />

            <div class="flex items-start gap-2.5 px-4 py-3 bg-emerald-50/60 border border-emerald-100 rounded-xl">
                <i class="fas fa-info-circle text-emerald-600 mt-0.5 text-sm"></i>
                <p class="text-[13px] text-emerald-800">Вы являетесь руководителем этого отдела</p>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:flex-col-reverse max-[500px]:justify-center">
                <x-ui.button variant="secondary" onclick="closeEditDepartmentModal()">
                    Отмена
                </x-ui.button>

                <x-ui.button type="submit" variant="primary" icon="fas fa-check">
                    Сохранить
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
