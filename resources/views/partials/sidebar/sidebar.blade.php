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
                    <img src="{{asset('img/logo.svg')}}">
                </div>
                <div class="logotype__text">
                    <h1 class="text-xl font-bold text-white">Менеджер<span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Плюс</span></h1>
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
                    @if(auth()->user()->isLeader())
                        <a href="{{route('tasks.admin')}}"
                           class="nav-item flex items-center px-4 py-3 text-sidebar-text hover:text-white hover:rounded-lg hover:bg-transparent/20 {{request()->routeIs('tasks.admin*') ? 'active' : ''}}">
                            <div
                                class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-landmark text-purple-500 text-sm"></i>
                            </div>
                            <span class="font-medium {{ $backgroundEnabled && $backgroundImage ? 'text-white' : '' }}">Моя
                            компания</span>
                        </a>
                    @endif
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
                    <div class="flex items-center mb-3 flex-wrap">
                        <div class="flex -space-x-2 mr-3 ">
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
            <!-- Файловое хранилище -->
            <div class="storage bg-transparent/10 p-4 rounded-xl border border-white/5">
                <div class="storage__top_wrapper flex items-center justify-between mb-2">
                    <div class="storage__top flex items-center gap-2">
                        <i class="fas fa-hard-drive text-primary-500"></i>
                        <h6 class="storage__title font-medium text-white text-sm">Хранилище</h6>
                        <!-- Иконка типа подписки -->
                        <i class="fas {{ $licenseIcon ?? 'fa-circle' }} {{ $licenseColor ?? 'text-gray-400' }} text-xs" title="{{ $licenseText ?? 'Базовый (2 ГБ)' }}"></i>
                    </div>
                    <span class="text-xs text-sidebar-text">{{ $usagePercentage ?? 0 }}%</span>
                </div>

                <!-- Прогресс бар -->
                <div class="storage__progress-bar w-full bg-white/10 rounded-full h-1.5 mb-2 overflow-hidden">
                    <div class="progress-bar h-full rounded-full transition-all duration-300"
                         style="width: {{ min($usagePercentage ?? 0, 100) }}%;
                    background: {{ ($usagePercentage ?? 0) > 90 ? '#ef4444' : (($usagePercentage ?? 0) > 70 ? '#f59e0b' : '#22c55e') }}">
                    </div>
                </div>

                <!-- Информация об использовании -->
                <div class="storage__info text-xs text-sidebar-text space-y-1">
                    <div class="flex justify-between">
                        <span>Использовано:</span>
                        <span class="text-white">{{ $usedStorageFormatted ?? '0 B' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Всего:</span>
                        <span class="text-white">{{ $totalStorageFormatted ?? '0 B' }}</span>
                    </div>

                    <!-- Предупреждение о переполнении -->
                    @if(isset($storageUsage) && $storageUsage && $storageUsage->isStorageLimitExceeded())
                        <div class="mt-2 pt-1 border-t border-red-500/30">
                            <p class="text-red-400 text-xs flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Лимит хранилища превышен!</span>
                            </p>
                        </div>
                    @elseif(isset($usagePercentage) && $usagePercentage > 80)
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
     class="fixed inset-0 bg-black/50 z-[47] hidden max-[638px]:[&.active]:block transition-opacity duration-300 max-[500px]:bg-black/90">
</div>

<!-- sidebar styles & scripts -->
@once

    @push('scripts')
        @vite('resources/js/components/sidebar.js')
    @endpush
@endonce
