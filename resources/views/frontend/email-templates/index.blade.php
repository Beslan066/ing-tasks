@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Шаблоны писем</h2>
            <button onclick="document.getElementById('createTemplateModal').showModal()"
                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition-colors">
                <i class="fas fa-plus mr-2"></i> Новый шаблон
            </button>
        </div>

        <!-- Фильтры -->
        <div class="mb-4 flex space-x-4">
            <select onchange="window.location.href = this.value"
                    class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="{{ route('email-templates.index') }}"
                    {{ !request('department_id') ? 'selected' : '' }}>
                    Все отделы
                </option>
                @foreach($departments as $department)
                    <option value="{{ route('email-templates.index', ['department_id' => $department->id]) }}"
                        {{ request('department_id') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Список шаблонов -->
        <div class="space-y-4">
            @foreach($templates as $template)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-lg font-semibold">{{ $template->name }}</h3>
                                @if($template->is_global)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                Глобальный
                            </span>
                                @endif
                                @if(!$template->is_active)
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                Неактивен
                            </span>
                                @endif
                            </div>
                            <p class="text-gray-600 mb-2">Тема: <span class="font-medium">{{ $template->subject }}</span></p>
                            @if($template->department)
                                <p class="text-sm text-gray-500">Отдел: {{ $template->department->name }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="previewTemplate({{ $template->id }})"
                                    class="text-primary hover:text-secondary">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editTemplate({{ $template->id }})"
                                    class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="duplicateTemplate({{ $template->id }})"
                                    class="text-green-500 hover:text-green-700">
                                <i class="fas fa-copy"></i>
                            </button>
                            <form action="{{ route('email-templates.destroy', $template) }}"
                                  method="POST"
                                  onsubmit="return confirm('Удалить шаблон?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        @if($templates->hasPages())
            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

    <!-- Модальное окно создания шаблона -->
    <dialog id="createTemplateModal" class="rounded-lg shadow-xl p-6 max-w-2xl">
        <h3 class="text-lg font-bold mb-4">Новый шаблон</h3>
        <form action="{{ route('email-templates.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Название</label>
                    <input type="text" name="name" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                    <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Выберите отдел</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Тема письма</label>
                <input type="text" name="subject" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Используйте {{имя}} для переменных</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Текст письма</label>
                <textarea name="body" rows="10" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_global" value="1" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Глобальный шаблон (для всех отделов)</span>
                </label>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('createTemplateModal').close()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Отмена
                </button>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg">
                    Сохранить
                </button>
            </div>
        </form>
    </dialog>

    <script>
        function previewTemplate(id) {
            fetch(`/email-templates/${id}/preview`)
                .then(response => response.json())
                .then(data => {
                    // Показываем превью в модальном окне
                    alert(`Тема: ${data.subject}\n\nТекст: ${data.body.substring(0, 500)}...`);
                });
        }

        function editTemplate(id) {
            // Редирект на страницу редактирования
            window.location.href = `/email-templates/${id}/edit`;
        }

        function duplicateTemplate(id) {
            if (confirm('Создать копию шаблона?')) {
                fetch(`/email-templates/${id}/duplicate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                }).then(() => {
                    window.location.reload();
                });
            }
        }

        // Закрытие модального окна при клике вне его
        document.getElementById('createTemplateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.close();
            }
        });
    </script>
@endsection
