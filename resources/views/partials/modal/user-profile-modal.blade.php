<div id="userProfileModal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Профиль пользователя</h3>
            <button id="closeUserModal" class="text-gray-500 hover:text-gray-700" onclick="closeUserProfileModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="text-center mb-6">
            <div
                class="w-20 h-20 rounded-full bg-gradient-to-r from-primary to-secondary mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold">
                {{mb_substr(auth()->user()->name, 0,1)}}
            </div>
            <h3 class="font-bold text-lg">{{auth()->user()->name}}</h3>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Email:</span>
                <span class="font-medium">{{auth()->user()->email}}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Телефон:</span>
                <span class="font-medium">+7 (999) 123-45-67</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Организация:</span>
                @if(isset(auth()->user()->company))
                    <span class="font-medium">{{auth()->user()->company->name}}</span>

                @endif
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Роль:</span>
                @if(isset(auth()->user()->role))
                    <span class="font-medium">{{auth()->user()->role->name}}</span>
                @endif
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{route('profile.edit')}}" id="editProfile"
               class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
                Редактировать
            </a>
            <form action="{{route('logout')}}" method="post">
                @csrf
                <button id="logout"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                        type="submit">
                    Выйти
                </button>
            </form>
        </div>
    </div>
</div>
