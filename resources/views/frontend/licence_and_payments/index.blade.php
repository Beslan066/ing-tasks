@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp

    <div id="license" class="container">
        <!-- Заголовок -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
            <div>
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white">Лицензия и оплата</h2>
                    <p class="text-white/70 mt-1">Управление подпиской и пользователями</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a]">Лицензия и оплата</h2>
                    <p class="text-gray-500 mt-1">Управление подпиской и пользователями</p>
                @endif
            </div>
        </div>

        <!-- Текущий план (статус подписки) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Карточка текущего плана -->
            @if($backgroundEnabled && $backgroundImage)
                <div class="lg:col-span-2 backdrop-blur-md bg-black/30 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Текущий план</h3>
                            <p class="text-white/70 text-sm mt-1">
                                @if(isset($currentPlan) && $currentPlan === 'premium')
                                    Премиум
                                @else
                                    Базовый
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            @if(isset($currentPlan) && $currentPlan === 'premium')
                                <span class="inline-block bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">Активен</span>
                                <p class="text-white/50 text-xs mt-2">Действует до: {{ $premiumUntil ?? '—' }}</p>
                            @else
                                <span class="inline-block bg-gray-500/20 text-gray-300 px-3 py-1 rounded-full text-sm">Бесплатный</span>
                                <p class="text-white/50 text-xs mt-2">До 5 пользователей</p>
                            @endif
                        </div>
                    </div>

                    <!-- Прогресс использования пользователей -->
                    <div class="mt-5">
                        <div class="flex justify-between text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white/70' : 'text-gray-600' }} mb-1">
                            <span>Использовано пользователей</span>
                            <span>{{ $usedUsers ?? 0 }} / {{ $maxUsers ?? 5 }}</span>
                        </div>
                        <div class="w-full {{ $backgroundEnabled && $backgroundImage ? 'bg-white/20' : 'bg-gray-200' }} rounded-full h-2">
                            @php
                                $userPercentage = $maxUsers > 0 ? ($usedUsers / $maxUsers) * 100 : 0;
                            @endphp
                            <div class="bg-[#16a34a] h-2 rounded-full" style="width: {{ min($userPercentage, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Прогресс хранилища -->
                    <div class="mt-4">
                        <div class="flex justify-between text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white/70' : 'text-gray-600' }} mb-1">
                            <span>Использовано хранилища</span>
                            <span>{{ number_format($usedStorageGB ?? 0, 2) }} ГБ / {{ $maxStorageGB ?? 2 }} ГБ</span>
                        </div>
                        <div class="w-full {{ $backgroundEnabled && $backgroundImage ? 'bg-white/20' : 'bg-gray-200' }} rounded-full h-2">
                            @php
                                $storagePercentage = $maxStorageGB > 0 ? ($usedStorageGB / $maxStorageGB) * 100 : 0;
                            @endphp
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min($storagePercentage, 100) }}%"></div>
                        </div>
                    </div>

                    @php
                        $pendingPayment = \App\Models\Payment::where('company_id', $company->id)
                            ->where('status', 'pending')
                            ->latest()
                            ->first();
                    @endphp

                    @if($pendingPayment && $currentPlan !== 'premium')
                        <div class="mt-5 pt-4 border-t border-yellow-500/30">
                            <div class="bg-yellow-500/10 rounded-lg p-4 border border-yellow-500/30">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-hourglass-half text-yellow-400 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-yellow-300 text-sm font-medium mb-2">
                                            У вас есть незавершенный платеж на сумму {{ number_format($pendingPayment->amount, 2) }} ₽
                                        </p>
                                        <p class="text-yellow-200/70 text-xs mb-3">
                                            ID платежа: {{ $pendingPayment->provider_payment_id }}
                                        </p>
                                        <div class="flex gap-3">
                                            <a href="{{ route('licence.payment.activate', $pendingPayment->id) }}"
                                               class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm transition">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Активировать подписку
                                            </a>
                                            <a href="{{ route('licence.payment.check', $pendingPayment->id) }}"
                                               class="inline-flex items-center px-4 py-2 bg-gray-500/30 hover:bg-gray-500/50 text-white rounded-lg text-sm transition">
                                                <i class="fas fa-sync-alt mr-2"></i>
                                                Проверить статус
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Доступные инструменты -->
                    <div class="mt-5 pt-4 border-t border-white/20">
                        <h4 class="text-white font-medium mb-2">Доступные возможности</h4>
                        <div class="flex flex-wrap gap-2">
                            @if(isset($features) && count($features) > 0)
                                @foreach($features as $feature)
                                    <span class="text-xs bg-white/10 text-white/80 px-2 py-1 rounded">✓ {{ $feature }}</span>
                                @endforeach
                            @else
                                <span class="text-xs bg-white/10 text-white/80 px-2 py-1 rounded">✓ Управление задачами</span>
                                <span class="text-xs bg-white/10 text-white/80 px-2 py-1 rounded">✓ Мессенджер</span>
                                <span class="text-xs bg-white/10 text-white/80 px-2 py-1 rounded">✓ Команды и проекты</span>
                                <span class="text-xs bg-white/10 text-white/80 px-2 py-1 rounded">✓ Файловое хранилище (2 ГБ)</span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Текущий план</h3>
                            <p class="text-gray-500 text-sm mt-1">
                                @if(isset($currentPlan) && $currentPlan === 'premium')
                                    Премиум
                                @else
                                    Базовый
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            @if(isset($currentPlan) && $currentPlan === 'premium')
                                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Активен</span>
                                <p class="text-gray-400 text-xs mt-2">Действует до: {{ $premiumUntil ?? '—' }}</p>
                            @else
                                <span class="inline-block bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">Бесплатный</span>
                                <p class="text-gray-400 text-xs mt-2">До 5 пользователей</p>
                            @endif
                        </div>
                    </div>

                    <!-- Прогресс использования пользователей -->
                    <div class="mt-5">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Использовано пользователей</span>
                            <span>{{ $usedUsers ?? 0 }} / {{ $maxUsers ?? 5 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-[#16a34a] h-2 rounded-full" style="width: {{ ($usedUsers ?? 0) / ($maxUsers ?? 5) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Прогресс хранилища -->
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Использовано хранилища</span>
                            <span>{{ number_format($usedStorageGB ?? 0, 2) }} ГБ / {{ $maxStorageGB ?? 2 }} ГБ</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($usedStorageGB ?? 0) / ($maxStorageGB ?? 2) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Доступные инструменты -->
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <h4 class="text-gray-700 font-medium mb-2">Доступные возможности</h4>
                        <div class="flex flex-wrap gap-2">
                            @if(isset($features) && count($features) > 0)
                                @foreach($features as $feature)
                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">✓ {{ $feature }}</span>
                                @endforeach
                            @else
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">✓ Управление задачами</span>
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">✓ Мессенджер</span>
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">✓ Команды и проекты</span>
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">✓ Файловое хранилище (2 ГБ)</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Действия с подпиской -->
            @if($backgroundEnabled && $backgroundImage)
                <div class="backdrop-blur-md bg-black/30 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 flex flex-col justify-between">
                    <div>
                        <h4 class="text-white font-bold text-lg">Нужно больше?</h4>
                        <p class="text-white/70 text-sm mt-2">Обновитесь до Премиум или добавьте пользователей</p>
                    </div>
                    <div class="mt-5 space-y-3">
                        <button onclick="openUpgradeModal()" class="w-full bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-2 px-4 rounded-lg transition">
                            🚀 Обновить до Премиум
                        </button>
                        <button onclick="openAddUsersModal()" class="w-full bg-white/10 hover:bg-white/20 text-white font-medium py-2 px-4 rounded-lg transition border border-white/30">
                            👥 Добавить пользователей
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 flex flex-col justify-between">
                    <div>
                        <h4 class="text-gray-800 font-bold text-lg">Нужно больше?</h4>
                        <p class="text-gray-500 text-sm mt-2">Обновитесь до Премиум или добавьте пользователей</p>
                    </div>
                    <div class="mt-5 space-y-3">
                        <button onclick="openUpgradeModal()" class="w-full bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-2 px-4 rounded-lg transition">
                            🚀 Обновить до Премиум
                        </button>
                        <button onclick="openAddUsersModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-lg transition border border-gray-200">
                            👥 Добавить пользователей
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Сравнение тарифов МенеджерПлюс -->
        <div class="mb-8">
            @if($backgroundEnabled && $backgroundImage)
                <h3 class="text-xl font-bold text-white mb-4">Сравнение тарифов</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Базовый тариф -->
                    <div class="backdrop-blur-md bg-black/30 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 border border-white/20">
                        <h4 class="text-xl font-bold text-white">Базовый</h4>
                        <p class="text-3xl font-bold text-white mt-3">0 ₽<span class="text-sm font-normal text-white/60">/мес</span></p>
                        <ul class="mt-4 space-y-2 text-white/80 text-sm">
                            <li>✓ До 5 пользователей</li>
                            <li>✓ Файловое хранилище до 2 ГБ</li>
                            <li>✓ Мессенджер</li>
                            <li>✓ Расширенная аналитика</li>
                            <li class="text-white/40">✗ Приоритетная поддержка</li>
                            <li class="text-white/40">✗ Ограниченный набор инструментов</li>
                        </ul>
                    </div>
                    <!-- Премиум тариф -->
                    <div class="backdrop-blur-md bg-black/30 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 border border-[#16a34a]/50 relative">
                        <div class="absolute -top-3 left-4 bg-[#16a34a] text-white text-xs px-3 py-1 rounded-full">Рекомендуем</div>
                        <h4 class="text-xl font-bold text-white">Премиум</h4>
                        <p class="text-3xl font-bold text-white mt-3">2 490 ₽<span class="text-sm font-normal text-white/60">/мес</span></p>
                        <p class="text-white/50 text-xs mt-1">+400 ₽ за доп. пользователя (свыше 15)</p>
                        <ul class="mt-4 space-y-2 text-white/80 text-sm">
                            <li>✓ До 15 пользователей (далее +400 ₽/мес)</li>
                            <li>✓ Файловое хранилище до 1 ТБ</li>
                            <li>✓ Приоритетная поддержка 24/7</li>
                            <li>✓ Мессенджер</li>
                            <li>✓ Полный набор инструментов</li>
                            <li>✓ СМС-оповещения для руководителей о статусе задач</li>
                        </ul>
                        <button onclick="openUpgradeModal()" class="mt-5 w-full bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-2 rounded-lg transition">
                            Выбрать Премиум
                        </button>
                    </div>
                </div>
            @else
                <h3 class="text-xl font-bold text-gray-800 mb-4">Сравнение тарифов МенеджерПлюс</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Базовый тариф -->
                    <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 border border-gray-200">
                        <h4 class="text-xl font-bold text-gray-800">Базовый</h4>
                        <p class="text-3xl font-bold text-gray-800 mt-3">0 ₽<span class="text-sm font-normal text-gray-500">/мес</span></p>
                        <ul class="mt-4 space-y-2 text-gray-600 text-sm">
                            <li>✓ До 5 пользователей</li>
                            <li>✓ Файловое хранилище до 2 ГБ</li>
                            <li>✓ Мессенджер</li>
                            <li class="text-gray-400">✗ Расширенная аналитика</li>
                            <li class="text-gray-400">✗ Приоритетная поддержка</li>
                            <li class="text-gray-400">✗ API доступ</li>
                            <li class="text-gray-400">✗ Управление ролями</li>
                            <li class="text-gray-400">✗ Интеграции</li>
                        </ul>
                    </div>
                    <!-- Премиум тариф -->
                    <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 border border-[#16a34a]/30 relative">
                        <div class="absolute -top-3 left-4 bg-[#16a34a] text-white text-xs px-3 py-1 rounded-full">Рекомендуем</div>
                        <h4 class="text-xl font-bold text-gray-800">Премиум</h4>
                        <p class="text-3xl font-bold text-gray-800 mt-3">2 490 ₽<span class="text-sm font-normal text-gray-500">/мес</span></p>
                        <p class="text-gray-400 text-xs mt-1">+400 ₽ за доп. пользователя (свыше 15)</p>
                        <ul class="mt-4 space-y-2 text-gray-600 text-sm">
                            <li>✓ До 15 пользователей (далее +400 ₽/мес)</li>
                            <li>✓ Файловое хранилище до 1 ТБ</li>
                            <li>✓ Приоритетная поддержка 24/7</li>
                            <li>✓ Полный набор инструментов:</li>
                            <ul class="ml-4 mt-1 space-y-1 text-gray-500 text-xs">
                                <li>• Управление задачами и проектами</li>
                                <li>• Мессенджер</li>
                                <li>• Расширенная аналитика и отчеты</li>
                                <li>• API доступ</li>
                                <li>• Управление ролями и правами</li>
                                <li>• Интеграции с сервисами</li>
                                <li>• Автоматизация процессов</li>
                                <li>• Календарь и планирование</li>
                                <li>• Мобильное приложение</li>
                                <li>• Экспорт данных</li>
                            </ul>
                        </ul>
                        <button onclick="openUpgradeModal()" class="mt-5 w-full bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-2 rounded-lg transition">
                            Выбрать Премиум
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- МОДАЛ: Обновление до Премиум -->
    <div id="upgradeModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 transition-all duration-300">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Обновление до Премиум</h3>
                <button onclick="closeUpgradeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Премиум подписка включает до 15 пользователей, 1 ТБ хранилища и все расширенные возможности.</p>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Выберите период</label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="premium_period" value="month" class="hidden peer" onchange="updatePremiumPrice()" checked>
                            <div class="border rounded-lg p-3 text-center peer-checked:border-[#16a34a] peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition">
                                <span class="font-medium">1 месяц</span>
                                <span class="block text-sm text-gray-500">2 490 ₽</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="premium_period" value="6months" class="hidden peer" onchange="updatePremiumPrice()">
                            <div class="border rounded-lg p-3 text-center peer-checked:border-[#16a34a] peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition">
                                <span class="font-medium">6 месяцев</span>
                                <span class="block text-sm text-gray-500">13 446 ₽</span>
                                <span class="text-xs text-green-600">-10%</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="premium_period" value="year" class="hidden peer" onchange="updatePremiumPrice()">
                            <div class="border rounded-lg p-3 text-center peer-checked:border-[#16a34a] peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition">
                                <span class="font-medium">12 месяцев</span>
                                <span class="block text-sm text-gray-500">25 398 ₽</span>
                                <span class="text-xs text-green-600">-15%</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="premiumLoadingIndicator" class="hidden mb-4 text-center py-2">
                    <i class="fas fa-spinner fa-spin text-[#16a34a] mr-2"></i>
                    <span class="text-gray-600 dark:text-gray-400">Создание платежа...</span>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span>Премиум подписка</span>
                        <span id="premiumBasePrice">2 490 ₽</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-[#16a34a]">
                        <span>Итого</span>
                        <span id="premiumTotalPrice">2 490 ₽</span>
                    </div>
                </div>

                <button onclick="processUpgrade()" id="premiumPayButton" class="w-full bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-2 rounded-lg transition">
                    Оплатить
                </button>
            </div>
        </div>
    </div>

    <!-- МОДАЛ: Добавление пользователей -->
    <div id="addUsersModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 transition-all duration-300">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Добавление пользователей</h3>
                <button onclick="closeAddUsersModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">Цена за одного дополнительного пользователя — 400 ₽/мес (свыше 15 пользователей).</p>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Количество дополнительных пользователей</label>
                    <input type="number" id="userCount" min="1" max="100" value="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#16a34a]" onchange="updateUserPrice()">
                    <p class="text-xs text-gray-400 mt-1">До 15 пользователей уже включены в Премиум</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Период</label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="user_period" value="month" class="hidden peer" onchange="updateUserPrice()" checked>
                            <div class="border rounded-lg p-3 text-center peer-checked:border-[#16a34a] peer-checked:bg-green-50 transition">
                                <span class="font-medium">1 месяц</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="user_period" value="6months" class="hidden peer" onchange="updateUserPrice()">
                            <div class="border rounded-lg p-3 text-center peer-checked:border-[#16a34a] peer-checked:bg-green-50 transition">
                                <span class="font-medium">6 месяцев</span>
                                <span class="text-xs text-green-600 block">-10%</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="user_period" value="year" class="hidden peer" onchange="updateUserPrice()">
                            <div class="border rounded-lg p-3 text-center peer-checked:border-[#16a34a] peer-checked:bg-green-50 transition">
                                <span class="font-medium">12 месяцев</span>
                                <span class="text-xs text-green-600 block">-15%</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="usersLoadingIndicator" class="hidden mb-4 text-center py-2">
                    <i class="fas fa-spinner fa-spin text-[#16a34a] mr-2"></i>
                    <span class="text-gray-600 dark:text-gray-400">Создание платежа...</span>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span>За дополнительных пользователей (400 ₽ × <span id="userCountDisplay">1</span>)</span>
                        <span id="userSubtotal">400 ₽</span>
                    </div>
                    <div class="flex justify-between text-sm text-green-600" id="userDiscountRow" style="display: none;">
                        <span>Скидка</span>
                        <span id="userDiscount">-0 ₽</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-[#16a34a] mt-2 pt-2 border-t border-gray-200">
                        <span>Итого</span>
                        <span id="userTotalPrice">400 ₽</span>
                    </div>
                </div>

                <button onclick="processAddUsers()" id="usersPayButton" class="w-full bg-[#16a34a] hover:bg-[#15803d] text-white font-medium py-2 rounded-lg transition">
                    Оплатить
                </button>
            </div>
        </div>
    </div>

    <script>
        // Модальные окна
        function openUpgradeModal() {
            document.getElementById('upgradeModal').classList.remove('hidden');
            document.getElementById('upgradeModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.querySelector('input[name="premium_period"][value="month"]').checked = true;
            updatePremiumPrice();
        }

        function closeUpgradeModal() {
            document.getElementById('upgradeModal').classList.add('hidden');
            document.getElementById('upgradeModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function openAddUsersModal() {
            document.getElementById('addUsersModal').classList.remove('hidden');
            document.getElementById('addUsersModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.getElementById('userCount').value = 1;
            document.querySelector('input[name="user_period"][value="month"]').checked = true;
            updateUserPrice();
        }

        function closeAddUsersModal() {
            document.getElementById('addUsersModal').classList.add('hidden');
            document.getElementById('addUsersModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Расчет цены Премиум
        function updatePremiumPrice() {
            const period = document.querySelector('input[name="premium_period"]:checked').value;
            let price = 2490;

            if (period === '6months') {
                price = 2490 * 6;
                price = price - (price * 0.10);
            } else if (period === 'year') {
                price = 2490 * 12;
                price = price - (price * 0.15);
            }

            document.getElementById('premiumBasePrice').innerText = (period === 'month' ? '2 490 ₽' : (period === '6months' ? '14 940 ₽' : '29 880 ₽'));
            document.getElementById('premiumTotalPrice').innerText = Math.round(price).toLocaleString() + ' ₽';
        }

        // Расчет цены пользователей
        function updateUserPrice() {
            const count = parseInt(document.getElementById('userCount').value) || 1;
            const period = document.querySelector('input[name="user_period"]:checked').value;
            const pricePerUser = 400;
            let subtotal = count * pricePerUser;
            let discount = 0;
            let total = subtotal;

            document.getElementById('userCountDisplay').innerText = count;

            if (period === '6months') {
                subtotal = count * pricePerUser * 6;
                discount = subtotal * 0.10;
                total = subtotal - discount;
                document.getElementById('userDiscountRow').style.display = 'flex';
                document.getElementById('userDiscount').innerText = '-' + Math.round(discount).toLocaleString() + ' ₽';
            } else if (period === 'year') {
                subtotal = count * pricePerUser * 12;
                discount = subtotal * 0.15;
                total = subtotal - discount;
                document.getElementById('userDiscountRow').style.display = 'flex';
                document.getElementById('userDiscount').innerText = '-' + Math.round(discount).toLocaleString() + ' ₽';
            } else {
                document.getElementById('userDiscountRow').style.display = 'none';
            }

            document.getElementById('userSubtotal').innerText = Math.round(subtotal).toLocaleString() + ' ₽';
            document.getElementById('userTotalPrice').innerText = Math.round(total).toLocaleString() + ' ₽';
        }

        // Обработка оплаты Премиум
        async function processUpgrade() {
            const period = document.querySelector('input[name="premium_period"]:checked').value;
            const button = document.getElementById('premiumPayButton');
            const loadingIndicator = document.getElementById('premiumLoadingIndicator');

            // Показываем индикатор загрузки
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Обработка...';
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("licence.payment.premium") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ period: period })
                });

                const data = await response.json();

                if (response.ok && data.success && data.payment_url) {
                    // Перенаправляем на страницу оплаты YooKassa
                    window.location.href = data.payment_url;
                } else {
                    throw new Error(data.error || 'Ошибка при создании платежа');
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('Ошибка: ' + error.message);

                // Сбрасываем кнопку
                button.disabled = false;
                button.innerHTML = 'Оплатить';
                loadingIndicator.classList.add('hidden');
            }
        }

        // Обработка оплаты дополнительных пользователей
        async function processAddUsers() {
            const count = parseInt(document.getElementById('userCount').value) || 1;
            const period = document.querySelector('input[name="user_period"]:checked').value;
            const button = document.getElementById('usersPayButton');
            const loadingIndicator = document.getElementById('usersLoadingIndicator');

            // Проверка лимита
            if (count < 1) {
                alert('Количество пользователей должно быть не менее 1');
                return;
            }

            if (count > 100) {
                alert('Максимальное количество дополнительных пользователей - 100');
                return;
            }

            // Показываем индикатор загрузки
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Обработка...';
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("licence.payment.additional-users") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        user_count: count,
                        period: period
                    })
                });

                const data = await response.json();

                if (response.ok && data.success && data.payment_url) {
                    // Перенаправляем на страницу оплаты YooKassa
                    window.location.href = data.payment_url;
                } else {
                    throw new Error(data.error || 'Ошибка при создании платежа');
                }
            } catch (error) {
                console.error('Payment error:', error);
                alert('Ошибка: ' + error.message);

                // Сбрасываем кнопку
                button.disabled = false;
                button.innerHTML = 'Оплатить';
                loadingIndicator.classList.add('hidden');
            }
        }

        // Закрытие по клику на фон
        document.getElementById('upgradeModal').addEventListener('click', function(e) {
            if (e.target === this) closeUpgradeModal();
        });
        document.getElementById('addUsersModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddUsersModal();
        });

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUpgradeModal();
                closeAddUsersModal();
            }
        });
    </script>
@endsection
