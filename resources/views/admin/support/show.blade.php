@extends('layouts.admin')

@section('title', 'Обращение #' . $ticket->id)

@section('content')
    <div class="row">
        <!-- Основной контент -->
        <div class="col-md-8">
            <!-- Сообщение пользователя -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="material-icons" style="font-size: 18px;">chat</i>
                            <strong>Сообщение пользователя</strong>
                        </div>
                        <small class="text-muted">{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $ticket->subject }}</h5>
                    <p class="card-text text-muted mb-3">
                        <i class="bi bi-person-circle"></i> {{ $ticket->name }}
                        <i class="bi bi-envelope ms-2"></i> {{ $ticket->email }}
                    </p>
                    <div class="alert alert-light">
                        {{ nl2br(e($ticket->message)) }}
                    </div>

                    @if($ticket->hasAttachment())
                        <div class="alert alert-info">
                            <i class="bi bi-paperclip"></i>
                            <strong>Вложение:</strong>
                            <a href="{{ route('admin.support.download', $ticket) }}" class="alert-link">
                                {{ $ticket->attachment_original_name }}
                            </a>
                            <small class="text-muted">({{ $ticket->attachment_size }})</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Переписка -->
            @if($ticket->replies->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-chat-dots"></i>
                        <strong>Переписка ({{ $ticket->replies->count() }} ответов)</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($ticket->replies as $reply)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <i class="bi bi-shield-check text-success"></i>
                                            <strong>{{ $reply->admin->name ?? 'Администратор' }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $reply->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                    <p class="mb-0">{{ nl2br(e($reply->message)) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Форма ответа -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-reply-all"></i>
                    <strong>Ответить пользователю</strong>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.support.reply', $ticket) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Ваш ответ</label>
                            <textarea name="message" rows="6" class="form-control"
                                      placeholder="Введите ответ..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Отправить ответ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-md-4">
            <!-- Статус -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-gear"></i>
                    <strong>Управление</strong>
                </div>
                <div class="card-body">
                    <label class="form-label fw-bold">Статус обращения</label>
                    <select id="statusSelect" class="form-select mb-3">
                        <option value="new" {{ $ticket->status == 'new' ? 'selected' : '' }}>🟡 Новое</option>
                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>🔵 В работе</option>
                        <option value="answered" {{ $ticket->status == 'answered' ? 'selected' : '' }}>🟢 Отвечено</option>
                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>⚫ Закрыто</option>
                    </select>

                    <button onclick="updateStatus()" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Обновить статус
                    </button>
                </div>
            </div>

            <!-- Информация -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i>
                    <strong>Информация</strong>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td width="40%"><strong>Создано:</strong></td>
                            <td>{{ $ticket->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Обновлено:</strong></td>
                            <td>{{ $ticket->updated_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ответов:</strong></td>
                            <td>{{ $ticket->replies->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>IP адрес:</strong></td>
                            <td><code>{{ $ticket->user_ip ?? '-' }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Действия -->
            <div class="card border-danger">

                <div class="card-body">
                    <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST"
                          onsubmit="return confirm('Безвозвратно удалить обращение?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Удалить обращение
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateStatus() {
            const status = document.getElementById('statusSelect').value;
            const ticketId = {{ $ticket->id }};

            fetch(`/admin/support/${ticketId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: status })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Статус обновлён!');
                        location.reload();
                    }
                })
                .catch(error => {
                    alert('❌ Ошибка при обновлении статуса');
                });
        }
    </script>
@endsection
