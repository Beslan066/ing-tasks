 <div id="confirmRestoreModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmRestoreModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash-restore text-3xl text-green-600"></i>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Восстановление задачи</h3>
                <p class="text-gray-600 text-center mb-6">
                    Задача будет восстановлена и появится на канбан-доске. Продолжить?
                </p>

                <div class="flex space-x-3">
                    <button onclick="closeConfirmRestoreModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmRestore()"
                            class="flex-1 px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition font-medium">
                        <i class="fas fa-check mr-2"></i>Восстановить
                    </button>
                </div>
            </div>
        </div>
    </div>
