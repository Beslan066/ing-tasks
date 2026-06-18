@extends('layouts.app')

@section('content')
    @php
        $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
        $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    @endphp
    <div x-data="photobankApp()" x-cloak>
        <!-- Заголовок и кнопки -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
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
                                <li class="text-sm {{ $backgroundEnabled && $backgroundImage ? 'text-white' : 'text-gray-800 dark:text-white/90' }}">Фотобанк</li>
                            </ol>
                        </nav>
        <div class="max-[500px]:hidden">
                @if($backgroundEnabled && $backgroundImage)
                    <h2 class="text-3xl font-bold text-white max-[500px]:text-[26px]">Фотобанк</h2>
                    <p class="text-white text-sm max-[500px]:text-[13px]">Инструменты для продуктивной работы</p>
                @else
                    <h2 class="text-3xl font-bold text-[#16a34a] max-[500px]:text-[26px]">Фотобанк</h2>
                    <p class="text-gray-700 text-sm max-[500px]:text-[13px]">Инструменты для продуктивной работы</p>
                @endif
            </div>
            <div class="flex gap-3">
                <button @click="showFilters = !showFilters"
                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
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
                    Добавить
                </button>
            </div>
        </div>

        <!-- Уведомления -->
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4" class="fixed bottom-4 right-4 z-50 max-w-sm w-full"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="toast.message"></span>
                </div>
            </div>
        </div>

        <!-- Раскрывающаяся панель фильтров - ВАРИАНТ С ФОНОМ -->
        @if($backgroundEnabled && $backgroundImage)
            <div x-show="showFilters"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="backdrop-blur-md bg-transparent/20 rounded-lg shadow-md p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Поиск</label>
                        <input type="text" x-model="filters.search" @input.debounce.500ms="loadPhotos"
                               class="w-full px-4 py-2 border-none bg-transparent/20 rounded-lg outline-none placeholder:text-white"
                               placeholder="Название, описание...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Категория</label>
                        <select x-model="filters.category" @change="loadPhotos"
                                class="w-full px-4 py-2 border-none bg-transparent/20 rounded-lg outline-none text-white">
                            <option value="">Все категории</option>
                            <template x-for="category in categoriesData" :key="category.id">
                                <option :value="category.id" x-text="category.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Теги</label>
                        <select x-model="filters.tags" @change="loadPhotos"
                                class="w-full px-4 py-2 border-none bg-transparent/20 rounded-lg outline-none text-white">
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
        @else
            <!-- Раскрывающаяся панель фильтров - ОБЫЧНЫЙ ВАРИАНТ -->
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
        @endif

        <!-- Быстрые категории -->
        <div class="flex items-center justify-center py-4 flex-wrap gap-2 mb-8">
            <button @click="setCategoryFilter('')"
                    :class="{'bg-green-600 text-white': !filters.category, 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300': filters.category}"
                    class="px-4 py-2 rounded-lg font-medium transition-colors border-none dark:border-gray-600">
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
        <div class="relative bg-transparent/20 backdrop-blur-md rounded-lg p-4">
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
                            <img class="w-full h-48 object-cover" :src="'/storage/' + photo.file_path" :alt="photo.title" loading="lazy">
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex gap-1 z-10">
                                <button @click.stop="deletePhoto(photo)"
                                        class="bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        @if($backgroundEnabled && $backgroundImage)
                            <h3 class="text-lg font-medium text-white dark:text-white mb-2"
                                x-text="hasActiveFilters ? 'Ничего не найдено' : 'Пока нет фотографий'"></h3>
                            <p class="text-white dark:text-gray-400 mb-6"
                               x-text="hasActiveFilters ? 'Попробуйте изменить параметры поиска' : 'Будьте первым, кто добавит фотографию!'">
                            </p>
                        @else
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                                x-text="hasActiveFilters ? 'Ничего не найдено' : 'Пока нет фотографий'"></h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6"
                               x-text="hasActiveFilters ? 'Попробуйте изменить параметры поиска' : 'Будьте первым, кто добавит фотографию!'">
                            </p>
                        @endif
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
        @include('partials.modal.photobank.upload')
    </div>

    <!-- Полноэкранный просмотр с инструментами -->
    @include('partials.modal.photobank.fullscreen-view')

    <style>
        [x-cloak] { display: none !important; }
        #fullscreenViewer.hidden { display: none !important; }
        #fullscreenViewer:not(.hidden) { display: flex !important; }
    </style>

    <script>
        function photobankApp() {
            return {
                // Состояние - ВСЕ переменные ДОЛЖНЫ быть внутри return
                showUploadModal: false,
                showNewCategory: false,
                showFilters: false,
                loading: false,
                loadingMore: false,
                uploadLoading: false,
                photos: [],
                categoriesData: [],
                tagsData: [],
                totalPhotos: 0,
                nextPageUrl: null,
                currentPhoto: null,
                currentIndex: 0,
                toast: { show: false, message: '', type: 'info' },
                fullscreenHandlers: null,

                // Фильтры
                filters: { search: '', category: '', tags: '' },

                // Форма загрузки
                uploadForm: { title: '', description: '', category_id: '', tags: [], photo: null },
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
                    console.log('Initializing app...');
                    // Загружаем категории и теги через AJAX
                    this.loadCategoriesAndTags();
                    this.loadPhotos();
                },

                // Загрузка категорий и тегов через AJAX
                async loadCategoriesAndTags() {
                    try {
                        console.log('Loading categories via AJAX...');

                        // Загружаем категории
                        const catsResponse = await fetch('/photobank/categories', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const catsData = await catsResponse.json();
                        console.log('Categories response:', catsData);

                        if (catsData.success && catsData.data) {
                            this.categoriesData = catsData.data;
                            console.log('Categories loaded:', this.categoriesData.length);
                        } else if (Array.isArray(catsData)) {
                            this.categoriesData = catsData;
                        } else if (catsData.data && Array.isArray(catsData.data)) {
                            this.categoriesData = catsData.data;
                        }

                        // Загружаем теги
                        const tagsResponse = await fetch('/photobank/tags', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        const tagsData = await tagsResponse.json();
                        console.log('Tags response:', tagsData);

                        if (tagsData.success && tagsData.data) {
                            this.tagsData = tagsData.data;
                            console.log('Tags loaded:', this.tagsData.length);
                        } else if (Array.isArray(tagsData)) {
                            this.tagsData = tagsData;
                        } else if (tagsData.data && Array.isArray(tagsData.data)) {
                            this.tagsData = tagsData.data;
                        }
                    } catch (error) {
                        console.error('Error loading categories/tags:', error);
                    }
                },

                // Утилиты
                showToast(type, message) {
                    this.toast.type = type;
                    this.toast.message = message;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
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
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
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
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        this.photos = [...this.photos, ...(data.photos || [])];
                        this.nextPageUrl = data.next_page_url;
                    } catch (error) {
                        console.error('Error loading more:', error);
                        this.showToast('error', 'Ошибка загрузки');
                    } finally {
                        this.loadingMore = false;
                    }
                },

                setCategoryFilter(categoryId) {
                    this.filters.category = categoryId;
                    this.loadPhotos();
                },

                clearFilters() {
                    this.filters = { search: '', category: '', tags: '' };
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
                        if (this.fullscreenHandlers) {
                            document.removeEventListener('keydown', this.fullscreenHandlers.handleKeydown);
                        }
                    };

                    const handleKeydown = (e) => {
                        if (e.key === 'Escape') handleClose();
                        if (e.key === 'ArrowLeft') handlePrev();
                        if (e.key === 'ArrowRight') handleNext();
                    };

                    this.fullscreenHandlers = { handleKeydown, handleClose };

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
                    newDownloadBtn.addEventListener('click', () => this.showDownloadModal());
                    newResizeBtn.addEventListener('click', () => this.showResizeModal());
                    newConvertBtn.addEventListener('click', () => this.showConvertModal());
                    newRatioBtn.addEventListener('click', () => this.showRatioModal());
                    document.addEventListener('keydown', handleKeydown);

                    updateContent();
                    viewer.classList.remove('hidden');
                    viewer.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                },

                hideAllModals() {
                    ['resizeModal', 'convertModal', 'ratioModal', 'downloadModal'].forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    });
                },

                showResizeModal() {
                    const modal = document.getElementById('resizeModal');
                    if (!modal) return;
                    document.getElementById('resizeWidth').value = '';
                    document.getElementById('resizeHeight').value = '';
                    document.getElementById('resizeCrop').checked = false;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    const applyBtn = document.getElementById('applyResizeBtn');
                    const cancelBtn = document.getElementById('cancelResizeBtn');
                    const newApplyBtn = applyBtn.cloneNode(true);
                    const newCancelBtn = cancelBtn.cloneNode(true);
                    applyBtn.parentNode.replaceChild(newApplyBtn, applyBtn);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                    newApplyBtn.onclick = () => {
                        const width = parseInt(document.getElementById('resizeWidth').value);
                        const height = parseInt(document.getElementById('resizeHeight').value);
                        if (width && height) {
                            this.applyResize(width, height, document.getElementById('resizeCrop').checked);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        } else {
                            this.showToast('error', 'Укажите ширину и высоту');
                        }
                    };
                    newCancelBtn.onclick = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    };
                },

                showConvertModal() {
                    const modal = document.getElementById('convertModal');
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    const options = document.querySelectorAll('#convertModal .convert-option');
                    const cancelBtn = document.getElementById('cancelConvertBtn');

                    options.forEach(opt => {
                        const newOpt = opt.cloneNode(true);
                        opt.parentNode.replaceChild(newOpt, opt);
                        newOpt.onclick = () => {
                            const format = newOpt.getAttribute('data-format');
                            this.applyConvert(format);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        };
                    });

                    const newCancelBtn = cancelBtn.cloneNode(true);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    newCancelBtn.onclick = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    };
                },

                showRatioModal() {
                    const modal = document.getElementById('ratioModal');
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    const options = document.querySelectorAll('#ratioModal .ratio-option');
                    const cancelBtn = document.getElementById('cancelRatioBtn');

                    options.forEach(opt => {
                        const newOpt = opt.cloneNode(true);
                        opt.parentNode.replaceChild(newOpt, opt);
                        newOpt.onclick = () => {
                            const ratio = newOpt.getAttribute('data-ratio');
                            this.applyAspectRatio(ratio);
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        };
                    });

                    const newCancelBtn = cancelBtn.cloneNode(true);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    newCancelBtn.onclick = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    };
                },

                showDownloadModal() {
                    const modal = document.getElementById('downloadModal');
                    if (!modal) return;

                    document.getElementById('downloadResizeEnable').checked = false;
                    document.getElementById('downloadResizeOptions').style.display = 'none';
                    document.getElementById('downloadRatioEnable').checked = false;
                    document.getElementById('downloadRatioOptions').style.display = 'none';
                    document.getElementById('downloadWidth').value = '';
                    document.getElementById('downloadHeight').value = '';
                    document.getElementById('downloadKeepProportions').checked = true;
                    document.getElementById('downloadCrop').checked = false;
                    document.getElementById('downloadQuality').value = 85;
                    document.getElementById('downloadQualityValue').textContent = '85%';

                    const defaultFormat = 'jpeg';
                    document.querySelectorAll('.download-format-option').forEach(btn => {
                        btn.classList.remove('bg-green-500', 'text-white');
                        btn.classList.add('bg-gray-100', 'dark:bg-gray-700');
                    });
                    const defaultBtn = document.querySelector(`.download-format-option[data-download-format="${defaultFormat}"]`);
                    if (defaultBtn) {
                        defaultBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                        defaultBtn.classList.add('bg-green-500', 'text-white');
                    }
                    this.toggleQualitySection(defaultFormat);

                    const applyBtn = document.getElementById('applyDownloadBtn');
                    const cancelBtn = document.getElementById('cancelDownloadBtn');
                    const newApplyBtn = applyBtn.cloneNode(true);
                    const newCancelBtn = cancelBtn.cloneNode(true);
                    applyBtn.parentNode.replaceChild(newApplyBtn, applyBtn);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                    document.querySelectorAll('.download-format-option').forEach(btn => {
                        const newBtn = btn.cloneNode(true);
                        btn.parentNode.replaceChild(newBtn, btn);
                        newBtn.onclick = () => {
                            document.querySelectorAll('.download-format-option').forEach(b => {
                                b.classList.remove('bg-green-500', 'text-white');
                                b.classList.add('bg-gray-100', 'dark:bg-gray-700');
                            });
                            newBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                            newBtn.classList.add('bg-green-500', 'text-white');
                            this.toggleQualitySection(newBtn.getAttribute('data-download-format'));
                        };
                    });

                    const resizeCheckbox = document.getElementById('downloadResizeEnable');
                    const newResizeCheckbox = resizeCheckbox.cloneNode(true);
                    resizeCheckbox.parentNode.replaceChild(newResizeCheckbox, resizeCheckbox);
                    newResizeCheckbox.onchange = (e) => {
                        document.getElementById('downloadResizeOptions').style.display = e.target.checked ? 'block' : 'none';
                    };

                    const ratioCheckbox = document.getElementById('downloadRatioEnable');
                    const newRatioCheckbox = ratioCheckbox.cloneNode(true);
                    ratioCheckbox.parentNode.replaceChild(newRatioCheckbox, ratioCheckbox);
                    newRatioCheckbox.onchange = (e) => {
                        document.getElementById('downloadRatioOptions').style.display = e.target.checked ? 'block' : 'none';
                        if (e.target.checked) {
                            document.getElementById('downloadResizeEnable').checked = false;
                            document.getElementById('downloadResizeOptions').style.display = 'none';
                        }
                    };

                    const qualitySlider = document.getElementById('downloadQuality');
                    const newQualitySlider = qualitySlider.cloneNode(true);
                    qualitySlider.parentNode.replaceChild(newQualitySlider, qualitySlider);
                    newQualitySlider.oninput = (e) => {
                        document.getElementById('downloadQualityValue').textContent = e.target.value + '%';
                    };

                    document.querySelectorAll('.download-ratio-option').forEach(btn => {
                        const newBtn = btn.cloneNode(true);
                        btn.parentNode.replaceChild(newBtn, btn);
                        newBtn.onclick = () => {
                            document.querySelectorAll('.download-ratio-option').forEach(b => {
                                b.classList.remove('bg-green-500', 'text-white');
                                b.classList.add('bg-gray-100', 'dark:bg-gray-700');
                            });
                            newBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                            newBtn.classList.add('bg-green-500', 'text-white');
                        };
                    });

                    newApplyBtn.onclick = () => {
                        const format = document.querySelector('.download-format-option.bg-green-500')?.getAttribute('data-download-format') || 'jpeg';
                        const quality = parseInt(document.getElementById('downloadQuality').value);
                        const resizeEnabled = document.getElementById('downloadResizeEnable').checked;
                        const ratioEnabled = document.getElementById('downloadRatioEnable').checked;
                        let width = null, height = null, crop = false, keepProportions = true;
                        if (resizeEnabled) {
                            width = document.getElementById('downloadWidth').value ? parseInt(document.getElementById('downloadWidth').value) : null;
                            height = document.getElementById('downloadHeight').value ? parseInt(document.getElementById('downloadHeight').value) : null;
                            crop = document.getElementById('downloadCrop').checked;
                            keepProportions = document.getElementById('downloadKeepProportions').checked;
                        }
                        let ratio = null;
                        if (ratioEnabled) {
                            const ratioBtn = document.querySelector('.download-ratio-option.bg-green-500');
                            if (ratioBtn) ratio = ratioBtn.getAttribute('data-download-ratio');
                        }
                        this.downloadWithSettings(format, quality, width, height, crop, keepProportions, ratio);
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    };
                    newCancelBtn.onclick = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    };
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                },

                toggleQualitySection(format) {
                    const qualitySection = document.getElementById('downloadQualitySection');
                    if (qualitySection) {
                        qualitySection.style.display = (format === 'jpeg' || format === 'webp') ? 'block' : 'none';
                    }
                },

                async downloadWithSettings(format, quality, width, height, crop, keepProportions, ratio) {
                    if (!this.currentPhoto) {
                        this.showToast('error', 'Фото не выбрано');
                        return;
                    }
                    this.showToast('info', 'Обработка изображения...');
                    try {
                        let currentUrl = '/storage/' + this.currentPhoto.file_path;
                        let currentPhotoData = this.currentPhoto;

                        if (ratio) {
                            const ratioResponse = await this.applyAspectRatioToPhoto(currentPhotoData.id, ratio);
                            if (ratioResponse.success) {
                                currentUrl = ratioResponse.url;
                                const imgInfo = await this.loadImageInfo(currentUrl);
                                currentPhotoData = { ...currentPhotoData, width: imgInfo.width, height: imgInfo.height, file_path: ratioResponse.path };
                            }
                        }

                        if (width || height) {
                            const resizeResponse = await this.applyResizeToPhoto(currentPhotoData.id, width, height, crop, keepProportions);
                            if (resizeResponse.success) {
                                currentUrl = resizeResponse.url;
                                const imgInfo = await this.loadImageInfo(currentUrl);
                                currentPhotoData = { ...currentPhotoData, width: imgInfo.width, height: imgInfo.height, file_path: resizeResponse.path };
                            }
                        }

                        const originalFormat = currentPhotoData.file_path?.split('.').pop()?.toLowerCase();
                        if (format !== originalFormat || (format === 'jpeg' || format === 'webp')) {
                            const convertResponse = await this.applyConvertToPhoto(currentPhotoData.id, format, quality);
                            if (convertResponse.success) currentUrl = convertResponse.url;
                        }

                        const a = document.createElement('a');
                        a.href = currentUrl;
                        const extension = format === 'jpeg' ? 'jpg' : format;
                        a.download = `${this.currentPhoto.title}_processed.${extension}`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        this.showToast('success', 'Изображение обработано и скачано');
                    } catch (error) {
                        console.error('Error processing image:', error);
                        this.showToast('error', 'Ошибка при обработке изображения');
                    }
                },

                async loadImageInfo(url) {
                    return new Promise((resolve, reject) => {
                        const img = new Image();
                        img.onload = () => resolve({ width: img.width, height: img.height });
                        img.onerror = reject;
                        img.src = url;
                    });
                },

                async applyAspectRatioToPhoto(photoId, ratio) {
                    const response = await fetch(`/photobank/photos/${photoId}/aspect-ratio`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ ratio: ratio })
                    });
                    return await response.json();
                },

                async applyResizeToPhoto(photoId, width, height, crop = false, keepProportions = true) {
                    const response = await fetch(`/photobank/photos/${photoId}/resize`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ width: width, height: height, crop: crop, keep_proportions: keepProportions })
                    });
                    return await response.json();
                },

                async applyConvertToPhoto(photoId, format, quality = 85) {
                    const response = await fetch(`/photobank/photos/${photoId}/convert`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ format: format, quality: quality })
                    });
                    return await response.json();
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
                    if (!this.currentPhoto) return;
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
                    if (!this.currentPhoto) return;
                    try {
                        const response = await fetch(`/photobank/photos/${this.currentPhoto.id}/resize`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ width: width, height: height, crop: crop })
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
                    if (!this.currentPhoto) return;
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
                        this.showToast('error', 'Введите название');
                        this.uploadLoading = false;
                        return;
                    }
                    if (!this.uploadForm.category_id) {
                        this.showToast('error', 'Выберите категорию');
                        this.uploadLoading = false;
                        return;
                    }
                    if (!this.uploadForm.photo) {
                        this.showToast('error', 'Выберите фотографию');
                        this.uploadLoading = false;
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
                            this.showToast('error', data.message || 'Ошибка при загрузке');
                        }
                    } catch (error) {
                        console.error('Error uploading photo:', error);
                        this.showToast('error', 'Ошибка при загрузке фотографии');
                    } finally {
                        this.uploadLoading = false;
                    }
                },

                resetUploadForm() {
                    this.uploadForm = { title: '', description: '', category_id: '', tags: [], photo: null };
                    this.previewUrl = '';
                    this.newCategory.name = '';
                    this.uploadErrors = {};
                    this.uploadMessage = '';
                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput) fileInput.value = '';
                },

                showUploadMessage(text, type) {
                    this.uploadMessage = text;
                    this.uploadMessageType = type;
                    setTimeout(() => {
                        this.uploadMessage = '';
                    }, 5000);
                }
            };
        }
    </script>
@endsection
