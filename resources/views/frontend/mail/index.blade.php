@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Шапка с навигацией -->
            <div class="mb-8">
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('departments.index') }}" class="text-gray-500 hover:text-primary">
                                Отделы
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-gray-700 font-medium">{{ $department->name }}</span>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-primary font-semibold">Почта</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Почта отдела</h1>
                        <p class="text-gray-600 mt-2">{{ $department->company->name }} • {{ $department->name }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="document.getElementById('importModal').showModal()"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-upload mr-2"></i> Импорт
                        </button>
                        <a href="{{ route('departments.emails.export', $department) }}"
                           class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i> Экспорт
                        </a>
                        <a href="{{ route('departments.emails.create', $department) }}"
                           class="inline-flex items-center px-5 py-2.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-500 transition-colors shadow-sm">
                            <i class="fas fa-pen mr-2"></i> Написать письмо
                        </a>
                    </div>
                </div>
            </div>

            <!-- Статистика и быстрые действия -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Всего писем</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $emails->total() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Непрочитанных</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $department->getUnreadEmailCount() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope-open text-red-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Основной контейнер -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Левая колонка - Фильтры -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow p-6 sticky top-8">
                        <h3 class="font-semibold text-gray-800 mb-4">Фильтры</h3>

                        <!-- Быстрые фильтры -->
                        <div class="space-y-2 mb-6">
                            @php
                                $currentFilter = request('filter', 'inbox');
                                $filterCounts = [
                                    'inbox' => $department->emails()->received()->where('is_archived', false)->count(),
                                    'sent' => $department->emails()->sent()->count(),
                                    'drafts' => $department->emails()->drafts()->count(),
                                    'archived' => $department->emails()->archived()->count(),
                                    'important' => $department->emails()->important()->count(),
                                    'with_attachments' => $department->emails()->withAttachments()->count(),
                                ];
                            @endphp

                            @foreach([
                                'inbox' => ['icon' => 'inbox', 'label' => 'Входящие', 'color' => 'blue'],
                                'sent' => ['icon' => 'paper-plane', 'label' => 'Отправленные', 'color' => 'green'],
                                'drafts' => ['icon' => 'file-alt', 'label' => 'Черновики', 'color' => 'yellow'],
                                'archived' => ['icon' => 'archive', 'label' => 'Архив', 'color' => 'gray'],
                                'important' => ['icon' => 'star', 'label' => 'Важные', 'color' => 'orange'],
                                'with_attachments' => ['icon' => 'paperclip', 'label' => 'С вложениями', 'color' => 'purple'],
                            ] as $filter => $data)
                                <a href="{{ route('departments.emails.index', ['department' => $department, 'filter' => $filter]) }}"
                                   class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors {{ $currentFilter === $filter ? 'bg-blue-50 border-l-4 border-primary' : '' }}">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-{{ $data['color'] }}-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-{{ $data['icon'] }} text-{{ $data['color'] }}-500"></i>
                                        </div>
                                        <span class="font-medium text-gray-700">{{ $data['label'] }}</span>
                                    </div>
                                    <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-full">
                                    {{ $filterCounts[$filter] ?? 0 }}
                                </span>
                                </a>
                            @endforeach
                        </div>

                        <!-- Дополнительные фильтры -->
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-700 mb-3">Дополнительно</h4>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           onchange="toggleUnreadFilter()"
                                           id="unreadFilter"
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">Только непрочитанные</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           onchange="toggleAttachmentFilter()"
                                           id="attachmentFilter"
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">С вложениями</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           onchange="toggleImportantFilter()"
                                           id="importantFilter"
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">Только важные</span>
                                </label>
                            </div>
                        </div>

                        <!-- Метки -->
                        @if($tags->count() > 0)
                            <div class="border-t pt-4 mt-4">
                                <h4 class="font-medium text-gray-700 mb-3">Метки</h4>
                                <div class="space-y-2">
                                    @foreach($tags as $tag)
                                        <button onclick="toggleTagFilter('{{ $tag->id }}')"
                                                class="tag-filter flex items-center justify-between w-full p-2 rounded hover:bg-gray-50"
                                                data-tag-id="{{ $tag->id }}">
                                            <div class="flex items-center">
                                                <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $tag->color }}"></span>
                                                <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $tag->emails_count ?? 0 }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Правая колонка - Список писем -->
                <div class="lg:col-span-3">
                    <!-- Панель поиска -->
                    <div class="bg-white rounded-xl shadow mb-6">
                        <div class="p-4 border-b">
                            <div class="relative">
                                <input type="text"
                                       id="emailSearch"
                                       placeholder="Поиск по письмам..."
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <div class="absolute left-4 top-3.5">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <div class="absolute right-4 top-3.5">
                                    <button onclick="clearSearch()" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Панель действий -->
                        <div class="p-4 border-b">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center space-x-3">
                                    <select id="bulkAction"
                                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 bg-white">
                                        <option value="">Действия</option>
                                        <option value="mark_read">Отметить как прочитанные</option>
                                        <option value="mark_unread">Отметить как непрочитанные</option>
                                        <option value="mark_important">Пометить важными</option>
                                        <option value="archive">В архив</option>
                                        <option value="delete">Удалить</option>
                                    </select>
                                    <button onclick="applyBulkAction()"
                                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">
                                        Применить
                                    </button>
                                    <span id="selectedCount" class="text-sm text-gray-500">Выбрано: 0</span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button onclick="refreshList()"
                                            class="p-2 text-gray-500 hover:text-primary rounded-lg hover:bg-gray-100">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <select id="sortOrder"
                                            onchange="updateSortOrder()"
                                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <option value="newest">Сначала новые</option>
                                        <option value="oldest">Сначала старые</option>
                                        <option value="subject_a-z">Тема (А-Я)</option>
                                        <option value="subject_z-a">Тема (Я-А)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Список писем -->
                        <div id="emailsList">
                            @if($emails->count() > 0)
                                @foreach($emails as $email)
                                    <div class="email-item border-b hover:bg-gray-50 transition-colors {{ !$email->is_read ? 'bg-blue-50' : '' }}"
                                         data-email-id="{{ $email->id }}"
                                         data-read="{{ $email->is_read ? 'true' : 'false' }}"
                                         data-important="{{ $email->is_important ? 'true' : 'false' }}"
                                         data-attachments="{{ $email->has_attachments ? 'true' : 'false' }}">
                                        <div class="p-4">
                                            <div class="flex items-start">
                                                <!-- Чекбокс выбора -->
                                                <div class="mr-3 mt-1">
                                                    <input type="checkbox"
                                                           class="email-checkbox rounded border-gray-300 text-primary focus:ring-primary"
                                                           data-email-id="{{ $email->id }}">
                                                </div>

                                                <!-- Иконка состояния -->
                                                <div class="mr-3 mt-1">
                                                    @if($email->is_important)
                                                        <i class="fas fa-star text-yellow-500" title="Важное"></i>
                                                    @elseif(!$email->is_read)
                                                        <i class="fas fa-circle text-blue-500 text-xs" title="Непрочитанное"></i>
                                                    @else
                                                        <i class="far fa-envelope text-gray-400" title="Прочитанное"></i>
                                                    @endif
                                                </div>

                                                <!-- Контент письма -->
                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ route('departments.emails.show', [$department, $email]) }}"
                                                       class="block hover:opacity-90">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <div class="flex items-center space-x-2">
                                                        <span class="font-semibold text-gray-900 truncate">
                                                            {{ $email->from_name }}
                                                        </span>
                                                                <span class="text-sm text-gray-500 hidden md:inline">
                                                            &lt;{{ $email->from_email }}&gt;
                                                        </span>
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                @if($email->has_attachments)
                                                                    <i class="fas fa-paperclip text-gray-400 text-sm" title="Есть вложения"></i>
                                                                @endif
                                                                <span class="text-sm text-gray-500 whitespace-nowrap">
                                                            {{ $email->sent_at ? $email->sent_at->format('d.m.Y H:i') : 'Черновик' }}
                                                        </span>
                                                            </div>
                                                        </div>

                                                        <h4 class="font-medium text-gray-800 mb-1 truncate">
                                                            {{ $email->subject }}
                                                            @if($email->is_draft)
                                                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full ml-2">Черновик</span>
                                                            @endif
                                                        </h4>

                                                        <p class="text-sm text-gray-600 line-clamp-2 mb-2">
                                                            {{ Str::limit(strip_tags($email->body), 150) }}
                                                        </p>

                                                        <!-- Метки -->
                                                        @if($email->tags->count() > 0)
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($email->tags as $tag)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                        {{ $tag->name }}
                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </a>
                                                </div>

                                                <!-- Действия -->
                                                <div class="ml-3 flex items-center space-x-1">
                                                    @if(!$email->is_draft)
                                                        <a href="{{ route('departments.emails.reply.form', [$department, $email]) }}"
                                                           class="p-2 text-gray-400 hover:text-primary rounded-lg hover:bg-gray-100"
                                                           title="Ответить">
                                                            <i class="fas fa-reply"></i>
                                                        </a>
                                                    @endif

                                                    @if($email->is_archived)
                                                        <form action="{{ route('departments.emails.unarchive', [$department, $email]) }}"
                                                              method="POST"
                                                              class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="p-2 text-yellow-500 hover:text-yellow-700 rounded-lg hover:bg-yellow-50"
                                                                    title="Извлечь из архива">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('departments.emails.archive', [$department, $email]) }}"
                                                              method="POST"
                                                              class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                                                                    title="В архив">
                                                                <i class="fas fa-archive"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('departments.emails.destroy', [$department, $email]) }}"
                                                          method="POST"
                                                          class="inline"
                                                          onsubmit="return confirm('Удалить это письмо?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="p-2 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50"
                                                                title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Пустой список -->
                                <div class="p-12 text-center">
                                    <div class="w-24 h-24 mx-auto mb-6 text-gray-300">
                                        <i class="fas fa-envelope-open-text text-6xl"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Писем пока нет</h3>
                                    <p class="text-gray-500 mb-6">
                                        @if(request('filter') === 'drafts')
                                            У вас пока нет черновиков
                                        @elseif(request('filter') === 'archived')
                                            Архив пуст
                                        @elseif(request('filter') === 'important')
                                            Нет важных писем
                                        @else
                                            В папке "{{ $currentFilter === 'inbox' ? 'Входящие' : 'Отправленные' }}" пока нет писем
                                        @endif
                                    </p>
                                    <a href="{{ route('departments.emails.create', $department) }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-500 transition-colors">
                                        <i class="fas fa-pen mr-2"></i> Написать письмо
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Пагинация -->
                        @if($emails->hasPages())
                            <div class="p-4 border-t">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-500">
                                        Показано {{ $emails->firstItem() }}–{{ $emails->lastItem() }} из {{ $emails->total() }} писем
                                    </div>
                                    <div>
                                        {{ $emails->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно импорта -->
    <dialog id="importModal" class="rounded-xl shadow-2xl p-0 max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Импорт писем</h3>
                <button onclick="document.getElementById('importModal').close()"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('departments.emails.import', $department) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="importForm">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Выберите файл</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="importFile" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-secondary">
                                    <span>Загрузить файл</span>
                                    <input id="importFile"
                                           name="file"
                                           type="file"
                                           accept=".csv,.xlsx,.xls"
                                           class="sr-only"
                                           required>
                                </label>
                                <p class="pl-1">или перетащите сюда</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV, Excel до 10MB</p>
                        </div>
                    </div>
                    <div id="fileName" class="mt-2 text-sm text-gray-500 hidden"></div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Формат файла</label>
                    <select name="format"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('importModal').close()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-secondary transition-colors">
                        Импортировать
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        // Глобальные переменные
        let selectedEmails = new Set();
        let activeFilters = {
            unread: false,
            attachments: false,
            important: false,
            tags: new Set()
        };

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация выпадающих меню
            initDropdowns();

            // Обработчик загрузки файла импорта
            document.getElementById('importFile').addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                const fileDisplay = document.getElementById('fileName');
                if (fileName) {
                    fileDisplay.textContent = `Выбран файл: ${fileName}`;
                    fileDisplay.classList.remove('hidden');
                } else {
                    fileDisplay.classList.add('hidden');
                }
            });

            // Drag & drop для импорта файла
            const dropZone = document.querySelector('.border-dashed');
            if (dropZone) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                dropZone.addEventListener('drop', handleDrop, false);
            }
        });

        // Предотвращение стандартного поведения браузера
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Обработка перетаскивания файла
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            const input = document.getElementById('importFile');

            if (files.length > 0) {
                input.files = files;
                const event = new Event('change');
                input.dispatchEvent(event);
            }
        }

        // Переключение фильтров
        function toggleUnreadFilter() {
            activeFilters.unread = !activeFilters.unread;
            applyFilters();
        }

        function toggleAttachmentFilter() {
            activeFilters.attachments = !activeFilters.attachments;
            applyFilters();
        }

        function toggleImportantFilter() {
            activeFilters.important = !activeFilters.important;
            applyFilters();
        }

        function toggleTagFilter(tagId) {
            const button = document.querySelector(`[data-tag-id="${tagId}"]`);
            if (activeFilters.tags.has(tagId)) {
                activeFilters.tags.delete(tagId);
                button.classList.remove('bg-blue-50', 'border-l-4', 'border-primary');
            } else {
                activeFilters.tags.add(tagId);
                button.classList.add('bg-blue-50', 'border-l-4', 'border-primary');
            }
            applyFilters();
        }

        // Применение фильтров
        function applyFilters() {
            const emailItems = document.querySelectorAll('.email-item');

            emailItems.forEach(item => {
                let shouldShow = true;

                // Проверка фильтра непрочитанных
                if (activeFilters.unread && item.dataset.read === 'true') {
                    shouldShow = false;
                }

                // Проверка фильтра вложений
                if (activeFilters.attachments && item.dataset.attachments === 'false') {
                    shouldShow = false;
                }

                // Проверка фильтра важных
                if (activeFilters.important && item.dataset.important === 'false') {
                    shouldShow = false;
                }

                // Показываем/скрываем элемент
                item.style.display = shouldShow ? 'block' : 'none';
            });
        }

        // Поиск писем
        let searchTimeout;
        document.getElementById('emailSearch').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase();
                const emailItems = document.querySelectorAll('.email-item');

                emailItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }, 300);
        });

        function clearSearch() {
            document.getElementById('emailSearch').value = '';
            const emailItems = document.querySelectorAll('.email-item');
            emailItems.forEach(item => {
                item.style.display = 'block';
            });
        }

        // Массовые действия
        function updateSelectedCount() {
            const count = selectedEmails.size;
            document.getElementById('selectedCount').textContent = `Выбрано: ${count}`;
        }

        function toggleEmailSelection(emailId) {
            const checkbox = document.querySelector(`[data-email-id="${emailId}"]`);
            if (checkbox.checked) {
                selectedEmails.add(emailId);
            } else {
                selectedEmails.delete(emailId);
            }
            updateSelectedCount();
        }

        function selectAllEmails() {
            const checkboxes = document.querySelectorAll('.email-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = true;
                selectedEmails.add(cb.dataset.emailId);
            });
            updateSelectedCount();
        }

        function deselectAllEmails() {
            const checkboxes = document.querySelectorAll('.email-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = false;
            });
            selectedEmails.clear();
            updateSelectedCount();
        }

        async function applyBulkAction() {
            const action = document.getElementById('bulkAction').value;
            if (!action) {
                alert('Выберите действие');
                return;
            }

            if (selectedEmails.size === 0) {
                alert('Выберите хотя бы одно письмо');
                return;
            }

            if (action === 'delete' && !confirm(`Удалить ${selectedEmails.size} писем?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("departments.emails.bulk", $department) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: action,
                        emails: Array.from(selectedEmails)
                    })
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Произошла ошибка');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ошибка при выполнении действия');
            }
        }

        // Обновление сортировки
        function updateSortOrder() {
            const sortOrder = document.getElementById('sortOrder').value;
            // Здесь можно добавить AJAX запрос для пересортировки
            console.log('Сортировка изменена на:', sortOrder);
        }

        // Обновление списка
        function refreshList() {
            location.reload();
        }

        // Инициализация выпадающих меню
        function initDropdowns() {
            // Обработчики для чекбоксов
            document.querySelectorAll('.email-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleEmailSelection(this.dataset.emailId);
                });
            });
        }

        // Обработка закрытия модальных окон
        document.getElementById('importModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.close();
            }
        });

        // Обработка отправки формы импорта
        document.getElementById('importForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('importFile');
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Пожалуйста, выберите файл для импорта');
                return;
            }

            // Можно добавить индикатор загрузки
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Импорт...';
            submitBtn.disabled = true;
        });
    </script>
@endpush

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .border-dashed:hover {
            border-color: #4f46e5;
            transition: border-color 0.2s ease;
        }

        .sticky {
            position: sticky;
        }

        /* Анимации */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .email-item {
            animation: fadeIn 0.3s ease;
        }

        /* Кастомный скроллбар */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
@endpush
