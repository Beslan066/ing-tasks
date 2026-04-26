@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="photobankApp()" x-init="init()" x-cloak>
        <!-- Заголовок и кнопки -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Фотобанк</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2" x-text="`${totalPhotos} фотографий`"></p>
            </div>
            <div class="flex gap-3">
                <button @click="showFilters = !showFilters"
                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Фильтры
                    <template x-if="hasActiveFilters">
                        <span class="bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
                              x-text="getActiveFiltersCount"></span>
                    </template>
                </button>

                <button @click="showUploadModal = true"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Добавить фото
                </button>
            </div>
        </div>

        <!-- Уведомления -->
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="fixed bottom-4 right-4 z-50 max-w-sm w-full"
             style="display: none;">
            <div :class="{'bg-green-500': toast.type === 'success', 'bg-red-500': toast.type === 'error', 'bg-blue-500': toast.type === 'info'}"
                 class="rounded-lg shadow-lg p-4 text-white">
                <div class="flex items-center gap-3">
                    <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <svg x-show="toast.type === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="toast.message"></span>
                </div>
            </div>
        </div>

        <!-- Раскрывающаяся панель фильтров -->
        <div x-show="showFilters"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Поиск</label>
                    <input type="text" x-model="filters.search" @input.debounce.500ms="loadPhotos"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                           placeholder="Название, описание...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Категория</label>
                    <select x-model="filters.category" @change="loadPhotos"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:text-white bg-white">
                        <option value="">Все категории</option>
                        <template x-for="category in categoriesData" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Теги</label>
                    <select x-model="filters.tags" @change="loadPhotos"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Все теги</option>
                        <template x-for="tag in tagsData" :key="tag.id">
                            <option :value="tag.id" x-text="tag.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div x-show="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                <template x-if="filters.search">
                <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                    Поиск: <span x-text="filters.search"></span>
                    <button @click="filters.search = ''; loadPhotos();" class="hover:text-blue-900">×</button>
                </span>
                </template>
                <template x-if="filters.category">
                <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                    Категория: <span x-text="getCategoryName(filters.category)"></span>
                    <button @click="filters.category = ''; loadPhotos();" class="hover:text-green-900">×</button>
                </span>
                </template>
                <template x-if="filters.tags">
                <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                    Тег: <span x-text="getTagName(filters.tags)"></span>
                    <button @click="filters.tags = ''; loadPhotos();" class="hover:text-purple-900">×</button>
                </span>
                </template>
                <button @click="clearFilters" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                    Очистить все
                </button>
            </div>
        </div>

        <!-- Быстрые категории -->
        <div class="flex items-center justify-center py-4 flex-wrap gap-2 mb-8">
            <button @click="setCategoryFilter('')"
                    :class="{'bg-green-600 text-white': !filters.category, 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300': filters.category}"
                    class="px-4 py-2 rounded-lg font-medium transition-colors border border-gray-300 dark:border-gray-600">
                Все категории
            </button>
            <template x-for="category in categoriesData" :key="category.id">
                <button @click="setCategoryFilter(category.id)"
                        :class="{'bg-green-600 text-white': filters.category == category.id, 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300': filters.category != category.id}"
                        class="px-4 py-2 rounded-lg font-medium transition-colors border border-gray-300 dark:border-gray-600"
                        x-text="category.name">
                </button>
            </template>
        </div>

        <!-- Галерея фотографий -->
        <div class="relative">
            <div x-show="loading" class="absolute inset-0 bg-white dark:bg-gray-900 bg-opacity-80 flex items-center justify-center z-10 rounded-lg">
                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                    <span>Загрузка фотографий...</span>
                </div>
            </div>

            <template x-if="photos.length > 0">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    <template x-for="photo in photos" :key="photo.id">
                        <div @click="openFullscreen(photo)"
                             class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden group relative transition-all duration-300 hover:shadow-lg cursor-pointer">
                            <img class="w-full h-48 object-cover"
                                 :src="'/storage/' + photo.file_path"
                                 :alt="photo.title"
                                 loading="lazy">

                            <!-- Кнопка удаления - не перехватывает клик -->
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex gap-1 z-10">
                                <button @click.stop="deletePhoto(photo)"
                                        class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Overlay с информацией - НЕ перехватывает клик -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-end p-3 pointer-events-none">
                                <div class="text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 mb-4 w-full pointer-events-none">
                                    <h3 class="font-semibold text-sm mb-1" x-text="photo.title"></h3>
                                    <p class="text-xs opacity-90 mb-2" x-text="photo.category ? photo.category.name : 'Без категории'"></p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="tag in photo.tags" :key="tag.id">
                                            <span class="bg-green-500 bg-opacity-80 px-2 py-1 rounded text-xs" x-text="tag.name"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="!loading && photos.length === 0">
                <div class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2" x-text="hasActiveFilters ? 'Ничего не найдено' : 'Пока нет фотографий'"></h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6" x-text="hasActiveFilters ? 'Попробуйте изменить параметры поиска' : 'Будьте первым, кто добавит фотографию!'"></p>
                        <button @click="hasActiveFilters ? clearFilters() : (showUploadModal = true)"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            <span x-text="hasActiveFilters ? 'Сбросить фильтры' : 'Добавить фото'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="hasMorePages && photos.length > 0" class="text-center mt-8">
            <button @click="loadMore" :disabled="loadingMore"
                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50">
                <span x-show="!loadingMore">Загрузить еще</span>
                <span x-show="loadingMore">Загрузка...</span>
            </button>
        </div>

        <!-- Модальное окно загрузки -->
        <div x-show="showUploadModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
             style="display: none;">
            <div @click.away="showUploadModal = false"
                 class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Добавить фотографию</h2>
                        <button @click="showUploadModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="uploadPhoto" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название *</label>
                            <input type="text" x-model="uploadForm.title"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                   required>
                            <span x-show="uploadErrors.title" x-text="uploadErrors.title" class="text-red-500 text-sm mt-1"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Описание</label>
                            <textarea x-model="uploadForm.description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Категория *</label>
                            <div class="flex gap-2">
                                <select x-model="uploadForm.category_id"
                                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 bg-white"
                                        required>
                                    <option value="">Выберите категорию</option>
                                    <template x-for="category in categoriesData" :key="category.id">
                                        <option :value="category.id" x-text="category.name"></option>
                                    </template>
                                </select>
                                <button type="button" @click="showNewCategory = !showNewCategory"
                                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                                    +
                                </button>
                            </div>

                            <div x-show="showNewCategory" class="flex gap-2 mt-2">
                                <input type="text" x-model="newCategory.name"
                                       placeholder="Новая категория"
                                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white">
                                <button type="button" @click="createCategory"
                                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">
                                    Создать
                                </button>
                            </div>
                            <span x-show="uploadErrors.category_id" x-text="uploadErrors.category_id" class="text-red-500 text-sm"></span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Теги</label>
                            <select x-model="uploadForm.tags" multiple
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white h-32">
                                <template x-for="tag in tagsData" :key="tag.id">
                                    <option :value="tag.id" x-text="tag.name"></option>
                                </template>
                            </select>
                            <p class="text-sm text-gray-500 mt-2">Для выбора нескольких тегов удерживайте Ctrl</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Фотография *</label>
                            <input type="file" @change="handleFileSelect"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                                   accept="image/*" required>
                            <span x-show="uploadErrors.photo" x-text="uploadErrors.photo" class="text-red-500 text-sm mt-1"></span>
                            <p class="text-sm text-gray-500 mt-2">Поддерживаемые форматы: JPEG, PNG, GIF, WebP. Максимальный размер: 20MB</p>
                        </div>

                        <div x-show="previewUrl">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Предпросмотр</label>
                            <img :src="previewUrl" class="max-w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                        </div>

                        <div x-show="uploadMessage"
                             :class="{'bg-green-100 border-green-400 text-green-700': uploadMessageType === 'success', 'bg-red-100 border-red-400 text-red-700': uploadMessageType === 'error'}"
                             class="px-4 py-3 rounded border">
                            <span x-text="uploadMessage"></span>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit"
                                    :disabled="uploadLoading"
                                    class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                <span x-show="!uploadLoading">Загрузить фотографию</span>
                                <span x-show="uploadLoading">Загрузка...</span>
                            </button>
                            <button type="button" @click="showUploadModal = false"
                                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                Отмена
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Полноэкранный просмотр с инструментами -->
    <div id="fullscreenViewer" class="fixed inset-0 bg-black bg-opacity-95 z-[100] hidden items-center justify-center">
        <!-- Кнопка закрытия -->
        <button id="closeFullscreen" class="absolute top-4 right-4 text-white hover:text-gray-300 z-20 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Счетчик -->
        <div id="photoCounter" class="absolute top-4 left-4 text-white bg-black bg-opacity-50 rounded-lg px-3 py-1 text-sm z-20"></div>

        <!-- Кнопки навигации -->
        <button id="prevPhoto" class="absolute left-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-3 transition-colors hover:bg-opacity-75 z-20">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button id="nextPhoto" class="absolute right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-3 transition-colors hover:bg-opacity-75 z-20">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Изображение -->
        <div class="max-w-7xl max-h-screen p-4">
            <img id="fullscreenImage" src="" alt="" class="max-w-full max-h-screen object-contain mx-auto">
        </div>

        <!-- Панель инструментов -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white p-6 z-20">
            <div class="container mx-auto">
                <!-- Информация о фото -->
                <div class="mb-4">
                    <h2 id="infoTitle" class="text-2xl font-bold mb-2"></h2>
                    <p id="infoDescription" class="text-gray-300 mb-2"></p>
                    <p id="infoCategory" class="text-sm text-gray-400 mb-2"></p>
                    <div id="infoTags" class="flex flex-wrap gap-2"></div>
                </div>

                <!-- Инструменты -->
                <div class="border-t border-gray-700 pt-4">
                    <div class="flex flex-wrap gap-3">
                        <!-- Скачать -->
                        <button id="downloadBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Скачать
                        </button>

                        <!-- Изменить размер -->
                        <button id="resizeBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                            Изменить размер
                        </button>

                        <!-- Конвертация -->
                        <button id="convertBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Конвертировать
                        </button>

                        <!-- Соотношение сторон -->
                        <button id="ratioBtn" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
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
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
        #fullscreenViewer.hidden {
            display: none !important;
        }
        #fullscreenViewer:not(.hidden) {
            display: flex !important;
        }
    </style>

    <script>
        function photobankApp() {
            return {
                // Состояние
                showUploadModal: false,
                showNewCategory: false,
                showFilters: false,
                loading: false,
                loadingMore: false,
                uploadLoading: false,
                photos: [],
                categoriesData: @json($categories->toArray()),
                tagsData: @json($tags->toArray()),
                totalPhotos: 0,
                nextPageUrl: null,
                currentPhoto: null,
                currentIndex: 0,
                toast: {
                    show: false,
                    message: '',
                    type: 'info'
                },

                // Фильтры
                filters: {
                    search: '',
                    category: '',
                    tags: ''
                },

                // Форма загрузки
                uploadForm: {
                    title: '',
                    description: '',
                    category_id: '',
                    tags: [],
                    photo: null
                },
                uploadErrors: {},
                uploadMessage: '',
                uploadMessageType: '',
                previewUrl: '',
                newCategory: { name: '' },

                // Computed
                get hasActiveFilters() {
                    return this.filters.search || this.filters.category || this.filters.tags;
                },
                get hasMorePages() {
                    return this.nextPageUrl !== null;
                },
                get getActiveFiltersCount() {
                    let count = 0;
                    if (this.filters.search) count++;
                    if (this.filters.category) count++;
                    if (this.filters.tags) count++;
                    return count;
                },

                // Инициализация
                init() {
                    this.loadPhotos();
                },

                // Утилиты
                showToast(type, message) {
                    this.toast.type = type;
                    this.toast.message = message;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },

                // Загрузка фотографий
                async loadPhotos() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.filters.search) params.append('search', this.filters.search);
                        if (this.filters.category) params.append('category', this.filters.category);
                        if (this.filters.tags) params.append('tags', this.filters.tags);
                        params.append('ajax', 'true');

                        const response = await fetch('/photobank?' + params.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        this.photos = data.photos || [];
                        this.totalPhotos = data.total || 0;
                        this.nextPageUrl = data.next_page_url;
                    } catch (error) {
                        console.error('Error loading photos:', error);
                        this.showToast('error', 'Ошибка загрузки фотографий');
                    } finally {
                        this.loading = false;
                    }
                },

                async loadMore() {
                    if (!this.nextPageUrl) return;
                    this.loadingMore = true;
                    try {
                        const response = await fetch(this.nextPageUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.photos = [...this.photos, ...(data.photos || [])];
                        this.nextPageUrl = data.next_page_url;
                    } catch (error) {
                        console.error('Error loading more photos:', error);
                        this.showToast('error', 'Ошибка загрузки дополнительных фото');
                    } finally {
                        this.loadingMore = false;
                    }
                },

                setCategoryFilter(categoryId) {
                    this.filters.category = categoryId;
                    this.loadPhotos();
                },

                clearFilters() {
                    this.filters = {
                        search: '',
                        category: '',
                        tags: ''
                    };
                    this.loadPhotos();
                },

                getCategoryName(categoryId) {
                    const category = this.categoriesData.find(c => c.id == categoryId);
                    return category ? category.name : '';
                },

                getTagName(tagId) {
                    const tag = this.tagsData.find(t => t.id == tagId);
                    return tag ? tag.name : '';
                },

                // Полноэкранный просмотр
                openFullscreen(photo) {
                    this.currentPhoto = photo;
                    this.currentIndex = this.photos.findIndex(p => p.id === photo.id);
                    this.initFullscreenViewer();
                },

                initFullscreenViewer() {
                    const viewer = document.getElementById('fullscreenViewer');
                    const image = document.getElementById('fullscreenImage');
                    const titleEl = document.getElementById('infoTitle');
                    const descEl = document.getElementById('infoDescription');
                    const categoryEl = document.getElementById('infoCategory');
                    const tagsEl = document.getElementById('infoTags');
                    const counterEl = document.getElementById('photoCounter');
                    const totalPhotos = this.photos.length;

                    const updateContent = () => {
                        if (!this.currentPhoto) return;
                        image.src = '/storage/' + this.currentPhoto.file_path;
                        titleEl.textContent = this.currentPhoto.title;
                        descEl.textContent = this.currentPhoto.description || '';
                        categoryEl.textContent = this.currentPhoto.category ? this.currentPhoto.category.name : 'Без категории';
                        counterEl.textContent = `${this.currentIndex + 1} / ${totalPhotos}`;

                        tagsEl.innerHTML = '';
                        if (this.currentPhoto.tags && this.currentPhoto.tags.length) {
                            this.currentPhoto.tags.forEach(tag => {
                                const span = document.createElement('span');
                                span.className = 'bg-green-500 bg-opacity-80 px-3 py-1 rounded-full text-sm';
                                span.textContent = tag.name;
                                tagsEl.appendChild(span);
                            });
                        }
                    };

                    const handlePrev = () => {
                        if (this.currentIndex > 0) {
                            this.currentIndex--;
                            this.currentPhoto = this.photos[this.currentIndex];
                            updateContent();
                        }
                    };

                    const handleNext = () => {
                        if (this.currentIndex < totalPhotos - 1) {
                            this.currentIndex++;
                            this.currentPhoto = this.photos[this.currentIndex];
                            updateContent();
                        }
                    };

                    const handleClose = () => {
                        viewer.classList.add('hidden');
                        viewer.classList.remove('flex');
                        document.body.style.overflow = '';
                        this.hideAllModals();
                    };

                    const handleKeydown = (e) => {
                        if (e.key === 'Escape') handleClose();
                        if (e.key === 'ArrowLeft') handlePrev();
                        if (e.key === 'ArrowRight') handleNext();
                    };

                    // Обновляем обработчики кнопок
                    const closeBtn = document.getElementById('closeFullscreen');
                    const prevBtn = document.getElementById('prevPhoto');
                    const nextBtn = document.getElementById('nextPhoto');
                    const downloadBtn = document.getElementById('downloadBtn');
                    const resizeBtn = document.getElementById('resizeBtn');
                    const convertBtn = document.getElementById('convertBtn');
                    const ratioBtn = document.getElementById('ratioBtn');

                    const newCloseBtn = closeBtn.cloneNode(true);
                    const newPrevBtn = prevBtn.cloneNode(true);
                    const newNextBtn = nextBtn.cloneNode(true);
                    const newDownloadBtn = downloadBtn.cloneNode(true);
                    const newResizeBtn = resizeBtn.cloneNode(true);
                    const newConvertBtn = convertBtn.cloneNode(true);
                    const newRatioBtn = ratioBtn.cloneNode(true);

                    if (closeBtn?.parentNode) closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);
                    if (prevBtn?.parentNode) prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
                    if (nextBtn?.parentNode) nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
                    if (downloadBtn?.parentNode) downloadBtn.parentNode.replaceChild(newDownloadBtn, downloadBtn);
                    if (resizeBtn?.parentNode) resizeBtn.parentNode.replaceChild(newResizeBtn, resizeBtn);
                    if (convertBtn?.parentNode) convertBtn.parentNode.replaceChild(newConvertBtn, convertBtn);
                    if (ratioBtn?.parentNode) ratioBtn.parentNode.replaceChild(newRatioBtn, ratioBtn);

                    newCloseBtn.addEventListener('click', handleClose);
                    newPrevBtn.addEventListener('click', handlePrev);
                    newNextBtn.addEventListener('click', handleNext);
                    newDownloadBtn.addEventListener('click', () => this.downloadPhoto());
                    newResizeBtn.addEventListener('click', () => this.showResizeModal());
                    newConvertBtn.addEventListener('click', () => this.showConvertModal());
                    newRatioBtn.addEventListener('click', () => this.showRatioModal());
                    document.addEventListener('keydown', handleKeydown);

                    // Сохраняем обработчики для удаления
                    this.fullscreenHandlers = { handleKeydown, handleClose };

                    updateContent();
                    viewer.classList.remove('hidden');
                    viewer.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                },

                hideAllModals() {
                    const modals = ['resizeModal', 'convertModal', 'ratioModal'];
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    });
                },

                showResizeModal() {
                    const modal = document.getElementById('resizeModal');
                    const widthInput = document.getElementById('resizeWidth');
                    const heightInput = document.getElementById('resizeHeight');
                    const cropCheck = document.getElementById('resizeCrop');
                    const applyBtn = document.getElementById('applyResizeBtn');
                    const cancelBtn = document.getElementById('cancelResizeBtn');

                    if (!modal) return;

                    widthInput.value = '';
                    heightInput.value = '';
                    cropCheck.checked = false;

                    const newApplyBtn = applyBtn.cloneNode(true);
                    const newCancelBtn = cancelBtn.cloneNode(true);
                    applyBtn.parentNode.replaceChild(newApplyBtn, applyBtn);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                    newApplyBtn.addEventListener('click', () => {
                        const width = parseInt(widthInput.value);
                        const height = parseInt(heightInput.value);
                        if (width && height) {
                            this.applyResize(width, height, cropCheck.checked);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        } else {
                            this.showToast('error', 'Укажите ширину и высоту');
                        }
                    });

                    newCancelBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                },

                showConvertModal() {
                    const modal = document.getElementById('convertModal');
                    const options = document.querySelectorAll('.convert-option');
                    const cancelBtn = document.getElementById('cancelConvertBtn');

                    if (!modal) return;

                    const newOptions = [];
                    options.forEach(opt => {
                        const newOpt = opt.cloneNode(true);
                        opt.parentNode.replaceChild(newOpt, opt);
                        newOptions.push(newOpt);
                    });

                    newOptions.forEach(opt => {
                        opt.addEventListener('click', () => {
                            const format = opt.getAttribute('data-format');
                            this.applyConvert(format);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        });
                    });

                    const newCancelBtn = cancelBtn.cloneNode(true);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    newCancelBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                },

                showRatioModal() {
                    const modal = document.getElementById('ratioModal');
                    const options = document.querySelectorAll('.ratio-option');
                    const cancelBtn = document.getElementById('cancelRatioBtn');

                    if (!modal) return;

                    const newOptions = [];
                    options.forEach(opt => {
                        const newOpt = opt.cloneNode(true);
                        opt.parentNode.replaceChild(newOpt, opt);
                        newOptions.push(newOpt);
                    });

                    newOptions.forEach(opt => {
                        opt.addEventListener('click', () => {
                            const ratio = opt.getAttribute('data-ratio');
                            this.applyAspectRatio(ratio);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        });
                    });

                    const newCancelBtn = cancelBtn.cloneNode(true);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    newCancelBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                },

                downloadPhoto() {
                    if (!this.currentPhoto) return;
                    const a = document.createElement('a');
                    a.href = '/storage/' + this.currentPhoto.file_path;
                    a.download = this.currentPhoto.title + '_' + this.currentPhoto.id + '.jpg';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    this.showToast('success', 'Скачивание начато');
                },

                async applyConvert(format) {
                    if (!this.currentPhoto) {
                        this.showToast('error', 'Фото не выбрано');
                        return;
                    }
                    try {
                        const response = await fetch(`/photobank/photos/${this.currentPhoto.id}/convert`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ format: format })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('success', `Конвертировано в ${format.toUpperCase()}`);
                            const a = document.createElement('a');
                            a.href = data.url;
                            a.download = `converted_${this.currentPhoto.id}.${format}`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        } else {
                            this.showToast('error', data.message || 'Ошибка конвертации');
                        }
                    } catch (error) {
                        console.error('Error converting:', error);
                        this.showToast('error', 'Ошибка при конвертации');
                    }
                },

                async applyResize(width, height, crop) {
                    if (!this.currentPhoto) {
                        this.showToast('error', 'Фото не выбрано');
                        return;
                    }
                    try {
                        const response = await fetch(`/photobank/photos/${this.currentPhoto.id}/resize`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                width: width,
                                height: height,
                                crop: crop
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('success', 'Размер изменен');
                            const a = document.createElement('a');
                            a.href = data.url;
                            a.download = `resized_${this.currentPhoto.id}.jpg`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        } else {
                            this.showToast('error', data.message || 'Ошибка изменения размера');
                        }
                    } catch (error) {
                        console.error('Error resizing:', error);
                        this.showToast('error', 'Ошибка при изменении размера');
                    }
                },

                async applyAspectRatio(ratio) {
                    if (!this.currentPhoto) {
                        this.showToast('error', 'Фото не выбрано');
                        return;
                    }
                    try {
                        const response = await fetch(`/photobank/photos/${this.currentPhoto.id}/aspect-ratio`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ ratio: ratio })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('success', `Соотношение изменено на ${ratio}`);
                            const a = document.createElement('a');
                            a.href = data.url;
                            a.download = `ratio_${this.currentPhoto.id}.jpg`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        } else {
                            this.showToast('error', data.message || 'Ошибка изменения соотношения');
                        }
                    } catch (error) {
                        console.error('Error changing aspect ratio:', error);
                        this.showToast('error', 'Ошибка при изменении соотношения');
                    }
                },

                // Удаление фото
                async deletePhoto(photo) {
                    if (!photo || !confirm(`Удалить фото "${photo.title}"?`)) return;

                    try {
                        const response = await fetch(`/photobank/photos/${photo.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('success', 'Фото удалено');
                            await this.loadPhotos();
                        } else {
                            this.showToast('error', data.message || 'Ошибка удаления');
                        }
                    } catch (error) {
                        console.error('Error deleting photo:', error);
                        this.showToast('error', 'Ошибка при удалении');
                    }
                },

                // Загрузка фото
                handleFileSelect(event) {
                    const file = event.target.files[0];

                    if (file) {
                        if (!file.type.startsWith('image/')) {
                            this.showUploadMessage('Пожалуйста, выберите изображение', 'error');
                            this.uploadForm.photo = null;
                            this.previewUrl = '';
                            return;
                        }

                        if (file.size > 20 * 1024 * 1024) {
                            this.showUploadMessage('Файл слишком большой. Максимум 20MB', 'error');
                            this.uploadForm.photo = null;
                            this.previewUrl = '';
                            return;
                        }

                        this.uploadForm.photo = file;

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewUrl = e.target.result;
                        };
                        reader.onerror = (e) => {
                            console.error('FileReader error:', e);
                            this.showUploadMessage('Ошибка при чтении файла', 'error');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.uploadForm.photo = null;
                        this.previewUrl = '';
                    }
                },

                async createCategory() {
                    if (!this.newCategory.name.trim()) {
                        this.showUploadMessage('Введите название категории', 'error');
                        return;
                    }

                    try {
                        const response = await fetch('/photobank/categories', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(this.newCategory)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.categoriesData.push(data.category);
                            this.uploadForm.category_id = data.category.id;
                            this.newCategory.name = '';
                            this.showNewCategory = false;
                            this.showUploadMessage('Категория создана успешно', 'success');
                        } else {
                            this.showUploadMessage(data.message || 'Ошибка при создании категории', 'error');
                        }
                    } catch (error) {
                        console.error('Error creating category:', error);
                        this.showUploadMessage('Ошибка при создании категории', 'error');
                    }
                },

                async uploadPhoto() {
                    this.uploadLoading = true;
                    this.uploadErrors = {};
                    this.uploadMessage = '';

                    if (!this.uploadForm.title.trim()) {
                        this.uploadErrors.title = 'Введите название';
                        this.uploadLoading = false;
                        this.showToast('error', 'Введите название');
                        return;
                    }

                    if (!this.uploadForm.category_id) {
                        this.uploadErrors.category_id = 'Выберите категорию';
                        this.uploadLoading = false;
                        this.showToast('error', 'Выберите категорию');
                        return;
                    }

                    if (!this.uploadForm.photo) {
                        this.uploadErrors.photo = 'Выберите фотографию';
                        this.uploadLoading = false;
                        this.showToast('error', 'Выберите фотографию');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('title', this.uploadForm.title);
                    formData.append('description', this.uploadForm.description);
                    formData.append('category_id', this.uploadForm.category_id);
                    formData.append('photo', this.uploadForm.photo);

                    if (Array.isArray(this.uploadForm.tags)) {
                        this.uploadForm.tags.forEach(tagId => formData.append('tags[]', tagId));
                    }

                    try {
                        const response = await fetch('/photobank/photos', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showToast('success', data.message || 'Фото успешно загружено');
                            this.resetUploadForm();
                            setTimeout(() => {
                                this.showUploadModal = false;
                                this.loadPhotos();
                            }, 1500);
                        } else {
                            if (data.errors) {
                                this.uploadErrors = data.errors;
                                this.showToast('error', 'Ошибка валидации');
                            } else {
                                this.showToast('error', data.message || 'Ошибка при загрузке');
                            }
                        }
                    } catch (error) {
                        console.error('Error uploading photo:', error);
                        this.showToast('error', 'Ошибка при загрузке фотографии');
                    } finally {
                        this.uploadLoading = false;
                    }
                },

                resetUploadForm() {
                    this.uploadForm = {
                        title: '',
                        description: '',
                        category_id: '',
                        tags: [],
                        photo: null
                    };
                    this.previewUrl = '';
                    this.newCategory.name = '';
                    this.uploadErrors = {};
                    this.uploadMessage = '';

                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                },

                showUploadMessage(text, type) {
                    this.uploadMessage = text;
                    this.uploadMessageType = type;
                    setTimeout(() => {
                        this.uploadMessage = '';
                    }, 5000);
                }
            }
        }
    </script>
@endsection
