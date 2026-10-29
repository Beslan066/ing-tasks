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
                    <h2 class="text-3xl font-bold max-[500px]:text-[26px]" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Мои отделы</h2>
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
                                class="flex-1 md:flex-none text-white px-3 py-2 md:px-4 md:py-2 rounded-lg flex items-center justify-center space-x-2 hover:from-green-700 hover:to-green-600 transition text-sm md:text-base" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-text-fill-color: white;">
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
                    @if($backgroundEnabled && $backgroundImage)
                        <div class="backdrop-blur-md bg-transparent/20 rounded-xl shadow p-12 text-center">
                            <h3 class="text-xl font-semibold text-white mb-3">Нет отделов</h3>
                            <p class="text-white mb-6">Создайте свой первый отдел для управления задачами и почтой</p>
                            <button onclick="openDepartmentModal()"
                                    class="text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center space-x-2 " style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-text-fill-color: white;">
                                <i class="fas fa-plus"></i>
                                <span>Создать отдел</span>
                            </button>
                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow p-12 text-center">
                            <h3 class="text-xl font-semibold text-gray-700 mb-3">Нет отделов</h3>
                            <p class="text-gray-500 mb-6">Создайте свой первый отдел для управления задачами и почтой</p>
                            <button onclick="openDepartmentModal()"
                                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-secondary transition-colors inline-flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Создать отдел</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
    <!-- Модальное окно создания отдела -->
    @include('partials.modal.department.create')
 <!-- Модальное окно удаления отдела -->
    @include('partials.modal.department.delete')
@endsection

@push('scripts')
    <script>

function openDepartmentModal() {
    const modal = document.getElementById('departmentModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeDepartmentModal() {
    const modal = document.getElementById('departmentModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');

    const form = document.getElementById('departmentForm');
    if (form) form.reset();
}

document.addEventListener('click', (e) => {
    const insideMenuContainer = e.target.closest('.card-menu-container');
    if (!insideMenuContainer) {
        closeAllDepartmentMenus();
    }
});

function toggleDepartmentMenu(button, departmentId) {
    const currentContainer = button.closest('.card-menu-container');
    if (!currentContainer) return;

    const menu = button.nextElementSibling;
    if (!menu) return;

    const isCurrentlyHidden = menu.classList.contains('hidden');
    closeAllDepartmentMenus(menu);
    menu.classList.toggle('hidden', !isCurrentlyHidden);
}

function closeAllDepartmentMenus(exceptMenu = null) {
   document.querySelectorAll('.card-menu-container > .js-dept-menu').forEach(menu => {
        if (menu !== exceptMenu) {
            menu.classList.add('hidden');
        }
    });
}


let searchTimeout;
const deptSearchInput = document.getElementById('departmentSearch');
if (deptSearchInput) {
    deptSearchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.department-card');

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                if (name.toLowerCase().includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }, 300);
    });
}


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


function sortDepartments(criteria) {
    alert(`Сортировка по: ${criteria}. В реальном приложении здесь будет перезагрузка с сортировкой.`);
}


function editDepartment(departmentId) {
    window.location.href = `/departments/${departmentId}/edit`;
}


function submitDepartment(button) {
    const form = document.getElementById('departmentForm');
    if (!form) return;

    button.disabled = true;
    const originalText = button.innerHTML;
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
            const container = document.getElementById('departments-container');
            if (container && data.html) {
                container.insertAdjacentHTML('afterbegin', data.html);
            }
            closeDepartmentModal();
            form.reset();
            location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при отправке данных', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('departmentModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDepartmentModal();
            }
        });
    }

    const filterToggle = document.getElementById('filterToggle');
    const dropdown = document.getElementById('filtersPanel');
    const chevron = document.getElementById('filterIcon');

    if (dropdown && chevron && filterToggle) {
        filterToggle.addEventListener('click', () => {
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden', 'fade-out-x');
                dropdown.classList.add('fade-in-x');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                dropdown.classList.remove('fade-in-x');
                dropdown.classList.add('fade-out-x');
                chevron.style.transform = 'rotate(0deg)';

                setTimeout(() => {
                    if (dropdown.classList.contains('fade-out-x')) {
                        dropdown.classList.add('hidden');
                    }
                }, 200);
            }
        });
    }
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
