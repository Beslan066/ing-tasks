<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>МенеджерПлюс - Современная система управления задачами</title>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta http-equiv="Content-Security-Policy" content="

default-src 'self';

script-src 'self' 'unsafe-inline' 'unsafe-eval' https://meet.jit.si https://cdnjs.cloudflare.com https://cdn.tailwindcss.com;

style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com;

style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com;

font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com;

img-src 'self' data: https:;

connect-src 'self' https://meet.jit.si wss://meet.jit.si;

frame-src https://meet.jit.si;

media-src https://meet.jit.si https:;

">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d'
                        },
                        brown: {
                            "50": "#fdf7f2",
                            "100": "#f9eee5",
                            "200": "#f2d9c5",
                            "300": "#e7bd9a",
                            "400": "#da9c6a",
                            "500": "#c47c45",
                            "600": "#a66238",
                            "700": "#854e2e",
                            "800": "#6a3e25",
                            "900": "#3d2416",
                        },
                        sidebar: {
                            bg: '#1a1f2e',
                            text: '#94a3b8',
                            active: '#ffffff',
                            hover: '#2d3447'
                        }
                    },
                    animation: {
                        'slide-in': 'slideIn 0.3s ease-out',
                        'pulse-glow': 'pulseGlow 2s infinite'
                    },
                    keyframes: {
                        slideIn: {
                            '0%': {transform: 'translateX(-20px)', opacity: '0'},
                            '100%': {transform: 'translateX(0)', opacity: '1'}
                        },
                        pulseGlow: {
                            '0%, 100%': {opacity: '1'},
                            '50%': {opacity: '0.7'}
                        }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: "Inter", sans-serif;
        }


        /* Стиль main-container по умолчанию */
        .main-container {
            background-color: #f9fafb;
        }

        .active-page {
            transition: padding 0.4s ease;
        }

        /* Стиль main-container с фоном */
        .main-container.has-background {
            background-size: cover;
            background-position: center;

            .chat-button {
                color: #fff;
            }

            .setting-button {
                color: #fff;
            }

            .avatar-container {
                position: relative;
                transition: transform 0.2s ease;
            }

            .avatar-container:hover {
                transform: translateY(-2px);
            }

            .online-indicator {
                position: absolute;
                bottom: 0;
                right: 0;
                width: 10px;
                height: 10px;
                background: linear-gradient(135deg, #22c55e, #16a34a);
                border-radius: 50%;
                animation: pulseGlow 2s infinite;
            }

            .progress-bar {
                background: linear-gradient(90deg, #22c55e, #16a34a);
                border-radius: 9999px;
                position: relative;
                overflow: hidden;
            }

            .progress-bar::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                animation: shimmer 2s infinite;
            }

            @keyframes shimmer {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }

            .badge {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: white;
                font-size: 0.7rem;
                padding: 2px 8px;
                border-radius: 9999px;
                font-weight: 600;
            }

            .category-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                margin-right: 10px;
                transition: all 0.3s ease;
            }

            .category-item:hover .category-dot {
                transform: scale(1.3);
            }

            .scrollbar-thin {
                scrollbar-width: thin;
                scrollbar-color: #4b5563 transparent;
            }

            .scrollbar-thin::-webkit-scrollbar {
                width: 4px;
            }

            .scrollbar-thin::-webkit-scrollbar-track {
                background: transparent;
            }

            .scrollbar-thin::-webkit-scrollbar-thumb {
                background: #4b5563;
                border-radius: 2px;
            }

            .dropdown-enter {
                animation: dropdownEnter 0.2s ease-out;
            }

            .custom-scrollbar {
                scrollbar-width: thin;
                scrollbar-color: transparent transparent;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: transparent;
                border-radius: 10px;
            }

            .custom-scrollbar:hover::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.2);
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.3);
            }

            @keyframes dropdownEnter {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            input:-webkit-autofill,
            input:-webkit-autofill:hover,
            input:-webkit-autofill:focus {
                -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset;
                -webkit-text-fill-color: #111827;
                transition: background-color 5000s ease-in-out 0s;
            }

            .dark input:-webkit-autofill,
            .dark input:-webkit-autofill:hover,
            .dark input:-webkit-autofill:focus {
                -webkit-box-shadow: 0 0 0px 1000px #374151 inset;
                -webkit-text-fill-color: white;
            }


            /* Стили для выпадающего меню почты */

            .email-nav-container .absolute {
                display: none;
                opacity: 0;
                transform: translateY(-10px);
                transition: opacity 0.2s ease, transform 0.2s ease;
            }

            .email-nav-container:hover .absolute {
                display: block;
                opacity: 1;
                transform: translateY(0);
            }

            /* Альтернативный вариант с visibility */

            .email-nav-container .email-dropdown {
                visibility: hidden;
                opacity: 0;
                transform: translateY(-10px);
                transition: all 0.2s ease;
                pointer-events: none;
            }

            .email-nav-container:hover .email-dropdown {
                visibility: visible;
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            /* Для мобильных устройств */
            @media (max-width: 768px) {
                .email-nav-container .absolute {
                    position: static;
                    display: none;
                    width: 100%;
                    box-shadow: none;
                    border: none;
                    margin-top: 0.5rem;
                }

                .email-nav-container.active .absolute {
                    display: block;
                }
            }

            .burger-btn span {
                background-color: #fff;
            }
        }

        .burger-btn {
            display: none;
            width: 30px;
            height: 22px; /* Высота уменьшена для ровного баланса трех линий */
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            z-index: 1000;
            background: transparent;
            border: none;
            padding: 0;
        }

        /* Стили для всех трех линий */
        .burger-btn span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: #000;
            transition: all 0.3s ease;
            border-radius: 20px;
            transform-origin: center;
        }

        /* Отображение на мобильных экранах */
        @media (max-width: 638px) {
            .burger-btn {
                display: flex;
            }
        }


        .burger-btn.active span:nth-child(1) {
            transform: translateY(9.5px) rotate(45deg);
        }

        .burger-btn.active span:nth-child(2) {
            opacity: 0;
            transform: scale(0);
        }

        .burger-btn.active span:nth-child(3) {
            transform: translateY(-9.5px) rotate(-45deg);
        }

        @media (max-width: 500px) {
            .sidebar {
                box-shadow: none;
            }

            .main-container:not(:has(.has-background)) {

            }

            .main-container:not(:has(.has-background)):has(.sidebar.active) .burger-btn span {
                color: #ffffff;
                background-color: #ffffff;
            }
        }

        /* Стили для подменю */
        #extraSubmenu {
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.3s ease;
            overflow-y: hidden;
        }

        /* Скроллбар для подменю если много пунктов */
        #extraSubmenu::-webkit-scrollbar {
            width: 3px;
        }

        #extraSubmenu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        #extraSubmenu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        /* Анимация для иконки */
        #extraMenuIcon {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Анимации для быстрого добавления */
        #quickAddFormInner {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: top center;
        }

        #showQuickAddBtn {
            transition: all 0.3s ease;
        }

        #showQuickAddBtn:hover {
            transform: translateY(-2px);
        }

        /* Стили для полей ввода */
        #quickTaskName:focus,
        #quickTaskPriority:focus,
        #quickTaskDeadline:focus,
        #quickTaskDescription:focus {
            background-color: white;
        }

        /* Кастомный скролл для текстареа */
        #quickTaskDescription::-webkit-scrollbar {
            width: 4px;
        }

        #quickTaskDescription::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #quickTaskDescription::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        /* Стили для datetime-local */
        input[type="datetime-local"] {
            color-scheme: light;
        }

        /* Плавное появление формы */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <!-- @stack('sidebar-styles') -->
</head>

<body class="bg-gray-50 font-sans">

@php
    $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
    $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
@endphp

