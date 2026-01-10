<x-guest-layout>
    <div class="mb-2 flex items-center w-100">
        <h1 class="text-2xl font-bold text-dark">Менеджер<span class="text-green-600">Плюс</span></h1>
    </div>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Имя')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Avatar Upload -->
        <div class="mt-4">
            <x-input-label for="avatar" :value="__('Аватар (опционально)')" />

            <div class="relative mt-1">
                <!-- Preview Container -->
                <div id="avatarPreviewContainer" class="hidden mb-3">
                    <div class="flex items-center space-x-3">
                        <img id="avatarPreview"
                             class="w-20 h-20 rounded-full object-cover border-2 border-gray-300"
                             src=""
                             alt="Preview">
                        <button type="button"
                                id="removeAvatarBtn"
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Удалить
                        </button>
                    </div>
                </div>

                <!-- Upload Input -->
                <div id="avatarUploadContainer" class="flex items-center justify-center w-full">
                    <label for="avatar"
                           class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-3 text-gray-500"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Нажмите для загрузки</span> или перетащите
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

            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Подтвердите пароль')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}">
                {{ __('Уже зарегистрированы?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Зарегистрироваться') }}
            </x-primary-button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const avatarInput = document.getElementById('avatar');
                const avatarPreview = document.getElementById('avatarPreview');
                const previewContainer = document.getElementById('avatarPreviewContainer');
                const uploadContainer = document.getElementById('avatarUploadContainer');
                const removeBtn = document.getElementById('removeAvatarBtn');

                // Drag and drop functionality
                const dropArea = uploadContainer.querySelector('label');

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
                    dropArea.classList.add('border-blue-500', 'bg-blue-50');
                }

                function unhighlight() {
                    dropArea.classList.remove('border-blue-500', 'bg-blue-50');
                }

                dropArea.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    avatarInput.files = files;
                    handleFile(files[0]);
                }

                // Handle file selection
                avatarInput.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        handleFile(this.files[0]);
                    }
                });

                function handleFile(file) {
                    if (file.size > 2 * 1024 * 1024) { // 2MB limit
                        alert('Файл слишком большой. Максимальный размер 2MB.');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        uploadContainer.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }

                // Remove avatar
                removeBtn.addEventListener('click', function() {
                    avatarInput.value = '';
                    previewContainer.classList.add('hidden');
                    uploadContainer.classList.remove('hidden');
                });
            });
        </script>
    @endpush

    @stack('scripts')
</x-guest-layout>
