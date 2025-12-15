<x-guest-layout>
    <div class=" mb-2 flex items-center w-100">
        <h1 class="text-2xl font-bold text-dark">Менеджер<span class="text-green-600">Плюс</span></h1>
    </div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" >
        @csrf

        <!-- Email Address -->
        <div >
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500 focus:border-green-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Запомнить меня') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4 justify-between">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('password.request') }}">
                    {{ __('Забыли пароль?') }}
                </a>
            @endif

            <div>

                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 " href="{{ route('register') }}">
                    {{ __('Зарегистрироваться') }}
                </a>
                <x-primary-button class="ms-3">
                    {{ __('Войти') }}
                </x-primary-button>
            </div>
        </div>

        <div class="mt-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Или войдите через</span>
                </div>
            </div>

{{--            <div class="mt-6 grid grid-cols-3 gap-3">--}}
{{--                <!-- ВКонтакте -->--}}
{{--                <a --}}
{{--                   class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">--}}
{{--                    <span class="sr-only">Войти через ВКонтакте</span>--}}
{{--                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">--}}
{{--                        <path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.408 0 15.684 0zm3.692 17.28h-1.729c-.612 0-.803-.425-1.729-1.43-.674-.705-1.093-.78-1.272-.78-.306 0-.394.074-.394.511v1.302c0 .364-.124.612-1.093.612-1.64 0-3.368-1.006-4.719-2.86-1.861-2.433-2.558-4.282-2.558-4.626 0-.18.074-.347.48-.347h1.729c.361 0 .495.167.612.58.73 2.224 1.978 4.174 2.498 4.174.18 0 .255-.088.255-.556v-2.103c-.06-.993-.539-1.078-.539-1.43 0-.167.124-.306.346-.306h2.719c.288 0 .394.153.394.5v3.229c0 .306.088.394.18.394.18 0 .306-.088.612-.394.956-1.093 1.85-2.77 1.85-2.77.096-.18.288-.347.577-.347h1.729c.43 0 .52.215.43.48-.217.84-2.17 3.706-2.17 3.706-.167.236-.24.36 0 .65.153.236.667.71 1.006 1.144.667.868 1.176 1.595 1.31 2.098.09.36-.18.54-.48.54z"/>--}}
{{--                    </svg>--}}
{{--                </a>--}}

{{--                <!-- Яндекс -->--}}
{{--                <a--}}
{{--                   class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">--}}
{{--                    <span class="sr-only">Войти через Яндекс</span>--}}
{{--                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">--}}
{{--                        <path d="M12 0a12 12 0 1 0 12 12A12 12 0 0 0 12 0zm5.56 17.25a.5.5 0 0 1-.5.5h-1.5a.5.5 0 0 1-.5-.5v-4.5a.5.5 0 0 0-.5-.5h-1.5a.5.5 0 0 0-.5.5v4.5a.5.5 0 0 1-.5.5h-1.5a.5.5 0 0 1-.5-.5V6.75a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 .5.5v4.5a.5.5 0 0 0 .5.5h1.5a.5.5 0 0 0 .5-.5v-4.5a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 .5.5z"/>--}}
{{--                    </svg>--}}
{{--                </a>--}}

{{--                <!-- Дзен -->--}}
{{--                <a--}}
{{--                   class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">--}}
{{--                    <span class="sr-only">Войти через Дзен</span>--}}
{{--                    <span class="text-gray-700 font-semibold">Дзен</span>--}}
{{--                </a>--}}
{{--            </div>--}}
        </div>
    </form>
</x-guest-layout>
