<div id="notificationsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Уведомления</h3>
            <button id="closeNotifications" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-4 max-h-96 overflow-y-auto">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white mt-1">
                    <i class="fas fa-tasks text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Новая задача назначена</p>
                    <p class="text-sm text-gray-600">Вам назначена задача "Интеграция платежной системы"</p>
                    <p class="text-xs text-gray-500 mt-1">2 часа назад</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white mt-1">
                    <i class="fas fa-check text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Задача завершена</p>
                    <p class="text-sm text-gray-600">Анна Петрова завершила задачу "Рефакторинг модуля авторизации"</p>
                    <p class="text-xs text-gray-500 mt-1">5 часов назад</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg">
                <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center text-white mt-1">
                    <i class="fas fa-exclamation text-sm"></i>
                </div>
                <div>
                    <p class="font-medium">Срок задачи истекает</p>
                    <p class="text-sm text-gray-600">Задача "Доработка главной страницы" должна быть завершена
                        завтра</p>
                    <p class="text-xs text-gray-500 mt-1">Вчера</p>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button class="text-primary hover:text-secondary font-medium">Показать все уведомления</button>
        </div>
    </div>
</div>
