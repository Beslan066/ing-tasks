<!-- Модальное окно просмотра задачи -->
<div id="taskViewModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center hidden z-50 p-4 backdrop-blur-sm max-[500px]:items-center">
    <div class="relative flex w-[90%] h-[90vh] max-w-[1500px] max-[500px]:w-[98%] max-[500px]:max-h-[85vh] max-[500px]:flex-col">

        <!-- Кнопки действий -->
        <div class="absolute right-0 top-20 -translate-y-1/2 -mr-14 flex flex-col gap-3 max-[500px]:!hidden">
            <button onclick="copyTaskLink()"
                    class="w-11 h-11 bg-white rounded-full shadow-lg shadow-slate-900/20 border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all duration-200 hover:scale-110"
                    title="Копировать ссылку">
                <i class="fas fa-link text-sm"></i>
            </button>
            <button onclick="printTask()"
                    class="w-11 h-11 bg-white rounded-full shadow-lg shadow-slate-900/20 border border-slate-200 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all duration-200 hover:scale-110"
                    title="Печать">
                <i class="fas fa-print text-sm"></i>
            </button>
            <button onclick="closeTaskViewModal()"
                    class="w-11 h-11 bg-white rounded-full shadow-lg shadow-slate-900/20 border border-slate-200 flex items-center justify-center text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-all duration-200 hover:scale-110"
                    title="Закрыть">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Контент модального окна -->
        <div class="bg-white rounded-[24px] shadow-2xl shadow-slate-900/25 border border-slate-200/50 w-full h-full overflow-hidden flex flex-col">
            <!-- Мобильный заголовок -->
            <div class="sticky top-0 bg-white/95 backdrop-blur-md border-b border-slate-200 px-5 py-4 hidden justify-between items-center max-[500px]:flex z-20">
                <h3 class="text-[17px] font-semibold text-slate-800">Просмотр задачи</h3>
                <button onclick="closeTaskViewModal()" class="text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-all duration-200 p-2 rounded-full">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div id="taskModalContent" class="flex-1 overflow-y-auto p-0 bg-white scrollbar-none [&::-webkit-scrollbar]:hidden">
                <div class="text-center py-16">
                    <i class="fas fa-spinner fa-spin text-3xl text-emerald-500"></i>
                    <p class="text-slate-500 mt-3 text-sm">Загрузка задачи...</p>
                </div>
            </div>
        </div>
    </div>
</div>
