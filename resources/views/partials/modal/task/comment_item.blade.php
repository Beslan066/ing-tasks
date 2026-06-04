{{-- resources/views/partials/modal/task/comment_item.blade.php --}}

<div class="comment-item" data-comment-id="{{ $comment->id }}" style="margin-left: {{ ($level ?? 0) * 20 }}px;">
    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition bg-white">
        {{-- Аватар --}}
        <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full {{ $comment->user->getAvatarColor() ?? 'bg-gray-400' }} flex items-center justify-center text-white text-xs font-semibold">
                {{ $comment->user->getInitials() ?? 'U' }}
            </div>
        </div>

        {{-- Контент комментария --}}
        <div class="flex-1 min-w-0">
            {{-- Заголовок --}}
            <div class="flex items-center flex-wrap gap-2 mb-1">
                <span class="font-semibold text-sm text-gray-800">{{ $comment->user->name }}</span>
                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->user_id === Auth::id())
                    <button onclick="editComment({{ $comment->id }})" class="text-gray-400 hover:text-gray-600 text-xs">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteComment({{ $comment->id }})" class="text-gray-400 hover:text-red-500 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
            </div>

            {{-- Текст комментария --}}
            <div class="comment-text text-gray-700 text-sm mb-2 break-words" data-comment-text="{{ $comment->id }}">
                {!! nl2br(e($comment->comment)) !!}
            </div>

            {{-- Кнопка ответа --}}
            <button onclick="showReplyForm({{ $comment->id }})" class="text-xs text-gray-400 hover:text-blue-500 transition">
                <i class="far fa-comment"></i> Ответить
            </button>

            {{-- Форма ответа --}}
            <div id="replyForm_{{ $comment->id }}" class="hidden mt-3">
                <div class="flex items-start space-x-2">
                    <div class="flex-shrink-0">
                        <div class="w-7 h-7 rounded-full {{ Auth::user()->getAvatarColor() ?? 'bg-gray-400' }} flex items-center justify-center text-white text-xs font-semibold">
                            {{ Auth::user()->getInitials() ?? 'U' }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <textarea id="replyText_{{ $comment->id }}"
                                  rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none text-sm"
                                  placeholder="Напишите ответ..."></textarea>
                        <div class="flex justify-end mt-2 space-x-2">
                            <button onclick="cancelReply({{ $comment->id }})" class="px-3 py-1 text-gray-600 hover:text-gray-800 text-xs">
                                Отмена
                            </button>
                            <button onclick="submitReply({{ $comment->id }})" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                Ответить
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ответы --}}
            @if($comment->replies && $comment->replies->count() > 0)
                <div class="replies-container mt-3 space-y-3">
                    @foreach($comment->replies as $reply)
                        @include('partials.modal.task.comment_item', ['comment' => $reply, 'taskId' => $taskId, 'level' => ($level ?? 0) + 1])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
