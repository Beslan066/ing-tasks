<div id="createSubtaskModal" class="fixed inset-0 bg-slate-900/40 flex items-center justify-center hidden z-[70] backdrop-blur-sm max-[500px]:p-4">
    <div class="bg-white modal-content rounded-[24px] w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
            scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh] transition-all duration-300">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Новая подзадача</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Заполните информацию о подзадаче</p>
                </div>
                <button type="button" onclick="closeCreateSubtaskModal()" aria-label="Закрыть"
                        class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form id="createSubtaskForm" class="px-8 py-6 space-y-7 max-[500px]:px-5 max-[500px]:py-4">
            @csrf
            <input type="hidden" id="subtask_parent_id" name="parent_id">

            <!-- Название -->
            <x-form.input
                name="name"
                id="subtask_name"
                label="Название"
                placeholder="Введите название подзадачи"
                :required="true" />

            <!-- Описание -->
            <x-form.textarea
                name="description"
                id="subtask_description"
                label="Описание"
                placeholder="Добавьте подробное описание..."
                :rows="3" />

            <!-- Приоритет и исполнитель -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <x-form.input-select
                    name="priority"
                    id="subtask_priority"
                    label="Приоритет"
                    :options="[
                        'низкий'      => 'Низкий',
                        'средний'     => 'Средний',
                        'высокий'     => 'Высокий',
                        'критический' => 'Критический',
                    ]"
                    selected="средний" />
                <x-form.input-select
                    name="user_id"
                    id="subtask_user_id"
                    label="Исполнитель"
                    placeholder="Не назначен"
                    :options="collect($filterData['users'] ?? [])->pluck('name', 'id')->all()" />
            </div>

            <!-- Дедлайн и часы -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div class="space-y-1.5">
                    <label for="subtask_deadline" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Дедлайн
                    </label>
                    <div class="relative group">
                        <input type="datetime-local" name="deadline" id="subtask_deadline"
                               class="w-full min-w-0 min-h-[42px] box-border appearance-none px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl [color-scheme:light] focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 cursor-pointer text-sm hover:border-slate-300">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="subtask_estimated_hours" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                        Планируемые часы
                    </label>
                    <div class="relative group">
                        <input type="number" name="estimated_hours" id="subtask_estimated_hours" min="0" step="0.5"
                               class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
                               placeholder="0.0">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 font-medium text-[12px] pointer-events-none">часов</span>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:justify-center max-[500px]:flex-col-reverse">
                <x-ui.button
                    variant="secondary"
                    onclick="closeCreateSubtaskModal()">
                    Отмена
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    variant="primary"
                    icon="fas fa-plus">
                    Добавить
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
