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

                <!-- Видеоконференции -->
                <div onclick="openConferenceManager()"
                     class="group cursor-pointer bg-white border border-gray-200 rounded-xl p-4 md:p-6 transition-all duration-300 hover:shadow-lg hover:border-green-300 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Видеоконференции</h3>
                            <p class="text-gray-500 text-sm mt-1">Создайте комнату и пригласите коллег</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Менеджер комнаты -->
    <div id="conferenceManager" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Видеоконференция</h2>
                <button onclick="closeConferenceManager()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <!-- Создание новой комнаты -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Создать новую комнату</h3>
                    <button onclick="createNewRoom()"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        + Создать комнату
                    </button>
                </div>

                <!-- Войти в существующую комнату -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-3">Войти в комнату</h3>
                    <input type="text" id="roomIdInput"
                           placeholder="Введите ID комнаты"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-3 focus:outline-none focus:border-green-500">
                    <button onclick="joinRoom()"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Войти
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Окно конференции -->
    <div id="conferenceModal" class="fixed inset-0 bg-black bg-opacity-95 z-50 hidden flex flex-col">
        <div class="bg-gray-900 text-white p-3 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="font-semibold">Комната: <span id="currentRoomId" class="text-green-400"></span></span>
                <button onclick="copyRoomLink()"
                        class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm transition-colors">
                    📋 Копировать ссылку
                </button>
            </div>
            <button onclick="closeConference()" class="text-red-400 hover:text-red-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1">
            <div id="jitsiContainer" class="w-full h-full"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentRoomId = null;
        let jitsiApi = null;

        // Открыть менеджер комнат
        function openConferenceManager() {
            document.getElementById('conferenceManager').style.display = 'flex';
            document.getElementById('roomIdInput').value = '';
        }

        function closeConferenceManager() {
            document.getElementById('conferenceManager').style.display = 'none';
        }

        // Создать новую комнату
        function createNewRoom() {
            const roomId = 'team_' + Date.now() + '_' + Math.random().toString(36).substr(2, 8);
            startConference(roomId);
            closeConferenceManager();
        }

        // Войти в существующую комнату
        function joinRoom() {
            const roomId = document.getElementById('roomIdInput').value.trim();
            if (!roomId) {
                alert('Введите ID комнаты');
                return;
            }
            startConference(roomId);
            closeConferenceManager();
        }

        // Запустить конференцию
        async function startConference(roomId) {
            currentRoomId = roomId;
            document.getElementById('currentRoomId').textContent = roomId;

            const modal = document.getElementById('conferenceModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Загружаем Jitsi API если нужно
            if (!window.JitsiMeetExternalAPI) {
                await loadJitsiAPI();
            }

            // Создаем конференцию
            const domain = 'meet.jit.si';
            const options = {
                roomName: roomId,
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
                }
            };

            jitsiApi = new JitsiMeetExternalAPI(domain, options);
        }

        // Скопировать ссылку на комнату
        function copyRoomLink() {
            if (!currentRoomId) return;

            // Ссылка для приглашения
            const inviteLink = `https://meet.jit.si/${currentRoomId}`;

            navigator.clipboard.writeText(inviteLink).then(() => {
                // Показываем уведомление
                const btn = event.target;
                const originalText = btn.innerText;
                btn.innerText = '✅ Скопировано!';
                setTimeout(() => {
                    btn.innerText = originalText;
                }, 2000);
            }).catch(() => {
                alert('Ссылка для приглашения: ' + inviteLink);
            });
        }

        // Загрузка Jitsi API
        function loadJitsiAPI() {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://meet.jit.si/external_api.js';
                script.async = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }

        // Закрыть конференцию
        function closeConference() {
            const modal = document.getElementById('conferenceModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';

            if (jitsiApi) {
                jitsiApi.dispose();
                jitsiApi = null;
            }

            document.getElementById('jitsiContainer').innerHTML = '';
            currentRoomId = null;
        }

        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('conferenceModal');
                if (modal.style.display === 'flex') {
                    closeConference();
                }
                closeConferenceManager();
            }
        });
    </script>
@endpush
