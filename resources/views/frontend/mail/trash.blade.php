@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Заголовок -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Корзина писем</h1>
                        <p class="text-sm text-gray-600 mt-1">Отдел: {{ $department->name }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('departments.emails.index', $department) }}"
                           class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <!-- Панель управления корзиной -->
            <div class="bg-white rounded-lg border mb-4 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-700">
                            В корзине <span class="font-semibold">{{ $emails->total() }}</span> писем
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="showRestoreAllModal()"
                                class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm hover:bg-green-200">
                            <i class="fas fa-undo mr-2"></i> Восстановить все
                        </button>
                        <button onclick="showClearTrashModal()"
                                class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200">
                            <i class="fas fa-trash mr-2"></i> Очистить корзину
                        </button>
                    </div>
                </div>
            </div>

            <!-- Список удаленных писем -->
            @if($emails->count() > 0)
                <div class="bg-white rounded-lg border divide-y divide-gray-200">
                    @foreach($emails as $email)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex flex-col md:flex-row md:items-start gap-4">
                                <!-- Информация о письме -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="font-medium text-gray-900">
                                                {{ $email->subject }}
                                                @if($email->is_draft)
                                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded ml-2">Черновик</span>
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                От: {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;
                                            </p>
                                        </div>
                                        <span class="text-xs text-gray-500">
                                {{ $email->deleted_at->format('d.m.Y H:i') }}
                            </span>
                                    </div>

                                    <!-- Информация об удалении -->
                                    <div class="bg-red-50 border border-red-100 rounded p-3 mb-3">
                                        <div class="flex items-center text-sm text-red-700">
                                            <i class="fas fa-trash mr-2"></i>
                                            <div>
                                                <p>Удалено пользователем:
                                                    <span class="font-semibold">{{ $email->deletedBy->name ?? 'Система' }}</span>
                                                </p>
                                                @if($email->delete_reason)
                                                    <p class="mt-1">Причина: {{ $email->delete_reason }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Краткое содержание -->
                                    <p class="text-sm text-gray-600 line-clamp-2">
                                        {{ Str::limit(strip_tags($email->body), 200) }}
                                    </p>

                                    <!-- Дополнительная информация -->
                                    <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-gray-500">
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $email->sent_at ? $email->sent_at->format('d.m.Y') : 'Не отправлено' }}
                            </span>
                                        @if($email->has_attachments)
                                            <span><i class="fas fa-paperclip mr-1"></i> С вложениями</span>
                                        @endif
                                        @if($email->tags->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($email->tags as $tag)
                                                    <span class="px-2 py-0.5 rounded-full text-xs"
                                                          style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Действия -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <form action="{{ route('departments.emails.trash.restore', [$department, $email]) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm hover:bg-green-100 w-full sm:w-auto">
                                            <i class="fas fa-undo mr-2"></i> Восстановить
                                        </button>
                                    </form>

                                    <form action="{{ route('departments.emails.trash.force', [$department, $email]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Полностью удалить это письмо? Это действие нельзя отменить.')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm hover:bg-red-100 w-full sm:w-auto">
                                            <i class="fas fa-trash mr-2"></i> Удалить навсегда
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Пагинация -->
                <div class="mt-4">
                    {{ $emails->links() }}
                </div>

            @else
                <!-- Пустая корзина -->
                <div class="bg-white rounded-lg border p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 text-gray-300">
                        <i class="fas fa-trash-alt text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Корзина пуста</h3>
                    <p class="text-gray-500 mb-6">Здесь будут появляться удаленные письма</p>
                    <a href="{{ route('departments.emails.index', $department) }}"
                       class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-secondary">
                        <i class="fas fa-inbox mr-2"></i> Вернуться к почте
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Модальное окно восстановления всех писем -->
    <dialog id="restoreAllModal" class="rounded-lg shadow-xl p-0 max-w-md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Восстановить все письма</h3>
                <button onclick="document.getElementById('restoreAllModal').close()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                Вы уверены, что хотите восстановить все {{ $emails->total() }} писем из корзины?
            </p>

            <form action="{{ route('departments.emails.trash.restore-all', $department) }}"
                  method="POST">
                @csrf
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('restoreAllModal').close()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                        Восстановить все
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Модальное окно очистки корзины -->
    <dialog id="clearTrashModal" class="rounded-lg shadow-xl p-0 max-w-md">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Очистка корзины</h3>
                <button onclick="document.getElementById('clearTrashModal').close()"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('departments.emails.trash.clear', $department) }}"
                  method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Удалить письма, находящиеся в корзине дольше:
                    </label>
                    <select name="days"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="7">7 дней</option>
                        <option value="30" selected>30 дней</option>
                        <option value="90">90 дней</option>
                        <option value="180">180 дней</option>
                        <option value="365">365 дней</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Письма будут удалены безвозвратно. Это действие нельзя отменить.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('clearTrashModal').close()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                        Очистить корзину
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        function showRestoreAllModal() {
            document.getElementById('restoreAllModal').showModal();
        }

        function showClearTrashModal() {
            document.getElementById('clearTrashModal').showModal();
        }

        // Закрытие модальных окон при клике вне их
        document.querySelectorAll('dialog').forEach(dialog => {
            dialog.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.close();
                }
            });
        });
    </script>
@endsection
