@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp
    <div>
        <!-- Заголовок и кнопки -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
            <nav class="hidden max-[500px]:block">
                            <ol class="flex items-center gap-1.5">
                                <li>
                                    <a class="inline-flex items-center gap-1.5 text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                       href="{{ route('welcome') }}">
                                        Главная
                                        <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                                                  stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                </li>
                                <li class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}" x-text="pageName">Мои отделы</li>
                            </ol>
                        </nav>
        <div class="max-[500px]:hidden">
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white max-[500px]:text-[26px]">Мои отделы</h2>
                    <p class="text-white text-sm max-[500px]:text-[13px]">Управляйте отделами, задачами и почтовой системой</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a] max-[500px]:text-[26px]">Мои отделы</h2>
                    <p class="text-gray-700 text-sm max-[500px]:text-[13px]">Управляйте отделами, задачами и почтовой системой</p>
                @endif
            </div>
            <div class="flex flex-wrap gap-3 w-full md:w-auto items-start">
                @if($backgroundEnabled && $backgroundImage)
                    <button id="filterToggle"
                            class="bg-transparent/20 border-none text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform duration-200"></i>
                    </button>
                @else
                    <button id="filterToggle"
                            class="bg-white border border-gray-300 text-gray-700 text-sm md:text-base px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-gray-50 transition">
                        <i class="fas fa-filter"></i>
                        <span>Фильтры</span>
                        <i id="filterIcon" class="fas fa-chevron-down ml-2 transition-transform duration-200"></i>
                    </button>
                @endif
                @if(auth()->user()->isLeader())
                        <button onclick="openDepartmentModal()"
                                class="flex-1 md:flex-none bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:from-green-700 hover:to-green-600 transition text-sm md:text-base">
                            <i class="fas fa-plus"></i>
                            <span>Новый отдел</span>
                        </button>
                @endif
            </div>
        </div>

        <!-- Фильтры и поиск - Скрытый блок -->
         @if($backgroundEnabled && $backgroundImage)
        <div id="filtersPanel" class="backdrop-blur-md bg-transparent/10 rounded-xl shadow mb-6 p-4 hidden">
            <div class="flex flex-col md:flex-row md:items-center">
                <div>
                    <div class="relative">
                        <input type="text"
                               placeholder="Поиск отделов..."
                               class="w-full min-w-[200px] pl-10 pr-4 py-2 border-none rounded-lg  outline-none bg-transparent/20 placeholder:text-white text-white"
                               id="departmentSearch">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="ml-4 flex space-x-4 max-[500px]:flex-col max-[500px]:space-y-3 max-[500px]:space-x-0 mt-4 md:mt-0 max-[500px]:w-full max-[765px]:ml-0">
                    <select class="border-none rounded-lg  px-4 py-2 bg-transparent/20 text-white"
                            id="filterStatus"
                            onchange="filterDepartments(this.value)">
                        <option class="text-gray-800" value="all">Все отделы</option>
                        <option class="text-gray-800" value="active">Активные</option>
                        <option class="text-gray-800" value="inactive">Неактивные</option>
                    </select>
                    <select class="border-none rounded-lg  px-4 py-2 outline-none bg-transparent/20 text-white"
                            id="sortBy"
                            onchange="sortDepartments(this.value)">
                        <option class="text-gray-800" value="name">Сортировать по названию</option>
                        <option class="text-gray-800" value="tasks">По количеству задач</option>
                        <option class="text-gray-800" value="emails">По новым письмам</option>
                    </select>
                </div>
            </div>
        </div>
        @else
        <div id="filtersPanel" class="bg-white rounded-xl shadow border border-gray-100 mb-6 p-4 hidden">
            <div class="flex flex-col md:flex-row md:items-center">
                <div>
                    <div class="relative">
                        <input type="text"
                               placeholder="Поиск отделов..."
                               class="w-full min-w-[200px] pl-10 pr-4 py-2 border-2 bg-white placeholder:text-gray-300 border-gray-300 rounded-lg px-4 focus:ring-1 focus:ring-green-500 outline-none"
                               id="departmentSearch">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-300"></i>
                    </div>
                </div>
                <div class="ml-4 flex space-x-4 max-[500px]:flex-col max-[500px]:space-y-3 max-[500px]:space-x-0 mt-4 md:mt-0 max-[500px]:w-full max-[765px]:ml-0">
                    <select class="rounded-lg  px-4 py-2 bg-white text-black border-2 border-gray-300 focus:ring-1 focus:ring-green-500 outline-none"
                            id="filterStatus"
                            onchange="filterDepartments(this.value)">
                        <option class="text-gray-800" value="all">Все отделы</option>
                        <option class="text-gray-800" value="active">Активные</option>
                        <option class="text-gray-800" value="inactive">Неактивные</option>
                    </select>
                    <select class="rounded-lg  px-4 py-2 bg-white text-black border-2 border-gray-300 focus:ring-1 focus:ring-green-500 outline-none"
                            id="sortBy"
                            onchange="sortDepartments(this.value)">
                        <option class="text-gray-800" value="name">Сортировать по названию</option>
                        <option class="text-gray-800" value="tasks">По количеству задач</option>
                        <option class="text-gray-800" value="emails">По новым письмам</option>
                    </select>
                </div>
            </div>
        </div>
        @endif
        <!-- Статистика по отделам -->
        @if($backgroundEnabled && $backgroundImage)
            <div class="flex gap-3 mb-4 overflow-x-auto snap-x snap-mandatory scrollbar-none sm:grid sm:grid-cols-1 md:grid-cols-2  xl:grid-cols-4 sm:gap-6 sm:mb-8">
                <div class="backdrop-blur-md bg-transparent/20 rounded-xl shadow p-6 border-none max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start">
                        <div>
                            <h2 class="text-lg font-bold text-white">Отделов</h2>
                            <p class="text-2xl font-bold text-white">{{ $departments->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-md bg-transparent/20 rounded-xl shadow p-6 border-none max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start">
                        <div>
                            <h2 class="text-lg font-bold text-white">Задач</h2>
                            <p class="text-2xl font-bold text-white">{{ $totalActiveTasks }}</p>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-md bg-transparent/20 rounded-xl shadow p-6 border-none max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start">
                        <div>
                            <h2 class="text-lg font-bold text-white">Писем</h2>
                            <p class="text-2xl font-bold text-white">{{ $totalUnreadEmails }}</p>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-md bg-transparent/20 rounded-xl shadow p-6 border-none max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start">
                        <div>
                            <h2 class="text-lg font-bold text-white">Сотрудников</h2>
                            <p class="text-2xl font-bold text-white">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="flex gap-3 mb-4 overflow-x-auto snap-x snap-mandatory scrollbar-none sm:grid sm:grid-cols-1 md:grid-cols-2  xl:grid-cols-4 sm:gap-6 sm:mb-8">
                <div class="bg-white rounded-xl shadow p-6 border border-gray-100 max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start max-[500px]:h-full">
                        <div class="max-[500px]:h-full max-[500px]:flex max-[500px]:flex-col max-[500px]:justify-between">
                            <p class="text-gray-500 text-sm">Всего отделов</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $departments->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center max-[500px]:w-5 max-[500px]:h-5 max-[500px]:bg-transparent">
                            <i class="fas fa-building text-blue-500 text-xl max-[500px]:text-[17px]/[18px]"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6 border border-gray-100 max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start max-[500px]:h-full">
                        <div class="max-[500px]:h-full max-[500px]:flex max-[500px]:flex-col max-[500px]:justify-between">
                            <p class="text-gray-500 text-sm">Активных задач</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $totalActiveTasks }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center max-[500px]:w-5 max-[500px]:h-5  max-[500px]:bg-transparent">
                            <i class="fas fa-tasks text-green-500 text-xl max-[500px]:text-[17px]/[18px]"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6 border border-gray-100 max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start max-[500px]:h-full">
                        <div class="max-[500px]:h-full max-[500px]:flex max-[500px]:flex-col max-[500px]:justify-between">
                            <p class="text-gray-500 text-sm">Непрочитанных писем</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $totalUnreadEmails }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center max-[500px]:w-5 max-[500px]:h-5  max-[500px]:bg-transparent">
                            <i class="fas fa-envelope text-purple-500 text-xl max-[500px]:text-[17px]/[18px]"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6 border border-gray-100 max-[500px]:w-[calc(45%-6px)] max-[500px]:h-[100px] shrink-0 snap-start max-[500px]:p-3">
                    <div class="flex items-center justify-between max-[500px]:items-start max-[500px]:h-full">
                        <div class="max-[500px]:h-full max-[500px]:flex max-[500px]:flex-col max-[500px]:justify-between">
                            <p class="text-gray-500 text-sm">Всего сотрудников</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center max-[500px]:w-5 max-[500px]:h-5  max-[500px]:bg-transparent">
                            <i class="fas fa-users text-orange-500 text-xl max-[500px]:text-[17px]/[18px]"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Список отделов -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="departments-container">
            @foreach($departments as $department)
                @include('frontend.department.partials.card', ['department' => $department])
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
    @include('partials.modal.department.create')

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
                        <!-- <button type="submit"
                                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            Создать
                        </button> -->
                        <!-- V -->
                        <button type="button" onclick="submitDepartment(this)"
                                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            Создать
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
                fetch(`/departments/${departmentId}/delete`, {
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
        // document.getElementById('departmentForm').addEventListener('submit', function(e) {
        //     e.preventDefault();

        //     // Здесь можно добавить валидацию перед отправкой
        //     const formData = new FormData(this);

        //     fetch(this.action, {
        //         method: 'POST',
        //         body: formData,
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
        //         },
        //     })
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.success) {
        //                 location.reload();
        //             } else {
        //                 alert('Ошибка: ' + data.message);
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //             alert('Произошла ошибка при создании отдела');
        //         });
        // });

        // v create dep
        function submitDepartment(button) {
            const form = document.getElementById('departmentForm');
            const btn = event.target; // Кнопка, на которую нажали

            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Создание...';

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Находим контейнер, где лежат все карточки
                     const container = document.getElementById('departments-container');
                    if (container) {
                        container.insertAdjacentHTML('afterbegin', data.html);
                    }
                    // Вставляем полученный от сервера HTML в начало списка

                    closeDepartmentModal();
                    form.reset();
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при отправке данных');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = 'Создать';
            });
        }

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

        // Переключение панели фильтров
        // document.addEventListener('DOMContentLoaded', function() {
        //     const filterToggle = document.getElementById('filterToggle');
        //     const filtersPanel = document.getElementById('filtersPanel');
        //     const filterIcon = document.getElementById('filterIcon');

        //     if (filterToggle && filtersPanel) {
        //         filterToggle.addEventListener('click', function() {
        //             const isHidden = filtersPanel.classList.contains('hidden');

        //             if (isHidden) {
        //                 filtersPanel.classList.remove('hidden');
        //                 filterIcon.style.transform = 'rotate(180deg)';
        //                 // Анимация появления
        //                 filtersPanel.style.animation = 'slideDown 0.3s ease';
        //             } else {
        //                 filtersPanel.classList.add('hidden');
        //                 filterIcon.style.transform = 'rotate(0deg)';
        //             }
        //         });
        //     }
        // });
        document.addEventListener('DOMContentLoaded', function() {
                const filterToggle = document.getElementById('filterToggle');
                const dropdown = document.getElementById('filtersPanel');
                const chevron = document.getElementById('filterIcon');

                if (!dropdown || !chevron||!filterToggle) return;


                filterToggle.addEventListener('click', ()=> {
                if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    dropdown.classList.remove('fade-out-x');
                    dropdown.classList.add('fade-in-x');
                    chevron.style.transform = 'rotate(180deg)';
                    console.log('if')
                } else {
                    console.log('else')
                    dropdown.classList.remove('fade-in-x');
                    dropdown.classList.add('fade-out-x');
                    chevron.style.transform = 'rotate(0deg)';

                    setTimeout(() => {
                        if (dropdown.classList.contains('fade-out-x')) {
                            dropdown.classList.add('hidden');
                        }
                    }, 200);
                }
                })
        })
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

        /* Анимация для панели фильтров */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #filtersPanel {
            animation: slideDown 0.3s ease;
        }

        /* Стили для кнопки фильтрации */
        #filterToggle {
            transition: all 0.2s ease;
        }

        #filterToggle:active {
            transform: scale(0.98);
        }

        /* Стили для иконки */
        #filterIcon {
            transition: transform 0.2s ease;
        }
    </style>
@endpush
