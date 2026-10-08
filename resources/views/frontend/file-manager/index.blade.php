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
    <div id="uploadModal" class="fixed inset-0 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm z-50">
        <div class="flex items-start pt-16 justify-center min-h-screen px-4 py-8">
            <div class="relative bg-white rounded-[24px] shadow-2xl shadow-slate-900/25 border border-slate-200/50 w-full max-w-lg overflow-hidden flex flex-col">

                {{-- Заголовок (как в других модалках) --}}
                <div class="px-8 pt-6 pb-5 border-b border-slate-100 flex items-start justify-between gap-3 flex-shrink-0">
                    <div>
                        <h3 class="text-[22px] font-semibold text-slate-800 tracking-tight">Загрузка файла</h3>
                        <p class="text-[13px] text-slate-400 mt-0.5">Выберите файл и укажите папку</p>
                    </div>
                    <button type="button" onclick="closeUploadModal()"
                            class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full flex-shrink-0">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Тело --}}
                <div class="px-8 py-6">

                    {{-- Прогресс --}}
                    <div id="uploadProgress" class="mb-5 hidden">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[13px] font-medium text-slate-700">Загрузка...</span>
                            <span id="uploadPercent" class="text-[13px] font-semibold text-emerald-600">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div id="uploadProgressBar" class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="uploadStatus" class="mt-2 text-[11px] text-slate-500">Подготовка к загрузке...</p>
                    </div>

                    {{-- Ошибка --}}
                    <div id="uploadError" class="mb-5 hidden">
                        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-start gap-2.5">
                            <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 flex-shrink-0"></i>
                            <div class="text-[13px]">
                                <strong class="font-semibold">Ошибка!</strong>
                                <span id="errorMessage" class="block mt-0.5"></span>
                            </div>
                        </div>
                    </div>

                    <form id="uploadForm" action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
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

                        {{-- Выбор файла --}}
                        <div class="mb-5">
                            <label for="fileInput" class="block text-slate-600 text-[13px] font-medium mb-1.5">
                                Выберите файл <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="file" id="fileInput" required
                                       class="block w-full text-[13px] text-slate-600 cursor-pointer
                                          file:mr-3 file:py-2.5 file:px-4
                                          file:rounded-xl file:border-0
                                          file:text-[13px] file:font-semibold file:text-white
                                          file:bg-emerald-500 hover:file:bg-emerald-600
                                          file:transition-all file:duration-200 file:cursor-pointer
                                          file:shadow-md file:shadow-emerald-500/20">
                            </div>
                            <p class="mt-2 text-[11px] text-slate-500">
                                Максимальный размер:
                                <span class="font-semibold text-emerald-600">{{ $maxFileSizeFormatted }}</span>
                                <span class="text-slate-400">(Тариф: {{ $company->getLicenseTypeName() ?? 'Базовый' }})</span>
                            </p>
                        </div>

                        {{-- Папка --}}
                        <div class="mb-2">
                            <label class="block text-slate-600 text-[13px] font-medium mb-1.5">
                                Папка <span class="text-slate-400 font-normal">(необязательно)</span>
                            </label>
                            <input type="text" name="folder"
                                   class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300"
                                   placeholder="Например: documents">
                        </div>
                    </form>
                </div>

                {{-- Кнопки --}}
                <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closeUploadModal()"
                            class="px-5 py-2.5 text-[13px] font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                        Отмена
                    </button>
                    <button type="submit" form="uploadForm"
                            class="px-6 py-2.5 text-[13px] font-semibold text-white bg-emerald-500 rounded-xl hover:bg-emerald-600 transition-all duration-200 flex items-center justify-center gap-2 shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/30 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                        <i class="fas fa-cloud-upload-alt text-[11px]"></i>
                        Загрузить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Warning -->
    <div id="warningModal" class="fixed inset-0 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm z-50">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative bg-white rounded-[24px] shadow-2xl shadow-slate-900/25 border border-slate-200/50 w-full max-w-md overflow-hidden flex flex-col">

                {{-- Тело --}}
                <div class="px-8 pt-6 pb-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full bg-rose-50 border border-rose-200">
                            <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-[17px] font-semibold text-slate-800 tracking-tight">
                                Внимание!
                            </h3>
                            <p id="warningMessage" class="mt-1.5 text-[13px] text-slate-600 leading-relaxed whitespace-pre-line">
                                Текст предупреждения
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Кнопка --}}
                <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button type="button" onclick="closeWarningModal()"
                            class="px-6 py-2.5 text-[13px] font-semibold text-white bg-emerald-500 rounded-xl hover:bg-emerald-600 transition-all duration-200 shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/30 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
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
                submitBtn.innerHTML = '<i class="fas fa-cloud-upload-alt text-[11px]"></i> Загрузить';
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
                submitBtn.innerHTML = '<i class="fas fa-cloud-upload-alt text-[11px]"></i> Загрузить';
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
                const originalText = submitBtn ? submitBtn.innerHTML : '<i class="fas fa-cloud-upload-alt text-[11px]"></i> Загрузить';
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Загрузка...</span>';
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
