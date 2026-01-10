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

            <div class="flex flex-col">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('password.request') }}">
                        {{ __('Забыли пароль?') }}
                    </a>
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 " href="{{ route('register') }}">
                        {{ __('Зарегистрироваться') }}
                    </a>
                @endif
            </div>

            <div>


                <x-primary-button class="ms-3">
                    {{ __('Войти') }}
                </x-primary-button>
            </div>
        </div>

    </form>

    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Или войдите через</span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
            <!-- VK -->
            <a href="{{ route('auth.vkontakte.redirect') }}"
               class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.07 2H8.93C3.33 2 2 3.33 2 8.93V15.07C2 20.67 3.33 22 8.93 22H15.07C20.67 22 22 20.67 22 15.07V8.93C22 3.33 20.67 2 15.07 2M18.15 16.27H16.69C16.14 16.27 15.97 15.82 15 14.83C14.12 14 13.74 13.88 13.53 13.88C13.24 13.88 13.15 13.96 13.15 14.38V15.69C13.15 16.04 13.04 16.26 12.11 16.26C10.57 16.26 8.86 15.32 7.66 13.59C5.85 11.05 5.36 9.13 5.36 8.75C5.36 8.54 5.43 8.34 5.85 8.34H7.32C7.69 8.34 7.83 8.5 7.97 8.9C8.69 10.96 10.38 13.09 11.53 13.09C11.86 13.09 11.97 12.96 11.97 12.5V10.1C11.97 9.12 12.1 9 12.5 9H12.51C12.81 9 12.92 9.08 12.92 9.38V12.48C12.92 12.8 13.04 12.91 13.38 12.91C13.76 12.91 14.1 12.75 14.45 12.41C15.36 11.38 16 9.57 16 9.57C16.11 9.28 16.28 9.14 16.61 9.14H18.08C18.37 9.14 18.45 9.34 18.37 9.65C18.19 10.16 17.37 11.61 16.43 13.06C15.85 13.85 15.52 14.22 15.38 14.46C15.17 14.8 15.28 15.01 15.58 15.39C16.09 15.98 16.87 16.74 17.19 17.08C17.39 17.29 17.54 17.69 17.46 17.94C17.36 18.18 17.21 18.27 16.9 18.27H18.15Z"/>
                </svg>
                <span class="ml-2">ВКонтакте</span>
            </a>

            <!-- Yandex -->
            <a href="{{ route('auth.yandex.redirect') }}"
               class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <span class="ml-2">Яндекс</span>
            </a>
        </div>
    </div>

    <!-- Обработка OAuth ошибок -->
    @if(session('error'))
        <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif
</x-guest-layout>
