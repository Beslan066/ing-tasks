@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
        $company = auth()->check() ? auth()->user()->company : null;

    @endphp
    <div class="">
        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `Хранилище`}">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                 <nav class="hidden max-[500px]:block">
                            <ol class="flex items-center gap-1.5">
                                <li>
                                    <a class="inline-flex items-center gap-1.5 text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}"
                                       href="{{ route('welcome') }}">
                                        Главная
                                        <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                                                  stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                </li>
                                <li class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}" x-text="pageName">Команда</li>
                            </ol>
                        </nav>
        <div class="max-[500px]:hidden">
                    @if($backgroundEnabled && $backgroundImage)
                        <h2 class="text-3xl font-bold text-white  max-[500px]:text-[26px]">Хранилище</h2>
                    @else
                        <h2 class="text-3xl font-bold max-[500px]:text-[26px]" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Хранилище</h2>
                    @endif
                </div>

            </div>
        </div>
        <!-- Breadcrumb End -->

        @if(auth()->user()->isManager() || auth()->user()->isSupervisor())
            <!-- ИНФОРМАЦИЯ О КОМПАНИИ И КНОПКА УЛУЧШЕНИЯ -->
            @if($company && $company->license_type === 'basic')
                @include('partials.subscription')
            @endif
        @endif

        @include('partials.file-manager.files-table', [
            'backgroundEnabled' => $backgroundEnabled,
            'backgroundImage'   => $backgroundImage,
            'fileStats'         => $fileStats,
            'files'             => $files,
        ])

    </div>

    <!-- Modal for File Upload -->
    <div id="uploadModal" class="fixed inset-0 hidden overflow-y-auto backdrop-blur-md bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0"></div>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Загрузка файла
                            </h3>
                            <div class="mt-4">
                                <!-- Прогресс бар -->
                                <div id="uploadProgress" class="mt-4 hidden">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Загрузка...</span>
                                        <span id="uploadPercent" class="text-sm font-medium text-green-600 dark:text-green-400">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div id="uploadProgressBar" class="bg-green-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="uploadStatus" class="mt-2 text-xs text-gray-500 dark:text-gray-400">Подготовка к загрузке...</p>
                                </div>

                                <!-- Блок ошибок -->
                                <div id="uploadError" class="mt-4 hidden">
                                    <div
                                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900/30 dark:border-red-700 dark:text-red-300"
                                        role="alert">
                                        <strong class="font-bold">Ошибка!</strong>
                                        <span id="errorMessage" class="block sm:inline"></span>
                                    </div>
                                </div>

                                <form id="uploadForm" action="{{ route('files.upload') }}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 cursor-pointer">
                                            Выберите файл
                                        </label>
                                     <input type="file" name="file" id="fileInput"
                                            class="block w-full text-sm text-gray-500 cursor-pointer
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold file:text-white
                                                    file:bg-gradient-to-br file:from-emerald-500 file:to-emerald-600
                                                    hover:file:from-emerald-600 hover:file:to-emerald-700"
                                            required>
                                        @php
                                            $maxFileSizeBytes = match($company->license_type) {
                                                'basic' => 104857600,
                                                'optimal' => 524288000,
                                                'premium' => 1073741824,
                                                default => 104857600
                                            };
                                            $maxFileSizeFormatted = match($company->license_type) {
                                                'basic' => '100 MB',
                                                'optimal' => '500 MB',
                                                'premium' => '1 GB',
                                                default => '100 MB'
                                            };
                                        @endphp
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Максимальный размер:
                                            <span class="font-medium text-green-600 dark:text-green-400">
                                                {{ $maxFileSizeFormatted }}
                                            </span>
                                            (Тариф: {{ $company->getLicenseTypeName() ?? 'Базовый' }})
                                        </p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Папка (необязательно)
                                        </label>
                                        <input type="text" name="folder"
                                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                               placeholder="Например: documents">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                   <button type="submit" form="uploadForm"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white border border-transparent rounded-md bg-gradient-to-br from-emerald-500 to-emerald-600 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(16,185,129,0.35)] active:translate-y-0 active:shadow-none">
                        Загрузить
                    </button>
                    <button type="button" onclick="closeUploadModal()"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-green-500 dark:bg-gray-600 dark:text-white dark:border-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Warning -->
    <div id="warningModal" class="fixed inset-0 hidden overflow-y-auto backdrop-blur-md bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0"></div>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                Внимание!
                            </h3>
                            <div class="mt-2">
                                <p id="warningMessage" class="text-sm text-gray-500 dark:text-gray-300">
                                    Текст предупреждения
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeWarningModal()"
                            class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-500 border border-transparent rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Понятно
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isUploading = false;

        // Функции для модального окна загрузки
        window.openUploadModal = function() {
            // Сбрасываем форму
            const form = document.getElementById('uploadForm');
            if (form) form.reset();

            // Скрываем прогресс и ошибки
            const progressDiv = document.getElementById('uploadProgress');
            const errorDiv = document.getElementById('uploadError');

            if (progressDiv) progressDiv.classList.add('hidden');
            if (errorDiv) errorDiv.classList.add('hidden');

            // Показываем модальное окно
            const modal = document.getElementById('uploadModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('block');
            }

            // Восстанавливаем кнопку
            const submitBtn = document.querySelector('#uploadForm button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = 'Загрузить';
                submitBtn.disabled = false;
            }

            isUploading = false;
        }

        window.closeUploadModal = function() {
            if (isUploading) {
                if (confirm('Загрузка файла в процессе. Вы уверены, что хотите закрыть окно?')) {
                    const modal = document.getElementById('uploadModal');
                    if (modal) {
                        modal.classList.remove('block');
                        modal.classList.add('hidden');
                    }
                    isUploading = false;
                }
            } else {
                const modal = document.getElementById('uploadModal');
                if (modal) {
                    modal.classList.remove('block');
                    modal.classList.add('hidden');
                }
            }
        }

        // Функции для модального окна предупреждения
        window.showWarningModal = function(message) {
            const warningModal = document.getElementById('warningModal');
            const warningMessage = document.getElementById('warningMessage');

            if (warningMessage) warningMessage.textContent = message;
            if (warningModal) {
                warningModal.classList.remove('hidden');
                warningModal.classList.add('block');
            }
        }

        window.closeWarningModal = function() {
            const warningModal = document.getElementById('warningModal');
            if (warningModal) {
                warningModal.classList.remove('block');
                warningModal.classList.add('hidden');
            }
        }

        // Функция для отображения ошибок
        window.showUploadError = function(message) {
            const errorDiv = document.getElementById('uploadError');
            const errorMessage = document.getElementById('errorMessage');

            if (errorMessage) errorMessage.textContent = message;
            if (errorDiv) errorDiv.classList.remove('hidden');

            // Скрываем прогресс
            const progressDiv = document.getElementById('uploadProgress');
            if (progressDiv) progressDiv.classList.add('hidden');

            // Восстанавливаем кнопку
            const submitBtn = document.querySelector('#uploadForm button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = 'Загрузить';
                submitBtn.disabled = false;
            }

            isUploading = false;

            // Авто-скрытие через 5 секунд
            setTimeout(() => {
                if (errorDiv) errorDiv.classList.add('hidden');
            }, 5000);
        }

        // Функция обновления прогресса
        window.updateProgress = function(percent, status) {
            const progressBar = document.getElementById('uploadProgressBar');
            const percentText = document.getElementById('uploadPercent');
            const statusText = document.getElementById('uploadStatus');

            if (progressBar) progressBar.style.width = percent + '%';
            if (percentText) percentText.textContent = Math.round(percent) + '%';
            if (statusText && status) statusText.textContent = status;
        }

        // Валидация файла
        window.validateFileSize = function(file) {
            const maxFileSize = {{ $maxFileSizeBytes ?? 104857600 }};
            const maxSizeFormatted = '{{ $maxFileSizeFormatted ?? "100 MB" }}';
            const licenseName = '{{ $company->getLicenseTypeName() ?? "Базовый" }}';

            if (!file) {
                showWarningModal('Пожалуйста, выберите файл');
                return false;
            }

            if (file.size === 0) {
                showWarningModal('Файл поврежден или пуст. Пожалуйста, выберите другой файл');
                return false;
            }

            if (file.size > maxFileSize) {
                const fileSizeMB = (file.size / 1048576).toFixed(2);
                const maxSizeMB = (maxFileSize / 1048576).toFixed(0);
                showWarningModal(`Файл слишком большой (${fileSizeMB} MB).\n\nМаксимальный размер для тарифа "${licenseName}" составляет ${maxSizeFormatted} (${maxSizeMB} MB).`);
                return false;
            }

            return true;
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM загружен');

            const uploadForm = document.getElementById('uploadForm');
            if (!uploadForm) {
                console.error('Форма uploadForm не найдена');
                return;
            }

            // Устанавливаем правильный action
            uploadForm.action = '{{ route("files.upload.ajax") }}';

            // Обработчик отправки формы
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Форма отправлена');

                if (isUploading) {
                    showWarningModal('Загрузка уже выполняется. Пожалуйста, подождите');
                    return;
                }

                const fileInput = document.getElementById('fileInput');
                if (!fileInput) {
                    showUploadError('Ошибка: элемент выбора файла не найден');
                    return;
                }

                const file = fileInput.files[0];
                if (!validateFileSize(file)) {
                    return;
                }

                // Показываем прогресс
                const progressDiv = document.getElementById('uploadProgress');
                const errorDiv = document.getElementById('uploadError');

                if (progressDiv) progressDiv.classList.remove('hidden');
                if (errorDiv) errorDiv.classList.add('hidden');

                updateProgress(0, 'Подготовка к загрузке...');

                // Меняем текст кнопки
                const submitBtn = uploadForm.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerHTML : 'Загрузить';
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Загрузка...</span>';
                    submitBtn.disabled = true;
                }

                isUploading = true;

                // Создаем FormData
                const formData = new FormData(uploadForm);

                // Создаем XMLHttpRequest
                const xhr = new XMLHttpRequest();

                // Прогресс загрузки
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = (e.loaded / e.total) * 100;
                        const loadedMB = (e.loaded / 1048576).toFixed(2);
                        const totalMB = (e.total / 1048576).toFixed(2);
                        updateProgress(percent, `Загружено: ${loadedMB} MB из ${totalMB} MB`);
                        console.log(`Прогресс: ${percent}%`);
                    }
                });

                // Обработка ответа
                xhr.addEventListener('load', function() {
                    console.log('Ответ получен, статус:', xhr.status);

                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log('Ответ сервера:', response);

                            if (response.success) {
                                updateProgress(100, 'Загрузка завершена! Обновление...');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                showUploadError(response.error || response.message || 'Неизвестная ошибка');
                                if (submitBtn) {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                                isUploading = false;
                            }
                        } catch(e) {
                            console.error('Ошибка парсинга JSON:', e);
                            showUploadError('Ошибка обработки ответа сервера');
                            if (submitBtn) {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                            isUploading = false;
                        }
                    } else {
                        let errorMsg = 'Ошибка загрузки файла';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.error || response.message || errorMsg;
                        } catch(e) {}
                        showUploadError(errorMsg);
                        if (submitBtn) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                        isUploading = false;
                    }
                });

                // Ошибка сети
                xhr.addEventListener('error', function() {
                    console.error('Ошибка сети');
                    showUploadError('Ошибка сети. Проверьте соединение с интернетом');
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                    isUploading = false;
                });

                // Таймаут
                xhr.addEventListener('timeout', function() {
                    console.error('Таймаут');
                    showUploadError('Превышено время ожидания. Попробуйте снова');
                    if (submitBtn) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                    isUploading = false;
                });

                // Отправляем запрос
                xhr.open('POST', uploadForm.action);
                xhr.timeout = 300000; // 5 минут
                xhr.send(formData);
                console.log('Запрос отправлен');
            });

            // Валидация при выборе файла
            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        console.log('Выбран файл:', file.name, 'Размер:', file.size);

                        // Скрываем ошибку
                        const errorDiv = document.getElementById('uploadError');
                        if (errorDiv) errorDiv.classList.add('hidden');

                        // Проверяем размер
                        const maxFileSize = {{ $maxFileSizeBytes ?? 104857600 }};
                        const maxSizeFormatted = '{{ $maxFileSizeFormatted ?? "100 MB" }}';
                        const licenseName = '{{ $company->getLicenseTypeName() ?? "Базовый" }}';

                        if (file.size > maxFileSize) {
                            const fileSizeMB = (file.size / 1048576).toFixed(2);
                            const maxSizeMB = (maxFileSize / 1048576).toFixed(0);
                            showWarningModal(`Файл слишком большой!\n\nРазмер: ${fileSizeMB} MB\nМаксимум для тарифа "${licenseName}": ${maxSizeFormatted} (${maxSizeMB} MB)`);
                            fileInput.value = '';
                        } else {
                            const fileSizeMB = (file.size / 1048576).toFixed(2);
                            updateProgress(0, `Выбран файл: ${file.name} (${fileSizeMB} MB). Нажмите "Загрузить"`);
                        }
                    }
                });
            }

            // Закрытие модального окна при клике на фон
            const uploadModal = document.getElementById('uploadModal');
            if (uploadModal) {
                uploadModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeUploadModal();
                    }
                });
            }

            const warningModal = document.getElementById('warningModal');
            if (warningModal) {
                warningModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeWarningModal();
                    }
                });
            }

            console.log('Инициализация завершена');
        });
    </script>
@endsection
