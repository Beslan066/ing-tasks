@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class=" mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Шапка с навигацией -->
            <div class="mb-8">
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-primary">
                                Главная
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-primary font-semibold">Моя почта</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Моя почта</h1>
                        <p class="text-gray-600 mt-2">{{ auth()->user()->name }} • {{ auth()->user()->email }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <!-- Ссылка на почту отдела, если пользователь состоит в отделе -->
                        @if(auth()->user()->department)
                            <a href="{{ route('departments.emails.index', auth()->user()->department) }}"
                               class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-building mr-2"></i> Почта отдела
                            </a>
                        @endif

                        <button onclick="document.getElementById('importModal').showModal()"
                                class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-upload mr-2"></i> Импорт
                        </button>
                        <a href="{{ route('personal.emails.export') }}"
                           class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i> Экспорт
                        </a>
                        <a href="{{ route('personal.emails.create') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition-colors shadow-sm">
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
                            <p class="text-2xl font-bold text-gray-800">{{ auth()->user()->getUnreadEmails() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope-open text-red-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Отправленные</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $sentCount ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-paper-plane text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                @if($trashedCount > 0)
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">В корзине</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $trashedCount }}</p>
                            </div>
                            <a href="{{ route('personal.emails.trash.index') }}"
                               class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                                <i class="fas fa-trash text-gray-500 text-xl"></i>
                            </a>
                        </div>
                    </div>
                @endif
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
                                    'inbox' => $inboxCount ?? 0,
                                    'sent' => $sentCount ?? 0,
                                    'drafts' => $draftCount ?? 0,
                                    'archived' => $archivedCount ?? 0,
                                    'important' => $importantCount ?? 0,
                                    'with_attachments' => $withAttachmentsCount ?? 0,
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
                                <a href="{{ route('personal.emails.index', ['filter' => $filter]) }}"
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

                        <!-- Отделы компании (для быстрого доступа) -->
                        @if($companyDepartments->count() > 0)
                            <div class="border-t pt-4">
                                <h4 class="font-medium text-gray-700 mb-3">Почта отделов</h4>
                                <div class="space-y-2">
                                    @foreach($companyDepartments as $department)
                                        @if($department->email)
                                            <a href="{{ route('departments.emails.index', $department) }}"
                                               class="flex items-center p-2 rounded hover:bg-gray-50">
                                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center mr-2">
                                                    <i class="fas fa-building text-indigo-500 text-xs"></i>
                                                </div>
                                                <span class="text-sm text-gray-700 truncate">{{ $department->name }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Дополнительные фильтры -->
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-medium text-gray-700 mb-3">Дополнительно</h4>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           onchange="toggleUnreadFilter()"
                                           id="unreadFilter"
                                           {{ request()->has('unread') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">Только непрочитанные</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           onchange="toggleAttachmentFilter()"
                                           id="attachmentFilter"
                                           {{ request()->has('attachments') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">С вложениями</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           onchange="toggleImportantFilter()"
                                           id="importantFilter"
                                           {{ request()->has('important') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="ml-2 text-sm text-gray-600">Только важные</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Правая колонка - Список писем -->
                <div class="lg:col-span-3">
                    <!-- Панель поиска -->
                    <div class="bg-white rounded-xl shadow mb-6">
                        <div class="p-4 border-b">
                            <form action="{{ route('personal.emails.index') }}" method="GET">
                                <div class="relative">
                                    <input type="text"
                                           name="search"
                                           id="emailSearch"
                                           value="{{ request('search') }}"
                                           placeholder="Поиск по письмам..."
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <div class="absolute left-4 top-3.5">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <div class="absolute right-4 top-3.5">
                                        @if(request('search'))
                                            <a href="{{ route('personal.emails.index') }}"
                                               class="text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <!-- Скрытые поля для сохранения фильтров -->
                                @if(request('filter'))
                                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                                @endif
                            </form>
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
                                        <option value="delete">В корзину</option>
                                    </select>
                                    <button onclick="applyBulkAction()"
                                            class="px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600">
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
                                            name="sort"
                                            onchange="this.form.submit()"
                                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Сначала новые</option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Сначала старые</option>
                                        <option value="subject_a-z" {{ request('sort') == 'subject_a-z' ? 'selected' : '' }}>Тема (А-Я)</option>
                                        <option value="subject_z-a" {{ request('sort') == 'subject_z-a' ? 'selected' : '' }}>Тема (Я-А)</option>
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
                                                    <a href="{{ route('personal.emails.show', $email) }}"
                                                       class="block hover:opacity-90">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <div class="flex items-center space-x-2">
                                                                <!-- Показываем отправителя или получателя в зависимости от типа -->
                                                                @if($filter === 'sent')
                                                                    <span class="font-semibold text-gray-900 truncate">
                                                                        Кому: {{ $email->to_emails[0] ?? 'Нет получателя' }}
                                                                    </span>
                                                                @else
                                                                    <span class="font-semibold text-gray-900 truncate">
                                                                        {{ $email->from_name ?? $email->from_email }}
                                                                    </span>
                                                                    <span class="text-sm text-gray-500 hidden md:inline">
                                                                        &lt;{{ $email->from_email }}&gt;
                                                                    </span>
                                                                @endif

                                                                <!-- Бейдж типа письма -->
                                                                @if($email->recipient_type === 'App\\Models\\Department')
                                                                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full">
                                                                        <i class="fas fa-building mr-1"></i> Отдел
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                @if($email->files->count() > 0)
                                                                    <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded flex items-center gap-1">
                                                                        <i class="fas fa-paperclip text-gray-400 text-xs"></i>
                                                                        {{ $email->files->count() }}
                                                                    </span>
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

                                                        <!-- Предпросмотр файлов -->
                                                        @if($email->files->count() > 0)
                                                            <div class="mb-2">
                                                                <div class="flex items-center gap-3 flex-wrap">
                                                                    @php
                                                                        $images = [];
                                                                        $otherFiles = [];

                                                                        foreach($email->files as $emailFile) {
                                                                            $extension = strtolower(pathinfo($emailFile->original_name, PATHINFO_EXTENSION));
                                                                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
                                                                                $images[] = $emailFile;
                                                                            } else {
                                                                                $otherFiles[] = $emailFile;
                                                                            }
                                                                        }
                                                                    @endphp

                                                                        <!-- Превью изображений -->
                                                                    @if(count($images) > 0)
                                                                        <div class="flex items-center gap-2">
                                                                            @foreach(array_slice($images, 0, 2) as $emailFile)
                                                                                @if($emailFile->file && $emailFile->file->path)
                                                                                    <div class="relative group">
                                                                                        <div class="w-16 h-16 bg-gray-100 rounded border border-gray-200 overflow-hidden flex items-center justify-center">
                                                                                            <img src="{{ Storage::url($emailFile->file->path) }}"
                                                                                                 alt="{{ $emailFile->original_name }}"
                                                                                                 class="max-w-full max-h-full object-contain"
                                                                                                 onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=\'fas fa-file-image text-gray-400 text-lg\'></i>';">
                                                                                        </div>
                                                                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                                                            <span class="text-white text-xs px-1 text-center truncate">{{ $emailFile->original_name }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            @endforeach

                                                                            @if(count($images) > 2)
                                                                                <div class="w-16 h-16 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                                                                    <span class="text-gray-600 text-sm">+{{ count($images) - 2 }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif

                                                                    <!-- Другие файлы -->
                                                                    @if(count($otherFiles) > 0)
                                                                        <div class="flex flex-col gap-1">
                                                                            @foreach(array_slice($otherFiles, 0, 3) as $emailFile)
                                                                                @php
                                                                                    $extension = strtolower(pathinfo($emailFile->original_name, PATHINFO_EXTENSION));
                                                                                    $icon = 'fa-file';
                                                                                    $type = 'Документ';

                                                                                    if (in_array($extension, ['pdf'])) {
                                                                                        $icon = 'fa-file-pdf';
                                                                                        $type = 'PDF';
                                                                                    } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                                        $icon = 'fa-file-word';
                                                                                        $type = 'Word';
                                                                                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                                                        $icon = 'fa-file-excel';
                                                                                        $type = 'Excel';
                                                                                    } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                                                        $icon = 'fa-file-powerpoint';
                                                                                        $type = 'PowerPoint';
                                                                                    } elseif (in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])) {
                                                                                        $icon = 'fa-file-archive';
                                                                                        $type = 'Архив';
                                                                                    } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'flac'])) {
                                                                                        $icon = 'fa-file-audio';
                                                                                        $type = 'Аудио';
                                                                                    } elseif (in_array($extension, ['mp4', 'avi', 'mov', 'mkv', 'wmv'])) {
                                                                                        $icon = 'fa-file-video';
                                                                                        $type = 'Видео';
                                                                                    } elseif (in_array($extension, ['txt'])) {
                                                                                        $icon = 'fa-file-alt';
                                                                                        $type = 'Текст';
                                                                                    }
                                                                                @endphp

                                                                                <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded text-sm text-gray-700 border border-gray-200">
                                                                                    <i class="fas {{ $icon }} text-gray-500"></i>
                                                                                    <div class="flex-1 min-w-0">
                                                                                        <div class="font-medium truncate" title="{{ $emailFile->original_name }}">
                                                                                            {{ Str::limit($emailFile->original_name, 25) }}
                                                                                        </div>
                                                                                        <div class="text-xs text-gray-500">{{ $type }} • {{ strtoupper($extension) }}</div>
                                                                                    </div>
                                                                                    @if($emailFile->file && $emailFile->file->size)
                                                                                        <div class="text-xs text-gray-500 whitespace-nowrap">
                                                                                            {{ round($emailFile->file->size / 1024) }} KB
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach

                                                                            @if(count($otherFiles) > 3)
                                                                                <div class="text-sm text-gray-600 px-2">
                                                                                    + ещё {{ count($otherFiles) - 3 }} файлов
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Общее количество файлов -->
                                                                <div class="mt-2 text-xs text-gray-500">
                                                                    Всего файлов: {{ $email->files->count() }}
                                                                    @php
                                                                        $totalSize = 0;
                                                                        foreach($email->files as $emailFile) {
                                                                            if($emailFile->file && $emailFile->file->size) {
                                                                                $totalSize += $emailFile->file->size;
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    @if($totalSize > 0)
                                                                        • {{ round($totalSize / 1024) }} KB
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif

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

                                                <!-- Действия для письма -->
                                                <div class="ml-3 flex items-center space-x-1">
                                                    @if(!$email->is_draft)
                                                        <a href="{{ route('personal.emails.reply.form', $email) }}"
                                                           class="p-2 text-gray-400 hover:text-primary rounded-lg hover:bg-gray-100"
                                                           title="Ответить">
                                                            <i class="fas fa-reply"></i>
                                                        </a>
                                                    @endif

                                                    <!-- Форма архивации -->
                                                    <form action="{{ route('personal.emails.toggle-archive', $email) }}"
                                                          method="POST"
                                                          class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                                class="p-2 {{ $email->is_archived ? 'text-yellow-500 hover:text-yellow-700' : 'text-gray-400 hover:text-gray-600' }} rounded-lg hover:bg-gray-100"
                                                                title="{{ $email->is_archived ? 'Извлечь из архива' : 'В архив' }}"
                                                                onclick="return confirm('{{ $email->is_archived ? 'Извлечь из архива?' : 'Переместить в архив?' }}')">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Форма удаления -->
                                                    <form action="{{ route('personal.emails.destroy', $email) }}"
                                                          method="POST"
                                                          class="inline"
                                                          id="deleteForm{{ $email->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                onclick="showDeleteModal('{{ $email->id }}', '{{ addslashes($email->subject) }}')"
                                                                class="p-2 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50"
                                                                title="В корзину">
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
                                        @elseif(request('filter') === 'sent')
                                            Вы еще не отправляли писем
                                        @else
                                            В папке "Входящие" пока нет писем
                                        @endif
                                    </p>
                                    <a href="{{ route('personal.emails.create') }}"
                                       class="inline-flex items-center px-5 py-2.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
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

            <form @if(isset($user->department)) action="{{ route('departments.emails.import', $department) }}"@endif
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

    <!-- Модальное окно удаления -->
    <dialog id="deleteModal" class="rounded-xl shadow-2xl p-0 max-w-md">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Переместить в корзину</h3>
                <button onclick="closeDeleteModal()"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="deleteForm"
                  action=""
                  method="POST">
                @csrf
                @method('DELETE')

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-3">
                        Письмо "<span id="emailSubject"></span>" будет перемещено в корзину.
                    </p>

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Причина удаления (необязательно):
                    </label>
                    <textarea name="delete_reason"
                              id="deleteReason"
                              rows="3"
                              placeholder="Например: устаревшая информация, ошибка в письме..."
                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 resize-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeDeleteModal()"
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Переместить в корзину
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

        // Инициализация
        document.addEventListener('DOMContentLoaded', function() {
            initEventListeners();
        });

        function initEventListeners() {
            // Обработчики для чекбоксов
            document.querySelectorAll('.email-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleEmailSelection(this.dataset.emailId);
                });
            });
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

        function updateSelectedCount() {
            const count = selectedEmails.size;
            document.getElementById('selectedCount').textContent = `Выбрано: ${count}`;
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

            if (action === 'delete' && !confirm(`Переместить ${selectedEmails.size} писем в корзину?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("personal.emails.bulk") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
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

        // Фильтрация
        function toggleUnreadFilter() {
            const isChecked = document.getElementById('unreadFilter').checked;
            updateUrlParam('unread', isChecked ? '1' : null);
        }

        function toggleAttachmentFilter() {
            const isChecked = document.getElementById('attachmentFilter').checked;
            updateUrlParam('attachments', isChecked ? '1' : null);
        }

        function toggleImportantFilter() {
            const isChecked = document.getElementById('importantFilter').checked;
            updateUrlParam('important', isChecked ? '1' : null);
        }

        function toggleTagFilter(tagId) {
            const currentTags = new URLSearchParams(window.location.search).getAll('tags[]');
            if (currentTags.includes(tagId)) {
                // Удаляем тег
                const newTags = currentTags.filter(tag => tag !== tagId);
                updateUrlParam('tags[]', newTags);
            } else {
                // Добавляем тег
                updateUrlParam('tags[]', [...currentTags, tagId]);
            }
        }

        function updateUrlParam(param, value) {
            const url = new URL(window.location.href);

            if (Array.isArray(value)) {
                url.searchParams.delete(param);
                value.forEach(val => {
                    if (val) url.searchParams.append(param, val);
                });
            } else {
                if (value) {
                    url.searchParams.set(param, value);
                } else {
                    url.searchParams.delete(param);
                }
            }

            window.location.href = url.toString();
        }

        function refreshList() {
            location.reload();
        }

        // Обработка удаления
        function showDeleteModal(emailId, subject) {
            if (confirm(`Переместить письмо "${subject}" в корзину?`)) {
                const form = document.getElementById(`deleteForm${emailId}`);
                if (form) {
                    form.submit();
                }
            }
        }
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
    </style>
@endpush
