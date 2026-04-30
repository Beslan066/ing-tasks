<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-32 w-80 h-80 bg-green-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse animation-delay-2000"></div>
        </div>

        <div class="max-w-md w-full space-y-6 relative z-10">
            <!-- Header -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="h-16 w-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-xl transform rotate-3">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
                <h2 class="text-4xl font-extrabold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Создать аккаунт
                </h2>
                <p class="mt-2 text-sm text-gray-600">Присоединяйтесь к МенеджерПлюс</p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Name Field -->
                <div class="space-y-1">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Имя и фамилия
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               class="pl-10 block w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150 ease-in-out"
                               placeholder="Алексей Иванов">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email Field -->
                <div class="space-y-1">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email адрес
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="pl-10 block w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150 ease-in-out"
                               placeholder="alex@company.ru">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Avatar Upload Field -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">
                        Аватар
                        <span class="text-xs text-gray-500 font-normal">(опционально)</span>
                    </label>

                    <div class="relative mt-1">
                        <!-- Preview Container -->
                        <div id="avatarPreviewContainer" class="hidden mb-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img id="avatarPreview"
                                         class="w-12 h-12 rounded-full object-cover border-2 border-green-500"
                                         src=""
                                         alt="Preview">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" id="avatarFileName"></p>
                                        <p class="text-xs text-gray-500">Выбран аватар</p>
                                    </div>
                                </div>
                                <button type="button"
                                        id="removeAvatarBtn"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition">
                                    Удалить
                                </button>
                            </div>
                        </div>

                        <!-- Upload Input -->
                        <div id="avatarUploadContainer">
                            <label for="avatar"
                                   class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200 group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-gray-500 transition"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-600">
                                        <span class="font-semibold text-green-600">Нажмите для загрузки</span> или перетащите
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF (MAX. 2MB)</p>
                                </div>
                                <input id="avatar"
                                       name="avatar"
                                       type="file"
                                       class="hidden"
                                       accept="image/jpeg,image/png,image/gif"/>
                            </label>
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                </div>

                <!-- Password Field -->
                <div class="space-y-1">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Пароль
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                               class="pl-10 block w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150 ease-in-out"
                               placeholder="Создайте надежный пароль">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    <p class="text-xs text-gray-500 mt-1">Пароль должен содержать минимум 8 символов</p>
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Подтвердите пароль
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="pl-10 block w-full border border-gray-300 rounded-lg px-3 py-2.5 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150 ease-in-out"
                               placeholder="Повторите пароль">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-green-300 group-hover:text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </span>
                    Создать аккаунт
                </button>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Уже есть аккаунт?
                        <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500 transition">
                            Войти
                        </a>
                    </p>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">Или зарегистрируйтесь через</span>
                </div>
            </div>

            <!-- Social Registration Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('auth.vkontakte.redirect') }}"
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.07 2H8.93C3.33 2 2 3.33 2 8.93V15.07C2 20.67 3.33 22 8.93 22H15.07C20.67 22 22 20.67 22 15.07V8.93C22 3.33 20.67 2 15.07 2M18.15 16.27H16.69C16.14 16.27 15.97 15.82 15 14.83C14.12 14 13.74 13.88 13.53 13.88C13.24 13.88 13.15 13.96 13.15 14.38V15.69C13.15 16.04 13.04 16.26 12.11 16.26C10.57 16.26 8.86 15.32 7.66 13.59C5.85 11.05 5.36 9.13 5.36 8.75C5.36 8.54 5.43 8.34 5.85 8.34H7.32C7.69 8.34 7.83 8.5 7.97 8.9C8.69 10.96 10.38 13.09 11.53 13.09C11.86 13.09 11.97 12.96 11.97 12.5V10.1C11.97 9.12 12.1 9 12.5 9H12.51C12.81 9 12.92 9.08 12.92 9.38V12.48C12.92 12.8 13.04 12.91 13.38 12.91C13.76 12.91 14.1 12.75 14.45 12.41C15.36 11.38 16 9.57 16 9.57C16.11 9.28 16.28 9.14 16.61 9.14H18.08C18.37 9.14 18.45 9.34 18.37 9.65C18.19 10.16 17.37 11.61 16.43 13.06C15.85 13.85 15.52 14.22 15.38 14.46C15.17 14.8 15.28 15.01 15.58 15.39C16.09 15.98 16.87 16.74 17.19 17.08C17.39 17.29 17.54 17.69 17.46 17.94C17.36 18.18 17.21 18.27 16.9 18.27H18.15Z"/>
                    </svg>
                    <span class="ml-2">ВКонтакте</span>
                </a>

                <a href="{{ route('auth.yandex.redirect') }}"
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <svg class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="ml-2">Яндекс ID</span>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.2; transform: scale(1.05); }
        }
        .animate-pulse {
            animation: pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const avatarInput = document.getElementById('avatar');
                const avatarPreview = document.getElementById('avatarPreview');
                const previewContainer = document.getElementById('avatarPreviewContainer');
                const uploadContainer = document.getElementById('avatarUploadContainer');
                const removeBtn = document.getElementById('removeAvatarBtn');
                const avatarFileName = document.getElementById('avatarFileName');

                // Drag and drop functionality
                const dropArea = document.querySelector('#avatarUploadContainer label');

                if (dropArea) {
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        dropArea.addEventListener(eventName, preventDefaults, false);
                    });

                    function preventDefaults(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropArea.addEventListener(eventName, highlight, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropArea.addEventListener(eventName, unhighlight, false);
                    });

                    function highlight() {
                        dropArea.classList.add('border-green-500', 'bg-green-50');
                    }

                    function unhighlight() {
                        dropArea.classList.remove('border-green-500', 'bg-green-50');
                    }

                    dropArea.addEventListener('drop', handleDrop, false);

                    function handleDrop(e) {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        avatarInput.files = files;
                        handleFile(files[0]);
                    }
                }

                // Handle file selection
                if (avatarInput) {
                    avatarInput.addEventListener('change', function(e) {
                        if (this.files && this.files[0]) {
                            handleFile(this.files[0]);
                        }
                    });
                }

                function handleFile(file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Пожалуйста, выберите файл в формате PNG, JPG или GIF.');
                        return;
                    }

                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Файл слишком большой. Максимальный размер 2MB.');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        if (avatarFileName) {
                            avatarFileName.textContent = file.name.length > 30 ? file.name.substring(0, 27) + '...' : file.name;
                        }
                        previewContainer.classList.remove('hidden');
                        uploadContainer.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }

                // Remove avatar
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        avatarInput.value = '';
                        previewContainer.classList.add('hidden');
                        uploadContainer.classList.remove('hidden');
                        if (avatarFileName) {
                            avatarFileName.textContent = '';
                        }
                    });
                }

                // Password strength indicator (optional enhancement)
                const passwordInput = document.getElementById('password');
                if (passwordInput) {
                    passwordInput.addEventListener('input', function() {
                        const strength = checkPasswordStrength(this.value);
                        updatePasswordStrength(strength);
                    });
                }

                function checkPasswordStrength(password) {
                    let strength = 0;
                    if (password.length >= 8) strength++;
                    if (password.match(/[a-z]+/)) strength++;
                    if (password.match(/[A-Z]+/)) strength++;
                    if (password.match(/[0-9]+/)) strength++;
                    if (password.match(/[$@#&!]+/)) strength++;
                    return strength;
                }

                function updatePasswordStrength(strength) {
                    // Remove existing strength indicator if any
                    const existingIndicator = document.querySelector('.password-strength');
                    if (existingIndicator) existingIndicator.remove();

                    if (passwordInput.value.length > 0) {
                        const strengthText = ['Очень слабый', 'Слабый', 'Средний', 'Хороший', 'Отличный'];
                        const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];

                        const indicator = document.createElement('div');
                        indicator.className = 'password-strength mt-2';
                        indicator.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 h-1 rounded-full bg-gray-200">
                                <div class="h-1 rounded-full transition-all duration-300 ${strengthColors[strength-1] || 'bg-gray-300'}" style="width: ${(strength/5)*100}%"></div>
                            </div>
                            <span class="text-xs ${strength > 0 ? 'text-gray-600' : 'text-gray-400'}">${strength > 0 ? strengthText[strength-1] : ''}</span>
                        </div>
                    `;
                        passwordInput.parentElement.parentElement.appendChild(indicator);
                    }
                }
            });
        </script>
    @endpush

    @stack('scripts')
</x-guest-layout>
