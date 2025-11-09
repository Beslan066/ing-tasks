<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Печать - {{ $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .print-date { text-align: right; color: #666; margin-bottom: 20px; }
        .user-info { margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-card { border: 1px solid #ddd; padding: 15px; text-align: center; border-radius: 5px; }
        .stat-value { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { color: #666; font-size: 14px; }
        .progress-bar { background: #e5e7eb; border-radius: 10px; height: 10px; margin: 10px 0; }
        .progress-fill { background: #059669; height: 100%; border-radius: 10px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Статистика сотрудника</h1>
    <h2>{{ $user->name }}</h2>
</div>

<div class="print-date">
    Отчет сформирован: {{ $printDate }}
</div>

<div class="user-info">
    <h3>Основная информация</h3>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Роль:</strong> {{ $user->role ? $user->role->name : 'Не назначена' }}</p>
    <p><strong>Отдел:</strong> {{ $user->department ? $user->department->name : 'Не назначен' }}</p>
    <p><strong>Статус:</strong> {{ $user->is_active ? 'Активный' : 'Неактивный' }}</p>
    <p><strong>Зарегистрирован:</strong> {{ $user->created_at->format('d.m.Y') }}</p>
    @if($user->last_login_at)
        <p><strong>Последний вход:</strong> {{ $user->last_login_at->format('d.m.Y H:i') }}</p>
    @endif
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $user->total_tasks_count }}</div>
        <div class="stat-label">Всего задач</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #059669;">{{ $user->completed_tasks_count }}</div>
        <div class="stat-label">Выполнено</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: #2563eb;">{{ $completion_rate }}%</div>
        <div class="stat-label">Средний % выполнения</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: {{ $user->overdue_tasks_count > 0 ? '#dc2626' : '#059669' }};">
            {{ $user->overdue_tasks_count }}
        </div>
        <div class="stat-label">Просрочено</div>
    </div>
</div>

<div style="margin-bottom: 30px;">
    <h3>Прогресс выполнения задач</h3>
    <div class="progress-bar">
        <div class="progress-fill" style="width: {{ min($completion_rate, 100) }}%"></div>
    </div>
    <div style="text-align: center; font-weight: bold; color: #2563eb;">
        {{ $completion_rate }}% выполнения
    </div>
</div>

<div class="no-print" style="margin-top: 30px; text-align: center;">
    <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
        Печатать
    </button>
    <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
        Закрыть
    </button>
</div>
</body>
</html>
