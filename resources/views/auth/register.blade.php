<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-32 w-80 h-80 bg-green-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse animation-delay-2000"></div>
        </div>

        <div class="max-w-md w-full space-y-6 relative z-10">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-4xl font-extrabold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Менеджер<span class="bg-gradient-to-r bg-clip-text text-transparent" style="color: linear-gradient(135deg, #10b981, #059669);">Плюс</span>
                </h2>
                <p class="mt-2 text-sm text-gray-600">Создайте аккаунт</p>
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
                        class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-[1.02]" style="background: linear-gradient(135deg, #10b981, #059669);">
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
            <div class="grid grid-cols-1 gap-3">

                <a href="{{ route('auth.yandex.redirect') }}"
                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                    <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.04 12c0-5.523 4.476-10 10-10 5.522 0 10 4.477 10 10s-4.478 10-10 10c-5.524 0-10-4.477-10-10z" fill="#FC3F1D"/><path d="M13.32 7.666h-.924c-1.694 0-2.585.858-2.585 2.123 0 1.43.616 2.1 1.881 2.959l1.045.704-3.003 4.487H7.49l2.695-4.014c-1.55-1.111-2.42-2.19-2.42-4.015 0-2.288 1.595-3.85 4.62-3.85h3.003v11.868H13.32V7.666z" fill="#fff"/></svg>
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
