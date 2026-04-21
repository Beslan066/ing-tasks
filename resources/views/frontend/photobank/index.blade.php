@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="photobankApp()">
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
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Теги</label>
                    <select x-model="filters.tags" @change="loadPhotos"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Все теги</option>
                        <template x-for="tag in tags" :key="tag.id">
                            <option :value="tag.id" x-text="tag.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div x-show="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                <template x-if="filters.search">
                <span class="inline-flex items-center gap-1 bg-blue-100 text-green-800 px-3 py-1 rounded-full text-sm">
                    Поиск: <span x-text="filters.search"></span>
                    <button @click="filters.search = ''; loadPhotos();" class="hover:text-green-900">×</button>
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
            <template x-for="category in categories" :key="category.id">
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
                             class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden group relative transition-all duration-300 hover:shadow-lg cursor-pointer"><img class="w-full h-48 object-cover"
                                 :src="getImageUrl(photo.file_path)"
                                 :alt="photo.title"
                                 loading="lazy">

                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-end p-3">
                                <div class="text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 mb-4 w-full">
                                    <h3 class="font-semibold text-sm mb-1" x-text="photo.title"></h3>
                                    <p class="text-xs opacity-90 mb-2" x-text="photo.category.name"></p>
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
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
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
                                    <template x-for="category in categories" :key="category.id">
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
                                <template x-for="tag in tags" :key="tag.id">
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

    <!-- Полноэкранный просмотр (отдельный div вне Alpine) -->
    <!-- Полноэкранный просмотр -->
    <div id="fullscreenViewer" class="fixed inset-0 bg-black bg-opacity-95 z-50 hidden items-center justify-center" style="display: none;">
        <button id="closeFullscreen" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <button id="prevPhoto" class="absolute left-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-3 transition-colors hover:bg-opacity-75">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button id="nextPhoto" class="absolute right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-3 transition-colors hover:bg-opacity-75">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <div class="max-w-7xl max-h-screen p-4">
            <img id="fullscreenImage" src="" alt="" class="max-w-full max-h-screen object-contain mx-auto">
        </div>

        <div id="fullscreenInfo" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white p-6">
            <div class="container mx-auto">
                <h2 id="infoTitle" class="text-2xl font-bold mb-2"></h2>
                <p id="infoDescription" class="text-gray-300 mb-2"></p>
                <p id="infoCategory" class="text-sm text-gray-400 mb-2"></p>
                <div id="infoTags" class="flex flex-wrap gap-2"></div>
                <p id="infoId" class="text-xs text-gray-500 mt-2"></p>
            </div>
        </div>

        <div id="photoCounter" class="absolute top-4 left-4 text-white bg-black bg-opacity-50 rounded-lg px-3 py-1 text-sm"></div>
    </div>
    <script>
        // Полноэкранный просмотр через отдельный объект
        const FullscreenViewer = {
            photos: [],
            currentIndex: 0,

            init() {
                this.viewer = document.getElementById('fullscreenViewer');
                this.image = document.getElementById('fullscreenImage');
                this.info = document.getElementById('fullscreenInfo');
                this.counter = document.getElementById('photoCounter');
                this.titleEl = document.getElementById('infoTitle');
                this.descriptionEl = document.getElementById('infoDescription');
                this.categoryEl = document.getElementById('infoCategory');
                this.tagsEl = document.getElementById('infoTags');
                this.idEl = document.getElementById('infoId');

                document.getElementById('closeFullscreen').onclick = () => this.close();
                document.getElementById('prevPhoto').onclick = () => this.prev();
                document.getElementById('nextPhoto').onclick = () => this.next();

                document.addEventListener('keydown', (e) => {
                    if (!this.isOpen()) return;
                    if (e.key === 'Escape') this.close();
                    if (e.key === 'ArrowLeft') this.prev();
                    if (e.key === 'ArrowRight') this.next();
                });
            },

            isOpen() {
                return this.viewer && this.viewer.classList.contains('flex');
            },

            open(photos, index) {
                this.photos = photos;
                this.currentIndex = index;
                this.updateContent();
                this.viewer.classList.remove('hidden');
                this.viewer.classList.add('flex');
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.viewer.classList.add('hidden');
                this.viewer.classList.remove('flex');
                document.body.style.overflow = '';
            },

            next() {
                if (this.currentIndex < this.photos.length - 1) {
                    this.currentIndex++;
                    this.updateContent();
                }
            },

            prev() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.updateContent();
                }
            },

            updateContent() {
                const photo = this.photos[this.currentIndex];
                if (!photo) return;

                this.image.src = '/storage/' + photo.file_path;
                this.titleEl.textContent = photo.title;
                this.descriptionEl.textContent = photo.description || '';
                this.categoryEl.textContent = photo.category.name;
                this.idEl.textContent = 'ID: ' + photo.id;

                // Теги
                this.tagsEl.innerHTML = '';
                if (photo.tags && photo.tags.length) {
                    photo.tags.forEach(tag => {
                        const span = document.createElement('span');
                        span.className = 'bg-green-500 bg-opacity-80 px-3 py-1 rounded-full text-sm';
                        span.textContent = tag.name;
                        this.tagsEl.appendChild(span);
                    });
                }

                // Счетчик
                if (this.photos.length > 1) {
                    this.counter.classList.remove('hidden');
                    this.counter.textContent = (this.currentIndex + 1) + ' / ' + this.photos.length;
                } else {
                    this.counter.classList.add('hidden');
                }

                // Информация
                this.info.classList.remove('hidden');
            }
        };

        function photobankApp() {
            return {
                showUploadModal: false,
                showNewCategory: false,
                showFilters: false,
                loading: false,
                loadingMore: false,
                uploadLoading: false,
                photos: [],
                categories: @json($categories),
                tags: @json($tags),
                totalPhotos: 0,
                nextPageUrl: null,

                filters: {
                    search: '',
                    category: '',
                    tags: ''
                },

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

                init() {
                    FullscreenViewer.init();
                    this.loadPhotos();
                },

                getImageUrl(filePath) {
                    return '/storage/' + filePath;
                },

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
                        this.photos = [];
                        this.totalPhotos = 0;
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
                    const category = this.categories.find(c => c.id == categoryId);
                    return category ? category.name : '';
                },

                getTagName(tagId) {
                    const tag = this.tags.find(t => t.id == tagId);
                    return tag ? tag.name : '';
                },

                openFullscreen(photo) {
                    const modalHtml = `
        <div id="tempFullscreen" class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center" style="z-index: 9999;">
            <button onclick="document.getElementById('tempFullscreen').remove()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="max-w-7xl max-h-screen p-4">
                <img src="/storage/${photo.file_path}" alt="${photo.title}" class="max-w-full max-h-screen object-contain mx-auto">
            </div>
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white p-6">
                <div class="container mx-auto">
                    <h2 class="text-2xl font-bold mb-2">${photo.title}</h2>
                    <p class="text-gray-300 mb-2">${photo.description || ''}</p>
                    <p class="text-sm text-gray-400 mb-2">${photo.category.name}</p>
                </div>
            </div>
        </div>
    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    document.body.style.overflow = 'hidden';

                    document.getElementById('tempFullscreen').addEventListener('click', function(e) {
                        if (e.target === this) this.remove();
                    });
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    this.uploadForm.photo = file;
                    if (file) {
                        this.previewUrl = URL.createObjectURL(file);
                    } else {
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
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(this.newCategory)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.categories.push(data.category);
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
                        return;
                    }

                    if (!this.uploadForm.category_id) {
                        this.uploadErrors.category_id = 'Выберите категорию';
                        this.uploadLoading = false;
                        return;
                    }

                    if (!this.uploadForm.photo) {
                        this.uploadErrors.photo = 'Выберите фотографию';
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
                    } else if (this.uploadForm.tags) {
                        formData.append('tags[]', this.uploadForm.tags);
                    }

                    try {
                        const response = await fetch('/photobank/photos', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showUploadMessage(data.message, 'success');
                            this.resetUploadForm();
                            setTimeout(() => {
                                this.showUploadModal = false;
                                this.loadPhotos();
                            }, 2000);
                        } else {
                            if (data.errors) {
                                this.uploadErrors = data.errors;
                            } else {
                                this.showUploadMessage(data.message, 'error');
                            }
                        }
                    } catch (error) {
                        console.error('Error uploading photo:', error);
                        this.showUploadMessage('Ошибка при загрузке фотографии', 'error');
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

        document.addEventListener('alpine:init', () => {
            Alpine.data('photobankApp', photobankApp);
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
