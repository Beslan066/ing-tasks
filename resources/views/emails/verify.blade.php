<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=
1.0">
    <title>Подтверждение Email</title>
    <style>
        /* Инлайн стили для максимальной совместимости с почтовиками */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f5f7; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none; }
        .wrapper { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #10b981, #059669); padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px 30px; text-align: center; color: #374151; line-height: 1.6; }
        .content p { font-size: 16px; margin-bottom: 30px; }
        .btn-container { margin: 30px 0; }
        /* Кнопка с вашим изумрудным градиентом */
        .btn { display: inline-block; padding: 12px 30px; font-size: 16px; font-weight: bold; color: #ffffff !important; text-decoration: none; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3); transition: all 0.3s ease; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- Шапка -->
        <div class="header">
            <h1>Добро пожаловать, {{ $user->name }}!</h1>
        </div>

        <!-- Контент -->
        <div class="content">
            <p>Спасибо за регистрацию. Пожалуйста, подтвердите ваш адрес электронной почты, чтобы получить полный доступ к личному кабинету.</p>

            <div class="btn-container">
                <a href="{{ $url }}" class="btn">Подтвердить Email</a>
            </div>

            <p style="font-size: 14px; color: #6b7280; margin-top: 20px;">
                Если кнопка выше не работает, скопируйте эту ссылку в браузер:<br>
                <a href="{{ $url }}" style="color: #10b981; word-break: break-all;">{{ $url }}</a>
            </p>
        </div>

        <!-- Подвал -->
        <div class="footer">
            <p>Вы получили это письмо, так как зарегистрировались на нашем сайте.</p>
            <p>&copy; {{ date('Y') }} Ваша Компания. Все права защищены.</p>
        </div>
    </div>

</body>
</html>
