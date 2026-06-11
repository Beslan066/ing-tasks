{{-- resources/views/partials/modal/task/subtask_item.blade.php --}}

<div class="subtask-item bg-gray-50 rounded-lg p-3 border border-gray-200 hover:shadow-sm transition" data-subtask-id="{{ $subtask->id }}">
    <div class="flex items-start justify-between">
        <div class="flex items-start space-x-3 flex-1">
            {{-- Чекбокс для отметки выполнения --}}
            <button onclick="toggleSubtask({{ $subtask->id }})"
                    class="mt-0.5 flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center transition-all
                        {{ $subtask->status === 'выполнена'
                            ? 'bg-green-500 border-green-500'
                            : 'border-gray-300 hover:border-green-400' }}">
                @if($subtask->status === 'выполнена')
                    <i class="fas fa-check text-white text-xs"></i>
                @endif
            </button>

            <div class="flex-1">
                <div class="flex items-center flex-wrap gap-2">
                    <p class="font-medium text-gray-800 {{ $subtask->status === 'выполнена' ? 'line-through text-gray-400' : '' }}">
                        {{ $subtask->name }}
                    </p>
                    @if($subtask->priority)
                        @php
                            $priorityClass = match($subtask->priority) {
                                'критический' => 'bg-red-200 text-red-800',
                                'высокий' => 'bg-red-100 text-red-700',
                                'средний' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $priorityClass }}">
                            {{ $subtask->priority }}
                        </span>
                    @endif
                </div>

                @if($subtask->description)
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $subtask->description }}</p>
                @endif

                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                    @if($subtask->user)
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full bg-gray-300 flex items-center justify-center mr-1">
                                <span class="text-[8px] font-medium">{{ $subtask->user->getInitials() }}</span>
                            </div>
                            <span>{{ $subtask->user->name }}</span>
                        </div>
                    @else
                        <span class="text-gray-400">Не назначен</span>
                    @endif

                    @if($subtask->deadline)
                        <div class="flex items-center {{ $subtask->deadline->isPast() && $subtask->status !== 'выполнена' ? 'text-red-500' : '' }}">
                            <i class="far fa-calendar-alt mr-1 text-xs"></i>
                            {{ $subtask->deadline->format('d.m.Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Меню действий для подзадачи --}}
        <div class="relative">
            <button onclick="toggleSubtaskMenu(event, {{ $subtask->id }})"
                    class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-ellipsis-v text-xs"></i>
            </button>
            <div id="subtaskMenu-{{ $subtask->id }}"
                 class="hidden absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border z-10">
                <div class="py-1">
                    @if($subtask->status !== 'выполнена')
                        <button onclick="editSubtask({{ $subtask->id }})"
                                class="w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-edit mr-2 text-blue-500"></i> Редактировать
                        </button>
                    @endif
                    @if(auth()->user()->canViewAllCompanyTasks() || $task->author_id === auth()->id())
                        <button onclick="deleteSubtask({{ $subtask->id }})"
                                class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-trash mr-2"></i> Удалить
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