<div class="flex min-h-screen main-container {{ $backgroundEnabled && $backgroundImage ? 'has-background' : '' }}"
     @if($backgroundEnabled && $backgroundImage)
         style="background-image: url('{{ $backgroundImage }}')"
    @endif>
    @include('partials.right-bar.right-bar')
    <!-- Боковая панель -->
    @include('partials.sidebar.sidebar')
    <!-- Основной контент -->
    <div
        class="flex-1 w-[calc(100%-16rem)] min-h-[calc(100vh-80px)] max-[638px]:w-full max-[500px]:pb-[20px] {{ $backgroundEnabled && $backgroundImage ? '' : 'bg-gray-100' }}">

        @include('partials.top-menu.top-menu')
        <div class="justify-between items-center relative pr-4 pl-4 pb-6 hidden max-[638px]:flex">

            <!-- Логотип -->
            <div>
                <a href="{{route('welcome')}}" class="flex items-center space-x-3 group">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg group-hover:shadow-primary-500/20 transition-all duration-300">
                        <i class="fas fa-tasks text-white text-lg"></i>
                    </div>
                    <div>
                        @if($backgroundEnabled && $backgroundImage)
                            <h1 class="text-xl font-bold text-white">Менеджер<span class="text-primary-500">Плюс</span>
                            </h1>
                            <p class="text-xs text-sidebar-text mt-1">Управление задачами</p>
                        @else
                            <h1 class="text-xl font-bold text-gray-700">Менеджер<span
                                    class="text-primary-500">Плюс</span></h1>
                            <p class="text-xs text-sidebar-text mt-1">Управление задачами</p>
                        @endif
                    </div>
                </a>
            </div>
            <!-- Бургер меню -->
            <button id="burger-btn" class="burger-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div id="home" class="page active-page p-6 pl-[calc(256px+1.5rem)] max-[638px]:pl-6 max-[550px]:p-3">
            @yield('content')
        </div>

        <div class="setting-button" style="position: fixed; bottom: 10px; right: 60px;" title="Выберите фон">
            <button onclick="openBackgroundSelector()"
                    class="w-full text-left px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-image mr-3"></i>
            </button>
        </div>

        <div class="chat-button" style="position: fixed; bottom: 10px; right: 20px;">
            <a href="{{route('chat.index')}}">
                <button class="bg-primary  p-2 rounded-full hover:bg-secondary transition-colors">
                    <i class="fas fa-comment-dots"></i>
                </button>
            </a>
        </div>

    </div>
</div>

<!-- Модальное окно для новой задачи -->
@include('partials.modal.task.create')

<!-- Модальное окно уведомлений -->
@include('partials.modal.notifications-modal')

<!-- Модальное окно профиля пользователя -->
@auth()
    @include('partials.modal.user-profile-modal')
@endauth


<!-- Модальное окно для новой категории -->
@include('partials.modal.category.create-category')

<!-- Модальное окно редактирования категории -->
@include('partials.modal.category.edit')

<!-- Модальное окно для нового отдела -->
@include('partials.modal.department.create')

<!-- Модальное окно редактирования отдела -->
@include('partials.modal.department.edit')

<!-- Модальное окно для нового пользователя -->
@include('partials.modal.user.create')

<!-- Модальное окно подтверждения удаления категории -->
@include('partials.modal.category.delete')

@include('partials.modal.background-selector')
<script>
    let workStartTime = null;
    let workTotalSeconds = 0;
    let workIsActive = true;
    let workLastActivity = Date.now();
    let sendInterval = null;
    let activityInterval = null;


    // Подсветка активного пункта в подменю
    document.addEventListener('DOMContentLoaded', function () {
        // Проверяем, открыт ли какой-то из подпунктов
        const currentRoute = window.location.pathname;
        const extraLinks = ['news', 'support', 'license', 'payment'];

        if (extraLinks.some(link => currentRoute.includes(link))) {
            // Автоматически открываем подменю если активен подпункт
            const submenu = document.getElementById('extraSubmenu');
            const icon = document.getElementById('extraMenuIcon');

            if (submenu && icon && submenu.style.maxHeight === '0px') {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                submenu.style.opacity = '1';
                icon.style.transform = 'rotate(180deg)';
            }
        }
    });

    function sendWorkTimeToServer(seconds, isFinal = false) {
        if (seconds <= 0) return;

        fetch('/track-work-time', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({work_seconds: seconds})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && !isFinal) {
                    workTotalSeconds = 0;
                }
            })
            .catch(error => console.error('Ошибка отправки времени:', error));
    }

    function startWorkTimer() {
        if (workStartTime === null) {
            workStartTime = Date.now();
            console.log('🚀 Трекинг времени начат');
        }
    }

    function stopWorkTimer() {
        if (workStartTime !== null && workIsActive) {
            const elapsed = Math.floor((Date.now() - workStartTime) / 1000);
            workTotalSeconds += elapsed;
            workStartTime = null;
            console.log(`⏸ Пауза. Добавлено: ${elapsed} сек.`);
        }
        workIsActive = false;
    }

    function resumeWorkTimer() {
        if (!workIsActive) {
            workStartTime = Date.now();
            workIsActive = true;
            console.log('▶ Возобновлен трекинг');
        }
    }

    function resetWorkActivity() {
        workLastActivity = Date.now();
        if (!workIsActive) {
            resumeWorkTimer();
        }
    }

    function sendAccumulatedTime() {
        let toSend = workTotalSeconds;

        if (workIsActive && workStartTime !== null) {
            toSend += Math.floor((Date.now() - workStartTime) / 1000);
            workStartTime = Date.now();
        }

        if (toSend > 0) {
            console.log(`📤 Отправка времени на сервер: ${toSend} сек.`);
            sendWorkTimeToServer(toSend);
        }
    }

    function sendOnUnload() {
        let toSend = workTotalSeconds;
        if (workIsActive && workStartTime !== null) {
            toSend += Math.floor((Date.now() - workStartTime) / 1000);
        }
        if (toSend > 0) {
            const data = new FormData();
            data.append('work_seconds', toSend);
            data.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
            navigator.sendBeacon('/track-work-time', data);
        }
    }

    function initWorkTracking() {
        startWorkTimer();

        const events = ['mousemove', 'mousedown', 'keypress', 'scroll', 'click', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, resetWorkActivity);
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('📱 Вкладка скрыта - пауза');
                stopWorkTimer();
            } else {
                console.log('📱 Вкладка видима - возобновление');
                resetWorkActivity();
            }
        });

        // Отправка каждые 30 секунд
        sendInterval = setInterval(sendAccumulatedTime, 30000);

        // Проверка активности каждые 10 секунд
        activityInterval = setInterval(() => {
            const now = Date.now();
            if (now - workLastActivity > 30000 && workIsActive) {
                stopWorkTimer();
            } else if (now - workLastActivity <= 30000 && !workIsActive) {
                resumeWorkTimer();
            }
        }, 10000);

        window.addEventListener('beforeunload', sendOnUnload);
        window.addEventListener('pagehide', sendOnUnload);
    }

    // Запуск
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWorkTracking);
    } else {
        initWorkTracking();
    }

    // Для отладки
    window.getMyWorkTime = () => {
        let total = workTotalSeconds;
        if (workIsActive && workStartTime !== null) {
            total += Math.floor((Date.now() - workStartTime) / 1000);
        }
        const minutes = Math.floor(total / 60);
        const seconds = total % 60;
        console.log(`📊 Рабочее время: ${minutes} мин. ${seconds} сек. (${total} сек.)`);
        return total;
    };

    console.log('🎯 Трекинг времени загружен');
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainContainer = document.querySelector('.main-container');
        const sidebar = document.querySelector('.sidebar');
        const resetInput = document.getElementById('reset-background-input');

        function applyBackground(enabled, imagePath) {
            if (enabled && imagePath) {
                mainContainer.classList.add('has-background');
                mainContainer.style.backgroundImage = `url(${imagePath})`;
                sidebar.classList.add('glass');
                resetInput.checked = false;
            } else {
                mainContainer.classList.remove('has-background');
                mainContainer.style.backgroundImage = '';
                sidebar.classList.remove('no-background');
                sidebar.classList.remove('glass');
                resetInput.checked = true;
            }
        }

        // Применяем сохраненные настройки
        @if($backgroundEnabled && $backgroundImage)
        applyBackground(true, '{{ $backgroundImage }}');
        @else
        applyBackground(false, null);
        @endif

            window.updateBackground = function (imagePath, enabled) {
            fetch('{{ route("user.updateBackground") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    background_image: imagePath,
                    background_enabled: enabled
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем страницу после успешного сохранения
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        };
    });
