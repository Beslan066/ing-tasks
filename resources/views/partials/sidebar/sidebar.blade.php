@php
    $company = auth()->check() ? auth()->user()->company : null;
    $storageUsage = $company ? \App\Models\StorageUsage::where('company_id', $company->id)->first() : null;

    // Если нет данных о хранилище, создаем объект с значениями по умолчанию
    if (!$storageUsage && $company) {
        $storageUsage = new \App\Models\StorageUsage();
        $storageUsage->total_storage_limit = 50 * 1024 * 1024 * 1024; // 50 GB по умолчанию
        $storageUsage->used_storage = 0;
        $storageUsage->license_type = $company->license_type ?? 'basic';
    }

    $usedStorageFormatted = $storageUsage ? $storageUsage->getFormattedUsedStorage() : '0 B';
    $totalStorageFormatted = $storageUsage ? $storageUsage->getFormattedTotalStorage() : '50 GB';
    $usagePercentage = $storageUsage ? $storageUsage->getUsagePercentage() : 0;
    $licenseType = $storageUsage ? $storageUsage->license_type : ($company->license_type ?? 'basic');

    // Определяем иконку и цвет для типа подписки
    $licenseIcon = match($licenseType) {
        'premium' => 'fa-crown',
        'optimal' => 'fa-star',
        default => 'fa-check-circle'
    };

    $licenseColor = match($licenseType) {
        'premium' => 'text-yellow-500',
        'optimal' => 'text-blue-500',
        default => 'text-gray-500'
    };

    $licenseText = match($licenseType) {
        'premium' => 'Премиум',
        'optimal' => 'Оптимальный',
        default => 'Базовый'
    };
@endphp

    <!-- Боковая панель -->
