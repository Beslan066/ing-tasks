<div id="confirmForceDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-[100] backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="confirmForceDeleteModalContent">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-800 mb-2">Удаление задачи</h3>
                <p class="text-gray-600 text-center mb-4">
                    Вы действительно хотите удалить эту задачу <span class="font-bold text-red-600">НАВСЕГДА</span>?
                </p>

                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                Это действие невозможно отменить. Задача и все связанные с ней данные будут удалены безвозвратно.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button onclick="closeConfirmForceDeleteModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                        Отмена
                    </button>
                    <button onclick="confirmForceDelete()"
                            class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 transition font-medium">
                        <i class="fas fa-trash-alt mr-2"></i>Удалить навсегда
                    </button>
                </div>
            </div>
        </div>
    </div>
