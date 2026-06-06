@if($backgroundEnabled && $backgroundImage)
    <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 min-[1370px]:grid-cols-4 min-[1600px]:grid-cols-6 gap-3 md:gap-6 mb-6 md:mb-8">
        <!-- Всего задач -->
        <div
            class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <a href="{{route('allTeamTasks')}}">
                        <h3 class="font-bold text-sm md:text-lg text-white">Всего задач</h3>
                    </a>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tasks text-blue-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['total'] }}</div>
                <div class="text-white text-md underline">
                    <a href="{{route('allTeamTasks')}}">Все задачи</a>
                </div>
            </div>
        </div>

        <!-- Назначены -->
        <div
            class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-white">Назначены</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-check text-purple-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['assigned'] }}</div>
        </div>

        <!-- В работе -->
        <div
            class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-white">В работе</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cogs text-white text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['in_progress'] }}</div>
        </div>

        <!-- На проверке -->
        <div
            class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-white">На проверке</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-search text-yellow-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['review'] }}</div>
        </div>

        <!-- Выполнено -->
        <div
            class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-white">Выполнено</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['completed'] }}</div>
        </div>

        <!-- Просрочено -->
        <div
            class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-white">Просрочено</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-transparent/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-2xl font-bold text-white">{{ $stats['overdue'] }}</div>
        </div>
    </div>
@else
    <div
        class="grid grid-cols-1 min-[550px]:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 min-[1370px]:grid-cols-4 min-[1600px]:grid-cols-6 gap-3 md:gap-6 mb-6 md:mb-8">
        <!-- Всего задач -->
        <div
            class=" bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-gray-800">Всего задач</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tasks text-blue-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-3xl font-bold" style="color: #16a34a;">{{ $stats['total'] }}</div>
        </div>

        <!-- Назначены -->
        <div
            class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-gray-800">Назначены</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-check text-purple-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-3xl font-bold text-purple-600">{{ $stats['assigned'] }}</div>
        </div>

        <!-- В работе -->
        <div
            class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-gray-800">В работе</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cogs text-orange-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-3xl font-bold text-orange-600">{{ $stats['in_progress'] }}</div>
        </div>

        <!-- На проверке -->
        <div
            class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-gray-800">На проверке</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-search text-yellow-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-3xl font-bold text-yellow-600">{{ $stats['review'] }}</div>
        </div>

        <!-- Выполнено -->
        <div
            class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-gray-800">Выполнено</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
        </div>

        <!-- Просрочено -->
        <div
            class="backdrop-blur-md bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6 card-hover flex flex-col justify-between max-[500px]:flex-row max-[500px]:items-center max-[500px]:py-2">
            <div
                class="flex items-start justify-between mb-3 md:mb-4 max-[500px]:gap-2 max-[500px]:items-center">
                <div>
                    <h3 class="font-bold text-sm md:text-lg text-gray-800">Просрочено</h3>
                </div>
                <div
                    class="w-8 h-8 md:w-12 md:h-12 bg-white rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-600 text-sm md:text-xl"></i>
                </div>
            </div>
            <div class="text-xl md:text-3xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
        </div>
    </div>
@endif
