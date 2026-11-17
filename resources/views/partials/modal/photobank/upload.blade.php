{{-- Upload Photo Modal --}}
<div x-show="showUploadModal"
     x-transition.opacity.duration.200ms
     x-effect="document.body.classList.toggle('overflow-hidden', showUploadModal)"
     @keydown.escape.window="showUploadModal = false"
     @click.self="showUploadModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
     style="display: none;">

    <div x-show="showUploadModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-5 scale-[0.98]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-5 scale-[0.98]"
         role="dialog"
         aria-modal="true"
         aria-labelledby="uploadPhotoModalTitle"
         class="bg-white dark:bg-gray-800 rounded-[24px] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                scrollbar-none [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:max-h-[85vh]">

        {{-- Заголовок --}}
        <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-slate-100 dark:border-gray-700">
            <div class="flex justify-between items-center px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div>
                    <h3 id="uploadPhotoModalTitle"
                        class="text-[22px] font-semibold text-slate-800 dark:text-gray-100 tracking-tight max-[500px]:text-xl">
                        Добавить фотографию
                    </h3>
                    <p class="text-[13px] text-slate-500 dark:text-gray-400 mt-0.5 max-[500px]:text-[12px]">
                        Загрузите изображение и укажите детали
                    </p>
                </div>
                <button type="button"
                        @click="showUploadModal = false"
                        aria-label="Закрыть"
                        class="text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Форма --}}
        <form @submit.prevent="uploadPhoto">
            @csrf

            <div class="px-8 py-6 space-y-6 max-[500px]:px-5 max-[500px]:py-4">

                {{-- Название --}}
                <div class="space-y-1.5">
                    <label for="uploadPhotoTitle"
                           class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                        Название <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="uploadPhotoTitle"
                           x-model="uploadForm.title"
                           placeholder="Введите название"
                           required
                           class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300">
                    <p x-show="uploadErrors.title" x-text="uploadErrors.title" class="text-[12px] text-rose-600"></p>
                </div>

                {{-- Описание --}}
                <div class="space-y-1.5">
                    <label for="uploadPhotoDescription"
                           class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                        Описание
                    </label>
                    <textarea id="uploadPhotoDescription"
                              x-model="uploadForm.description"
                              rows="3"
                              placeholder="Добавьте описание..."
                              class="w-full px-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300 resize-none"></textarea>
                </div>

                {{-- Категория --}}
                <div class="space-y-1.5">
                    <label for="uploadPhotoCategory"
                           class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                        Категория <span class="text-rose-500">*</span>
                    </label>

                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <select id="uploadPhotoCategory"
                                    x-model="uploadForm.category_id"
                                    required
                                    class="w-full appearance-none pl-4 pr-10 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 text-sm hover:border-slate-300 cursor-pointer">
                                <option value="">Выберите категорию</option>
                                <template x-for="category in categoriesData" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>

                        <button type="button"
                                @click="showNewCategory = !showNewCategory"
                                :aria-expanded="showNewCategory"
                                aria-label="Новая категория"
                                title="Новая категория"
                                class="flex-shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-slate-600 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-gray-600 hover:border-slate-300 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-slate-200/60">
                            <i class="fas text-xs transition-transform duration-200"
                               :class="showNewCategory ? 'fa-minus' : 'fa-plus'"></i>
                        </button>
                    </div>

                    {{-- Новая категория --}}
                    <div x-show="showNewCategory"
                         x-transition.opacity.duration.200ms
                         class="flex gap-2 pt-1">
                        <input type="text"
                               x-model="newCategory.name"
                               placeholder="Название новой категории"
                               aria-label="Название новой категории"
                               class="flex-1 min-w-0 px-4 py-2.5 bg-slate-50/50 dark:bg-gray-700/50 border border-slate-200 dark:border-gray-600 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 dark:text-gray-100 placeholder-slate-400 text-sm hover:border-slate-300">
                        <x-ui.button
                            type="button"
                            variant="action"
                            icon="fas fa-check"
                            @click="createCategory"
                        >
                            Создать
                        </x-ui.button>
                    </div>

                    <p x-show="uploadErrors.category_id" x-text="uploadErrors.category_id" class="text-[12px] text-rose-600"></p>
                </div>

                {{-- Теги --}}
                <div class="space-y-2">
                    <span class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                        Теги
                    </span>

                    <div class="flex flex-wrap gap-2">
                        <template x-if="!tagsData || tagsData.length === 0">
                            <p class="text-[12px] text-slate-500 dark:text-gray-400">Теги пока не созданы</p>
                        </template>

                        <template x-for="tag in tagsData" :key="tag.id">
                            <label class="inline-flex items-center px-3 py-1.5 rounded-full border text-[12px] font-medium cursor-pointer select-none transition-all duration-200 focus-within:ring-4 focus-within:ring-emerald-500/10"
                                   :class="uploadForm.tags.some(id => id == tag.id)
                                        ? 'bg-emerald-50 border-emerald-300 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/40 dark:text-emerald-300'
                                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200'">
                                <input type="checkbox"
                                       :value="tag.id"
                                       x-model="uploadForm.tags"
                                       class="sr-only">
                                <i class="fas fa-check text-[9px] mr-1.5"
                                   x-show="uploadForm.tags.some(id => id == tag.id)"></i>
                                <span x-text="tag.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Файл --}}
                <div class="space-y-1.5">
                    <span class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">
                        Фотография <span class="text-rose-500">*</span>
                    </span>

                    <div x-data="{ drag: false }"
                         @dragover.prevent="drag = true"
                         @dragleave="drag = false"
                         @drop="drag = false"
                         class="relative group border border-dashed rounded-xl p-8 text-center transition-all duration-300 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10 max-[500px]:p-6"
                         :class="drag
                             ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 scale-[0.99]'
                             : 'border-slate-300 dark:border-gray-600 bg-slate-50/50 dark:bg-gray-700/30 hover:bg-slate-50 dark:hover:bg-gray-700/50'">
                        <input type="file"
                               @change="handleFileSelect"
                               accept="image/*"
                               required
                               aria-label="Выбрать фотографию"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-100 dark:border-gray-600 group-hover:scale-110 group-hover:border-emerald-100 transition-all duration-300">
                                <i class="fas fa-cloud-upload-alt text-xl text-emerald-500"></i>
                            </div>
                            <p class="text-[13px] font-medium text-slate-700 dark:text-gray-100 mb-1">Нажмите или перетащите файл сюда</p>
                            <p class="text-[12px] text-slate-500 dark:text-gray-400">JPEG, PNG, GIF, WebP</p>
                            <p class="text-[11px] text-slate-500 dark:text-gray-400 mt-0.5">Максимальный размер: 20MB</p>
                        </div>
                    </div>

                    <p x-show="uploadErrors.photo" x-text="uploadErrors.photo" class="text-[12px] text-rose-600"></p>
                </div>

                {{-- Предпросмотр --}}
                <div x-show="previewUrl"
                     x-transition.opacity.duration.200ms
                     class="space-y-1.5">
                    <span class="block text-slate-600 dark:text-gray-300 text-[13px] font-medium max-[500px]:text-[12px]">Предпросмотр</span>
                    <div class="rounded-xl border border-slate-200 dark:border-gray-600 bg-slate-50/50 dark:bg-gray-700/30 p-2">
                        <img :src="previewUrl" alt="Предпросмотр" class="w-full max-h-64 object-contain rounded-lg">
                    </div>
                </div>

                {{-- Сообщение --}}
                <div x-show="uploadMessage"
                     x-transition.opacity.duration.200ms
                     role="status"
                     :class="uploadMessageType === 'success'
                         ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-300'
                         : 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-500/10 dark:border-rose-500/30 dark:text-rose-300'"
                     class="flex items-start gap-2.5 px-4 py-3 rounded-xl border text-[13px]">
                    <i class="fas mt-0.5"
                       :class="uploadMessageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                    <span x-text="uploadMessage"></span>
                </div>
            </div>

            {{-- Кнопки действий --}}
            <div class="sticky bottom-0 z-10 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-t border-slate-100 dark:border-gray-700
                        flex justify-end items-center gap-3 px-8 py-4
                        max-[500px]:px-5 max-[500px]:flex-col-reverse max-[500px]:justify-center">
                <x-ui.button
                    type="button"
                    variant="secondary"
                    @click="showUploadModal = false"
                >
                    Отмена
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    variant="primary"
                    icon="fas fa-upload"
                    x-bind:disabled="uploadLoading"
                >
                    <span x-show="!uploadLoading">Загрузить фотографию</span>
                    <span x-show="uploadLoading"><i class="fas fa-spinner fa-spin mr-1.5"></i>Загрузка...</span>
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
