 <div id="upgradeModal"
        role="dialog" aria-modal="true" aria-labelledby="upgradeModalTitle"
        onclick="if (event.target === this) closeUpgradeModal()"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">

        <div class="modal-content bg-white rounded-[24px] w-full max-w-md max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                    scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

            <!-- Заголовок -->
            <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
                <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                    <div class="min-w-0">
                        <h3 id="upgradeModalTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Обновление до Премиум</h3>
                        <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Выберите период подписки</p>
                    </div>
                    <button type="button" onclick="closeUpgradeModal()" aria-label="Закрыть"
                            class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="px-8 py-6 space-y-5 max-[500px]:px-5 max-[500px]:py-4">
                <!-- Что входит -->
                <ul class="space-y-2 text-[13px] text-slate-600">
                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-emerald-500 text-[11px]"></i>До 15 пользователей</li>
                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-emerald-500 text-[11px]"></i>1 ТБ хранилища</li>
                    <li class="flex items-center gap-2.5"><i class="fas fa-check text-emerald-500 text-[11px]"></i>Все расширенные возможности</li>
                </ul>

                <!-- Период -->
                <fieldset class="space-y-1.5">
                    <legend class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] mb-1.5">Период</legend>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="premium_period" value="month" class="sr-only peer" onchange="updatePremiumPrice()" checked>
                            <div class="h-full flex flex-col items-center justify-center gap-0.5 border border-slate-200 rounded-xl p-3 text-center bg-slate-50/50 hover:border-slate-300 transition-all duration-200
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                                <span class="text-[13px] font-medium text-slate-800">1 месяц</span>
                                <span class="text-[13px] text-slate-500">1 490 ₽</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="premium_period" value="6months" class="sr-only peer" onchange="updatePremiumPrice()">
                            <div class="h-full flex flex-col items-center justify-center gap-0.5 border border-slate-200 rounded-xl p-3 text-center bg-slate-50/50 hover:border-slate-300 transition-all duration-200
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                                <span class="text-[13px] font-medium text-slate-800">6 месяцев</span>
                                <span class="text-[13px] text-slate-500">13 446 ₽</span>
                                <span class="mt-1 text-[12px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">−10%</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="premium_period" value="year" class="sr-only peer" onchange="updatePremiumPrice()">
                            <div class="h-full flex flex-col items-center justify-center gap-0.5 border border-slate-200 rounded-xl p-3 text-center bg-slate-50/50 hover:border-slate-300 transition-all duration-200
                                        peer-checked:border-emerald-500 peer-checked:bg-emerald-50/60 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20">
                                <span class="text-[13px] font-medium text-slate-800">12 месяцев</span>
                                <span class="text-[13px] text-slate-500">25 398 ₽</span>
                                <span class="mt-1 text-[12px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">−15%</span>
                            </div>
                        </label>
                    </div>
                </fieldset>

                <!-- Индикатор создания платежа -->
                <div id="premiumLoadingIndicator" class="hidden text-center py-1" role="status">
                    <i class="fas fa-spinner fa-spin text-emerald-500 mr-2"></i>
                    <span class="text-[13px] text-slate-500">Создание платежа...</span>
                </div>

                <!-- Сумма -->
                <div class="px-4 py-3.5 bg-slate-50/50 border border-slate-100 rounded-xl space-y-2">
                    <div class="flex justify-between text-[13px] text-slate-600">
                        <span>Премиум подписка</span>
                        <span id="premiumBasePrice">1 490 ₽</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-100 text-sm font-semibold text-slate-800">
                        <span>Итого</span>
                        <span id="premiumTotalPrice" class="text-emerald-700">1 490 ₽</span>
                    </div>
                </div>

                <x-ui.button type="button" id="premiumPayButton" variant="primary" icon="fas fa-credit-card"
                            class="w-full" onclick="processUpgrade()">
                    <span>Оплатить</span>
                </x-ui.button>
            </div>
        </div>
    </div>