<div id="sidebar-menu"
     class="sidebar h-full w-full max-w-64 py-2 px-4 fixed {{ $backgroundEnabled && $backgroundImage ? 'glass' : '' }}">
    <div class="relative h-full w-full sm:flex flex-col max-[638px]:flex overflow-hidden">

        <!-- Логотип -->
        <a href="{{route('welcome')}}" class="mb-8 logotype">
            <div class="flex items-center space-x-3 group">
                <div id="logo-icon"
                     class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:shadow-primary-500/20 transition-all duration-300">
                    <i class="fas fa-tasks text-white text-lg"></i>
                </div>
                <div class="logotype__text">
                    <h1 class="text-xl font-bold text-white">Менеджер<span class="text-primary-500">Плюс</span></h1>
                    <p class="text-xs text-sidebar-text mt-1 text-nowrap whitespace-nowrap">Управление задачами</p>
                </div>
            </div>
        </a>

        <!-- Навигация -->
        <div
            class="flex-1 space-y-2 overflow-x-hidden overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
            <!-- Главное меню -->
            <div class="mb-4">

                <div class="space-y-1">
                    <a href="{{route('welcome')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('welcome*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-primary-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check text-primary-500 text-sm"></i>
                        </div>
                        <span class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Мои
                            задачи</span>
                    </a>
                    <a href="{{route('tasks.admin')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:rounded-lg hover:bg-transparent/20 {{request()->routeIs('tasks.admin*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-landmark text-purple-500 text-sm"></i>
                        </div>
                        <span class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Моя
                            компания</span>
                    </a>
                    <a href="{{route('departments.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('departments.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-building text-orange-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Отделы</span>
                    </a>

                    <a href="{{route('team.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('team.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-users text-blue-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Пользователи</span>
                    </a>

                    <a href="{{route('chat.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('chat.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-comments text-pink-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Мессенджер</span>
                    </a>

                    <a href="{{route('files.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('files.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-brown-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-hard-drive text-brown-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Хранилище</span>
                    </a>

                    <a href="{{route('tools.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('tools.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-brown-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-tools text-yellow-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Инструменты</span>
                    </a>
                    <a href="{{route('frontend.news.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('news.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-brown-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-newspaper text-green-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Новости,
                            поддержка</span>
                    </a>

                    <a href="{{route('licence.index')}}"
                       class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:bg-transparent/20 hover:rounded-lg {{request()->routeIs('license.index*') ? 'active' : ''}}">
                        <div
                            class="w-8 h-8 rounded-lg bg-brown-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-credit-card text-yellow-500 text-sm"></i>
                        </div>
                        <span
                            class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Лицензия
                            и оплата</span>
                    </a>
                </div>

            </div>

            <!-- Онлайн пользователи -->
            <div class="mb-4 online-users">
                <h3
                    class="text-xs font-semibold text-sidebar-text uppercase text-nowrap whitespace-nowrap tracking-wider mb-3 px-2">
                    В СЕТИ</h3>

                @if(isset($onlineUsersCount) && $onlineUsersCount > 0)
                    <div class="flex items-center mb-3">
                        <div class="flex -space-x-2 mr-3">
                            @if(isset($onlineUsers) && $onlineUsers->count() > 0)
                                @foreach($onlineUsers->take(3) as $user)
                                    <div class="avatar-container">
                                        <div class="w-8 h-8 rounded-full {{ $user['color'] ?? 'bg-gradient-to-br from-blue-500 to-purple-600' }}  flex items-center justify-center text-white text-xs font-bold shadow-lg"
                                             title="{{ $user['name'] ?? 'Пользователь' }}">
                                            {{ $user['initials'] ?? '??' }}
                                        </div>
                                        <div class="online-indicator"></div>
                                    </div>
                                @endforeach
                                @php
                                    $moreOnline = $onlineUsersCount - min(3, $onlineUsers->count());
                                @endphp
                                @if($moreOnline > 0)
                                    <div class="w-8 h-8 rounded-full bg-sidebar-hover flex items-center justify-center text-sidebar-text text-xs font-bold shadow-lg"
                                         title="Еще {{ $moreOnline }} онлайн">
                                        +{{ $moreOnline }}
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div>
                            <div class="text-white font-medium text-sm">{{ $onlineUsersCount }} онлайн</div>
                            <div class="text-sidebar-text text-xs flex-shrink-0 whitespace-nowrap">Активные сейчас</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-2">
                        <i class="fas fa-users text-sidebar-text text-lg mb-1"></i>
                        <p class="text-xs text-sidebar-text">Сейчас никого нет в сети</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Нижняя часть -->
        <div class="mt-auto space-y-4 pt-4 border-t border-white/10">
            <!-- Файловое хранилище -->
            <div class="bg-transparent/10 p-4 rounded-xl border border-white/5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-hard-drive text-primary-500"></i>
                        <h6 class="font-medium text-white text-sm">Хранилище</h6>
                        <!-- Иконка типа подписки -->
                        <i class="fas {{ $licenseIcon }} {{ $licenseColor }} text-xs" title="{{ $licenseText }}"></i>
                    </div>
                    <span class="text-xs text-sidebar-text">{{ $usagePercentage }}%</span>
                </div>

                <!-- Прогресс бар -->
                <div class="w-full bg-white/10 rounded-full h-1.5 mb-2 overflow-hidden">
                    <div class="progress-bar h-full rounded-full transition-all duration-300"
                         style="width: {{ min($usagePercentage, 100) }}%;
                                background: {{ $usagePercentage > 90 ? '#ef4444' : ($usagePercentage > 70 ? '#f59e0b' : '#22c55e') }}">
                    </div>
                </div>

                <!-- Информация об использовании -->
                <div class="text-xs text-sidebar-text space-y-1">
                    <div class="flex justify-between">
                        <span>Использовано:</span>
                        <span class="text-white">{{ $usedStorageFormatted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Всего:</span>
                        <span class="text-white">{{ $totalStorageFormatted }}</span>
                    </div>

                    <!-- Предупреждение о переполнении -->
                    @if($storageUsage && $storageUsage->isStorageLimitExceeded())
                        <div class="mt-2 pt-1 border-t border-red-500/30">
                            <p class="text-red-400 text-xs flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Лимит хранилища превышен!</span>
                            </p>
                        </div>
                    @elseif($usagePercentage > 80)
                        <div class="mt-2 pt-1 border-t border-yellow-500/30">
                            <p class="text-yellow-400 text-xs flex items-center gap-1">
                                <i class="fas fa-clock"></i>
                                <span>Заканчивается место в хранилище</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
   <button class="sidebar-toggle-btn">
    <i class="fas fa-chevron-left"></i>
</button>
</div>
<!-- Оверлей для боковой панели -->
<div id="sidebar-overlay"
     class="fixed inset-0 bg-black/50 z-[998] hidden max-[638px]:[&.active]:block transition-opacity duration-300 max-[500px]:bg-black/90">
</div>

@once
    <!-- sidebar styles -->
    <style>
        .main-container.has-background {
            .nav-item {
                position: relative;
                transition: all 0.3s ease;
                border-radius: 12px;
                overflow: hidden;
            }

            .nav-item::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: 3px;
                background: linear-gradient(180deg, #22c55e, #16a34a);
                transform: scaleY(0);
                transition: transform 0.3s ease;
            }

            .nav-item:hover::before {
                transform: scaleY(1);
            }

            .nav-item.active::before {
                transform: scaleY(1);
            }

            .nav-item.active {
                background: rgba(34, 197, 94, 0.1);
            }

            .sidebar:hover {
                box-shadow: 0 0 50px rgba(34, 197, 94, 0.1);
            }
            .sidebar-toggle-btn {
                background: none;
            }
        }

        .nav-item {
            overflow: hidden;
            flex-shrink: 0;
        }

        .nav-item.active {
            transform: translateX(5px);
            color: #fff;
            background-color: rgb(0 0 0 / 0.2);
            border-radius: 0.5rem;
        }

        .nav-item>span {
            white-space: nowrap;
        }

        .nav-item:hover {
            transform: translateX(5px);
        }

        .sidebar {
            background: linear-gradient(180deg, #1a1f2e 0%, #161b28 100%);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;

            &.glass {
                backdrop-filter: blur(10px) saturate(160%);
                background: none;
                -webkit-backdrop-filter: blur(10px) saturate(160%);
                box-shadow: 10px 0 15px -3px rgba(0, 0, 0, 0.1);
                border-right: 1px solid rgba(255, 255, 255, 0.1);
            }
        }

        /* Стили sidebar когда фон выбран - убираем background */
        .sidebar.no-background {
            background: transparent !important;
            backdrop-filter: none !important;
            box-shadow: none !important;
        }

        /* ------v */
        .sidebar.glass {
            backdrop-filter: blur(10px) saturate(160%);
            background: none;
            -webkit-backdrop-filter: blur(10px) saturate(160%);
            box-shadow: 10px 0 15px -3px rgba(0, 0, 0, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
.sidebar-toggle-btn {
    position: absolute;
    top: 60px;
    right: -20px;
    z-index: 10;
    width: 20px;
    height: 32px;
    border: none;
    border-radius: 0 0.5rem 0.5rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #000000;
    color: var(--sidebar-text, #a0aec0);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    cursor: pointer;
    transition: color 0.15s ease-in-out;
}

.sidebar-toggle-btn i {
    font-size: 14px;
    padding-left: 2px;
}

.sidebar-toggle-btn:hover {
    color: #ffffff;
}

        /* Анимация для progress-bar */
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        @media(min-width: 638px) {
            .sidebar {
                transition: max-width 0.3s ease-in-out;
            }

            .main-container.sidebar-mode-collapsed {
                .sidebar {
                    max-width: 4.5rem !important;

                    .logotype__text {
                        visibility: hidden;
                        opacity: 0;
                        max-width: 0;
                        max-height: 0;
                        transition: all 0.3s ease;
                    }

                    .tags {
                        visibility: hidden;
                        opacity: 0;
                        max-width: 0;
                        max-height: 0;
                        transition: all 0.3s ease;
                        position: absolute;
                    }
                }
.sidebar-toggle-btn i {
    transform: rotate(180deg);
}
                .active-page {
                    padding-left: calc(1.5rem + 4.5rem);
                }

                .nav-item {
                    padding-left: 0;
                    padding-right: 0;
                    justify-content: center;

                }

                .nav-item.active {
                    transform: none !important;
                    color: #fff;
                    border-radius: 0.5rem;
                    background-color: rgb(0 0 0 / 0.2);
                }

                .nav-item span {
                    visibility: hidden;
                    opacity: 0;
                    max-width: 0;
                    max-height: 0;
                    transition: all 0.3s ease;
                }

                .nav-item>div {
                    margin-right: 0;
                }

                .nav-item:hover {
                    transform: scale(1.1);
                    background: none;
                }

                .online-users {
                    h3 {
                        visibility: hidden;
                        opacity: 0;
                        max-width: 0;
                        max-height: 0;
                        transition: all 0.3s ease;
                    }
                }

                .tags {
                    h3 {
                        visibility: hidden;
                        opacity: 0;
                        max-width: 0;
                        max-height: 0;
                        transition: all 0.3s ease;
                    }

                    p {
                        visibility: hidden;
                        opacity: 0;
                        max-width: 0;
                        max-height: 0;
                        transition: all 0.3s ease;
                    }
                }

                .user-profile__name {
                    visibility: hidden;
                    opacity: 0;
                    max-width: 0;
                    max-height: 0;
                    transition: all 0.3s ease;
                }

                .user-profile__arrow-icon {
                    visibility: hidden;
                    opacity: 0;
                    max-width: 0;
                    max-height: 0;
                    transition: all 0.3s ease;
                }
                .sidebar:hover {
                    max-width: 16rem !important;
                    z-index: 10;
                    .logotype__text,
                    .tags,
                    .tags h3,
                    .nav-item span,
                    .online-users h3,
                    .tags p,
                    .user-profile__name,
                    .user-profile__arrow-icon {
                        visibility: visible;
                        opacity: 1;
                        max-width: none;
                        max-height: none;
                    }
                    .tags {
                        position: unset;
                    }
                    .nav-item {
                        padding-left: 1rem;
                        padding-right: 1rem;
                        justify-content: start;
                        gap: 0.75rem;

                    }
                    .nav-item:hover {
                        transform: translateX(5px);
                        color: #fff;
                        background-color: rgb(0 0 0 / 0.2);
                        border-radius: 0.5rem;
                    }
                }
            }
            .main-container.has-background.sidebar-mode-collapsed {
                .nav-item.active{
                    background: rgba(34, 197, 94, 0.1);
                }
            }
            #logo-icon {
                pointer-events: all;
            }

            #sidebar-overlay {
                display: none !important;
            }
        }

        @media (max-width: 638px) {
            .sidebar {
                position: fixed !important;
                top: 0;
                left: -100%;
                height: 100%;
                z-index: 999;
                width: 20rem !important;
                padding-bottom: 0.5rem;

                transition-property: left;
                transition-duration: 300ms;
                transition-timing-function: ease-in-out;
                box-shadow: none;
            }

            .sidebar.active {
                left: 0;
            }
            .sidebar-toggle-btn {
                display: none !important;
            }
        }

        @media (max-width: 500px) {
            .sidebar {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // const logoIcon = document.getElementById('logo-icon');
            const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
            const mainContainer = document.querySelector('.main-container');
            const navItems = document.querySelectorAll('.nav-item');

            const isCollapsed = localStorage.getItem('sidebar-mode-collapsed') === 'true';

            if (isCollapsed && mainContainer) {
                mainContainer.classList.add('sidebar-mode-collapsed');
            }


            if (sidebarToggleBtn && mainContainer) {
                sidebarToggleBtn.addEventListener('click', function () {
                    if (window.innerWidth > 638) {

                        mainContainer.classList.toggle('sidebar-mode-collapsed');


                        const currentlyCollapsed = mainContainer.classList.contains('sidebar-mode-collapsed');

                        localStorage.setItem('sidebar-mode-collapsed', currentlyCollapsed);
                    }
                });
            }

            const currentPath = window.location.pathname;
            navItems.forEach(item => {
                console.log(item);
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('active');
                }
            });
        });
    </script>
@endonce
