<!-- Модальное окно добавления пользователей -->
<div id="addUsersModal"
     role="dialog" aria-modal="true" aria-labelledby="addUsersModalTitle"
     onclick="if (event.target === this) closeAddUsersModal()"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">

    <div class="modal-content bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h3 id="addUsersModalTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Добавление пользователей</h3>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Укажите количество и период оплаты</p>
                </div>
                <button type="button" onclick="closeAddUsersModal()" aria-label="Закрыть"
                        class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <div class="px-8 py-6 space-y-5 max-[500px]:px-5 max-[500px]:py-4">
            <!-- Условия -->
            <div class="flex items-start gap-2.5 px-4 py-3 bg-emerald-50/60 border border-emerald-100 rounded-xl">
                <i class="fas fa-info-circle text-emerald-600 mt-0.5 text-sm"></i>
                <p class="text-[13px] text-emerald-800">Цена за одного дополнительного пользователя — 400 ₽/мес (свыше 15 пользователей).</p>
            </div>

            <!-- Количество -->
            <div class="space-y-1.5">
                <label for="userCount" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
                    Количество дополнительных пользователей
                </label>
                <input type="number" id="userCount" min="1" max="100" value="1" onchange="updateUserPrice()"
                       class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 text-sm hover:border-slate-300">
                <p class="text-[12px] text-slate-500">До 15 пользователей уже включены в Премиум</p>
            </div>

            <!-- Период -->
            <fieldset>
                <legend class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] mb-1.5">Период</legend>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="user_period" value="month" class="sr-only peer" onchange="updateUserPrice()" checked>
                        <div class="h-full flex flex-col items-center justify-center gap-1 border border-slate-200 rounded-xl p-3 text-center bg-slate-50/50 hover:border-slate-300 transition-all duration-200
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                            <span class="text-[13px] font-medium text-slate-800">1 месяц</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="user_period" value="6months" class="sr-only peer" onchange="updateUserPrice()">
                        <div class="h-full flex flex-col items-center justify-center gap-1 border border-slate-200 rounded-xl p-3 text-center bg-slate-50/50 hover:border-slate-300 transition-all duration-200
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                            <span class="text-[13px] font-medium text-slate-800">6 месяцев</span>
                            <span class="text-[12px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">−10%</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="user_period" value="year" class="sr-only peer" onchange="updateUserPrice()">
                        <div class="h-full flex flex-col items-center justify-center gap-1 border border-slate-200 rounded-xl p-3 text-center bg-slate-50/50 hover:border-slate-300 transition-all duration-200
                                    peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                            <span class="text-[13px] font-medium text-slate-800">12 месяцев</span>
                            <span class="text-[12px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">−15%</span>
                        </div>
                    </label>
                </div>
            </fieldset>

            <!-- Индикатор создания платежа -->
            <div id="usersLoadingIndicator" class="hidden text-center py-1" role="status">
                <i class="fas fa-spinner fa-spin text-emerald-500 mr-2"></i>
                <span class="text-[13px] text-slate-500">Создание платежа...</span>
            </div>

            <!-- Сумма -->
            <div class="px-4 py-3.5 bg-slate-50/50 border border-slate-100 rounded-xl space-y-2">
                <div class="flex justify-between gap-3 text-[13px] text-slate-600">
                    <span>За дополнительных пользователей (400 ₽ × <span id="userCountDisplay">1</span>)</span>
                    <span id="userSubtotal" class="shrink-0">400 ₽</span>
                </div>
                <div class="flex justify-between text-[13px] text-emerald-700" id="userDiscountRow" style="display: none;">
                    <span>Скидка</span>
                    <span id="userDiscount">-0 ₽</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100 text-sm font-semibold text-slate-800">
                    <span>Итого</span>
                    <span id="userTotalPrice" class="text-emerald-700">400 ₽</span>
                </div>
            </div>

            <x-ui.button type="button" id="usersPayButton" variant="primary" icon="fas fa-credit-card"
                         class="w-full" onclick="processAddUsers()">
                <span>Оплатить</span>
            </x-ui.button>
        </div>
    </div>
</div>
