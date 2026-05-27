@extends('layouts.admin')

@section('title', 'Обращения в поддержку')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">
                        <i class="material-icons" style="font-size: 20px; vertical-align: middle;">support_agent</i>
                        Обращения в поддержку
                    </h5>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-sm btn-success" onclick="window.location.reload()">
                        <i class="bi bi-arrow-repeat"></i> Обновить
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Фильтры -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Статус</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all">Все статусы</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>🟡 Новые</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>🔵 В работе</option>
                            <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>🟢 Отвечено</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>⚫ Закрыто</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Поиск</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Поиск по имени, email, теме, ID..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-search"></i> Поиск
                            </button>
                            @if(request('search') || request('status') != 'all')
                                <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-x-circle"></i> Сброс
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <span>Всего</span>
                                <h5 class="mb-0">{{ $tickets->total() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица -->
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                    <tr>
                        <th width="60">ID</th>
                        <th>Отправитель</th>
                        <th>Тема</th>
                        <th width="120">Статус</th>
                        <th width="80">Ответов</th>
                        <th width="150">Дата</th>
                        <th width="120">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $ticket->name }}</div>
                                <small class="text-muted">{{ $ticket->email }}</small>
                            </td>
                            <td>{{ Str::limit($ticket->subject, 50) }}</td>
                            <td>
                                @php
                                    $badgeClass = match($ticket->status) {
                                        'new' => 'bg-warning',
                                        'in_progress' => 'bg-info',
                                        'answered' => 'bg-success',
                                        'closed' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                    $statusText = match($ticket->status) {
                                        'new' => '🟡 Новое',
                                        'in_progress' => '🔵 В работе',
                                        'answered' => '🟢 Отвечено',
                                        'closed' => '⚫ Закрыто',
                                        default => $ticket->status
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-center">{{ $ticket->replies_count }}</td>
                            <td>
                                <small>{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('admin.support.show', $ticket) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="deleteTicket({{ $ticket->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">Нет обращений</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <div class="mt-3">
                {{ $tickets->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Форма удаления -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function deleteTicket(id) {
            if (confirm('Вы уверены, что хотите удалить это обращение?')) {
                const form = document.getElementById('deleteForm');
                form.action = '/admin/support/' + id;
                form.submit();
            }
        }
    </script>
@endsection
