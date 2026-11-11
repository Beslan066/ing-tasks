<!-- Модальное окно загрузки фотографии (Alpine.js) -->
<div x-show="showUploadModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     role="dialog" aria-modal="true" aria-labelledby="uploadPhotoTitle"
     class="fixed inset-0 bg-slate-900/40 flex items-center justify-center p-4 z-50 backdrop-blur-sm"
     style="display: none;">

    <div @click.away="showUploadModal = false"
         x-show="showUploadModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         class="bg-white rounded-[24px] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl shadow-slate-900/10
                [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden max-[500px]:w-[98%] max-[500px]:max-h-[85vh]">

        <!-- Заголовок -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="flex justify-between items-center gap-4 px-8 py-6 max-[500px]:px-5 max-[500px]:py-4">
                <div class="min-w-0">
                    <h2 id="uploadPhotoTitle" class="text-[22px] font-semibold text-slate-800 tracking-tight max-[500px]:text-xl">Добавить фотографию</h2>
                    <p class="text-[13px] text-slate-400 mt-0.5 max-[500px]:text-[12px]">Заполните информацию и выберите файл</p>
                </div>
                <button type="button" @click="showUploadModal = false" aria-label="Закрыть"
                        class="shrink-0 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 transition-all duration-200 p-2.5 rounded-full focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Форма -->
        <form @submit.prevent="uploadPhoto" class="px-8 py-6 space-y-5 max-[500px]:px-5 max-[500px]:py-4">
            @csrf

            <!-- Название -->
            <div class="space-y-1.5">
                <label for="uploadPhotoName" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
                    Название <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="uploadPhotoName" x-model="uploadForm.title"
                       placeholder="Введите название" required
                       class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300">
                <p x-show="uploadErrors.title" x-text="uploadErrors.title" class="text-rose-500 text-xs mt-1"></p>
            </div>

            <!-- Описание -->
            <div class="space-y-1.5">
                <label for="uploadPhotoDescription" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
                    Описание
                </label>
                <textarea id="uploadPhotoDescription" x-model="uploadForm.description" rows="3"
                          placeholder="Добавьте описание..."
                          class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300 resize-none"></textarea>
            </div>

            <!-- Категория -->
            <div class="space-y-1.5">
                <label for="uploadPhotoCategory-button" class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px] cursor-pointer">
                    Категория <span class="text-rose-500">*</span>
                </label>
                <div class="flex gap-2">
                    <x-ui.select
                        id="uploadPhotoCategory"
                        variant="light"
                        class="flex-1 min-w-0"
                        placeholder="Выберите категорию"
                        empty-text="Категорий пока нет"
                        :options="[]"
                        x-model="uploadForm.category_id"
                        x-effect="options = categoriesData.map(c => ({ value: String(c.id), label: c.name }))"
                    />
                    <button type="button" @click="showNewCategory = !showNewCategory"
                            aria-label="Новая категория"
                            class="shrink-0 w-[42px] h-[42px] flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        <svg class="w-4 h-4 transition-transform duration-200" :class="showNewCategory ? 'rotate-45' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"></path>
                        </svg>
                    </button>
                </div>

                <div x-show="showNewCategory" class="flex gap-2 mt-2 max-[500px]:flex-col">
                    <input type="text" x-model="newCategory.name" placeholder="Новая категория"
                           aria-label="Название новой категории"
                           class="flex-1 min-w-0 px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-slate-700 placeholder-slate-400 text-sm hover:border-slate-300">
                    <x-ui.button type="button" variant="primary" x-on:click="createCategory">
                        Создать
                    </x-ui.button>
                </div>

                <p x-show="uploadErrors.category_id" x-text="uploadErrors.category_id" class="text-rose-500 text-xs mt-1"></p>
            </div>

            <!-- Теги -->
            <div class="space-y-1.5">
                <span class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">Теги</span>
                <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                    <template x-for="tag in tagsData" :key="tag.id">
                        <label class="cursor-pointer">
                            <input type="checkbox" class="sr-only peer" :value="tag.id" x-model="uploadForm.tags">
                            <span x-text="tag.name"
                                  class="inline-block px-3 py-1.5 rounded-full border border-slate-200 bg-slate-50/50 text-[13px] text-slate-600 hover:border-slate-300 transition-all duration-200
                                         peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/20"></span>
                        </label>
                    </template>
                    <p x-show="!tagsData || !tagsData.length" class="text-[12px] text-slate-500">Тегов пока нет</p>
                </div>
            </div>

            <!-- Файл -->
            <div class="space-y-1.5">
                <span class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">
                    Фотография <span class="text-rose-500">*</span>
                </span>

                <div class="relative group border border-dashed border-slate-300 rounded-xl p-8 text-center bg-slate-50/50 hover:bg-slate-50 transition-all duration-300 max-[500px]:p-6">
                    <input type="file" @change="handleFileSelect" accept="image/*" required
                           aria-label="Выберите фотографию"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="flex flex-col items-center justify-center pointer-events-none">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-slate-100 group-hover:scale-110 group-hover:border-emerald-100 transition-all duration-300">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M16 8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <p class="text-[13px] font-medium text-slate-700 mb-1">Нажмите или перетащите фото сюда</p>
                        <p class="text-[12px] text-slate-500">JPEG, PNG, GIF, WebP</p>
                        <p class="text-[12px] text-slate-500 mt-0.5">Максимальный размер: 20MB</p>
                    </div>
                </div>

                <p x-show="uploadErrors.photo" x-text="uploadErrors.photo" class="text-rose-500 text-xs mt-1"></p>
            </div>

            <!-- Предпросмотр -->
            <div x-show="previewUrl" class="space-y-1.5">
                <span class="block text-slate-600 text-[13px] font-medium max-[500px]:text-[12px]">Предпросмотр</span>
                <img :src="previewUrl" alt="Предпросмотр"
                     class="w-full h-48 object-cover rounded-xl border border-slate-200">
            </div>

            <!-- Сообщение о результате -->
            <div x-show="uploadMessage"
                 :class="{
                     'bg-emerald-50 border-emerald-100 text-emerald-800': uploadMessageType === 'success',
                     'bg-rose-50 border-rose-100 text-rose-800': uploadMessageType === 'error'
                 }"
                 class="px-4 py-3 rounded-xl border text-[13px]" role="status">
                <span x-text="uploadMessage"></span>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end items-center gap-3 pt-6 border-t border-slate-100 max-[500px]:flex-col-reverse max-[500px]:justify-center">
                <x-ui.button type="button" variant="secondary" x-on:click="showUploadModal = false">
                    Отмена
                </x-ui.button>

                <x-ui.button type="submit" variant="primary" x-bind:disabled="uploadLoading">
                    <span x-show="!uploadLoading">Загрузить фотографию</span>
                    <span x-show="uploadLoading">Загрузка...</span>
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
