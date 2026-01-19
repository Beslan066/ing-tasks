@extends('layouts.app')

@section('content')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">SMTP настройки</h2>
            <button onclick="document.getElementById('createSmtpModal').showModal()"
                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition-colors">
                <i class="fas fa-plus mr-2"></i> Новые настройки
            </button>
        </div>

        <!-- Фильтры -->
        <div class="mb-4">
            <select onchange="window.location.href = this.value"
                    class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="{{ route('smtp-settings.index') }}"
                    {{ !request('department_id') ? 'selected' : '' }}>
                    Все отделы
                </option>
                @foreach($departments as $department)
                    <option value="{{ route('smtp-settings.index', ['department_id' => $department->id]) }}"
                        {{ request('department_id') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Список настроек -->
        <div class="space-y-4">
            @foreach($settings as $setting)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-lg font-semibold">{{ $setting->from_name }}</h3>
                                @if($setting->is_default)
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                По умолчанию
                            </span>
                                @endif
                                @if($setting->is_active)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                Активен
                            </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                Неактивен
                            </span>
                                @endif
                            </div>
                            <p class="text-gray-600 mb-1">Email: <span class="font-medium">{{ $setting->from_address }}</span></p>
                            <p class="text-gray-600 mb-1">Сервер: <span class="font-medium">{{ $setting->host }}:{{ $setting->port }}</span></p>
                            <p class="text-sm text-gray-500">Отдел: {{ $setting->department->name }}</p>

                            @if($setting->meta && isset($setting->meta['last_test_at']))
                                <p class="text-xs text-gray-400 mt-2">
                                    Последний тест: {{ \Carbon\Carbon::parse($setting->meta['last_test_at'])->diffForHumans() }}
                                    @if($setting->meta['last_test_result'] === 'success')
                                        <span class="text-green-600">✓ Успешно</span>
                                    @else
                                        <span class="text-red-600">✗ Ошибка</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="testSmtp({{ $setting->id }})"
                                    class="text-green-500 hover:text-green-700"
                                    title="Тест">
                                <i class="fas fa-vial"></i>
                            </button>
                            <button onclick="editSmtp({{ $setting->id }})"
                                    class="text-blue-500 hover:text-blue-700"
                                    title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(!$setting->is_default)
                                <form action="{{ route('smtp-settings.set-default', $setting) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit" class="text-yellow-500 hover:text-yellow-700"
                                            title="Сделать по умолчанию">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('smtp-settings.toggle-active', $setting) }}"
                                  method="POST">
                                @csrf
                                <button type="submit" class="text-{{ $setting->is_active ? 'gray' : 'green' }}-500 hover:text-{{ $setting->is_active ? 'gray' : 'green' }}-700"
                                        title="{{ $setting->is_active ? 'Деактивировать' : 'Активировать' }}">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </form>
                            <form action="{{ route('smtp-settings.destroy', $setting) }}"
                                  method="POST"
                                  onsubmit="return confirm('Удалить настройки SMTP?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"
                                        title="Удалить">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        @if($settings->hasPages())
            <div class="mt-4">
                {{ $settings->links() }}
            </div>
        @endif
    </div>

    <!-- Модальное окно создания SMTP настроек -->
    <dialog id="createSmtpModal" class="rounded-lg shadow-xl p-6 max-w-2xl">
        <h3 class="text-lg font-bold mb-4">Новые SMTP настройки</h3>
        <form action="{{ route('smtp-settings.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Отдел</label>
                    <select name="department_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Выберите отдел</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP сервер</label>
                    <input type="text" name="host" required placeholder="smtp.example.com"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Порт</label>
                    <input type="number" name="port" required placeholder="587"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Шифрование</label>
                    <select name="encryption" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Без шифрования</option>
                        <option value="ssl">SSL</option>
                        <option value="tls">TLS</option>
                        <option value="starttls">STARTTLS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Имя пользователя</label>
                    <input type="text" name="username" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">От кого (email)</label>
                    <input type="email" name="from_address" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">От кого (имя)</label>
                    <input type="text" name="from_name" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="mb-4 flex space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked
                           class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Активен</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_default" value="1"
                           class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">По умолчанию для отдела</span>
                </label>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('createSmtpModal').close()"
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
        function testSmtp(id) {
            if (!confirm('Отправить тестовое письмо на ваш email?')) {
                return;
            }

            fetch(`/smtp-settings/${id}/test`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    window.location.reload();
                })
                .catch(error => {
                    alert('Ошибка при тестировании SMTP');
                });
        }

        function editSmtp(id) {
            // Здесь можно реализовать модальное окно редактирования
            // или редирект на отдельную страницу
            window.location.href = `/smtp-settings/${id}/edit`;
        }

        // Закрытие модального окна при клике вне его
        document.getElementById('createSmtpModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.close();
            }
        });
    </script>
@endsection
