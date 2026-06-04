{{-- resources/views/partials/modal/task/modal_content.blade.php --}}

<div class="flex h-full">
    {{-- ЛЕВАЯ КОЛОНКА - Информация о задаче --}}
    <div class="w-1/2 border-r border-gray-200 pr-6 overflow-y-auto max-h-[calc(90vh-70px)]">
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

            {{-- Приоритет --}}
            @if($task->priority)
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium
                        @if($task->priority === 'критический') bg-red-100 text-red-700
                        @elseif($task->priority === 'высокий') bg-orange-100 text-orange-700
                        @elseif($task->priority === 'средний') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-700 @endif">
                        <i class="fas fa-flag"></i>
                        {{ $task->priority }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Описание --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wide">Описание</h3>
            <div class="text-gray-700 text-sm whitespace-pre-wrap bg-gray-50 p-3 rounded-lg">
                {{ $task->description ?: 'Нет описания' }}
            </div>
        </div>

        {{-- Информационные блоки в стиле Битрикс24 --}}
        <div class="space-y-4">
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
            {{-- Временная отладка --}}
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="text-xs text-gray-400 p-2 bg-gray-50 rounded">
                    <i class="fas fa-info-circle mr-1"></i>
                    Файлов не найдено
                </div>
            </div>
        @endif
    </div>

    {{-- ПРАВАЯ КОЛОНКА - Комментарии в стиле мессенджера --}}
    <div class="w-1/2 pl-6 flex flex-col h-100 chat-background">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 p-2">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                <i class="fas fa-comments text-blue-500 mr-2"></i>Сообщения к задаче
                @if(!$task->is_personal && isset($comments) && $comments)
                    <span class="ml-1 text-gray-400">({{ $comments->total() ?? 0 }})</span>
                @endif
            </h3>
        </div>

        @if($task->is_personal)
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-lock text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Сообщений к задаче нет</p>
                    <p class="text-sm text-gray-400 mt-1">Комментарии недоступны для личных задач</p>
                </div>
            </div>
        @else
            {{-- Список комментариев в стиле чата --}}
            <div id="commentsList" class="flex-1 overflow-y-auto space-y-3 pr-2" style="max-height: calc(90vh - 200px);">
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
                            <p class="text-gray-500">Нет комментариев</p>
                            <p class="text-sm text-gray-400 mt-1">Напишите первое сообщение</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Форма добавления комментария --}}
            @if(isset($canComment) && $canComment)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-9 h-9 rounded-full {{ $currentUser->getAvatarColor() ?? 'bg-gray-400' }} flex items-center justify-center text-white text-sm font-semibold">
                                {{ $currentUser->getInitials() ?? 'U' }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <textarea id="commentInput"
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm"
                                      placeholder="Напишите комментарий..."></textarea>
                            <div class="flex justify-end mt-2">
                                <button onclick="submitComment({{ $task->id }})"
                                        class="px-4 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm flex items-center gap-1">
                                    <i class="fas fa-paper-plane"></i> Отправить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    // Устанавливаем ID задачи ГЛОБАЛЬНО
    if (typeof window !== 'undefined') {
        window.currentTaskId = {{ $task->id }};
        window.taskId = {{ $task->id }}; // дублируем для надежности
    }

    console.log('Task ID set to:', window.currentTaskId);

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
                        // Вставляем перед кнопкой загрузки, если она есть
                        const loadMoreBtn = commentsList.querySelector('.text-center.pt-2');
                        if (loadMoreBtn) {
                            commentsList.insertBefore(comment, loadMoreBtn);
                        } else {
                            commentsList.appendChild(comment);
                        }
                    }
                });

                // Удаляем кнопку загрузки если это последняя страница
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
