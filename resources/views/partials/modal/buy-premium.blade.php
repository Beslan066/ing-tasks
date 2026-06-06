<div id="upgradeModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 backdrop-blur-md">
    <div class="bg-white rounded-lg p-4 md:p-6 w-full max-w-md mx-4">
        <div class="text-center mb-4">
            <div
                class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-crown text-3xl text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Улучшение до Премиум</h3>
            <p class="text-gray-600 text-sm">
                Вы уверены, что хотите перейти на тариф <strong class="text-yellow-600">Премиум</strong>?
            </p>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 mb-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-gem text-purple-600"></i>
                <span class="font-semibold text-gray-800">Преимущества Премиум:</span>
            </div>
            <ul class="text-sm text-gray-700 space-y-1 ml-6">
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> 1 ТБ хранилища</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Файлы до 1 ГБ</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Приоритетная поддержка</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Расширенная аналитика</li>
            </ul>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <form action="{{ route('company.upgrade-license') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="new_license_type" value="premium">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-crown"></i>
                    <span>Да, улучшить</span>
                </button>
            </form>
            <button onclick="closeUpgradeModal()"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                Отмена
            </button>
        </div>
    </div>
</div>
