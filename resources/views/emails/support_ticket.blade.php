<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Новое обращение в поддержку</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #16a34a;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 5px;
        }
        .value {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="header">
    <h2>📬 Новое обращение в поддержку</h2>
</div>
<div class="content">
    <div class="field">
        <div class="label">👤 Отправитель:</div>
        <div class="value">{{ $ticket['name'] }} ({{ $ticket['email'] }})</div>
    </div>

    <div class="field">
        <div class="label">📌 Тема:</div>
        <div class="value">{{ $ticket['subject'] }}</div>
    </div>

    <div class="field">
        <div class="label">💬 Сообщение:</div>
        <div class="value" style="white-space: pre-wrap;">{{ $ticket['message'] }}</div>
    </div>

    @if(!empty($ticket['attachment_original_name']))
        <div class="field">
            <div class="label">📎 Вложение:</div>
            <div class="value">{{ $ticket['attachment_original_name'] }} ({{ $ticket['attachment_size'] }})</div>
        </div>
    @endif

    <div class="field">
        <div class="label">🕐 Время отправки:</div>
        <div class="value">{{ now()->format('d.m.Y H:i:s') }}</div>
    </div>

    <div class="field">
        <div class="label">🌐 IP адрес:</div>
        <div class="value">{{ $ticket['user_ip'] ?? 'Не определен' }}</div>
    </div>
</div>
<div class="footer">
    <p>Это письмо было отправлено автоматически с сайта. Пожалуйста, не отвечайте на него.</p>
    <p>Для ответа отправителю используйте email: {{ $ticket['email'] }}</p>
</div>
</body>
</html>