</script>
<script>
    // Глобальные переменные
    let currentModalType = '';
    let currentEditingTaskId = null;
    let selectedFiles = [];
    let allFiles = [];
    let currentPreviewFile = null;
    let draggedTaskCard = null;

    window.selectedFiles = [];
    window.allFiles = [];

    (function () {
        // Функция для проверки - открыта ли модалка личной задачи
        function isPersonalTaskModal() {
            const modal = document.getElementById('taskModal');
            const h3 = modal ? modal.querySelector('h3') : null;
            return modal && !modal.classList.contains('hidden') && h3 && h3.textContent === 'Новая личная задача';
        }

        // Сохраняем ссылку на оригинальный обработчик
        let originalSubmitHandler = null;

        // Ждем загрузки DOM
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('taskForm');
            if (!form) return;

            // Получаем все обработчики submit (если есть)
            const oldSubmit = form.submit;

            // Переопределяем submit
            form.submit = function () {
                if (isPersonalTaskModal()) {
                    // Для личных задач - отправляем через AJAX
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn?.innerHTML;

                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Создание...';
                        submitBtn.disabled = true;
                    }

                    fetch('/tasks/personal/store', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // alert('Личная задача успешно создана!');
                                showNotification('Личная задача успешно создана', 'success');
                                closeTaskModal();
                                setTimeout(() => {
                                    location.reload();
                                }, 600);
                            } else {
                                // alert(data.message || 'Ошибка при создании задачи');
                                showNotification(data.message || 'Ошибка при создании задачи', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            // alert('Ошибка при создании задачи');
                            showNotification('Ошибка при создании задачи', 'error');
                        })
                        .finally(() => {
                            if (submitBtn) {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                        });
                    return false;
                }
                // Для обычных задач - вызываем оригинальный submit
                return oldSubmit ? oldSubmit.call(this) : HTMLFormElement.prototype.submit.call(this);
            };

            // Добавляем свой обработчик на submit
            form.addEventListener('submit', function (e) {
                if (isPersonalTaskModal()) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    // Вызываем наш переопределенный submit
                    form.submit();
                    return false;
                }
            }, true); // true - чтобы выполнился первым
        });
    })();

    // Добавим интерактивности для сайдбара
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.email-dropdown-item').forEach(item => {
            item.addEventListener('click', function (e) {
                const url = this.getAttribute('href');
                const badge = this.querySelector('.unread-badge');
                if (badge && badge.textContent > 0) {
                    updateUnreadCount();
                }
                window.location.href = url;
            });
        });

        const emailButton = document.querySelector('.email-nav-button');
        if (emailButton) {
            emailButton.addEventListener('click', function (e) {
                if (window.innerWidth < 768) {
                    e.preventDefault();
                    const dropdown = this.querySelector('.email-dropdown');
                    dropdown.classList.toggle('hidden');
                }
            });
        }


        // const currentPath = window.location.pathname;
        // navItems.forEach(item => {
        //     if (item.getAttribute('href') === currentPath) {
        //         item.classList.add('active');
        //     }
        // });

        // Инициализация drag and drop после загрузки
        initTaskDragAndDrop();
        updateTaskCounters();
    });

    setTimeout(() => {
        const elements = document.querySelectorAll('.sidebar > *');
        elements.forEach((el, index) => {
            el.style.animation = `slide-in 0.3s ease-out ${index * 0.1}s both`;
        });
    }, 100);

    // ==================== ФУНКЦИИ ДЛЯ МОДАЛЬНЫХ ОКОН ====================

    function openTaskModal() {
        currentModalType = 'task';
        const modal = document.getElementById('taskModal');
        if (modal) modal.classList.remove('hidden');
        resetTaskForm();
    }

    async function openEditTaskModal(taskId) {
        currentModalType = 'task';
        currentEditingTaskId = taskId;

        try {
            const response = await fetch(`/tasks/${taskId}/get`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                const task = data.task;
                let taskIdField = document.getElementById('task_id');
                if (!taskIdField) {
                    taskIdField = document.createElement('input');
                    taskIdField.type = 'hidden';
                    taskIdField.name = 'task_id';
                    taskIdField.id = 'task_id';
                    const form = document.getElementById('taskForm');
                    if (form) form.appendChild(taskIdField);
                }
                taskIdField.value = task.id;

                const nameInput = document.querySelector('input[name="name"]');
                if (nameInput) nameInput.value = task.name || '';

                const descTextarea = document.querySelector('textarea[name="description"]');
                if (descTextarea) descTextarea.value = task.description || '';

                const prioritySelect = document.querySelector('select[name="priority"]');
                if (prioritySelect) prioritySelect.value = task.priority || 'средний';

                const deptSelect = document.querySelector('select[name="department_id"]');
                if (deptSelect) deptSelect.value = task.department_id || '';

                const catSelect = document.querySelector('select[name="category_id"]');
                if (catSelect) catSelect.value = task.category_id || '';

                const userSelect = document.querySelector('select[name="user_id"]');
                if (userSelect) userSelect.value = task.user_id || '';

                const hoursInput = document.querySelector('input[name="estimated_hours"]');
                if (hoursInput) hoursInput.value = task.estimated_hours || '';

                if (task.deadline) {
                    const deadlineDate = new Date(task.deadline);
                    const formattedDate = deadlineDate.toISOString().slice(0, 16);
                    const deadlineInput = document.querySelector('input[name="deadline"]');
                    if (deadlineInput) deadlineInput.value = formattedDate;
                } else {
                    const deadlineInput = document.querySelector('input[name="deadline"]');
                    if (deadlineInput) deadlineInput.value = '';
                }

                const statusSelect = document.querySelector('select[name="status"]');
                if (statusSelect) statusSelect.value = task.status || 'назначена';

                const modalTitle = document.querySelector('#taskModal h3');
                if (modalTitle) modalTitle.textContent = 'Редактировать задачу';

                const modalDesc = document.querySelector('#taskModal p');
                if (modalDesc) modalDesc.textContent = 'Редактирование информации о задаче';

                const submitBtn = document.querySelector('#taskModal button[type="submit"]');
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Сохранить изменения';

                const modal = document.getElementById('taskModal');
                if (modal) modal.classList.remove('hidden');
            } else {
                showNotification(data.message || 'Ошибка при загрузке задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка при загрузке задачи:', error);
            showNotification('Ошибка при загрузке задачи: ' + error.message, 'error');
        }
    }

    function resetTaskForm() {
        const form = document.getElementById('taskForm');
        if (form) form.reset();

        selectedFiles = [];
        updateSelectedFilesDisplay();

        const uploadInput = document.getElementById('uploadNewFilesInput');
        if (uploadInput) uploadInput.value = '';

        const uploadContainer = document.getElementById('uploadFilesContainer');
        if (uploadContainer) uploadContainer.innerHTML = '';

        const uploadList = document.getElementById('uploadFilesList');
        if (uploadList) uploadList.classList.add('hidden');

        const modalTitle = document.querySelector('#taskModal h3');
        if (modalTitle) modalTitle.textContent = 'Новая задача';

        const modalDesc = document.querySelector('#taskModal p');
        if (modalDesc) modalDesc.textContent = 'Заполните информацию о задаче';

        const submitBtn = document.querySelector('#taskModal button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-plus mr-2"></i>Создать задачу';

        const taskIdField = document.getElementById('task_id');
        if (taskIdField) taskIdField.remove();

        currentEditingTaskId = null;
    }

    function closeTaskModal() {
        const modal = document.getElementById('taskModal');
        if (modal) modal.classList.add('hidden');
        resetTaskForm();
    }


    function createUserModal() {
        currentModalType = 'user';
        const modal = document.getElementById('newUserModal');
        if (modal) modal.classList.remove('hidden');
    }

    function openCategoryModal() {
        currentModalType = 'category';
        const modal = document.getElementById('categoryModal');
        if (modal) modal.classList.remove('hidden');
    }

    function openDepartmentModal() {
        currentModalType = 'department';
        const modal = document.getElementById('departmentModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeUserModal() {
        const modal = document.getElementById('newUserModal');
        if (modal) modal.classList.add('hidden');
        const form = document.getElementById('userForm');
        if (form) form.reset();
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        if (modal) modal.classList.add('hidden');
        const form = document.getElementById('categoryForm');
        if (form) form.reset();
    }

    function closeDepartmentModal() {
        const modal = document.getElementById('departmentModal');
        if (modal) modal.classList.add('hidden');
        const form = document.getElementById('departmentForm');
        if (form) form.reset();
    }

    // ==================== УПРАВЛЕНИЕ ФАЙЛАМИ ====================

    function switchFileTab(tabName) {
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.tab === tabName) {
                btn.classList.add('active');
            }
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
            content.classList.add('hidden');
        });
        const activeContent = document.getElementById(tabName + 'TabContent');
        if (activeContent) {
            activeContent.classList.remove('hidden');
            activeContent.classList.add('active');
        }
    }

    async function openFileManager() {
        const modal = document.getElementById('fileManagerModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            await loadFiles();
        }
    }

    function closeFileManager() {
        const modal = document.getElementById('fileManagerModal');
        if (modal) modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    async function loadFiles() {
        const contentDiv = document.getElementById('fileManagerContent');
        if (!contentDiv) return;

        contentDiv.innerHTML = `<div class="col-span-full text-center py-12"><i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i><p class="text-gray-600">Загрузка файлов...</p></div>`;
        try {
            const response = await fetch('/tasks/file-storage/get-files', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            if (!response.ok) throw new Error('Ошибка загрузки файлов');
            const files = await response.json();
            allFiles = files;
            renderFiles(files);
            updateSelectedCount();
        } catch (error) {
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-red-600"><i class="fas fa-exclamation-triangle text-3xl mb-4"></i><p class="text-lg font-medium">Ошибка загрузки файлов</p><p class="text-sm mt-2">${error.message}</p><button onclick="loadFiles()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-redo mr-2"></i>Повторить</button></div>`;
        }
    }

    function renderFiles(files) {
        const contentDiv = document.getElementById('fileManagerContent');
        if (!contentDiv) return;

        if (files.length === 0) {
            contentDiv.innerHTML = `<div class="col-span-full text-center py-12 text-gray-600"><i class="fas fa-folder-open text-3xl mb-4"></i><p class="text-lg font-medium">Файлы не найдены</p><p class="text-sm mt-2">В хранилище нет доступных файлов</p></div>`;
            return;
        }
        let html = '<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">';
        files.forEach(file => {
            const isSelected = selectedFiles.some(f => f.id === file.id);
            const fileIcon = getFileIcon(file.extension);
            const fileType = getFileTypeClass(file.extension);
            html += `<div class="file-card bg-white border ${isSelected ? 'border-blue-500 shadow-md' : 'border-gray-200'} rounded-lg p-4 cursor-pointer transition-all hover:shadow-lg" onclick="toggleFileSelection(${file.id})"><div class="flex flex-col h-full"><div class="flex justify-end mb-2"><div class="w-5 h-5 rounded border ${isSelected ? 'bg-blue-500 border-blue-500' : 'border-gray-300'} flex items-center justify-center" onclick="event.stopPropagation(); toggleFileSelection(${file.id})">${isSelected ? '<i class="fas fa-check text-white text-xs"></i>' : ''}</div></div><div class="flex justify-center mb-3"><div class="w-16 h-16 ${fileType.bg} rounded-lg flex items-center justify-center"><span class="text-2xl">${fileIcon}</span></div></div><div class="flex-1"><p class="text-sm font-medium text-gray-800 truncate text-center mb-1" title="${file.name}">${file.name}</p><p class="text-xs text-gray-500 text-center">${formatFileSize(file.size)}</p><p class="text-xs text-gray-400 text-center mt-1">${formatDate(file.created_at)}</p></div><div class="flex justify-center space-x-2 mt-3 pt-3 border-t border-gray-100"><button onclick="event.stopPropagation(); previewFile(${file.id})" class="text-gray-400 hover:text-blue-600 p-1" title="Предпросмотр"><i class="fas fa-eye"></i></button><button onclick="event.stopPropagation(); downloadFile(${file.id})" class="text-gray-400 hover:text-green-600 p-1" title="Скачать"><i class="fas fa-download"></i></button></div></div></div>`;
        });
        html += '</div>';
        contentDiv.innerHTML = html;
    }

    function toggleFileSelection(fileId) {
        const file = allFiles.find(f => f.id === fileId);
        if (!file) return;
        const index = selectedFiles.findIndex(f => f.id === fileId);
        if (index === -1) {
            selectedFiles.push(file);
        } else {
            selectedFiles.splice(index, 1);
        }
        renderFiles(allFiles);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCount = document.getElementById('selectedCount');
        const confirmCount = document.getElementById('confirmCount');
        if (selectedCount) selectedCount.textContent = selectedFiles.length;
        if (confirmCount) confirmCount.textContent = selectedFiles.length;
    }

    function confirmStorageFileSelection() {
        if (selectedFiles.length === 0) {
            showNotification('Выберите хотя бы один файл', 'warning');
            return;
        }
        const selectedFilesInput = document.getElementById('selectedFiles');
        if (selectedFilesInput) selectedFilesInput.value = JSON.stringify(selectedFiles);
        updateSelectedFilesDisplay();
        switchFileTab('storage');
        closeFileManager();
    }

    function updateSelectedFilesDisplay() {
        const container = document.getElementById('selectedFilesContainer');
        const fileCounter = document.getElementById('fileCounter');
        const fileCount = document.getElementById('fileCount');

        if (!container) return;

        if (selectedFiles.length === 0) {
            container.innerHTML = `<div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg"><i class="fas fa-folder-open text-3xl text-gray-300 mb-3"></i><p class="text-sm text-gray-500">Файлы не выбраны</p><p class="text-xs text-gray-400 mt-1">Нажмите "Открыть хранилище" для выбора</p></div>`;
            if (fileCounter) fileCounter.classList.add('hidden');
        } else {
            let html = '';
            selectedFiles.forEach(file => {
                const fileIcon = getFileIcon(file.extension);
                const fileType = getFileTypeClass(file.extension);
                html += `<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-white transition-colors"><div class="flex items-center space-x-3"><div class="w-10 h-10 ${fileType.bg} rounded flex items-center justify-center"><span class="text-lg">${fileIcon}</span></div><div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-800 truncate">${file.name}</p><div class="flex items-center space-x-3 mt-1"><span class="text-xs text-gray-500">${formatFileSize(file.size)}</span><span class="text-xs text-gray-400">•</span><span class="text-xs text-gray-400">${formatDate(file.created_at)}</span></div></div></div><div class="flex items-center space-x-2"><button onclick="previewSelectedFile(${file.id})" class="text-gray-400 hover:text-blue-600 p-1" title="Предпросмотр"><i class="fas fa-eye"></i></button><button onclick="removeSelectedFile(${file.id})" class="text-gray-400 hover:text-red-600 p-1" title="Удалить"><i class="fas fa-times"></i></button></div></div>`;
            });
            container.innerHTML = html;
            if (fileCount) fileCount.textContent = selectedFiles.length;
            if (fileCounter) fileCounter.classList.remove('hidden');
        }
    }

    function removeSelectedFile(fileId) {
        selectedFiles = selectedFiles.filter(f => f.id !== fileId);
        updateSelectedFilesDisplay();
        updateSelectedCount();
    }

    function clearSelectedFiles() {
        if (selectedFiles.length === 0) return;
        if (confirm(`Удалить все выбранные файлы (${selectedFiles.length})?`)) {
            selectedFiles = [];
            updateSelectedFilesDisplay();
            updateSelectedCount();
        }
    }

    function previewFile(fileId) {
        const file = allFiles.find(f => f.id === fileId);
        if (!file) return;
        currentPreviewFile = file;
        const previewPanel = document.getElementById('fileManagerPreviewPanel');
        const content = document.getElementById('filePreviewContent');
        if (!previewPanel || !content) return;

        const fileIcon = getFileIcon(file.extension);
        const fileType = getFileTypeClass(file.extension);
        content.innerHTML = `<div class="bg-white rounded-lg p-4 shadow-sm"><div class="flex items-center justify-between mb-4"><div class="flex items-center space-x-3"><div class="w-12 h-12 ${fileType.bg} rounded-lg flex items-center justify-center"><span class="text-2xl">${fileIcon}</span></div><div><h4 class="font-semibold text-gray-800 truncate max-w-xs">${file.name}</h4><p class="text-sm text-gray-500">${formatFileSize(file.size)}</p></div></div><div class="flex items-center space-x-2"><button onclick="downloadFile(${file.id})" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"><i class="fas fa-download mr-1"></i>Скачать</button><button onclick="toggleFileSelection(${file.id})" class="px-3 py-1 ${selectedFiles.some(f => f.id === file.id) ? 'bg-gray-200 text-gray-700' : 'bg-green-600 text-white'} rounded-lg hover:opacity-90 text-sm">${selectedFiles.some(f => f.id === file.id) ? '✓ Выбран' : 'Выбрать'}</button></div></div><div class="space-y-3 border-t border-gray-100 pt-4"><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Тип файла</p><p class="text-sm font-medium">${file.extension ? file.extension.toUpperCase() : 'Неизвестно'}</p></div><div><p class="text-xs text-gray-500 mb-1">Дата загрузки</p><p class="text-sm font-medium">${formatDate(file.created_at, true)}</p></div></div><div id="filePreviewContainer" class="mt-4"><div class="border border-gray-200 rounded-lg p-4 bg-gray-50">${getFilePreview(file)}</div></div></div></div>`;
        previewPanel.classList.remove('hidden');
    }

    function previewSelectedFile(fileId) {
        const file = selectedFiles.find(f => f.id === fileId);
        if (file) previewFile(fileId);
    }

    function closeFilePreview() {
        const previewPanel = document.getElementById('fileManagerPreviewPanel');
        if (previewPanel) previewPanel.classList.add('hidden');
    }

    function getFilePreview(file) {
        const extension = file.extension ? file.extension.toLowerCase() : '';
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            return `<div class="text-center"><p class="text-sm text-gray-600 mb-2">Изображение</p><div class="bg-gray-200 rounded p-2 inline-block"><i class="fas fa-image text-4xl text-gray-400"></i></div><p class="text-xs text-gray-500 mt-2">Для просмотра скачайте файл</p></div>`;
        } else if (['pdf'].includes(extension)) {
            return `<div class="text-center"><p class="text-sm text-gray-600 mb-2">PDF документ</p><div class="bg-red-100 rounded p-2 inline-block"><i class="fas fa-file-pdf text-4xl text-red-400"></i></div><p class="text-xs text-gray-500 mt-2">Для просмотра скачайте файл</p></div>`;
        } else if (['doc', 'docx'].includes(extension)) {
            return `<div class="text-center"><p class="text-sm text-gray-600 mb-2">Word документ</p><div class="bg-blue-100 rounded p-2 inline-block"><i class="fas fa-file-word text-4xl text-blue-400"></i></div><p class="text-xs text-gray-500 mt-2">Для просмотра скачайте файл</p></div>`;
        } else if (['xls', 'xlsx'].includes(extension)) {
            return `<div class="text-center"><p class="text-sm text-gray-600 mb-2">Excel таблица</p><div class="bg-green-100 rounded p-2 inline-block"><i class="fas fa-file-excel text-4xl text-green-400"></i></div><p class="text-xs text-gray-500 mt-2">Для просмотра скачайте файл</p></div>`;
        } else {
            return `<div class="text-center"><p class="text-sm text-gray-600 mb-2">Файл ${extension.toUpperCase()}</p><div class="bg-gray-200 rounded p-2 inline-block"><i class="fas fa-file text-4xl text-gray-400"></i></div><p class="text-xs text-gray-500 mt-2">Для просмотра скачайте файл</p></div>`;
        }
    }

    const uploadInput = document.getElementById('uploadNewFilesInput');
    if (uploadInput) {
        uploadInput.addEventListener('change', function (e) {
            const container = document.getElementById('uploadFilesContainer');
            const list = document.getElementById('uploadFilesList');
            if (!container) return;

            container.innerHTML = '';
            if (this.files.length > 0 && list) {
                list.classList.remove('hidden');
                Array.from(this.files).forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200';
                    const fileIcon = getFileIcon(file.name.split('.').pop());
                    const fileType = getFileTypeClass(file.name.split('.').pop());
                    div.innerHTML = `<div class="flex items-center space-x-3"><div class="w-10 h-10 ${fileType.bg} rounded flex items-center justify-center"><span class="text-lg">${fileIcon}</span></div><div><p class="text-sm font-medium text-gray-800 truncate max-w-[200px]">${file.name}</p><p class="text-xs text-gray-500">${formatFileSize(file.size)}</p></div></div><button type="button" onclick="removeUploadedFile(${index})" class="text-red-500 hover:text-red-700 p-1"><i class="fas fa-times"></i></button>`;
                    container.appendChild(div);
                });
            } else if (list) {
                list.classList.add('hidden');
            }
        });
    }

    function removeUploadedFile(index) {
        const input = document.getElementById('uploadNewFilesInput');
        if (!input) return;

        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, i) => {
            if (i !== index) dt.items.add(file);
        });
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    const uploadArea = document.querySelector('.file-upload-area');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-blue-400', 'bg-blue-50');
        });
        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-blue-400', 'bg-blue-50');
            const input = document.getElementById('uploadNewFilesInput');
            if (input && e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    function getFileIcon(extension) {
        const ext = (extension || '').toLowerCase();
        const icons = {
            'pdf': '📄',
            'doc': '📝',
            'docx': '📝',
            'xls': '📊',
            'xlsx': '📊',
            'jpg': '🖼️',
            'jpeg': '🖼️',
            'png': '🖼️',
            'gif': '🖼️',
            'zip': '📦',
            'rar': '📦',
            '7z': '📦',
            'txt': '📃',
            'mp3': '🎵',
            'mp4': '🎬',
            'avi': '🎬',
            'mov': '🎬',
            'wav': '🎵',
            'ppt': '📊',
            'pptx': '📊'
        };
        return icons[ext] || '📎';
    }

    function getFileTypeClass(extension) {
        const ext = (extension || '').toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(ext)) return {
            bg: 'bg-pink-100',
            text: 'text-pink-600'
        };
        else if (['pdf'].includes(ext)) return {bg: 'bg-red-100', text: 'text-red-600'};
        else if (['doc', 'docx', 'txt', 'rtf'].includes(ext)) return {bg: 'bg-blue-100', text: 'text-blue-600'};
        else if (['xls', 'xlsx', 'csv'].includes(ext)) return {bg: 'bg-green-100', text: 'text-green-600'};
        else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) return {bg: 'bg-yellow-100', text: 'text-yellow-600'};
        else if (['mp3', 'wav', 'ogg', 'flac'].includes(ext)) return {bg: 'bg-purple-100', text: 'text-purple-600'};
        else if (['mp4', 'avi', 'mov', 'wmv', 'flv'].includes(ext)) return {
            bg: 'bg-indigo-100',
            text: 'text-indigo-600'
        };
        else return {bg: 'bg-gray-100', text: 'text-gray-600'};
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function formatDate(dateString, full = false) {
        const date = new Date(dateString);
        if (full) return date.toLocaleDateString('ru-RU', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        return date.toLocaleDateString('ru-RU');
    }

    async function downloadFile(fileId) {
        const file = allFiles.find(f => f.id === fileId);
        if (!file) return;
        try {
            const response = await fetch(`/file-storage/download/${fileId}`, {
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''}
            });
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = file.name;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }
        } catch (error) {
            showNotification('Ошибка при скачивании файла', 'error');
        }
    }

    const fileSearch = document.getElementById('fileManagerSearch');
    const typeFilterEl = document.getElementById('fileManagerTypeFilter');
    const sortByEl = document.getElementById('fileManagerSortBy');

    if (fileSearch && typeFilterEl && sortByEl) {
        fileSearch.addEventListener('input', function () {
            filterAndRenderFiles();
        });
        typeFilterEl.addEventListener('change', function () {
            filterAndRenderFiles();
        });
        sortByEl.addEventListener('change', function () {
            filterAndRenderFiles();
        });
    }

    function filterAndRenderFiles() {
        const searchTerm = document.getElementById('fileManagerSearch')?.value.toLowerCase() || '';
        const typeFilterVal = document.getElementById('fileManagerTypeFilter')?.value || '';
        const sortByVal = document.getElementById('fileManagerSortBy')?.value || 'newest';
        let filteredFiles = [...allFiles];
        if (searchTerm) filteredFiles = filteredFiles.filter(file => file.name.toLowerCase().includes(searchTerm));
        if (typeFilterVal) {
            filteredFiles = filteredFiles.filter(file => {
                const ext = (file.extension || '').toLowerCase();
                switch (typeFilterVal) {
                    case 'image':
                        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(ext);
                    case 'document':
                        return ['pdf', 'doc', 'docx', 'txt', 'rtf'].includes(ext);
                    case 'video':
                        return ['mp4', 'avi', 'mov', 'wmv', 'flv'].includes(ext);
                    case 'audio':
                        return ['mp3', 'wav', 'ogg', 'flac'].includes(ext);
                    case 'archive':
                        return ['zip', 'rar', '7z', 'tar', 'gz'].includes(ext);
                    default:
                        return true;
                }
            });
        }
        filteredFiles.sort((a, b) => {
            switch (sortByVal) {
                case 'oldest':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'name_asc':
                    return a.name.localeCompare(b.name);
                case 'name_desc':
                    return b.name.localeCompare(a.name);
                case 'size_asc':
                    return a.size - b.size;
                case 'size_desc':
                    return b.size - a.size;
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });
        renderFiles(filteredFiles);
    }

    // ==================== ОБРАБОТКА ФОРМ ====================

    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const selectedFilesData = JSON.parse(document.getElementById('selectedFiles')?.value || '[]');
            selectedFilesData.forEach(file => {
                formData.append('selected_file_ids[]', file.id);
            });
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : '';
            const name = formData.get('name');
            const departmentId = formData.get('department_id');
            if (!name || !departmentId) {
                showNotification('Заполните обязательные поля: название задачи и отдел', 'error');
                return;
            }
            const isEditMode = !!formData.get('task_id');
            if (submitButton) {
                submitButton.innerHTML = isEditMode ? '<i class="fas fa-spinner fa-spin mr-2"></i>Сохранение...' : '<i class="fas fa-spinner fa-spin mr-2"></i>Создание...';
                submitButton.disabled = true;
            }
            try {
                const taskId = formData.get('task_id');
                let url, method;
                if (taskId) {
                    url = `/tasks/${taskId}/update`;
                    method = 'POST';
                    formData.append('_method', 'patch');
                } else {
                    url = '/tasks/store';
                    method = 'POST';
                }
                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json'
                    }
                });
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Ожидался JSON, но получили: ${text.substring(0, 100)}`);
                }
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || `HTTP error! status: ${response.status}`);
                if (data.success) {
                    closeTaskModal();
                    showNotification(isEditMode ? 'Задача успешно обновлена!' : 'Задача успешно создана!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message || 'Ошибка при сохранении задачи', 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification(error.message || 'Произошла ошибка при сохранении задачи', 'error');
            } finally {
                if (submitButton) {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            const password = formData.get('password');
            const passwordConfirmation = formData.get('password_confirmation');
            if (password !== passwordConfirmation) {
                showNotification('Пароли не совпадают', 'error');
                return;
            }
            if (password.length < 8) {
                showNotification('Пароль должен содержать минимум 8 символов', 'error');
                return;
            }
            if (submitButton) {
                submitButton.textContent = 'Создание...';
                submitButton.disabled = true;
            }
            try {
                const response = await fetch('/users/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    closeUserModal();
                    showNotification('Пользователь успешно создан!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Произошла ошибка при создании пользователя', 'error');
            } finally {
                if (submitButton) {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            if (submitButton) {
                submitButton.textContent = 'Создание...';
                submitButton.disabled = true;
            }
            try {
                const response = await fetch('/category/create', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    closeCategoryModal();
                    showNotification('Категория успешно создана!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Произошла ошибка при создании категории', 'error');
            } finally {
                if (submitButton) {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    let currentEditingCategoryId = null;

    async function openEditCategoryModal(categoryId) {
        currentEditingCategoryId = categoryId;
        try {
            const response = await fetch(`/category/${categoryId}/edit`);
            const category = await response.json();
            if (category) {
                const editIdField = document.getElementById('edit_category_id');
                if (editIdField) editIdField.value = category.id;
                const editNameField = document.getElementById('edit_category_name');
                if (editNameField) editNameField.value = category.name;
                const colorInput = document.querySelector(`input[name="color"][value="${category.color}"]`);
                if (colorInput) colorInput.checked = true;
                const modal = document.getElementById('editCategoryModal');
                if (modal) modal.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Ошибка при загрузке категории:', error);
            showNotification('Ошибка при загрузке категории', 'error');
        }
    }

    function closeEditCategoryModal() {
        const modal = document.getElementById('editCategoryModal');
        if (modal) modal.classList.add('hidden');
        const form = document.getElementById('editCategoryForm');
        if (form) form.reset();
        currentEditingCategoryId = null;
    }

    const editCategoryForm = document.getElementById('editCategoryForm');
    if (editCategoryForm) {
        editCategoryForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            if (submitButton) {
                submitButton.textContent = 'Сохранение...';
                submitButton.disabled = true;
            }
            try {
                const response = await fetch('/category/update', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'patch'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    closeEditCategoryModal();
                    showNotification('Категория успешно обновлена!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Произошла ошибка при обновлении категории', 'error');
            } finally {
                if (submitButton) {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    const departmentForm = document.getElementById('departmentForm');
    if (departmentForm) {
        departmentForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            if (submitButton) {
                submitButton.textContent = 'Создание...';
                submitButton.disabled = true;
            }
            try {
                const response = await fetch('/departments/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    closeDepartmentModal();
                    showNotification('Отдел успешно создан!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Произошла ошибка при создании отдела', 'error');
            } finally {
                if (submitButton) {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    let currentEditingDepartmentId = null;

    async function openEditDepartmentModal(departmentId) {
        currentEditingDepartmentId = departmentId;
        try {
            const response = await fetch(`/departments/${departmentId}/edit`);
            const department = await response.json();
            if (department) {
                const editIdField = document.getElementById('edit_department_id');
                if (editIdField) editIdField.value = department.id;
                const editNameField = document.getElementById('edit_department_name');
                if (editNameField) editNameField.value = department.name;
                const editCompanyField = document.getElementById('edit_department_company');
                if (editCompanyField) editCompanyField.value = department.company_id;
                const modal = document.getElementById('editDepartmentModal');
                if (modal) modal.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Ошибка при загрузке отдела:', error);
            showNotification('Ошибка при загрузке отдела', 'error');
        }
    }

    function closeEditDepartmentModal() {
        const modal = document.getElementById('editDepartmentModal');
        if (modal) modal.classList.add('hidden');
        const form = document.getElementById('editDepartmentForm');
        if (form) form.reset();
        currentEditingDepartmentId = null;
    }

    const editDepartmentForm = document.getElementById('editDepartmentForm');
    if (editDepartmentForm) {
        editDepartmentForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            if (submitButton) {
                submitButton.textContent = 'Сохранение...';
                submitButton.disabled = true;
            }
            try {
                const response = await fetch('/departments/update', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (data.success) {
                    closeEditDepartmentModal();
                    showNotification('Отдел успешно обновлен!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Произошла ошибка при обновлении отдела: ' + error.message, 'error');
            } finally {
                if (submitButton) {
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    //УВЕДОМЛЕНИЯ

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : type === 'warning' ? 'bg-yellow-500 text-white' : 'bg-blue-500 text-white'}`;
        notification.innerHTML = `<div class="flex items-center"><i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} mr-2"></i><span>${message}</span></div>`;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) notification.parentNode.removeChild(notification);
            }, 300);
        }, 5000);
    }

    // DRAG AND DROP ДЛЯ ЗАДАЧ

    function mapStatusForServer(status) {
        const statusMap = {
            'new': 'назначена',
            'in-progress': 'в работе',
            'review': 'на проверке',
            'done': 'выполнена'
        };
        return statusMap[status] || status;
    }

    function initTaskDragAndDrop() {
        const taskCards = document.querySelectorAll('.task-card');
        taskCards.forEach(task => {
            task.setAttribute('draggable', 'true');
            task.removeEventListener('dragstart', handleDragStart);
            task.removeEventListener('dragend', handleDragEnd);
            task.addEventListener('dragstart', handleDragStart);
            task.addEventListener('dragend', handleDragEnd);
        });

        const columns = document.querySelectorAll('.board-column');
        columns.forEach(column => {
            column.removeEventListener('dragover', handleDragOver);
            column.removeEventListener('dragleave', handleDragLeave);
            column.removeEventListener('drop', handleDrop);
            column.addEventListener('dragover', handleDragOver);
            column.addEventListener('dragleave', handleDragLeave);
            column.addEventListener('drop', handleDrop);
        });
    }

    function handleDragStart(e) {
        draggedTaskCard = this;
        const taskId = this.getAttribute('data-task');
        e.dataTransfer.setData('text/plain', taskId);
        e.dataTransfer.effectAllowed = 'move';
        this.style.opacity = '0.5';
    }

    function handleDragEnd(e) {
        if (draggedTaskCard) {
            draggedTaskCard.style.opacity = '1';
        }
        draggedTaskCard = null;
        document.querySelectorAll('.board-column').forEach(column => {
            column.style.backgroundColor = '';
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        this.style.backgroundColor = '#e5e7eb';
    }

    function handleDragLeave(e) {
        this.style.backgroundColor = '';
    }

    async function handleDrop(e) {
        e.preventDefault();
        this.style.backgroundColor = '';

        if (!draggedTaskCard) return;

        const newStatus = this.getAttribute('data-status');
        const taskId = draggedTaskCard.getAttribute('data-task');
        const currentColumn = draggedTaskCard.closest('.board-column');
        const currentStatus = currentColumn ? currentColumn.getAttribute('data-status') : null;

        if (currentStatus === newStatus) {
            draggedTaskCard.style.opacity = '1';
            draggedTaskCard = null;
            return;
        }

        const taskCard = draggedTaskCard;
        const serverStatus = mapStatusForServer(newStatus);

        taskCard.style.opacity = '0.5';
        taskCard.style.cursor = 'wait';

        try {
            // ИСПРАВЛЕНО: используем правильный URL из маршрутов
            const response = await fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({status: serverStatus})
            });

            const data = await response.json();

            if (data.success) {
                const taskContainer = this.querySelector('.task-container');
                if (taskContainer) {
                    taskContainer.appendChild(taskCard);
                }
                taskCard.style.opacity = '1';
                taskCard.style.cursor = 'move';
                updateTaskCounters();
                showNotification('Статус задачи обновлен', 'success');
            } else {
                taskCard.style.opacity = '1';
                taskCard.style.cursor = 'move';
                showNotification(data.message || 'Ошибка при обновлении статуса', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            taskCard.style.opacity = '1';
            taskCard.style.cursor = 'move';
            showNotification('Ошибка при обновлении статуса', 'error');
        } finally {
            draggedTaskCard = null;
        }
    }

    function updateTaskCounters() {
        const columns = [
            {selector: '[data-status="new"]', counterSelector: '.board-column[data-status="new"] span:first-child'},
            {
                selector: '[data-status="in-progress"]',
                counterSelector: '.board-column[data-status="in-progress"] span:first-child'
            },
            {
                selector: '[data-status="review"]',
                counterSelector: '.board-column[data-status="review"] span:first-child'
            },
            {selector: '[data-status="done"]', counterSelector: '.board-column[data-status="done"] span:first-child'}
        ];

        columns.forEach(column => {
            const container = document.querySelector(`.task-container${column.selector}`);
            const counter = document.querySelector(column.counterSelector);
            if (container && counter) {
                counter.textContent = container.querySelectorAll('.task-card').length;
            }
        });
    }

    // Функции для удаления категории

    let currentDeletingCategoryId = null;

    function openDeleteCategoryModal(categoryId, categoryName) {
        currentDeletingCategoryId = categoryId;
        const nameSpan = document.getElementById('deleteCategoryName');
        if (nameSpan) nameSpan.textContent = categoryName;
        const idField = document.getElementById('delete_category_id');
        if (idField) idField.value = categoryId;
        const modal = document.getElementById('deleteCategoryModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteCategoryModal() {
        const modal = document.getElementById('deleteCategoryModal');
        if (modal) modal.classList.add('hidden');
        currentDeletingCategoryId = null;
    }

    const deleteCategoryForm = document.getElementById('deleteCategoryForm');
    if (deleteCategoryForm) {
        deleteCategoryForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : '';
            if (submitButton) {
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Удаление...';
                submitButton.disabled = true;
            }
            try {
                const response = await fetch('/category/delete', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    closeDeleteCategoryModal();
                    showNotification('Категория успешно удалена!', 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showNotification('Произошла ошибка при удалении категории: ' + error.message, 'error');
            } finally {
                if (submitButton) {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            }
        });
    }

    // ==================== БЫСТРОЕ ДОБАВЛЕНИЕ ЗАДАЧИ (НОВЫЙ СТИЛЬ) ====================
    function showQuickAddForm() {
        const showBtn = document.getElementById('showQuickAddBtn');
        const form = document.getElementById('quickAddForm');
        const formInner = document.getElementById('quickAddFormInner');

        if (showBtn && form && formInner) {
            showBtn.classList.add('hidden');
            form.classList.remove('hidden');

            // Анимация появления
            setTimeout(() => {
                formInner.classList.remove('scale-95', 'opacity-0');
                formInner.classList.add('scale-100', 'opacity-100');
            }, 10);

            // Фокусируемся на поле ввода названия
            setTimeout(() => {
                document.getElementById('quickTaskName').focus();
            }, 200);
        }
    }

    function hideQuickAddForm() {
        const showBtn = document.getElementById('showQuickAddBtn');
        const form = document.getElementById('quickAddForm');
        const formInner = document.getElementById('quickAddFormInner');

        if (formInner) {
            formInner.classList.remove('scale-100', 'opacity-100');
            formInner.classList.add('scale-95', 'opacity-0');
        }

        setTimeout(() => {
            if (form && showBtn) {
                form.classList.add('hidden');
                showBtn.classList.remove('hidden');
            }
        }, 200);

        // Очищаем форму
        document.getElementById('quickTaskName').value = '';
        document.getElementById('quickTaskDescription').value = '';
        document.getElementById('quickTaskDeadline').value = '';
        document.getElementById('quickTaskPriority').value = 'средний';
    }

    async function createQuickTask() {
        const taskName = document.getElementById('quickTaskName').value.trim();

        if (!taskName) {
            const input = document.getElementById('quickTaskName');
            input.classList.add('shake');
            input.style.border = '2px solid #ef4444';
            setTimeout(() => {
                input.classList.remove('shake');
                input.style.border = '';
            }, 500);
            showNotification('Пожалуйста, укажите название задачи', 'error');
            document.getElementById('quickTaskName').focus();
            return;
        }

        const submitBtn = document.querySelector('#quickAddForm button[onclick="createQuickTask()"]');
        const originalText = submitBtn?.innerHTML;

        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Создание...</span>';
            submitBtn.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('name', taskName);
            formData.append('description', document.getElementById('quickTaskDescription').value);
            formData.append('priority', document.getElementById('quickTaskPriority').value);
            formData.append('deadline', document.getElementById('quickTaskDeadline').value || '');
            formData.append('status', 'назначена');
            formData.append('is_personal', '1');
            formData.append('_token', '{{ csrf_token() }}');

            @if(auth()->check())
            formData.append('user_id', '{{ auth()->id() }}');
            formData.append('author_id', '{{ auth()->id() }}');
            @endif

            const response = await fetch('/tasks/personal/store', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Задача "' + escapeHtml(taskName) + '" успешно создана!', 'success');
                hideQuickAddForm();

                // Добавляем новую задачу в колонку без перезагрузки страницы
                if (data.task) {
                    addTaskToColumn(data.task);
                } else {
                    // Если нет данных задачи в ответе, просто перезагружаем
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            } else {
                showNotification(data.message || 'Ошибка при создании задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при создании задачи', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    }

    function showSuccessNotification(message, taskName) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-20 right-4 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 transform transition-all duration-300 translate-x-full';
        notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="font-semibold">${message}</p>
                <p class="text-sm text-white/80">"${escapeHtml(taskName.substring(0, 50))}${taskName.length > 50 ? '...' : ''}"</p>
            </div>
        </div>
    `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) notification.parentNode.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Добавляем поддержку Enter и Escape
    document.addEventListener('DOMContentLoaded', function () {
        const quickTaskName = document.getElementById('quickTaskName');
        if (quickTaskName) {
            quickTaskName.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    createQuickTask();
                }
            });
        }

        // Escape для закрытия формы
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const form = document.getElementById('quickAddForm');
                if (form && !form.classList.contains('hidden')) {
                    hideQuickAddForm();
                }
            }
        });
    });

    // Эффект встряски для инпутов
    const style = document.createElement('style');
    style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .shake {
        animation: shake 0.3s ease-in-out;
    }
`;
    document.head.appendChild(style);

    // Конец быстрого добавления
</script>

<script>

    // Модальные окна
    document.getElementById('newTaskBtn').addEventListener('click', function () {
        document.getElementById('taskModal').classList.remove('hidden');
    });

    document.getElementById('newUserBtn').addEventListener('click', function () {
        document.getElementById('newUserModal').classList.remove('hidden');
    });


    // Закрытие модальных окон
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('taskModal').classList.add('hidden');
    });

    document.getElementById('cancelTask').addEventListener('click', function () {
        document.getElementById('taskModal').classList.add('hidden');
    });


    // // Обработка формы создания задачи
    // document.getElementById('taskForm').addEventListener('submit', function (e) {
    //     e.preventDefault();
    //     alert('Задача успешно создана!');
    //     document.getElementById('taskModal').classList.add('hidden');
    //     // Здесь будет код для добавления задачи на доску
    // });

    // Перетаскивание задач
    let draggedTask = null;

    document.querySelectorAll('.task-card').forEach(task => {
        task.addEventListener('dragstart', function () {
            draggedTask = this;
            setTimeout(() => {
                this.style.opacity = '0.5';
            }, 0);
        });

        task.addEventListener('dragend', function () {
            setTimeout(() => {
                this.style.opacity = '1';
                draggedTask = null;
            }, 0);
        });
    });

    document.querySelectorAll('.board-column').forEach(column => {
        column.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.backgroundColor = '#e5e7eb';
        });

        column.addEventListener('dragleave', function () {
            this.style.backgroundColor = '#f3f4f6';
        });

        column.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.backgroundColor = '#f3f4f6';

            if (draggedTask) {
                const status = this.getAttribute('data-status');
                const taskContainer = this.querySelector('.task-container');

                // Обновляем статус задачи в данных
                const taskId = draggedTask.getAttribute('data-task');
                const task = appData.tasks.find(t => t.id == taskId);
                if (task) {
                    task.status = status;
                }

                // Перемещаем задачу в новый контейнер
                taskContainer.appendChild(draggedTask);

                // Обновляем счетчики задач
                updateTaskCounters();
            }
        });
    });

    // Обновление счетчиков задач
    function updateTaskCounters() {
        const statuses = ['new', 'in-progress', 'review', 'done'];

        statuses.forEach(status => {
            const container = document.querySelector(`.task-container[data-status="${status}"]`);
            const counter = document.querySelector(`.board-column[data-status="${status}"] .bg-gray-200,
                                                      .board-column[data-status="${status}"] .bg-blue-100,
                                                      .board-column[data-status="${status}"] .bg-yellow-100,
                                                      .board-column[data-status="${status}"] .bg-green-100`);

            if (container && counter) {
                const count = container.querySelectorAll('.task-card').length;
                counter.textContent = count;
            }
        });
    }

    // Инициализация приложения
    document.addEventListener('DOMContentLoaded', function () {
        updateTaskCounters();

        // Добавляем обработчики для элементов боковой панели
        document.querySelectorAll('.workspace-item, .board-item, .user-item').forEach(item => {
            item.addEventListener('click', function () {
                const type = this.classList.contains('workspace-item') ? 'workspace' :
                    this.classList.contains('board-item') ? 'board' : 'user';
                const id = this.getAttribute(`data-${type}`);

                // В реальном приложении здесь будет загрузка данных
                console.log(`Выбран ${type}: ${id}`);
            });
        });
    });

    // Открыть модальное окно просмотра задачи
    async function openTaskViewModal(taskId) {

        const modal = document.getElementById('taskViewModal');
        // Добавляем blur при открытии
        modal.style.backdropFilter = 'blur(10px)';
        modal.classList.remove('hidden');

        try {
            const response = await fetch(`/tasks/${taskId}/view`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                const task = data.task;
                const modalContent = document.getElementById('taskModalContent');
// Модалка Информация о задача на странице /team/tasks
                modalContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Основная информация -->
                    <div class="md:col-span-2">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">${task.name}</h4>
                        <p class="text-gray-600 mb-4">${task.description || 'Описание отсутствует'}</p>
                    </div>

                    <!-- Детали задачи -->
                    <div class="space-y-4">
                        <div class="flex align-items-center">
                            <div class="mr-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(task.status)}">
                                    ${task.status_icon || ''} ${task.status}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Приоритет</label>
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md ${getPriorityStyle(task.priority).bg} border ${getPriorityStyle(task.priority).border}">
                                    <div class="flex items-end gap-[3px] h-5">
                                        <div class="w-1.5 rounded-sm ${getPriorityStyle(task.priority).level >= 1 ? getPriorityStyle(task.priority).filled : getPriorityStyle(task.priority).empty} h-2"></div>
                                        <div class="w-1.5 rounded-sm ${getPriorityStyle(task.priority).level >= 2 ? getPriorityStyle(task.priority).filled : getPriorityStyle(task.priority).empty} h-3"></div>
                                        <div class="w-1.5 rounded-sm ${getPriorityStyle(task.priority).level >= 3 ? getPriorityStyle(task.priority).filled : getPriorityStyle(task.priority).empty} h-4"></div>
                                        <div class="w-1.5 rounded-sm ${getPriorityStyle(task.priority).level >= 4 ? getPriorityStyle(task.priority).filled : getPriorityStyle(task.priority).empty} h-5"></div>
                                    </div>
                                    <span class="text-xs font-medium ${getPriorityStyle(task.priority).text}">${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}</span>
                                </div>
                            </div>
                        </div>

                        ${task.department ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                                <p class="text-gray-900">${task.department.name}</p>
                            </div>
                        ` : ''}

                        ${task.category ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                            <p class="text-gray-900">${task.category.name}</p>
                        </div>
                        ` : ''}

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Автор</label>
                            <p class="text-gray-900">${task.author.name}</p>
                        </div>
                    </div>

                    <!-- Временные параметры -->
                    <div class="space-y-4">
                        ${task.deadline ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Дедлайн</label>
                            <p class="text-gray-900 ${new Date(task.deadline) < new Date() ? 'text-red-600 font-semibold' : ''}">
                                ${formatDateTime(task.deadline)}
                                ${new Date(task.deadline) < new Date() ? '(Просрочено)' : ''}
                            </p>
                        </div>
                        ` : ''}

                        ${task.estimated_hours ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Планируемое время</label>
                            <p class="text-gray-900">${task.estimated_hours} часов</p>
                        </div>
                        ` : ''}

                        ${task.actual_hours ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Фактическое время</label>
                            <p class="text-gray-900">${task.actual_hours} часов</p>
                        </div>
                        ` : ''}

                        ${task.completed_at ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Завершено</label>
                            <p class="text-gray-900">${formatDateTime(task.completed_at)}</p>
                        </div>
                        ` : ''}

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Создана</label>
                            <p class="text-gray-900">${formatDateTime(task.created_at)}</p>
                        </div>
                    </div>

                    <!-- Файлы -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Прикрепленные файлы</label>
                        ${task.files && task.files.length > 0 ? `
                            <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                                ${task.files.map(file => `
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-paperclip text-gray-500"></i>
                                            <div>
                                                <a href="/storage/${file.file_path}" target="_blank"
                                                   class="text-blue-600 hover:text-blue-800 font-medium block">
                                                    ${file.name}
                                                </a>
                                                <span class="text-xs text-gray-500">
                                                    ${Math.round(file.file_size / 1024)} KB •
                                                    ${formatDateTime(file.created_at)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `
                            <p class="text-gray-500 text-center py-4">Файлы отсутствуют</p>
                        `}
                    </div>
                </div>

                ${task.rejections && task.rejections.length > 0 ? `
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">История отказов</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        ${task.rejections.map(rejection => `
                            <div class="bg-red-50 border border-red-200 rounded p-3">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-sm font-medium text-red-800">${rejection.user?.name || 'Пользователь'}</span>
                                    <span class="text-xs text-red-600">${formatDateTime(rejection.created_at)}</span>
                                </div>
                                <p class="text-sm text-red-700">${rejection.reason}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- Кнопки действий -->
                <div class="flex space-x-3 mt-6 pt-4 border-t border-gray-200 max-[500px]:flex-col max-[500px]:space-x-0 max-[500px]:space-y-3">
                    ${task.status === 'назначена' ? `
                        <button onclick="startTask(${task.id})"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i>Начать работу
                        </button>
                    ` : ''}

                    ${task.status === 'в работе' ? `
                        <button onclick="sendForReview(${task.id})"
                                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                            <i class="fas fa-check-circle mr-2"></i>Отправить на проверку
                        </button>
                    ` : ''}

                    ${task.status !== 'выполнена' ? `
                        <button onclick="showRejectModal(${task.id})"
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>Отказаться от задачи
                        </button>
                    ` : ''}

                    <button onclick="closeTaskViewModal()"
                            class="text-white px-4 py-2 rounded-lg hover:bg-gray-400 transition" style="background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);">
                        Закрыть
                    </button>
                </div>
            `;

                document.getElementById('taskViewModal').classList.remove('hidden');
            } else {
                showNotification(data.message || 'Ошибка при загрузке данных задачи', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при загрузке данных задачи', 'error');
        }
    }

    async function startTask(taskId) {
        try {
            const response = await fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({status: 'в работе'})
            });

            const data = await response.json();

            if (data.success) {
                showNotification('Задача переведена в работу!', 'success');
                closeTaskViewModal();
                location.reload();
            } else {
                showNotification(data.message || 'Ошибка при обновлении статуса', 'error');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showNotification('Ошибка при обновлении статуса', 'error');
        }
    }

    // Закрыть модальное окно просмотра задачи
    function closeTaskViewModal() {
        document.getElementById('taskViewModal').classList.add('hidden');
        document.getElementById('taskModalContent').innerHTML = '';

        const modal = document.getElementById('taskViewModal');
        modal.classList.add('hidden');
        // Убираем blur при закрытии (опционально)
        modal.style.backdropFilter = '';
    }

    // Вспомогательные функции
    function getStatusColor(status) {
        const colors = {
            'не назначена': 'bg-gray-100 text-gray-800',
            'назначена': 'bg-blue-100 text-blue-800',
            'в работе': 'bg-purple-100 text-purple-800',
            'на проверке': 'bg-yellow-100 text-yellow-800',
            'выполнена': 'bg-green-100 text-green-800',
            'просрочена': 'bg-red-100 text-red-800'
        };
        return colors[status] || colors['не назначена'];
    }


    function getPriorityStyle(priority) {
        const styles = {
            'низкий': {
                level: 1,
                bg: 'bg-green-50',
                border: 'border-green-200',
                filled: 'bg-green-500',
                empty: 'bg-green-200',
                text: 'text-green-700'
            },
            'средний': {
                level: 2,
                bg: 'bg-blue-50',
                border: 'border-blue-200',
                filled: 'bg-blue-500',
                empty: 'bg-blue-100',
                text: 'text-blue-700'
            },
            'высокий': {
                level: 3,
                bg: 'bg-orange-50',
                border: 'border-orange-200',
                filled: 'bg-orange-500',
                empty: 'bg-orange-100',
                text: 'text-orange-700'
            },
            'критический': {
                level: 4,
                bg: 'bg-red-50',
                border: 'border-red-200',
                filled: 'bg-red-500',
                empty: 'bg-red-100',
                text: 'text-red-700'
            }
        };

        return styles[priority] || styles['средний'];
    }

    function formatDateTime(dateString) {
        if (!dateString) return 'Не указано';
        const date = new Date(dateString);
        return date.toLocaleDateString('ru-RU', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

</script>
<script>
    const burgerBtn = document.getElementById('burger-btn');
    const sidebarMenu = document.getElementById('sidebar-menu');
    const overlay = document.getElementById('sidebar-overlay');

    function closeSidebar() {
        sidebarMenu.classList.remove('active');
        burgerBtn.classList.remove('active');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    burgerBtn.addEventListener('click', function () {
        sidebarMenu.classList.toggle('active');
        burgerBtn.classList.toggle('active');

        if (sidebarMenu.classList.contains('active')) {
            document.body.classList.add('overflow-hidden');
            overlay.classList.remove('hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
            overlay.classList.add('hidden');
        }
    });

    overlay.addEventListener('click', closeSidebar);
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments)
        };
        m[i].l = 1 * new Date();
        for (var j = 0; j < document.scripts.length; j++) {
            if (document.scripts[j].src === r) {
                return;
            }
        }
        k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js?id=109251601', 'ym');

    ym(109251601, 'init', {
        ssr: true,
        webvisor: true,
        clickmap: true,
        ecommerce: "dataLayer",
        referrer: document.referrer,
        url: location.href,
        accurateTrackBounce: true,
        trackLinks: true
    });
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/109251601" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->
@stack('scripts')

</body>
</html>
