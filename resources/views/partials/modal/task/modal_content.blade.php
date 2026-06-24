{{-- ВРЕМЕННО: отладка подзадач --}}
@php
    \Log::info('=== IN VIEW ===');
    \Log::info('$subtasks count: ' . (isset($subtasks) ? $subtasks->count() : 'NOT SET'));
    if (isset($subtasks) && $subtasks->count() > 0) {
        foreach($subtasks as $st) {
            \Log::info('View subtask: ' . $st->id . ' - ' . $st->name);
        }
    }
@endphp

<div class="flex h-full max-[800px]:flex-col">

    {{-- ЛЕВАЯ КОЛОНКА - Информация о задаче --}}
    <div class="w-2/5 border-r border-gray-200 pr-6 overflow-y-auto flex flex-col max-[800px]:hidden">
        <div>
            {{-- Заголовок и статус --}}
        <div class="mb-6">
            <div class="flex items-start justify-between">
                <h2 class="text-xl font-bold text-gray-800 break-words pr-4">{{ $task->name }}</h2>
                <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap flex-shrink-0
                    @if($task->status === 'выполнена') bg-green-100 text-green-800
                    @elseif($task->status === 'в работе') bg-blue-100 text-blue-800
                    @elseif($task->status === 'не назначена') bg-yellow-100 text-yellow-800
                    @elseif($task->status === 'просрочена') bg-red-100 text-red-800
                    @elseif($task->status === 'на проверке') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $task->status }}
                </span>
            </div>

            {{-- Приоритет с индикаторами уровня (как на второй странице) --}}
            @if($task->priority)
                @php
                    $prioritySignals = [
                        'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                        'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                        'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                        'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                    ];
                    $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                @endphp
                <div class="mt-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}">
                        <div class="flex items-end gap-[3px] h-5">
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"></div>
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"></div>
                        </div>
                        <span class="text-sm font-medium {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Описание --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wide">Описание</h3>
            <div class="text-gray-700 text-sm whitespace-pre-wrap bg-gray-50 p-[10px] rounded-lg">
                {{ $task->description ?: 'Нет описания' }}
            </div>
        </div>

        {{-- Информационные блоки в стиле Битрикс24 --}}
        <div class="space-y-4 bg-white p-[10px] rounded-lg">
            {{-- Исполнитель --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Исполнитель:</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full {{ $task->user ? $task->user->getAvatarColor() : 'bg-gray-300' }} flex items-center justify-center text-white text-xs font-medium">
                            {{ $task->user ? $task->user->getInitials() : '?' }}
                        </div>
                        <span class="text-sm text-gray-800">{{ $task->user?->name ?? 'Не назначен' }}</span>
                    </div>
                </div>
            </div>

            {{-- Автор --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Автор:</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full {{ $task->author->getAvatarColor() }} flex items-center justify-center text-white text-xs font-medium">
                            {{ $task->author->getInitials() }}
                        </div>
                        <span class="text-sm text-gray-800">{{ $task->author->name }}</span>
                    </div>
                </div>
            </div>

            {{-- Отдел --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Отдел:</div>
                <div class="flex-1 text-sm text-gray-800">{{ $task->department?->name ?? ($task->is_personal ? 'Личная задача' : 'Общая задача') }}</div>
            </div>

            {{-- Категория --}}
            @if($task->category)
                <div class="flex items-start">
                    <div class="w-24 text-sm text-gray-500 flex-shrink-0">Категория:</div>
                    <div class="flex-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $task->category->color }}20; color: {{ $task->category->color }}">
                        {{ $task->category->name }}
                    </span>
                    </div>
                </div>
            @endif

            {{-- Дедлайн --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Дедлайн:</div>
                <div class="flex-1">
                    @if($task->deadline)
                        <span class="text-sm {{ $task->deadline->isPast() && $task->status !== 'выполнена' ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                            {{ $task->deadline->format('d.m.Y H:i') }}
                        </span>
                        @if($task->deadline->isPast() && $task->status !== 'выполнена')
                            <span class="ml-2 text-xs text-red-500">(Просрочено)</span>
                        @endif
                    @else
                        <span class="text-sm text-gray-400">Не указан</span>
                    @endif
                </div>
            </div>

            {{-- Время --}}
            @if($task->estimated_hours || $task->actual_hours)
                <div class="flex items-start">
                    <div class="w-24 text-sm text-gray-500 flex-shrink-0">Время:</div>
                    <div class="flex-1">
                        @if($task->estimated_hours)
                            <div class="text-sm text-gray-600">Планируемое: {{ $task->estimated_hours }} ч.</div>
                        @endif
                        @if($task->actual_hours)
                            <div class="text-sm text-gray-600">Фактическое: {{ $task->actual_hours }} ч.</div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Даты создания и завершения --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Создана:</div>
                <div class="flex-1 text-sm text-gray-600">{{ $task->created_at->format('d.m.Y H:i') }}</div>
            </div>

            @if($task->completed_at)
                <div class="flex items-start">
                    <div class="w-24 text-sm text-gray-500 flex-shrink-0">Завершена:</div>
                    <div class="flex-1 text-sm text-gray-600">{{ $task->completed_at->format('d.m.Y H:i') }}</div>
                </div>
            @endif
        </div>

        {{-- Файлы --}}
        @php
            // Принудительно проверяем и загружаем файлы
            if (!isset($files) || $files === null) {
                $files = collect();
            }

            // Дополнительная проверка через связь задачи
            if ($files->count() == 0 && isset($task) && $task->relationLoaded('files')) {
                $files = $task->files;
            }

            // Логирование для отладки (видно в ларавел лог)
            if ($files->count() > 0) {
                \Log::info('Files found in view: ' . $files->count());
                foreach($files as $f) {
                    \Log::info('File name: ' . ($f->name ?? 'no name'));
                }
            }
        @endphp

        @if($files && $files->count() > 0)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-500 mb-3 uppercase tracking-wide">
                    <i class="fas fa-paperclip mr-2"></i>Вложения ({{ $files->count() }})
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($files as $file)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                @php
                                    $fileName = $file->name ?? $file->original_name ?? 'Файл';
                                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                    $icon = 'fa-file-alt';
                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = 'fa-file-image';
                                    elseif (in_array($extension, ['pdf'])) $icon = 'fa-file-pdf';
                                    elseif (in_array($extension, ['doc', 'docx'])) $icon = 'fa-file-word';
                                    elseif (in_array($extension, ['xls', 'xlsx'])) $icon = 'fa-file-excel';
                                    elseif (in_array($extension, ['zip', 'rar', '7z'])) $icon = 'fa-file-archive';

                                    $filePath = $file->file_path ?? $file->path ?? '';
                                    $fileUrl = $filePath ? Storage::url($filePath) : '#';
                                    $fileSize = $file->size ?? $file->file_size ?? 0;
                                    $formattedSize = $fileSize ? round($fileSize / 1024, 1) . ' KB' : '~ KB';
                                @endphp
                                <i class="fas {{ $icon }} text-gray-400 group-hover:text-blue-500 transition"></i>
                                <a href="{{ $fileUrl }}"
                                   target="_blank"
                                   class="text-blue-500 hover:underline text-sm truncate"
                                   title="{{ $fileName }}">
                                    {{ $fileName }}
                                </a>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                                {{ $formattedSize }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="text-xs text-gray-400 p-2 bg-gray-50 rounded">
                    <i class="fas fa-info-circle mr-1"></i>
                    Файлов не найдено
                </div>
            </div>
        @endif
        </div>


        <div class="mt-auto">
           <div class="py-1 grid grid-cols-6 gap-1 max-[1250px]:grid-cols-4">

                 @if($task->status === 'назначена') <button onclick="startTask({{ $task->id }})" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[900px]:col-span-4">
                    <i class="fas fa-play mr-2 text-white"></i> Начать
                </button>
                 @elseif($task->status === 'в работе') <button onclick="sendForReview({{ $task->id }})" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[900px]:col-span-4">
                    <i class="fas fa-play mr-2 text-white"></i> Отправить на проверку
                </button>
                @else <button onclick="startTask({{ $task->id }})" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[900px]:col-span-4">
                    <i class="fas fa-play mr-2 text-white"></i> Завершить
                </button>
                @endif

                <button onclick="showRejectModal({{ $task->id }})" class="bg-red-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-red-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[900px]:col-span-4">
                    <i class="fas fa-times-circle mr-2"></i> Отказаться
                </button>
                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                    <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-center col-span-2 max-[900px]:col-span-4">
                        <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                    </button>
                @endif
                    <!-- КНОПКА АРХИВАЦИИ -->
                    @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                        <button onclick="archiveTask({{ $task->id }})"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-center col-span-2 max-[900px]:col-span-4">
                            <i class="fas fa-archive mr-2 text-yellow-500"></i> В архив
                        </button>
                    @endif
                <button onclick="openCreateSubtaskModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-center col-span-2 max-[1250px]:col-span-4">
                    <i class="fas fa-list mr-2 text-green-500"></i>Подзадача
                </button>
            </div>
        </div>
    </div>

    {{-- ПРАВАЯ КОЛОНКА --}}
    <div class="w-3/5 flex flex-col h-100 chat-background rounded-lg max-[800px]:w-full max-[800px]:h-full">
        {{-- Табы для переключения между комментариями и подзадачами --}}
        <div class="items-center border-b border-gray-200 bg-white flex">
             <button onclick="switchTaskTab('info')"
                            id="tabInfoBtn"
                            class="flex-1 px-4 py-3 text-sm font-medium transition-all duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hidden max-[800px]:block max-[500px]:h-full">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="max-[500px]:hidden">Информация о задаче</span>
                        <span class="hidden max-[500px]:inline">Инфо</span>
                    </button>
            <button onclick="switchTaskTab('comments')"
                    id="tabCommentsBtn"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-all duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700  max-[500px]:h-full">
                <i class="fas fa-comments mr-2"></i>Сообщения
                <span id="commentsCount" class="ml-1 text-xs text-gray-400 max-[500px]:hidden">
                    ({{ isset($comments) && $comments ? (method_exists($comments, 'total') ? $comments->total() : $comments->count()) : 0 }})
                </span>
            </button>
            <button onclick="switchTaskTab('subtasks')"
                    id="tabSubtasksBtn"
                    class="flex-1 px-4 py-3 text-sm font-medium transition-all duration-200 border-b-2 border-transparent text-gray-500 hover:text-gray-700 max-[500px]:h-full">
                <i class="fas fa-tasks mr-2"></i>Подзадачи
                <span id="subtasksCount" class="ml-1 text-xs text-gray-400 max-[500px]:hidden">({{ $subtasks->count() }})</span>
            </button>
        </div>

        {{-- КОНТЕНТ: Комментаии --}}
        <div id="commentsTab" class="flex-1 flex flex-col h-full">

            @if($task->is_personal)
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-lock text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Сообщений к задаче нет</p>
                        <p class="text-sm text-gray-400 mt-1">Сообщения недоступны для личных задач</p>
                    </div>
                </div>
            @else
                {{-- Список комментариев --}}
                <div id="commentsList" class="flex-1 overflow-y-auto space-y-3 p-2" style="max-height: calc(90vh - 200px);">
                    @if(isset($comments) && $comments && $comments->count() > 0)
                        @foreach($comments as $comment)
                            @include('partials.modal.task.comment_item', ['comment' => $comment, 'taskId' => $task->id, 'level' => 0])
                        @endforeach

                        @if($comments->hasMorePages())
                            <div class="text-center py-2">
                                <button onclick="loadMoreComments({{ $task->id }}, '{{ $comments->nextPageUrl() }}')"
                                        class="text-sm text-blue-500 hover:text-blue-600">
                                    <i class="fas fa-chevron-down mr-1"></i> Загрузить еще
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-comment-dots text-5xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-400 mt-1">Напишите первое сообщение</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Форма добавления комментария --}}
                @if(isset($canComment) && $canComment)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-start space-x-3">
                            <div class="flex-1 relative p-4">
                                <textarea id="commentInput"
                                          rows="2"
                                          class="w-full px-3 py-2 pr-12 border placeholder-pt-2 border-gray-300 rounded-lg focus:ring-2 h-[80px] focus:ring-blue-500 focus:border-transparent resize-none text-sm"
                                          placeholder="Напишите сообщение..."></textarea>
                                <button onclick="submitComment({{ $task->id }})"
                                        class="absolute right-[2rem] top-1/2 -translate-y-1/2 p-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm flex w-[40px] h-[40px] items-center justify-center">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- КОНТЕНТ: Подзадачи --}}
        <div id="subtasksTab" class="flex-1 flex flex-col h-full hidden">
            <div class="mt-2 flex justify-end">
                @if(auth()->user()->canViewAllCompanyTasks() || $task->author_id === auth()->id())
                    <button onclick="openCreateSubtaskModal({{ $task->id }})"
                            class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition flex items-center mr-2  ">
                        <i class="fas fa-plus mr-1"></i> Добавить подзадачу
                    </button>
                @endif
            </div>

            <div id="subtasksList" class="flex-1 overflow-y-auto space-y-2 p-2" style="max-height: calc(90vh - 200px);">
                @if($task->subtasks && $task->subtasks->count() > 0)
                    @foreach($task->subtasks as $subtask)
                        <div class="subtask-item bg-gray-50 rounded-lg p-3 border border-gray-200" data-subtask-id="{{ $subtask->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3 flex-1">
                                    <button onclick="toggleSubtask({{ $subtask->id }})"
                                            class="mt-0.5 flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-all
                                {{ $subtask->status === 'выполнена' ? 'bg-green-500 border-green-500' : 'border-gray-300' }}">
                                        @if($subtask->status === 'выполнена')
                                            <i class="fas fa-check text-white text-xs"></i>
                                        @endif
                                    </button>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $subtask->name }}</p>
                                        @if($subtask->description)
                                            <p class="text-sm text-gray-500">{{ $subtask->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                                            @if($subtask->user)
                                                <span>👤 {{ $subtask->user->name }}</span>
                                            @endif
                                            @if($subtask->deadline)
                                                <span>📅 {{ $subtask->deadline->format('d.m.Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button onclick="deleteSubtask({{ $subtask->id }})" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- КОНТЕНТ: Информация --}}
        <div id="infoTab" class="w-2/5 border-r border-gray-200 p-6 overflow-y-auto flex-col hidden max-[800px]:w-full max-[500px]:p-4">
        <div>
            {{-- Заголовок и статус --}}
        <div class="mb-6">
            <div class="flex items-start justify-between">
                <h2 class="text-xl font-bold text-gray-800 break-words pr-4">{{ $task->name }}</h2>
                <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap flex-shrink-0
                    @if($task->status === 'выполнена') bg-green-100 text-green-800
                    @elseif($task->status === 'в работе') bg-blue-100 text-blue-800
                    @elseif($task->status === 'не назначена') bg-yellow-100 text-yellow-800
                    @elseif($task->status === 'просрочена') bg-red-100 text-red-800
                    @elseif($task->status === 'на проверке') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $task->status }}
                </span>
            </div>

            {{-- Приоритет с индикаторами уровня (как на второй странице) --}}
            @if($task->priority)
                @php
                    $prioritySignals = [
                        'низкий' => ['level' => 1, 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'filled' => 'bg-green-500', 'empty' => 'bg-green-200', 'text' => 'text-green-700'],
                        'средний' => ['level' => 2, 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-100', 'text' => 'text-blue-700'],
                        'высокий' => ['level' => 3, 'color' => 'orange', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-100', 'text' => 'text-orange-700'],
                        'критический' => ['level' => 4, 'color' => 'red', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'filled' => 'bg-red-500', 'empty' => 'bg-red-100', 'text' => 'text-red-700'],
                    ];
                    $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                @endphp
                <div class="mt-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md {{ $signal['bg'] }} border {{ $signal['border'] }}">
                        <div class="flex items-end gap-[3px] h-5">
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-2"></div>
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                            <div class="w-1.5 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-5"></div>
                        </div>
                        <span class="text-sm font-medium {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Описание --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wide">Описание</h3>
            <div class="text-gray-700 text-sm whitespace-pre-wrap bg-gray-50 p-[10px] rounded-lg">
                {{ $task->description ?: 'Нет описания' }}
            </div>
        </div>

        {{-- Информационные блоки в стиле Битрикс24 --}}
        <div class="space-y-4 bg-white p-[10px] rounded-lg">
            {{-- Исполнитель --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Исполнитель:</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full {{ $task->user ? $task->user->getAvatarColor() : 'bg-gray-300' }} flex items-center justify-center text-white text-xs font-medium">
                            {{ $task->user ? $task->user->getInitials() : '?' }}
                        </div>
                        <span class="text-sm text-gray-800">{{ $task->user?->name ?? 'Не назначен' }}</span>
                    </div>
                </div>
            </div>

            {{-- Автор --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Автор:</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full {{ $task->author->getAvatarColor() }} flex items-center justify-center text-white text-xs font-medium">
                            {{ $task->author->getInitials() }}
                        </div>
                        <span class="text-sm text-gray-800">{{ $task->author->name }}</span>
                    </div>
                </div>
            </div>

            {{-- Отдел --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Отдел:</div>
                <div class="flex-1 text-sm text-gray-800">{{ $task->department?->name ?? ($task->is_personal ? 'Личная задача' : 'Общая задача') }}</div>
            </div>

            {{-- Категория --}}
            @if($task->category)
                <div class="flex items-start">
                    <div class="w-24 text-sm text-gray-500 flex-shrink-0">Категория:</div>
                    <div class="flex-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $task->category->color }}20; color: {{ $task->category->color }}">
                        {{ $task->category->name }}
                    </span>
                    </div>
                </div>
            @endif

            {{-- Дедлайн --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Дедлайн:</div>
                <div class="flex-1">
                    @if($task->deadline)
                        <span class="text-sm {{ $task->deadline->isPast() && $task->status !== 'выполнена' ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                            {{ $task->deadline->format('d.m.Y H:i') }}
                        </span>
                        @if($task->deadline->isPast() && $task->status !== 'выполнена')
                            <span class="ml-2 text-xs text-red-500">(Просрочено)</span>
                        @endif
                    @else
                        <span class="text-sm text-gray-400">Не указан</span>
                    @endif
                </div>
            </div>

            {{-- Время --}}
            @if($task->estimated_hours || $task->actual_hours)
                <div class="flex items-start">
                    <div class="w-24 text-sm text-gray-500 flex-shrink-0">Время:</div>
                    <div class="flex-1">
                        @if($task->estimated_hours)
                            <div class="text-sm text-gray-600">Планируемое: {{ $task->estimated_hours }} ч.</div>
                        @endif
                        @if($task->actual_hours)
                            <div class="text-sm text-gray-600">Фактическое: {{ $task->actual_hours }} ч.</div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Даты создания и завершения --}}
            <div class="flex items-start">
                <div class="w-24 text-sm text-gray-500 flex-shrink-0">Создана:</div>
                <div class="flex-1 text-sm text-gray-600">{{ $task->created_at->format('d.m.Y H:i') }}</div>
            </div>

            @if($task->completed_at)
                <div class="flex items-start">
                    <div class="w-24 text-sm text-gray-500 flex-shrink-0">Завершена:</div>
                    <div class="flex-1 text-sm text-gray-600">{{ $task->completed_at->format('d.m.Y H:i') }}</div>
                </div>
            @endif
        </div>

        {{-- Файлы --}}
        @php
            // Принудительно проверяем и загружаем файлы
            if (!isset($files) || $files === null) {
                $files = collect();
            }

            // Дополнительная проверка через связь задачи
            if ($files->count() == 0 && isset($task) && $task->relationLoaded('files')) {
                $files = $task->files;
            }

            // Логирование для отладки (видно в ларавел лог)
            if ($files->count() > 0) {
                \Log::info('Files found in view: ' . $files->count());
                foreach($files as $f) {
                    \Log::info('File name: ' . ($f->name ?? 'no name'));
                }
            }
        @endphp

        @if($files && $files->count() > 0)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-500 mb-3 uppercase tracking-wide">
                    <i class="fas fa-paperclip mr-2"></i>Вложения ({{ $files->count() }})
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($files as $file)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                @php
                                    $fileName = $file->name ?? $file->original_name ?? 'Файл';
                                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                    $icon = 'fa-file-alt';
                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = 'fa-file-image';
                                    elseif (in_array($extension, ['pdf'])) $icon = 'fa-file-pdf';
                                    elseif (in_array($extension, ['doc', 'docx'])) $icon = 'fa-file-word';
                                    elseif (in_array($extension, ['xls', 'xlsx'])) $icon = 'fa-file-excel';
                                    elseif (in_array($extension, ['zip', 'rar', '7z'])) $icon = 'fa-file-archive';

                                    $filePath = $file->file_path ?? $file->path ?? '';
                                    $fileUrl = $filePath ? Storage::url($filePath) : '#';
                                    $fileSize = $file->size ?? $file->file_size ?? 0;
                                    $formattedSize = $fileSize ? round($fileSize / 1024, 1) . ' KB' : '~ KB';
                                @endphp
                                <i class="fas {{ $icon }} text-gray-400 group-hover:text-blue-500 transition"></i>
                                <a href="{{ $fileUrl }}"
                                   target="_blank"
                                   class="text-blue-500 hover:underline text-sm truncate"
                                   title="{{ $fileName }}">
                                    {{ $fileName }}
                                </a>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                                {{ $formattedSize }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="text-xs text-gray-400 p-2 bg-gray-50 rounded">
                    <i class="fas fa-info-circle mr-1"></i>
                    Файлов не найдено
                </div>
            </div>
        @endif
        </div>


        <div class="mt-auto">
           <div class="py-1 grid grid-cols-6 gap-1 max-[1250px]:grid-cols-4">

                 @if($task->status === 'назначена') <button onclick="startTask({{ $task->id }})" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-2">
                    <i class="fas fa-play mr-2 text-white"></i> Начать
                </button>
                 @elseif($task->status === 'в работе') <button onclick="sendForReview({{ $task->id }})" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-2">
                    <i class="fas fa-play mr-2 text-white"></i> Отправить на проверку
                </button>
                @else <button onclick="startTask({{ $task->id }})" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-2">
                    <i class="fas fa-play mr-2 text-white"></i> Завершить
                </button>
                @endif

                <button onclick="showRejectModal({{ $task->id }})" class="bg-red-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-red-700 transition flex items-center justify-center space-x-2 text-sm md:text-base col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-2">
                    <i class="fas fa-times-circle mr-2"></i> Отказаться
                </button>
                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                    <button onclick="openEditModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-center col-span-2 max-[800px]:col-span-2 max-[470px]:col-span-4">
                        <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                    </button>
                @endif
                    <!-- КНОПКА АРХИВАЦИИ -->
                    @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                        <button onclick="archiveTask({{ $task->id }})"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-center col-span-2 max-[800px]:col-span-2 max-[470px]:col-span-4">
                            <i class="fas fa-archive mr-2 text-yellow-500"></i> В архив
                        </button>
                    @endif
                <button onclick="openCreateSubtaskModal({{ $task->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-center col-span-2 max-[800px]:col-span-4">
                    <i class="fas fa-list mr-2 text-green-500"></i>Подзадача
                </button>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
    // Устанавливаем ID задачи ГЛОБАЛЬНО
    if (typeof window !== 'undefined') {
        window.currentTaskId = {{ $task->id }};
        window.taskId = {{ $task->id }};
    }

    console.log('Task ID set to:', window.currentTaskId);

    // Переключение между вкладками - эта функция не работает, такая же функция написана в app.blade.php
    // function switchTaskTab(tab) {
    //     console.log('switchTaskTab1')
    //     const commentsTab = document.getElementById('commentsTab');
    //     const infoTab = document.getElementById('infoTab');
    //     const subtasksTab = document.getElementById('subtasksTab');
    //     const commentsBtn = document.getElementById('tabCommentsBtn');
    //     const subtasksBtn = document.getElementById('tabSubtasksBtn');
    //     const infoBtn = document.getElementById('tabInfoBtn')

    //     if (tab === 'comments') {
    //         commentsTab.classList.remove('hidden');
    //         subtasksTab.classList.add('hidden');
    //          infoTab.classList.add('hidden');
    //          infoTab.classList.remove('flex');

    //         commentsBtn.classList.add('border-green-500', 'text-green-600');
    //         commentsBtn.classList.remove('border-transparent', 'text-gray-500');
    //         subtasksBtn.classList.remove('border-green-500', 'text-green-600');
    //         subtasksBtn.classList.add('border-transparent', 'text-gray-500');
    //         infoBtn.classList.remove('border-green-500', 'text-green-600');
    //         infoBtn.classList.add('border-transparent', 'text-gray-500');

    //     } else if(tab==='info') {
    //     infoTab.classList.remove('hidden')
    //     infoTab.classList.add('flex')
    //      commentsTab.classList.add('hidden');
    //       subtasksTab.classList.add('hidden');

    //   commentsBtn.classList.remove('border-green-500', 'text-green-600');
    //         commentsBtn.classList.add('border-transparent', 'text-gray-500');
    //         subtasksBtn.classList.remove('border-green-500', 'text-green-600');
    //         subtasksBtn.classList.add('border-transparent', 'text-gray-500');
    //         infoBtn.classList.add('border-green-500', 'text-green-600');
    //         infoBtn.classList.remove('border-transparent', 'text-gray-500');

    //     }else {
    //         commentsTab.classList.add('hidden');
    //          infoTab.classList.add('hidden');
    //         subtasksTab.classList.remove('hidden');

    //        commentsBtn.classList.add('border-green-500', 'text-green-600');
    //         commentsBtn.classList.remove('border-transparent', 'text-gray-500');
    //         subtasksBtn.classList.remove('border-green-500', 'text-green-600');
    //         subtasksBtn.classList.add('border-transparent', 'text-gray-500');
    //         infoBtn.classList.remove('border-green-500', 'text-green-600');
    //         infoBtn.classList.add('border-transparent', 'text-gray-500');
    //     }
    // }
//   document.addEventListener('DOMContentLoaded', () => {
//         console.log('default tab')
//     const isMobile = window.matchMedia('(max-width: 500px)').matches;
//     const defaultTab = isMobile ? 'info' : 'comments';
//     console.log(defaultTab,'lldsadsakdsa')
//     switchTaskTab(defaultTab);
// });
    // Функция загрузки дополнительных комментариев
    function loadMoreComments(taskId, nextPageUrl) {
        const button = event?.target;
        if (button) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Загрузка...';
            button.disabled = true;
        }

        fetch(nextPageUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => response.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newComments = temp.querySelectorAll('.comment-item');
                const commentsList = document.getElementById('commentsList');

                newComments.forEach(comment => {
                    if (commentsList) {
                        const loadMoreBtn = commentsList.querySelector('.text-center.pt-2');
                        if (loadMoreBtn) {
                            commentsList.insertBefore(comment, loadMoreBtn);
                        } else {
                            commentsList.appendChild(comment);
                        }
                    }
                });

                const loadMoreBtn = document.querySelector('#commentsList .text-center.pt-2 button');
                if (loadMoreBtn && !temp.querySelector('#commentsList .text-center.pt-2 button')) {
                    loadMoreBtn.closest('.text-center.pt-2')?.remove();
                } else if (button && loadMoreBtn) {
                    button.innerHTML = '<i class="fas fa-chevron-down mr-1"></i> Загрузить еще';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error loading more comments:', error);
                if (typeof showNotification === 'function') {
                    showNotification('Ошибка при загрузке комментариев', 'error');
                }
                if (button) {
                    button.innerHTML = '<i class="fas fa-chevron-down mr-1"></i> Загрузить еще';
                    button.disabled = false;
                }
            });
    }
</script>
<style>
    @media(max-width:800px) {
        #taskModalContent {
            padding-left: 0 !important;
        }
    }
    @media(max-width:500px) {
        #taskModalContent {
            padding-top: 0 !important;
        }
    }
</style>
