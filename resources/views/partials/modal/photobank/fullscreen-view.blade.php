<div id="fullscreenViewer" class="fixed inset-0 bg-black bg-opacity-95 z-[100] hidden items-center justify-center backdrop-blur-md">
    <button id="closeFullscreen"
            class="absolute top-4 right-4 text-white hover:text-gray-300 z-20 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div id="photoCounter" class="absolute top-4 left-4 text-white bg-black bg-opacity-50 rounded-lg px-3 py-1 text-sm z-20"></div>

    <button id="prevPhoto"
            class="absolute left-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-3 transition-colors hover:bg-opacity-75 z-20">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button id="nextPhoto"
            class="absolute right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-3 transition-colors hover:bg-opacity-75 z-20">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>

    <div class="max-w-7xl max-h-screen p-4">
        <img id="fullscreenImage" src="" alt="" class="max-w-full max-h-screen object-contain mx-auto">
    </div>

    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white p-6 z-20">
        <div class="container mx-auto">
            <div class="mb-4">
                <h2 id="infoTitle" class="text-2xl font-bold mb-2"></h2>
                <p id="infoDescription" class="text-gray-300 mb-2"></p>
                <p id="infoCategory" class="text-sm text-gray-400 mb-2"></p>
                <div id="infoTags" class="flex flex-wrap gap-2"></div>
            </div>
            <div class="border-t border-gray-700 pt-4">
                <div class="flex flex-wrap gap-3">
                    <button id="downloadBtn"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Скачать
                    </button>
                    <button id="resizeBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Изменить размер
                    </button>
                    <button id="convertBtn"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Конвертировать
                    </button>
                    <button id="ratioBtn"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Соотношение
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно изменения размера -->
    <div id="resizeModal" class="fixed inset-0 bg-black bg-opacity-80 z-30 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Изменить размер</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ширина (px)</label>
                    <input type="number" id="resizeWidth" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Ширина">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Высота (px)</label>
                    <input type="number" id="resizeHeight" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Высота">
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="resizeCrop" class="rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Обрезать для точного соответствия</span>
                </label>
            </div>
            <div class="flex gap-3 mt-6">
                <button id="applyResizeBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">Применить</button>
                <button id="cancelResizeBtn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">Отмена</button>
            </div>
        </div>
    </div>

    <!-- Модальное окно конвертации -->
    <div id="convertModal" class="fixed inset-0 bg-black bg-opacity-80 z-30 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Конвертировать в формат</h3>
            <div class="grid grid-cols-2 gap-3">
                <button data-format="jpeg" class="convert-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">JPEG</div>
                    <div class="text-xs text-gray-500">Хорош для фотографий</div>
                </button>
                <button data-format="png" class="convert-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">PNG</div>
                    <div class="text-xs text-gray-500">Поддерживает прозрачность</div>
                </button>
                <button data-format="webp" class="convert-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">WebP</div>
                    <div class="text-xs text-gray-500">Современный формат</div>
                </button>
                <button data-format="gif" class="convert-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">GIF</div>
                    <div class="text-xs text-gray-500">Для анимации</div>
                </button>
            </div>
            <button id="cancelConvertBtn" class="w-full mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">Отмена</button>
        </div>
    </div>

    <!-- Модальное окно соотношения сторон -->
    <div id="ratioModal" class="fixed inset-0 bg-black bg-opacity-80 z-30 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Выберите соотношение сторон</h3>
            <div class="grid grid-cols-2 gap-3">
                <button data-ratio="16:9" class="ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">16:9</div>
                    <div class="text-xs text-gray-500">Широкоэкранное</div>
                </button>
                <button data-ratio="4:3" class="ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">4:3</div>
                    <div class="text-xs text-gray-500">Классическое</div>
                </button>
                <button data-ratio="1:1" class="ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">1:1</div>
                    <div class="text-xs text-gray-500">Квадратное</div>
                </button>
                <button data-ratio="3:2" class="ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">3:2</div>
                    <div class="text-xs text-gray-500">Фотографическое</div>
                </button>
                <button data-ratio="2:3" class="ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-3 rounded-lg text-center transition-colors">
                    <div class="font-medium">2:3</div>
                    <div class="text-xs text-gray-500">Портретное</div>
                </button>
            </div>
            <button id="cancelRatioBtn" class="w-full mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">Отмена</button>
        </div>
    </div>

    <!-- НОВОЕ: Модальное окно скачивания с настройками -->
    <div id="downloadModal" class="fixed inset-0 bg-black bg-opacity-80 z-30 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Настройки скачивания</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Формат</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" data-download-format="jpeg" class="download-format-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center transition-colors">
                            <div class="font-medium">JPEG</div>
                            <div class="text-xs text-gray-500">Хорошее качество</div>
                        </button>
                        <button type="button" data-download-format="png" class="download-format-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center transition-colors">
                            <div class="font-medium">PNG</div>
                            <div class="text-xs text-gray-500">Прозрачность</div>
                        </button>
                        <button type="button" data-download-format="webp" class="download-format-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center transition-colors">
                            <div class="font-medium">WebP</div>
                            <div class="text-xs text-gray-500">Современный</div>
                        </button>
                        <button type="button" data-download-format="gif" class="download-format-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center transition-colors">
                            <div class="font-medium">GIF</div>
                            <div class="text-xs text-gray-500">Для анимации</div>
                        </button>
                    </div>
                </div>

                <div id="downloadQualitySection">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Качество <span class="text-xs text-gray-500">(1-100)</span></label>
                    <input type="range" id="downloadQuality" min="1" max="100" value="85" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Низкое</span>
                        <span>Среднее</span>
                        <span>Высокое</span>
                    </div>
                    <span id="downloadQualityValue" class="text-sm text-gray-600 dark:text-gray-400">85%</span>
                </div>

                <div>
                    <label class="flex items-center gap-2 mb-3">
                        <input type="checkbox" id="downloadResizeEnable" class="rounded">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Изменить размер</span>
                    </label>
                    <div id="downloadResizeOptions" style="display: none;" class="space-y-3 ml-6">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Ширина (px)</label>
                                <input type="number" id="downloadWidth" placeholder="Авто" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Высота (px)</label>
                                <input type="number" id="downloadHeight" placeholder="Авто" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="downloadKeepProportions" checked class="rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Сохранять пропорции</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="downloadCrop" class="rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Обрезать для точного соответствия</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 mb-3">
                        <input type="checkbox" id="downloadRatioEnable" class="rounded">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Применить соотношение сторон</span>
                    </label>
                    <div id="downloadRatioOptions" style="display: none;" class="ml-6">
                        <div class="grid grid-cols-5 gap-2">
                            <button type="button" data-download-ratio="16:9" class="download-ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center text-sm transition-colors">16:9</button>
                            <button type="button" data-download-ratio="4:3" class="download-ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center text-sm transition-colors">4:3</button>
                            <button type="button" data-download-ratio="1:1" class="download-ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center text-sm transition-colors">1:1</button>
                            <button type="button" data-download-ratio="3:2" class="download-ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center text-sm transition-colors">3:2</button>
                            <button type="button" data-download-ratio="2:3" class="download-ratio-option bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 p-2 rounded-lg text-center text-sm transition-colors">2:3</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button id="applyDownloadBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">Скачать</button>
                <button id="cancelDownloadBtn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">Отмена</button>
            </div>
        </div>
    </div>
</div>
