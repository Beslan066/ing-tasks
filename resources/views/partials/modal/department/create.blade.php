<!-- Модальное окно создания отдела -->
<div id="departmentModal"
     role="dialog" aria-modal="true" aria-labelledby="departmentModalTitle"
     onclick="if (event.target === this) closeDepartmentModal()"
     class="fixed inset-0 bg-slate-900/40 hidden items-center justify-center z-50 backdrop-blur-sm p-4">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h3 id="departmentModalTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Новый отдел</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Заполните информацию об отделе</p>
                </div>
                <button type="button" onclick="closeDepartmentModal()" aria-label="Закрыть"
                        class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="departmentForm" action="{{ route('departments.store') }}" method="POST"
              class="px-8 py-6 space-y-5 max-[500px]:px-5 max-[500px]:py-4">
            @csrf

            <x-form.input
                name="name"
                id="new_department_name"
                label="Название отдела"
                placeholder="Например, Отдел разработки"
                :required="true"
            />

            <x-form.input-select
                name="company_id"
                id="new_department_company"
                label="Компания"
                placeholder="Выберите компанию"
                :required="true"
                :selected="$currentUser->company_id ?? null"
                :options="collect($ownedCompanies ?? [])->pluck('name', 'id')->all()"
            />

            <x-form.input-select
                name="supervisor_id"
                id="new_department_supervisor"
                label="Руководитель"
                placeholder="Не назначен"
                :options="collect($assignableUsers ?? [])->pluck('name', 'id')->all()"
            />

            <x-form.input-select
                name="status"
                id="new_department_status"
                label="Статус"
                selected="active"
                :options="[
                    'active'   => 'Активный',
                    'inactive' => 'Неактивный',
                ]"
            />

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse">
                <x-ui.button type="button" variant="secondary" onclick="closeDepartmentModal()">
                    Отмена
                </x-ui.button>

                <x-ui.button type="submit" variant="primary" icon="fas fa-plus">
                    Создать
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
