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

        <!-- Основной контейнер с таблицей -->
        <div class="bg-white rounded-lg shadow-sm md:shadow-md p-4 md:p-6">
            <!-- Кнопки экспорта и печати -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                <div class="text-base md:text-lg font-semibold text-gray-700">
                    Инструменты
                </div>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">

                </div>
            </div>

            <!-- Карточки инструментов -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <!-- Фотобанк -->
                <div onclick="openTool('photobank')"
                     class="group cursor-pointer bg-white border border-gray-200 rounded-xl p-4 md:p-6 transition-all duration-300 hover:shadow-lg hover:border-green-300 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Фотобанк</h3>
                            <p class="text-gray-500 text-sm mt-1">Хранилище изображений и медиафайлов вашей организации</p>
                            <div class="mt-3 text-green-600 text-sm font-medium flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                Открыть
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Яндекс Телемост -->
                <div onclick="openTool('telemost')"
                     class="group cursor-pointer bg-white border border-gray-200 rounded-xl p-4 md:p-6 transition-all duration-300 hover:shadow-lg hover:border-green-300 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-200 transition-colors">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Яндекс Телемост</h3>
                            <p class="text-gray-500 text-sm mt-1">Видеоконференции и онлайн-встречи высокого качества</p>
                            <div class="mt-3 text-green-600 text-sm font-medium flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                Открыть
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Здесь можно добавить дополнительные карточки -->
                <!-- Пример дополнительной карточки -->
                <div onclick="openTool('calendar')"
                     class="group cursor-pointer bg-white border border-gray-200 rounded-xl p-4 md:p-6 transition-all duration-300 hover:shadow-lg hover:border-green-300 hover:-translate-y-1">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Календарь</h3>
                            <p class="text-gray-500 text-sm mt-1">Планирование встреч и событий команды</p>
                            <div class="mt-3 text-green-600 text-sm font-medium flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                Открыть
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openTool(tool) {
            switch(tool) {
                case 'photobank':
                    window.location.href = '{{ route("photobank") }}';
                    break;
                case 'telemost':
                    // URL для Яндекс Телемоста - замените на ваш актуальный адрес
                    window.open('https://telemost.yandex.ru/', '_blank');
                    break;
                case 'calendar':
                    // URL для календаря - настройте под ваши нужды
                    window.location.href = '/calendar';
                    break;
                default:
                    console.log('Неизвестный инструмент');
            }
        }
    </script>
@endpush
