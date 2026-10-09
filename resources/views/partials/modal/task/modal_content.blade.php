<div class="flex h-full max-[800px]:flex-col">

    {{-- ЛЕВАЯ КОЛОНКА - Информация о задаче (белая, контрастная) --}}
    <div class="w-2/5 border-r border-slate-200 p-6 overflow-y-auto flex flex-col max-[800px]:hidden scrollbar-none [&::-webkit-scrollbar]:hidden bg-white">
        <div>
            {{-- Заголовок и статус --}}
            <div class="mb-6">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-[20px] font-bold text-slate-900 tracking-tight break-words leading-snug">{{ $task->name }}</h2>
                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap flex-shrink-0 uppercase tracking-wide
                        @if($task->status === 'выполнена') bg-emerald-100 text-emerald-800 border border-emerald-200
                        @elseif($task->status === 'в работе') bg-blue-100 text-blue-800 border border-blue-200
                        @elseif($task->status === 'не назначена') bg-amber-100 text-amber-800 border border-amber-200
                        @elseif($task->status === 'просрочена') bg-rose-100 text-rose-800 border border-rose-200
                        @elseif($task->status === 'на проверке') bg-orange-100 text-orange-800 border border-orange-200
                        @else bg-slate-100 text-slate-800 border border-slate-200 @endif">
                        {{ $task->status }}
                    </span>
                </div>

                {{-- Приоритет --}}
                @if($task->priority)
                    @php
                        $prioritySignals = [
                            'низкий' => ['level' => 1, 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'filled' => 'bg-emerald-500', 'empty' => 'bg-emerald-200', 'text' => 'text-emerald-800'],
                            'средний' => ['level' => 2, 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-200', 'text' => 'text-blue-800'],
                            'высокий' => ['level' => 3, 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-200', 'text' => 'text-orange-800'],
                            'критический' => ['level' => 4, 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'filled' => 'bg-rose-500', 'empty' => 'bg-rose-200', 'text' => 'text-rose-800'],
                        ];
                        $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                    @endphp
                    <div class="mt-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $signal['bg'] }} border {{ $signal['border'] }}">
                            <div class="flex items-end gap-[3px] h-4">
                                <div class="w-1 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-1.5"></div>
                                <div class="w-1 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-2.5"></div>
                                <div class="w-1 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-3.5"></div>
                                <div class="w-1 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-4"></div>
                            </div>
                            <span class="text-[12px] font-semibold {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Описание --}}
            <div class="mb-6">
                <h3 class="text-[11px] font-bold text-slate-500 mb-2 uppercase tracking-widest">Описание</h3>
                <div class="text-slate-700 text-[13px] leading-relaxed whitespace-pre-wrap bg-slate-50 border border-slate-200 p-3.5 rounded-xl">
                    {{ $task->description ?: 'Нет описания' }}
                </div>
            </div>

            {{-- Информационные блоки --}}
            <div class="space-y-4 bg-white border border-slate-200 p-4 rounded-xl shadow-sm">

                {{-- Исполнитель --}}
                <div class="flex items-start">
                    <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Исполнитель:</div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full {{ $task->user ? $task->user->getAvatarColor() : 'bg-slate-400' }} flex items-center justify-center text-white text-[10px] font-semibold shadow-sm">
                            {{ $task->user ? $task->user->getInitials() : '?' }}
                        </div>
                        <span class="text-[13px] text-slate-800 font-medium">{{ $task->user?->name ?? 'Не назначен' }}</span>
                    </div>
                </div>

                {{-- Автор --}}
                <div class="flex items-start">
                    <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Автор:</div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full {{ $task->author->getAvatarColor() }} flex items-center justify-center text-white text-[10px] font-semibold shadow-sm">
                            {{ $task->author->getInitials() }}
                        </div>
                        <span class="text-[13px] text-slate-800 font-medium">{{ $task->author->name }}</span>
                    </div>
                </div>

                {{-- Отдел --}}
                <div class="flex items-start">
                    <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Отдел:</div>
                    <div class="flex-1 text-[13px] text-slate-800 font-medium">{{ $task->department?->name ?? ($task->is_personal ? 'Личная задача' : 'Общая задача') }}</div>
                </div>

                {{-- Категория --}}
                @if($task->category)
                    <div class="flex items-start">
                        <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Категория:</div>
                        <div class="flex-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border"
                                  style="background-color: {{ $task->category->color }}15; color: {{ $task->category->color }}; border-color: {{ $task->category->color }}40;">
                                {{ $task->category->name }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Дедлайн --}}
                <div class="flex items-start">
                    <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Дедлайн:</div>
                    <div class="flex-1">
                        @if($task->deadline)
                            <span class="text-[13px] font-semibold {{ $task->deadline->isPast() && $task->status !== 'выполнена' ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $task->deadline->format('d.m.Y H:i') }}
                            </span>
                            @if($task->deadline->isPast() && $task->status !== 'выполнена')
                                <span class="ml-1.5 text-[10px] text-rose-600 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded-full font-medium">Просрочено</span>
                            @endif
                        @else
                            <span class="text-[13px] text-slate-400">Не указан</span>
                        @endif
                    </div>
                </div>

                {{-- Время --}}
                @if($task->estimated_hours || $task->actual_hours)
                    <div class="flex items-start">
                        <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Время:</div>
                        <div class="flex-1 space-y-0.5">
                            @if($task->estimated_hours)
                                <div class="text-[13px] text-slate-700">План: <span class="font-semibold text-slate-900">{{ $task->estimated_hours }} ч.</span></div>
                            @endif
                            @if($task->actual_hours)
                                <div class="text-[13px] text-slate-700">Факт: <span class="font-semibold text-slate-900">{{ $task->actual_hours }} ч.</span></div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Даты --}}
                <div class="flex items-start">
                    <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Создана:</div>
                    <div class="flex-1 text-[13px] text-slate-800">{{ $task->created_at->format('d.m.Y H:i') }}</div>
                </div>

                @if($task->completed_at)
                    <div class="flex items-start">
                        <div class="w-24 text-[12px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Завершена:</div>
                        <div class="flex-1 text-[13px] text-slate-800">{{ $task->completed_at->format('d.m.Y H:i') }}</div>
                    </div>
                @endif
            </div>

            {{-- Файлы --}}
            @php
                if (!isset($files) || $files === null) $files = collect();
                if ($files->count() == 0 && isset($task) && $task->relationLoaded('files')) $files = $task->files;
            @endphp

            @if($files && $files->count() > 0)
                <div class="mt-5 pt-5 border-t-2 border-slate-100">
                    <h3 class="text-[11px] font-bold text-slate-500 mb-3 uppercase tracking-widest">
                        Вложения ({{ $files->count() }})
                    </h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1 scrollbar-none [&::-webkit-scrollbar]:hidden">
                        @foreach($files as $file)
                            <div class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-lg hover:border-emerald-300 hover:shadow-sm transition-all duration-200 group">
                                <div class="flex items-center space-x-2.5 flex-1 min-w-0">
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
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp

                                    <i class="fas {{ $icon }} text-slate-400 group-hover:text-emerald-600 transition text-[12px]"></i>

                                    @if($isImage)
                                        <span
                                            data-preview="true"
                                            data-image-url="{{ $fileUrl }}"
                                            data-image-name="{{ addslashes($fileName) }}"
                                            class="text-slate-700 group-hover:text-emerald-700 text-[13px] font-medium truncate cursor-pointer"
                                            title="{{ $fileName }}"
                                        >
                                            {{ $fileName }}
                                        </span>
                                    @else
                                        <a href="{{ $fileUrl }}"
                                           target="_blank"
                                           class="text-slate-700 group-hover:text-emerald-700 text-[13px] font-medium truncate"
                                           title="{{ $fileName }}">
                                            {{ $fileName }}
                                        </a>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-500 flex-shrink-0 ml-2 font-semibold bg-slate-100 px-1.5 py-0.5 rounded">
                                    {{ $formattedSize }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mt-5 pt-5 border-t-2 border-slate-100">
                    <div class="text-[11px] text-slate-500 p-3 bg-slate-50 border border-slate-200 rounded-lg text-center font-medium">
                        <i class="fas fa-paperclip mr-1.5 opacity-60"></i>Нет вложений
                    </div>
                </div>
            @endif
        </div>

        {{-- Кнопки действий --}}
        <div class="mt-auto pt-6">
            <div class="py-1 grid grid-cols-6 gap-2 max-[1250px]:grid-cols-4">

                @if($task->status === 'назначена')
                    <x-ui.button
                        variant="action"
                        icon="fas fa-play"
                        onclick="startTask({{ $task->id }})"
                        class="col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-4">
                        Начать
                    </x-ui.button>
                @elseif($task->status === 'в работе')
                    <x-ui.button
                        variant="action"
                        icon="fas fa-paper-plane"
                        onclick="sendForReview({{ $task->id }})"
                        class="col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-4">
                        На проверку
                    </x-ui.button>
                @else
                    <x-ui.button
                        variant="action"
                        icon="fas fa-check"
                        onclick="startTask({{ $task->id }})"
                        class="col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-4">
                        Завершить
                    </x-ui.button>
                @endif

                <x-ui.button
                    variant="danger-outline"
                    icon="fas fa-times-circle"
                    onclick="showRejectModal({{ $task->id }})"
                    class="col-span-3 max-[1250px]:col-span-2 max-[800px]:col-span-4">
                    Отказаться
                </x-ui.button>
            </div>

            {{-- Дополнительные ссылки --}}
            <div class="mt-4 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-center gap-1 max-[800px]:grid max-[800px]:grid-cols-2 max-[800px]:gap-2">
                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                    <x-ui.button
                        variant="ghost"
                        icon="fas fa-edit text-blue-500"
                        onclick="openEditModal({{ $task->id }})">
                        Редактировать
                    </x-ui.button>
                @endif

                @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                    <x-ui.button
                        variant="ghost"
                        icon="fas fa-archive text-amber-500"
                        onclick="archiveTask({{ $task->id }})">
                        В архив
                    </x-ui.button>
                @endif

                <x-ui.button
                    variant="ghost"
                    icon="fas fa-list text-emerald-500"
                    onclick="openCreateSubtaskModal({{ $task->id }})">
                    Подзадача
                </x-ui.button>
            </div>
        </div>
    </div>

    {{-- ПРАВАЯ КОЛОНКА (теперь чётко серая) --}}
    <div class="w-3/5 flex flex-col h-full bg-slate-50 border-l border-slate-200 rounded-r-[24px] max-[800px]:w-full max-[800px]:h-full max-[800px]:rounded-none max-[800px]:rounded-b-[24px] max-[800px]:border-l-0 max-[800px]:border-t">
        {{-- Табы --}}
        <div class="items-center border-b border-slate-200 bg-white flex rounded-tr-[24px] max-[800px]:rounded-none overflow-hidden">
            <button onclick="switchTaskTab('info')"
                    id="tabInfoBtn"
                    class="flex-1 px-4 py-3.5 text-[13px] font-semibold transition-all duration-200 border-b-2 border-transparent text-slate-500 hover:text-slate-800 hidden max-[800px]:block">
                <i class="fas fa-info-circle mr-1.5 opacity-70"></i>
                <span class="max-[500px]:hidden">Информация</span>
                <span class="hidden max-[500px]:inline">Инфо</span>
            </button>
            <button onclick="switchTaskTab('comments')"
                    id="tabCommentsBtn"
                    class="flex-1 px-4 py-3.5 text-[13px] font-semibold transition-all duration-200 border-b-2 border-transparent text-slate-500 hover:text-slate-800">
                <i class="fas fa-comments mr-1.5 opacity-70"></i>Сообщения
                <span id="commentsCount" class="ml-1 text-[11px] text-slate-400 max-[500px]:hidden">
                    ({{ isset($comments) && $comments ? (method_exists($comments, 'total') ? $comments->total() : $comments->count()) : 0 }})
                </span>
            </button>
            <button onclick="switchTaskTab('subtasks')"
                    id="tabSubtasksBtn"
                    class="flex-1 px-4 py-3.5 text-[13px] font-semibold transition-all duration-200 border-b-2 border-transparent text-slate-500 hover:text-slate-800">
                <i class="fas fa-tasks mr-1.5 opacity-70"></i>Подзадачи
                <span id="subtasksCount" class="ml-1 text-[11px] text-slate-400 max-[500px]:hidden">({{ $subtasks->count() }})</span>
            </button>
        </div>

        {{-- КОНТЕНТ: Комментарии --}}
        <div id="commentsTab" class="flex-1 flex flex-col h-full overflow-hidden">
            @if($task->is_personal)
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-lock text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-600 text-[14px] font-semibold">Сообщений к задаче нет</p>
                        <p class="text-[12px] text-slate-400 mt-1">Комментарии недоступны для личных задач</p>
                    </div>
                </div>
            @else
                {{-- Список комментариев --}}
                <div id="commentsList" class="flex-1 overflow-y-auto p-5 space-y-4 scrollbar-none [&::-webkit-scrollbar]:hidden">
                    @if(isset($comments) && $comments && $comments->count() > 0)
                        @foreach($comments as $comment)
                            @include('partials.modal.task.comment_item', ['comment' => $comment, 'taskId' => $task->id, 'level' => 0])
                        @endforeach

                        @if($comments->hasMorePages())
                            <div class="text-center py-2">
                                <button onclick="loadMoreComments({{ $task->id }}, '{{ $comments->nextPageUrl() }}')"
                                        class="text-[12px] text-emerald-700 hover:text-emerald-800 font-semibold bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition-all">
                                    <i class="fas fa-chevron-down mr-1"></i> Загрузить еще
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="flex-1 flex items-center justify-center h-full">
                            <div class="text-center">
                                <i class="fas fa-comment-dots text-5xl text-slate-300 mb-3"></i>
                                <p class="text-[14px] text-slate-500 font-medium">Напишите первое сообщение</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Форма добавления комментария --}}
                @if(isset($canComment) && $canComment)
                    <div class="p-4 bg-white border-t border-slate-200">
                        <div class="relative">
                            <textarea id="commentInput"
                                      rows="1"
                                      class="w-full px-4 py-3 pr-12 bg-white border border-slate-300 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 resize-none text-[13px] text-slate-700 placeholder-slate-400 min-h-[48px] max-h-[120px] shadow-sm"
                                      placeholder="Напишите сообщение..."></textarea>
                            <button onclick="submitComment({{ $task->id }})"
                                    class="absolute right-2 bottom-2 p-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-all duration-200 w-8 h-8 flex items-center justify-center shadow-md shadow-emerald-500/20">
                                <i class="fas fa-paper-plane text-[11px]"></i>
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- КОНТЕНТ: Подзадачи --}}
        <div id="subtasksTab" class="flex-1 flex flex-col h-full overflow-hidden hidden">
            <div class="p-4 flex justify-end bg-white border-b border-slate-200">
                @if(auth()->user()->canViewAllCompanyTasks() || $task->author_id === auth()->id())
                    <button onclick="openCreateSubtaskModal({{ $task->id }})"
                            class="text-[12px] bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition-all duration-200 flex items-center font-semibold shadow-md shadow-emerald-500/20">
                        <i class="fas fa-plus mr-1.5 text-[10px]"></i> Добавить подзадачу
                    </button>
                @endif
            </div>

            <div id="subtasksList" class="flex-1 overflow-y-auto p-4 space-y-2 scrollbar-none [&::-webkit-scrollbar]:hidden">
                @if($task->subtasks && $task->subtasks->count() > 0)
                    @foreach($task->subtasks as $subtask)
                        <div class="subtask-item bg-white rounded-xl p-3.5 border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all duration-200" data-subtask-id="{{ $subtask->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start space-x-3 flex-1">
                                    <button onclick="toggleSubtask({{ $subtask->id }})"
                                            class="mt-0.5 flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200
                                            {{ $subtask->status === 'выполнена' ? 'bg-emerald-500 border-emerald-500' : 'border-slate-300 hover:border-emerald-400' }}">
                                        @if($subtask->status === 'выполнена')
                                            <i class="fas fa-check text-white text-[10px]"></i>
                                        @endif
                                    </button>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-800 text-[13px] break-words">{{ $subtask->name }}</p>
                                        @if($subtask->description)
                                            <p class="text-[12px] text-slate-500 mt-0.5 break-words">{{ $subtask->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-500 font-medium">
                                            @if($subtask->user)
                                                <span class="flex items-center"><i class="fas fa-user mr-1 opacity-60"></i>{{ $subtask->user->name }}</span>
                                            @endif
                                            @if($subtask->deadline)
                                                <span class="flex items-center"><i class="fas fa-calendar mr-1 opacity-60"></i>{{ $subtask->deadline->format('d.m.Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <button onclick="deleteSubtask({{ $subtask->id }})" class="text-slate-300 hover:text-rose-500 transition p-1">
                                    <i class="fas fa-trash text-[12px]"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <i class="fas fa-tasks text-5xl text-slate-300 mb-3"></i>
                            <p class="text-[14px] text-slate-500 font-medium">Нет подзадач</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- КОНТЕНТ: Информация (мобильная версия) --}}
        <div id="infoTab" class="flex-col hidden h-full overflow-y-auto p-5 scrollbar-none [&::-webkit-scrollbar]:hidden bg-white">
            <div class="mb-5">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <h2 class="text-[17px] font-bold text-slate-900 leading-snug break-words">{{ $task->name }}</h2>
                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full whitespace-nowrap flex-shrink-0 uppercase tracking-wide
                        @if($task->status === 'выполнена') bg-emerald-100 text-emerald-800 border border-emerald-200
                        @elseif($task->status === 'в работе') bg-blue-100 text-blue-800 border border-blue-200
                        @elseif($task->status === 'не назначена') bg-amber-100 text-amber-800 border border-amber-200
                        @elseif($task->status === 'просрочена') bg-rose-100 text-rose-800 border border-rose-200
                        @elseif($task->status === 'на проверке') bg-orange-100 text-orange-800 border border-orange-200
                        @else bg-slate-100 text-slate-800 border border-slate-200 @endif">
                        {{ $task->status }}
                    </span>
                </div>

                @if($task->priority)
                    @php
                        $prioritySignals = [
                            'низкий' => ['level' => 1, 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'filled' => 'bg-emerald-500', 'empty' => 'bg-emerald-200', 'text' => 'text-emerald-800'],
                            'средний' => ['level' => 2, 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'filled' => 'bg-blue-500', 'empty' => 'bg-blue-200', 'text' => 'text-blue-800'],
                            'высокий' => ['level' => 3, 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'filled' => 'bg-orange-500', 'empty' => 'bg-orange-200', 'text' => 'text-orange-800'],
                            'критический' => ['level' => 4, 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'filled' => 'bg-rose-500', 'empty' => 'bg-rose-200', 'text' => 'text-rose-800'],
                        ];
                        $signal = $prioritySignals[$task->priority] ?? $prioritySignals['средний'];
                    @endphp
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg {{ $signal['bg'] }} border {{ $signal['border'] }}">
                        <div class="flex items-end gap-[2px] h-3.5">
                            <div class="w-1 rounded-sm {{ $signal['level'] >= 1 ? $signal['filled'] : $signal['empty'] }} h-1.5"></div>
                            <div class="w-1 rounded-sm {{ $signal['level'] >= 2 ? $signal['filled'] : $signal['empty'] }} h-2.5"></div>
                            <div class="w-1 rounded-sm {{ $signal['level'] >= 3 ? $signal['filled'] : $signal['empty'] }} h-3"></div>
                            <div class="w-1 rounded-sm {{ $signal['level'] >= 4 ? $signal['filled'] : $signal['empty'] }} h-3.5"></div>
                        </div>
                        <span class="text-[11px] font-semibold {{ $signal['text'] }}">{{ ucfirst($task->priority) }}</span>
                    </div>
                @endif
            </div>

            <div class="mb-5">
                <h3 class="text-[10px] font-bold text-slate-500 mb-2 uppercase tracking-widest">Описание</h3>
                <div class="text-slate-700 text-[12px] leading-relaxed whitespace-pre-wrap bg-slate-50 border border-slate-200 p-3 rounded-xl">
                    {{ $task->description ?: 'Нет описания' }}
                </div>
            </div>

            <div class="space-y-3 bg-white border border-slate-200 p-4 rounded-xl mb-5">
                <div class="flex items-start">
                    <div class="w-20 text-[11px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Исполнитель:</div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full {{ $task->user ? $task->user->getAvatarColor() : 'bg-slate-400' }} flex items-center justify-center text-white text-[9px] font-semibold">
                            {{ $task->user ? $task->user->getInitials() : '?' }}
                        </div>
                        <span class="text-[12px] text-slate-800 font-medium">{{ $task->user?->name ?? 'Не назначен' }}</span>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-20 text-[11px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Автор:</div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full {{ $task->author->getAvatarColor() }} flex items-center justify-center text-white text-[9px] font-semibold">
                            {{ $task->author->getInitials() }}
                        </div>
                        <span class="text-[12px] text-slate-800 font-medium">{{ $task->author->name }}</span>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-20 text-[11px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Отдел:</div>
                    <div class="flex-1 text-[12px] text-slate-800 font-medium">{{ $task->department?->name ?? ($task->is_personal ? 'Личная' : 'Общая') }}</div>
                </div>
                @if($task->deadline)
                    <div class="flex items-start">
                        <div class="w-20 text-[11px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Дедлайн:</div>
                        <div class="flex-1">
                            <span class="text-[12px] font-semibold {{ $task->deadline->isPast() && $task->status !== 'выполнена' ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $task->deadline->format('d.m.Y H:i') }}
                            </span>
                        </div>
                    </div>
                @endif
                <div class="flex items-start">
                    <div class="w-20 text-[11px] text-slate-500 font-medium flex-shrink-0 pt-0.5">Создана:</div>
                    <div class="flex-1 text-[12px] text-slate-800">{{ $task->created_at->format('d.m.Y H:i') }}</div>
                </div>
            </div>

            @if($files && $files->count() > 0)
                <div class="pt-4 border-t-2 border-slate-100">
                    <h3 class="text-[10px] font-bold text-slate-500 mb-3 uppercase tracking-widest">
                        Вложения ({{ $files->count() }})
                    </h3>
                    <div class="space-y-2">
                        @foreach($files as $file)
                            @php
                                $fileName = $file->name ?? $file->original_name ?? 'Файл';
                                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $icon = 'fa-file-alt';
                                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $icon = 'fa-file-image';
                                elseif (in_array($extension, ['pdf'])) $icon = 'fa-file-pdf';
                                elseif (in_array($extension, ['doc', 'docx'])) $icon = 'fa-file-word';
                                elseif (in_array($extension, ['xls', 'xlsx'])) $icon = 'fa-file-excel';
                                $filePath = $file->file_path ?? $file->path ?? '';
                                $fileUrl = $filePath ? Storage::url($filePath) : '#';
                                $fileSize = $file->size ?? $file->file_size ?? 0;
                                $formattedSize = $fileSize ? round($fileSize / 1024, 1) . ' KB' : '~ KB';
                            @endphp
                            <a href="{{ $fileUrl }}" target="_blank" class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-lg hover:border-emerald-300 transition-all">
                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                    <i class="fas {{ $icon }} text-slate-400 text-[11px]"></i>
                                    <span class="text-slate-700 text-[12px] font-medium truncate">{{ $fileName }}</span>
                                </div>
                                <span class="text-[10px] text-slate-500 flex-shrink-0 ml-2 font-semibold">{{ $formattedSize }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 pt-5 border-t-2 border-slate-100 space-y-2">
                @if($task->status === 'назначена')
                    <button onclick="startTask({{ $task->id }})"
                            class="w-full text-white px-4 py-2.5 rounded-xl flex items-center justify-center space-x-2 text-[13px] font-semibold bg-emerald-500 hover:bg-emerald-600 transition-all duration-200 shadow-md shadow-emerald-500/20">
                        <i class="fas fa-play text-[11px]"></i>
                        <span>Начать</span>
                    </button>
                @elseif($task->status === 'в работе')
                    <button onclick="sendForReview({{ $task->id }})"
                            class="w-full text-white px-4 py-2.5 rounded-xl flex items-center justify-center space-x-2 text-[13px] font-semibold bg-emerald-500 hover:bg-emerald-600 transition-all duration-200 shadow-md shadow-emerald-500/20">
                        <i class="fas fa-paper-plane text-[11px]"></i>
                        <span>На проверку</span>
                    </button>
                @endif

                <button onclick="showRejectModal({{ $task->id }})"
                        class="w-full bg-white border-2 border-rose-200 text-rose-600 px-4 py-2.5 rounded-xl hover:bg-rose-50 transition-all duration-200 flex items-center justify-center space-x-2 text-[13px] font-semibold">
                    <i class="fas fa-times-circle text-[11px]"></i>
                    <span>Отказаться</span>
                </button>

                <div class="grid grid-cols-3 gap-2 pt-2">
                    @if($task->author_id == auth()->id() || auth()->user()->isLeader())
                        <button onclick="openEditModal({{ $task->id }})"
                                class="px-3 py-2 text-[11px] text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg flex items-center justify-center gap-1.5 transition-all font-medium">
                            <i class="fas fa-edit text-blue-500"></i>
                            <span>Изменить</span>
                        </button>
                        <button onclick="archiveTask({{ $task->id }})"
                                class="px-3 py-2 text-[11px] text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg flex items-center justify-center gap-1.5 transition-all font-medium">
                            <i class="fas fa-archive text-amber-500"></i>
                            <span>Архив</span>
                        </button>
                    @endif
                    <button onclick="openCreateSubtaskModal({{ $task->id }})"
                            class="px-3 py-2 text-[11px] text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg flex items-center justify-center gap-1.5 transition-all font-medium">
                        <i class="fas fa-list text-emerald-500"></i>
                        <span>Задача</span>
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
                        const loadMoreBtn = commentsList.querySelector('.text-center.py-2');
                        if (loadMoreBtn) {
                            commentsList.insertBefore(comment, loadMoreBtn);
                        } else {
                            commentsList.appendChild(comment);
                        }
                    }
                });

                const loadMoreBtn = document.querySelector('#commentsList .text-center.py-2 button');
                if (loadMoreBtn && !temp.querySelector('#commentsList .text-center.py-2 button')) {
                    loadMoreBtn.closest('.text-center.py-2')?.remove();
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
