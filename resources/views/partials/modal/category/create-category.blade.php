<div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 backdrop-blur-md">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Новая категория</h3>
            <button onclick="closeCategoryModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="categoryForm" action="{{ route('category.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Название категории</label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="Введите название категории" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Цвет</label>
                <div class="flex space-x-3">
                    <!-- Синий -->
                    <div class="color-option relative">
                        <input type="radio" name="color" value="#3B82F6" class="hidden color-radio" id="color-blue" checked>
                        <label for="color-blue" class="w-10 h-10 bg-blue-500 rounded-full cursor-pointer block transition-all duration-200 hover:scale-110 hover:shadow-lg"></label>
                        <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center check-icon hidden">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- Красный -->
                    <div class="color-option relative">
                        <input type="radio" name="color" value="#EF4444" class="hidden color-radio" id="color-red">
                        <label for="color-red" class="w-10 h-10 bg-red-500 rounded-full cursor-pointer block transition-all duration-200 hover:scale-110 hover:shadow-lg"></label>
                        <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center check-icon hidden">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- Зеленый -->
                    <div class="color-option relative">
                        <input type="radio" name="color" value="#10B981" class="hidden color-radio" id="color-green">
                        <label for="color-green" class="w-10 h-10 bg-green-500 rounded-full cursor-pointer block transition-all duration-200 hover:scale-110 hover:shadow-lg"></label>
                        <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center check-icon hidden">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- Желтый -->
                    <div class="color-option relative">
                        <input type="radio" name="color" value="#F59E0B" class="hidden color-radio" id="color-yellow">
                        <label for="color-yellow" class="w-10 h-10 bg-yellow-500 rounded-full cursor-pointer block transition-all duration-200 hover:scale-110 hover:shadow-lg"></label>
                        <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center check-icon hidden">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>

                    <!-- Фиолетовый -->
                    <div class="color-option relative">
                        <input type="radio" name="color" value="#8B5CF6" class="hidden color-radio" id="color-purple">
                        <label for="color-purple" class="w-10 h-10 bg-purple-500 rounded-full cursor-pointer block transition-all duration-200 hover:scale-110 hover:shadow-lg"></label>
                        <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center check-icon hidden">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Скрытое поле с company_id -->
            @if(auth()->user() && auth()->user()->company_id)
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
            @endif

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Категория будет создана для компании:
                    <strong>{{ auth()->user()->company->name ?? 'Не указана' }}</strong>
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCategoryModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Создать</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colorRadios = document.querySelectorAll('#categoryModal .color-radio');

            function updateColorSelection() {
                // Убираем выделение со всех
                document.querySelectorAll('#categoryModal .color-option').forEach(option => {
                    option.classList.remove('selected');
                    const checkIcon = option.querySelector('.check-icon');
                    if (checkIcon) checkIcon.classList.add('hidden');
                });

                // Выделяем выбранный
                const selectedRadio = document.querySelector('#categoryModal .color-radio:checked');
                if (selectedRadio) {
                    const parentDiv = selectedRadio.closest('.color-option');
                    parentDiv.classList.add('selected');
                    const checkIcon = parentDiv.querySelector('.check-icon');
                    if (checkIcon) {
                        checkIcon.classList.remove('hidden');
                        // Анимация появления
                        checkIcon.style.transform = 'scale(0)';
                        setTimeout(() => {
                            checkIcon.style.transform = 'scale(1)';
                        }, 50);
                    }
                }
            }

            // Добавляем обработчики для всех radio
            colorRadios.forEach(radio => {
                radio.addEventListener('change', updateColorSelection);
            });

            // Инициализация при загрузке
            updateColorSelection();
        });
    </script>

    <style>
        #categoryModal .color-option .check-icon {
            transition: transform 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        #categoryModal .color-option.selected label {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5), 0 0 0 6px rgba(59, 130, 246, 0.2);
        }

        #categoryModal .color-option:hover label {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush
