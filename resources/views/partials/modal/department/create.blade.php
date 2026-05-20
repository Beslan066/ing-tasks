<!-- Модальное окно создания отдела -->
<div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4 backdrop-blur-md">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Создать новый отдел</h3>
                <button onclick="closeDepartmentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="departmentForm" action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Название отдела</label>
                        <input type="text"
                               name="name"
                               required
                               class="w-full  border-gray-300 rounded-lg px-4 py-3 border-2 focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none"
                               placeholder="Например, Отдел разработки">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Компания</label>
                        <select name="company_id"
                                required
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-green-400 focus:ring-4 focus:ring-green-100 bg-white outline-none">
                            <option value="">Выберите компанию</option>
                            @foreach($ownedCompanies as $company)
                                <option value="{{ $company->id }}"
                                    {{ $currentUser->company_id == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Руководитель</label>
                        <select name="supervisor_id"
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-green-400 focus:ring-4 focus:ring-green-100 bg-white outline-none">
                            <option value="">Не назначен</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                            <select name="status"
                                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-green-400 focus:ring-4 focus:ring-green-100 bg-white outline-none">
                                <option value="active" selected>Активный</option>
                                <option value="inactive">Неактивный</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeDepartmentModal()"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        Создать
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
