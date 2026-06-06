<div class="mb-6 md:mb-8">
    @if($backgroundEnabled && $backgroundImage)
        <div class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-building-columns text-3xl text-white"></i>
                        <h2 class="text-2xl md:text-3xl font-bold text-white">{{ $company->name }}</h2>
                        @if($company->verified)
                            <i class="fas fa-check-circle text-blue-400 text-xl" title="Верифицирована"></i>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm text-white/80">
                        @if($company->phone)
                            <span><i class="fas fa-phone mr-1"></i> {{ $company->phone }}</span>
                        @endif
                        <span><i class="fas fa-users mr-1"></i> Сотрудников: {{ $company->getActiveUsersCount() }}</span>
                        <span><i class="fas fa-tasks mr-1"></i> Всего задач: {{ $company->getTasksCount() }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <span class="text-sm text-white/70">Тарифный план:</span>
                        <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold
                                        @if($company->license_type === 'premium') bg-gradient-to-r from-purple-500 to-pink-500 text-white
                                        @elseif($company->license_type === 'optimal') bg-blue-500 text-white
                                        @else bg-gray-600 text-white @endif">
                                        {{ $company->getLicenseTypeName() }}
                                    </span>
                    </div>
                    <div class="text-sm text-white/70">
                        <i class="fas fa-database mr-1"></i>
                        Хранилище:
                        @php
                            $usedBytes = $company->getStorageStats()['used'] ?? 0;
                            $limitBytes = $company->getStorageLimit();
                            $usedGB = round($usedBytes / 1073741824, 2);
                            $limitGB = $company->license_type === 'premium' ? 1024 : 2;
                        @endphp
                        @if($usedGB < 1)
                            {{ round($usedBytes / 1048576, 2) }} МБ / {{ $limitGB }} ГБ
                        @else
                            {{ number_format($usedGB, 2) }} ГБ / {{ $limitGB }} ГБ
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 border border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-building text-3xl text-green-600"></i>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $company->name }}</h2>
                        @if($company->verified)
                            <i class="fas fa-check-circle text-blue-500 text-xl" title="Верифицирована"></i>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                        @if($company->phone)
                            <span><i
                                    class="fas fa-phone mr-1 text-green-500"></i> {{ $company->phone }}</span>
                        @endif
                        <span><i class="fas fa-users mr-1 text-green-500"></i> Сотрудников: {{ $company->getActiveUsersCount() }}</span>
                        <span><i class="fas fa-tasks mr-1 text-green-500"></i> Всего задач: {{ $company->getTasksCount() }}</span>
                    </div>
                </div>
                <div>
                    @if($company->license_type !== 'premium')
                        <button onclick="openUpgradeModal()"
                                class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-2 px-4 md:py-3 md:px-6 rounded-lg shadow-lg transition duration-300 transform hover:scale-105 flex items-center gap-2 text-sm md:text-base">
                            <i class="fas fa-crown"></i>
                            <span>Улучшить подписку</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @else
                        <span
                            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-2 px-4 md:py-3 md:px-6 rounded-lg shadow-lg inline-flex items-center gap-2 text-sm md:text-base">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Премиум</span>
                                        <i class="fas fa-star"></i>
                                    </span>
                    @endif
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <span class="text-sm text-gray-600">Тарифный план:</span>
                        <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold
                                        @if($company->license_type === 'premium') bg-gradient-to-r from-purple-500 to-pink-500 text-white
                                        @elseif($company->license_type === 'optimal') bg-blue-500 text-white
                                        @else bg-gray-500 text-white @endif">
                                        {{ $company->getLicenseTypeName() }}
                                    </span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-database mr-1 text-green-500"></i>
                        Хранилище:
                        @php
                            $usedBytes = $company->getStorageStats()['used'] ?? 0;
                            $limitBytes = $company->getStorageLimit();
                            $usedGB = round($usedBytes / 1073741824, 2);
                            $limitGB = $company->license_type === 'premium' ? 1024 : 2;
                        @endphp
                        @if($usedGB < 1)
                            {{ round($usedBytes / 1048576, 2) }} МБ / {{ $limitGB }} ГБ
                        @else
                            {{ number_format($usedGB, 2) }} ГБ / {{ $limitGB }} ГБ
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
