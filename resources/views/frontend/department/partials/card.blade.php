@if($backgroundEnabled && $backgroundImage)
    <div class="backdrop-blur-md bg-transparent/20 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden department-card"
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
                            <h3 class="font-bold text-xl text-white">{{ $department->name }}</h3>
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
                            <div class="border-t border-gray-100"></div>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               onclick="openEditDepartmentModal({{ $department->id }})">
                                <i class="fas fa-edit mr-3 text-primary"></i>
                                Редактировать
                            </a>
                            <a href="javascript:void(0);" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"
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
                <div class="backdrop-blur-md bg-transparent/10 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white">Активных задач</p>
                            <p class="text-2xl font-bold text-gray-500">{{ $department->getActiveTasksCount() }}</p>
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

                {{--                            <div class="bg-gray-50 rounded-lg p-4">--}}
                {{--                                <div class="flex items-center justify-between">--}}
                {{--                                    <div>--}}
                {{--                                        <p class="text-sm text-gray-500">Почта</p>--}}
                {{--                                        <p class="text-2xl font-bold text-gray-800">{{ $department->getEmailCount() }}</p>--}}
                {{--                                    </div>--}}
                {{--                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">--}}
                {{--                                        <i class="fas fa-envelope text-purple-500"></i>--}}
                {{--                                    </div>--}}
                {{--                                </div>--}}
                {{--                                @if($department->getUnreadEmailCount() > 0)--}}
                {{--                                    <p class="text-xs text-blue-500 mt-2">--}}
                {{--                                        <i class="fas fa-circle mr-1"></i>--}}
                {{--                                        {{ $department->getUnreadEmailCount() }} непрочитанных--}}
                {{--                                    </p>--}}
                {{--                                @endif--}}
                {{--                            </div>--}}

                <div class="backdrop-blur-md bg-transparent/10 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white">Сотрудники</p>
                            <p class="text-2xl font-bold text-gray-500">{{ $department->getUsersCount() }}</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-green-500"></i>
                        </div>
                    </div>
                </div>

                <div class="backdrop-blur-md bg-transparent/10 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-white">Файлы</p>
                            <p class="text-2xl font-bold text-gray-500">{{ $department->files()->count() }}</p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-folder text-yellow-500"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информация о руководителе и последней активности -->
            <div class="border-gray-100 pt-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        @if($department->supervisor)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold mr-2 backdrop-blur-md bg-transparent/10">
                                    {{ substr($department->supervisor->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $department->supervisor->name }}</p>
                                    <p class="text-xs text-gray-500">Руководитель</p>
                                </div>
                            </div>
                        @else
                            <div class="text-white text-sm">
                                <i class="fas fa-user-slash mr-2"></i>
                                Руководитель не назначен
                            </div>
                        @endif
                    </div>

                    {{--                                <div class="text-sm text-gray-500">--}}
                    {{--                                    @if($department->emails()->count() > 0)--}}
                    {{--                                        @php--}}
                    {{--                                            $lastEmail = $department->emails()->latest()->first();--}}
                    {{--                                        @endphp--}}
                    {{--                                        <i class="fas fa-clock mr-1"></i>--}}
                    {{--                                        Последнее письмо: {{ $lastEmail->created_at->diffForHumans() }}--}}
                    {{--                                    @else--}}
                    {{--                                        <i class="fas fa-inbox mr-1"></i>--}}
                    {{--                                        Писем нет--}}
                    {{--                                    @endif--}}
                    {{--                                </div>--}}
                </div>
            </div>
        </div>

        <!-- Футер с участниками -->
        <div class="bg-transparent/20 px-6 py-4 border-none">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-white mb-2">Участники отдела</p>
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
{{--                    <a href=""--}}
{{--                       class="bg- text-white border border-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 hover:text-gray-800 transition-colors">--}}
{{--                        Подробнее--}}
{{--                    </a>--}}
                </div>
            </div>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden department-card"
         data-name="{{ strtolower($department->name) }}"
         data-status="{{ $department->status }}">
        <div class="p-6">
            <!-- Заголовок отдела -->
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-gray-800 text-xl"></i>
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
                            <div class="border-t border-gray-100"></div>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               onclick="openEditDepartmentModal({{ $department->id }})">
                                <i class="fas fa-edit mr-3 text-primary"></i>
                                Редактировать
                            </a>
                            <a href="javascript:void(0);" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50"
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

                {{--                            <div class="bg-gray-50 rounded-lg p-4">--}}
                {{--                                <div class="flex items-center justify-between">--}}
                {{--                                    <div>--}}
                {{--                                        <p class="text-sm text-gray-500">Почта</p>--}}
                {{--                                        <p class="text-2xl font-bold text-gray-800">{{ $department->getEmailCount() }}</p>--}}
                {{--                                    </div>--}}
                {{--                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">--}}
                {{--                                        <i class="fas fa-envelope text-purple-500"></i>--}}
                {{--                                    </div>--}}
                {{--                                </div>--}}
                {{--                                @if($department->getUnreadEmailCount() > 0)--}}
                {{--                                    <p class="text-xs text-blue-500 mt-2">--}}
                {{--                                        <i class="fas fa-circle mr-1"></i>--}}
                {{--                                        {{ $department->getUnreadEmailCount() }} непрочитанных--}}
                {{--                                    </p>--}}
                {{--                                @endif--}}
                {{--                            </div>--}}

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

                    {{--                                <div class="text-sm text-gray-500">--}}
                    {{--                                    @if($department->emails()->count() > 0)--}}
                    {{--                                        @php--}}
                    {{--                                            $lastEmail = $department->emails()->latest()->first();--}}
                    {{--                                        @endphp--}}
                    {{--                                        <i class="fas fa-clock mr-1"></i>--}}
                    {{--                                        Последнее письмо: {{ $lastEmail->created_at->diffForHumans() }}--}}
                    {{--                                    @else--}}
                    {{--                                        <i class="fas fa-inbox mr-1"></i>--}}
                    {{--                                        Писем нет--}}
                    {{--                                    @endif--}}
                    {{--                                </div>--}}
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
@endif
