<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ответ на ваше обращение</title>
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
        .ticket-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #16a34a;
        }
        .reply-message {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .reply-message h4 {
            color: #16a34a;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #16a34a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
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
    <h2>📬 Ответ на ваше обращение</h2>
</div>
<div class="content">
    <div class="ticket-info">
        <p><strong>Тема:</strong> {{ $ticket->subject }}</p>
        <p><strong>Дата обращения:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
        <p><strong>Номер обращения:</strong> #{{ $ticket->id }}</p>
    </div>

    <div class="reply-message">
        <h4>👨‍💻 {{ $adminName }} ответил(а):</h4>
        <div style="white-space: pre-wrap;">{{ $reply->message }}</div>
    </div>

    <p>Вы можете ответить на это письмо, чтобы продолжить диалог.</p>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">

    <div style="font-size: 14px; color: #666;">
        <p><strong>Ваше сообщение:</strong></p>
        <div style="background: #f3f4f6; padding: 10px; border-radius: 5px; white-space: pre-wrap;">{{ $ticket->message }}</div>
    </div>
</div>
<div class="footer">
    <p>© {{ date('Y') }} Служба поддержки. Все права защищены.</p>
</div>
</body>
</html>
