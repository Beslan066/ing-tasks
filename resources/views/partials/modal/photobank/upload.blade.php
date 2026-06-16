<div x-show="showUploadModal" x-transition:enter="transition ease-out duration-300 "
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 backdrop-blur-md" style="display: none;">
    <div @click.away="showUploadModal = false"
         class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Добавить фотографию</h2>
                <button @click="showUploadModal = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="uploadPhoto" class="space-y-6">
                @csrf
                <div>
                    <input type="text" x-model="uploadForm.title"
                           placeholder="Название *" class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none dark:bg-gray-700 dark:text-white"
                           required >
                    <span x-show="uploadErrors.title" x-text="uploadErrors.title" class="text-red-500 text-sm mt-1"></span>
                </div>

                <div>
                            <textarea x-model="uploadForm.description" rows="3"
                                      class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600
                                      rounded-lg focus:border-green-400 focus:ring-4 focus:ring-green-100
                                      outline-none dark:bg-gray-700 dark:text-white" placeholder="Описание"></textarea>
                </div>

                <div>
                    <div class="flex gap-2">
                        <select x-model="uploadForm.category_id"
                                class="flex-1 px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none dark:bg-gray-700 bg-white"
                                required>
                            <option value="">Выберите категорию</option>
                            <template x-for="category in categoriesData" :key="category.id">
                                <option :value="category.id" x-text="category.name"></option>
                            </template>
                        </select>
                        <button type="button" @click="showNewCategory = !showNewCategory"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">+</button>
                    </div>
                    <div x-show="showNewCategory" class="flex gap-2 mt-2">
                        <input type="text" x-model="newCategory.name" placeholder="Новая категория"
                               class="flex-1 px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none dark:bg-gray-700 dark:text-white">
                        <button type="button" @click="createCategory"
                                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">Создать</button>
                    </div>
                    <span x-show="uploadErrors.category_id" x-text="uploadErrors.category_id" class="text-red-500 text-sm"></span>
                </div>

                <div>
                    <select x-model="uploadForm.tags" multiple
                            class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none dark:bg-gray-700 dark:text-white h-32">
                        <template x-for="tag in tagsData" :key="tag.id">
                            <option :value="tag.id" x-text="tag.name"></option>
                        </template>
                    </select>
                    <p class="text-sm text-gray-500 mt-2">Для выбора нескольких тегов удерживайте Ctrl</p>
                </div>

                <div>
                    <input type="file" @change="handleFileSelect"
                           class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-green-400 focus:ring-4 focus:ring-green-100 outline-none dark:bg-gray-700 dark:text-white"
                           accept="image/*" required>
                    <span x-show="uploadErrors.photo" x-text="uploadErrors.photo" class="text-red-500 text-sm mt-1"></span>
                    <p class="text-sm text-gray-500 mt-2">Поддерживаемые форматы: JPEG, PNG, GIF, WebP. Максимальный размер: 20MB</p>
                </div>

                <div x-show="previewUrl">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Предпросмотр</label>
                    <img :src="previewUrl" class="max-w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                </div>

                <div x-show="uploadMessage" :class="{'bg-green-100 border-green-400 text-green-700': uploadMessageType === 'success', 'bg-red-100 border-red-400 text-red-700': uploadMessageType === 'error'}"
                     class="px-4 py-3 rounded border">
                    <span x-text="uploadMessage"></span>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" :disabled="uploadLoading"
                            class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <span x-show="!uploadLoading">Загрузить фотографию</span>
                        <span x-show="uploadLoading">Загрузка...</span>
                    </button>
                    <button type="button" @click="showUploadModal = false"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>
