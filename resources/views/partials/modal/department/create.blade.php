<div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новый отдел</h3>
            <button onclick="closeDepartmentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="departmentForm" action="{{route('departments.store')}}" method="post">
            @csrf
            @method('post')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название отдела</label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название отдела" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Компания</label>
                <select name="company_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">

                    @if(isset($ownedCompanies))
                        @foreach($ownedCompanies as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Вы будете автоматически назначены руководителем этого отдела
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDepartmentModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Создать
                    отдел
                </button>
            </div>
        </form>
    </div>
</div>
