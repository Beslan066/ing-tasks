@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Заголовок и кнопки -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Мои отделы</h1>
                <p class="text-gray-600 mt-2">Управляйте отделами, задачами и почтовой системой</p>
            </div>
            <button onclick="openDepartmentModal()"
                    class="bg-primary-500 text-white px-6 py-3 rounded-lg hover:bg-primary-600 transition-colors flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Новый отдел</span>
            </button>
        </div>

        <!-- Статистика по отделам -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Всего отделов</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $departments->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-building text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Активных задач</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalActiveTasks }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Непрочитанных писем</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalUnreadEmails }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Всего сотрудников</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-orange-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Фильтры и поиск -->
        <div class="bg-white rounded-xl shadow mb-6 p-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text"
                               placeholder="Поиск отделов..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-green-500 focus:border-transparent outline-none"
                               id="departmentSearch">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-green-500  bg-white"
                            onchange="filterDepartments(this.value)">
                        <option value="all">Все отделы</option>
                        <option value="active">Активные</option>
                        <option value="inactive">Неактивные</option>
                    </select>
                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-green-500 bg-white"
                            onchange="sortDepartments(this.value)">
                        <option value="name">Сортировать по названию</option>
                        <option value="tasks">По количеству задач</option>
                        <option value="emails">По новым письмам</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Список отделов -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($departments as $department)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden department-card"
                     data-name="{{ strtolower($department->name) }}"
                     data-status="{{ $department->status }}">
                    <div class="p-6">
                        <!-- Заголовок отдела -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center">
                                        <i class="fas fa-building text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xl text-gray-800">{{ $department->name }}</h3>
                                        <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $department->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $department->status === 'active' ? 'Активный' : 'Неактивный' }}
                                    </span>
                                            @if($department->company)
                                                <span class="text-gray-500 text-sm">{{ $department->company->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <button class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100"
                                        onclick="toggleDepartmentMenu(this, {{ $department->id }})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <!-- Выпадающее меню -->
                                <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                    <div class="py-1">
                                        <a href="{{ route('departments.emails.index', $department) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-envelope mr-3 text-blue-500"></i>
                                            Почта отдела
                                        </a>
                                        <a href=""
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-tasks mr-3 text-green-500"></i>
                                            Задачи отдела
                                        </a>
                                        <a href=""
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-folder mr-3 text-yellow-500"></i>
                                            Файлы отдела
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                           onclick="editDepartment({{ $department->id }})">
                                            <i class="fas fa-edit mr-3 text-primary"></i>
                                            Редактировать
                                        </a>
                                        <a href="#" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                           onclick="deleteDepartment({{ $department->id }})">
                                            <i class="fas fa-trash mr-3"></i>
                                            Удалить отдел
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Статистика отдела -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Активных задач</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $department->getActiveTasksCount() }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-tasks text-blue-500"></i>
                                    </div>
                                </div>
                                @if($department->getOverdueTasks()->count() > 0)
                                    <p class="text-xs text-red-500 mt-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $department->getOverdueTasks()->count() }} просроченных
                                    </p>
                                @endif
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Почта</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $department->getEmailCount() }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-envelope text-purple-500"></i>
                                    </div>
                                </div>
                                @if($department->getUnreadEmailCount() > 0)
                                    <p class="text-xs text-blue-500 mt-2">
                                        <i class="fas fa-circle mr-1"></i>
                                        {{ $department->getUnreadEmailCount() }} непрочитанных
                                    </p>
                                @endif
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Сотрудники</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $department->getUsersCount() }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-green-500"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Файлы</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $department->files()->count() }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-folder text-yellow-500"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Быстрые действия -->
                        <div class="flex space-x-3 mb-6">
                            <a href="{{ route('departments.emails.index', $department->id) }}"
                               class="flex-1 bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-3 rounded-lg text-center transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-pen"></i>
                                <span>Написать письмо</span>
                            </a>
                            <a href="{{ route('tasks.create', ['department_id' => $department->id]) }}"
                               class="flex-1 bg-green-50 text-green-500 hover:bg-green-600 px-4 py-3 rounded-lg hover:text-white text-center transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-plus-circle"></i>
                                <span>Создать задачу</span>
                            </a>
                            <a href=""
                               class="flex-1 bg-gray-50 text-gray-600 hover:bg-gray-100 px-4 py-3 rounded-lg text-center transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-cog"></i>
                                <span>Настройки</span>
                            </a>
                        </div>

                        <!-- Информация о руководителе и последней активности -->
                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    @if($department->supervisor)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold mr-2">
                                                {{ substr($department->supervisor->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">{{ $department->supervisor->name }}</p>
                                                <p class="text-xs text-gray-500">Руководитель</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-gray-400 text-sm">
                                            <i class="fas fa-user-slash mr-2"></i>
                                            Руководитель не назначен
                                        </div>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-500">
                                    @if($department->emails()->count() > 0)
                                        @php
                                            $lastEmail = $department->emails()->latest()->first();
                                        @endphp
                                        <i class="fas fa-clock mr-1"></i>
                                        Последнее письмо: {{ $lastEmail->created_at->diffForHumans() }}
                                    @else
                                        <i class="fas fa-inbox mr-1"></i>
                                        Писем нет
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Футер с участниками -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Участники отдела</p>
                                <div class="flex -space-x-2">
                                    @foreach($department->users()->limit(5)->get() as $user)
                                        <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden"
                                             title="{{ $user->name }} ({{ $user->isOnline() ? 'онлайн' : 'офлайн' }})">
                                            @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold {{ $user->getAvatarColor() }}">
                                                    {{ $user->getInitials() }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($department->users()->count() > 5)
                                        <div class="w-8 h-8 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-gray-600 text-xs font-bold">
                                            +{{ $department->users()->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                @if($department->getUnreadEmailCount() > 0)
                                    <a href="{{ route('departments.emails.index', ['department' => $department, 'filter' => 'inbox']) }}"
                                       class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition-colors flex items-center space-x-2">
                                        <i class="fas fa-envelope"></i>
                                        <span>Открыть почту</span>
{{--                                        @if($department->getUnreadEmailCount() > 0)--}}
{{--                                            <span class="bg- text-primary text-xs px-2 py-1 rounded-full">--}}
{{--                                        {{ $department->getUnreadEmailCount() }}--}}
{{--                                    </span>--}}
{{--                                        @endif--}}
                                    </a>
                                @endif
                                <a href=""
                                   class="bg- text-gray-700 border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($departments->count() === 0)
                <div class="col-span-2">
                    <div class="bg-white rounded-xl shadow p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-6 text-gray-300">
                            <i class="fas fa-building text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">Нет отделов</h3>
                        <p class="text-gray-500 mb-6">Создайте свой первый отдел для управления задачами и почтой</p>
                        <button onclick="openDepartmentModal()"
                                class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition-colors inline-flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Создать отдел</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <!-- Модальное окно создания отдела -->
    <div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
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
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-1 focus:ring-green-500 outline-none"
                                   placeholder="Например, Отдел разработки">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Компания</label>
                            <select name="company_id"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-1 focus:ring-green-500 bg-white outline-none">
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
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-1 focus:ring-green-500 bg-white outline-none">
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
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-1 focus:ring-green-500  bg-white outline-none">
                                    <option value="active" selected>Активный</option>
                                    <option value="inactive">Неактивный</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox"
                                           id="enable_email"
                                           name="enable_email"
                                           checked
                                           class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500 focus:border-green-500">
                                    <label for="enable_email" class="text-sm text-gray-700">Включить почту</label>
                                </div>
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
                            Создать отдел
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Открытие/закрытие модального окна
        function openDepartmentModal() {
            document.getElementById('departmentModal').classList.remove('hidden');
            document.getElementById('departmentModal').classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeDepartmentModal() {
            document.getElementById('departmentModal').classList.add('hidden');
            document.getElementById('departmentModal').classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('departmentForm').reset();
        }

        // Переключение меню отдела
        function toggleDepartmentMenu(button, departmentId) {
            const menu = button.nextElementSibling;
            const isHidden = menu.classList.contains('hidden');

            // Закрыть все другие меню
            document.querySelectorAll('.relative > div:last-child').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                }
            });

            // Переключить текущее меню
            if (isHidden) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }

            // Закрыть меню при клике вне его
            if (isHidden) {
                const closeMenu = (e) => {
                    if (!menu.contains(e.target) && !button.contains(e.target)) {
                        menu.classList.add('hidden');
                        document.removeEventListener('click', closeMenu);
                    }
                };
                setTimeout(() => {
                    document.addEventListener('click', closeMenu);
                }, 10);
            }
        }

        // Поиск отделов
        let searchTimeout;
        document.getElementById('departmentSearch').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.department-card');

                cards.forEach(card => {
                    const name = card.getAttribute('data-name');
                    if (name.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }, 300);
        });

        // Фильтрация отделов
        function filterDepartments(status) {
            const cards = document.querySelectorAll('.department-card');

            cards.forEach(card => {
                const cardStatus = card.getAttribute('data-status');

                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Сортировка отделов (заглушка - в реальности нужно перезагружать страницу)
        function sortDepartments(criteria) {
            alert(`Сортировка по: ${criteria}. В реальном приложении здесь будет перезагрузка с сортировкой.`);
        }

        // Удаление отдела
        function deleteDepartment(departmentId) {
            if (confirm('Вы уверены, что хотите удалить отдел? Все данные (задачи, письма, файлы) будут удалены.')) {
                fetch(`/departments/${departmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Ошибка при удалении: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Произошла ошибка при удалении');
                    });
            }
        }

        // Редактирование отдела
        function editDepartment(departmentId) {
            // В реальном приложении здесь будет загрузка данных и открытие модального окна редактирования
            window.location.href = `/departments/${departmentId}/edit`;
        }

        // Закрытие модального окна при клике вне его
        document.getElementById('departmentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDepartmentModal();
            }
        });

        // Обработка отправки формы
        document.getElementById('departmentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Здесь можно добавить валидацию перед отправкой
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при создании отдела');
                });
        });

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Закрыть меню при клике вне
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.relative > button') && !e.target.closest('.relative > div:last-child')) {
                    document.querySelectorAll('.relative > div:last-child').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .department-card {
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Стили для статуса онлайн */
        .online-dot {
            width: 8px;
            height: 8px;
            background-color: #10B981;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }

        .offline-dot {
            width: 8px;
            height: 8px;
            background-color: #9CA3AF;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
    </style>
@endpush
