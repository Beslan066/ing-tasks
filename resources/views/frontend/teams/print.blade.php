<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Печать - Команда</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .print-date { text-align: right; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total { font-weight: bold; background-color: #f9f9f9; }
        .completed { color: #059669; }
        .overdue { color: #dc2626; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Команда организации</h1>
    <p>Список сотрудников</p>
</div>

<div class="print-date">
    Отчет сформирован: {{ $printDate }}
</div>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Отдел</th>
        <th>Всего задач</th>
        <th>Выполнено</th>
        <th>% выполнения</th>
        <th>Просрочено</th>
        <th>Статус</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        @php
            $stats = $user->getTaskCompletionStats();
            $overdue = $user->assignedTasks()
                ->where('status', '!=', 'выполнена')
                ->where('deadline', '<', now())
                ->count();
        @endphp
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role ? $user->role->name : '-' }}</td>
            <td>{{ $user->department ? $user->department->name : '-' }}</td>
            <td>{{ $stats['total'] }}</td>
            <td class="completed">{{ $stats['completed'] }}</td>
            <td>{{ $stats['completion_rate'] }}%</td>
            <td class="{{ $overdue > 0 ? 'overdue' : '' }}">{{ $overdue }}</td>
            <td>{{ $user->is_active ? 'Активный' : 'Неактивный' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="total">
    Всего сотрудников: {{ $users->count() }}
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
