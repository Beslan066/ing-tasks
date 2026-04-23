@extends('layouts.app')

@section('content')
    <div id="team">
        <!-- Заголовок и кнопка -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold" style="color: #16a34a;">Команда</h1>
                <p class="text-gray-500 text-sm md:text-base">Участники вашей организации</p>
            </div>
        </div>

        <!-- Основной контейнер -->
        <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                <div class="text-base md:text-lg font-semibold text-gray-700">
                    Инструменты
                </div>
            </div>

            <!-- Карточки инструментов -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <!-- Фотобанк -->
                <div onclick="window.location.href='{{ route('photobank') }}'"
                     class="group cursor-pointer bg-white border border-gray-200 rounded-xl p-4 md:p-6 transition-all duration-300 hover:shadow-lg hover:border-green-300 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Фотобанк</h3>
                            <p class="text-gray-500 text-sm mt-1">Хранилище изображений и медиафайлов</p>
                        </div>
                    </div>
                </div>

                <!-- Видеоконференции (Jitsi Meet) -->
                <div onclick="openVideoConference()"
                     class="group cursor-pointer bg-white border border-gray-200 rounded-xl p-4 md:p-6 transition-all duration-300 hover:shadow-lg hover:border-green-300 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Видеоконференции</h3>
                            <p class="text-gray-500 text-sm mt-1">Проводите онлайн-встречи на сайте</p>
                        </div>
                    </div>
                </div>

                <!-- Создание видеоконференций - неограниченное количество

                До 75-100 участников в одной конференции (зависит от качества связи)

                Неограниченная длительность вызовов (важно: см. нюансы ниже)

                Демонстрация экрана

                Чат в конференции

                Запись конференции (на локальный диск)

                Поднятие руки

                Видеофон (размытие фона)

                Трансляция YouTube

                Совместный просмотр видео

                Совместная работа с документами (Etherpad)

                -->

                <!-- Дополнительные карточки можно добавить -->
            </div>
        </div>
    </div>

    <!-- Модальное окно для видеоконференции -->
    <div id="conferenceModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Видеоконференция</h2>
                    <p class="text-sm text-gray-500">Комната: <span id="roomName"></span></p>
                </div>
                <button onclick="closeConference()" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 p-4">
                <div id="jitsiContainer" class="w-full h-full"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Загрузка Jitsi API динамически
        function loadJitsiAPI() {
            return new Promise((resolve, reject) => {
                if (window.JitsiMeetExternalAPI) {
                    resolve();
                    return;
                }

                const script = document.createElement('script');
                script.src = 'https://meet.jit.si/external_api.js';
                script.async = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        async function openVideoConference() {
            try {
                // Показываем модальное окно
                const modal = document.getElementById('conferenceModal');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                // Генерируем уникальное имя комнаты
                const roomName = 'team_' + Date.now() + '_' + Math.random().toString(36).substr(2, 8);
                document.getElementById('roomName').textContent = roomName;

                // Загружаем Jitsi API
                await loadJitsiAPI();

                // Создаем конференцию
                const domain = 'meet.jit.si';
                const options = {
                    roomName: roomName,
                    width: '100%',
                    height: '100%',
                    parentNode: document.getElementById('jitsiContainer'),
                    userInfo: {
                        displayName: 'Участник ' + Math.floor(Math.random() * 1000)
                    },
                    configOverwrite: {
                        startWithVideoMuted: true,
                        startWithAudioMuted: false,
                        disableDeepLinking: true,
                        enableWelcomePage: false,
                        prejoinPageEnabled: false
                    },
                    interfaceConfigOverwrite: {
                        TOOLBAR_BUTTONS: [
                            'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                            'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                            'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                            'videoquality', 'filmstrip', 'feedback', 'stats', 'shortcuts',
                            'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone'
                        ]
                    }
                };

                // Сохраняем API для закрытия
                window.jitsiApi = new JitsiMeetExternalAPI(domain, options);

            } catch (error) {
                console.error('Ошибка загрузки видеоконференции:', error);
                alert('Не удалось загрузить видеоконференцию. Проверьте подключение к интернету.');
                closeConference();
            }
        }

        function closeConference() {
            const modal = document.getElementById('conferenceModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';

            // Закрываем конференцию
            if (window.jitsiApi) {
                window.jitsiApi.dispose();
                window.jitsiApi = null;
            }

            // Очищаем контейнер
            const container = document.getElementById('jitsiContainer');
            container.innerHTML = '';

            // Сбрасываем имя комнаты
            document.getElementById('roomName').textContent = '';
        }

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('conferenceModal');
                if (modal.style.display === 'flex') {
                    closeConference();
                }
            }
        });
    </script>
@endpush
